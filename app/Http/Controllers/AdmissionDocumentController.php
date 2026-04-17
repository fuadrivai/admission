<?php

namespace App\Http\Controllers;

use App\Mail\AdmissionEmail;
use App\Models\Admission;
use App\Models\AdmissionDocument;
use App\Services\AdmissionDocumentService;
use App\Services\AdmissionService;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Spatie\PdfToImage\Pdf as PdfToImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Str;
use Imagick;
use ImagickPixel;

use function App\Helpers\imageToBase64;
use function App\Helpers\setupMail;

class AdmissionDocumentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    private AdmissionDocumentService $admissionDocumentService;
    private AdmissionService $admissionService;

    public function __construct(AdmissionDocumentService $admissionDocumentService, AdmissionService $admissionService)
    {
        $this->admissionDocumentService = $admissionDocumentService;
        $this->admissionService = $admissionService;
    }

    public function code($code)
    {
        $admission = Admission::with([
            'level:id,name',
            'grade:id,name',
            'branch:id,name',
            'applicant:id,fullname',
            'applicant.parents:id,applicant_id,role,fullname,phone,email'
        ])->where('code', $code)->firstOrFail();

        $parents = $admission->applicant->parents->keyBy('role');

        $father   = $parents->get('father');
        $mother   = $parents->get('mother');
        $guardian = $parents->get('guardian');

        return view(
            'enrolment.form.student-file',
            compact('admission', 'father', 'mother', 'guardian')
        );
    }
    public function byAdmissionId($id)
    {
        $document = $this->admissionDocumentService->getByAdmissionId($id);
        return response()->json($document);
    }


    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            $admission =  $this->admissionService->show($request->admission_id);
            $baseFolder = $admission->code;
            $mapping = [
                'ktp_father'        => 'father',
                'ktp_mother'        => 'mother',
                'birth_certificate' => 'child',
                'family_card'       => 'family',
            ];

            foreach ($mapping as $field => $owner) {
                if (!$request->hasFile($field)) continue;

                $file = $request->file($field);
                $ext  = strtolower($file->getClientOriginalExtension());
                $filename = Str::random(20) . '.jpg'; // kita paksa jadi JPG
                $path = "{$baseFolder}/originals/{$filename}";
                // $path = $file->store("{$baseFolder}/originals",'admission');

                if (in_array($ext, ['jpg', 'jpeg', 'png'])) {

                    $image = Image::make($file)
                        ->orientate() // fix rotasi iPhone
                        ->resize(1000, null, function ($constraint) {
                            $constraint->aspectRatio();
                            $constraint->upsize();
                        })
                        ->encode('jpg', 60); // compress (0–100)

                    Storage::disk('admission')->put($path, (string) $image);

                } else {
                    // file non-image (pdf, dll)
                    $filename = Str::random(20) . '.' . $ext;

                    Storage::disk('admission')->putFileAs("{$baseFolder}/originals",$file,$filename);

                    $path = "{$baseFolder}/originals/{$filename}";
                }

                AdmissionDocument::updateOrCreate(
                    [
                        'admission_id' => $request->admission_id,
                        'type'         => $field,
                    ],
                    [
                        'file_path'     => $path,
                        'original_name' => $file->getClientOriginalName(),
                        'mime_type'     => $file->getClientMimeType(),
                        'file_size'     => $file->getSize(),
                        'owner_role'    => $owner,
                        'verified_at'   => Carbon::now(),
                    ]
                );
            }

            $documents = AdmissionDocument::where('admission_id', $request->admission_id)
                ->orderBy('id')
                ->get();

            $images    = [];
            $outputDir = Storage::disk('admission')->path("{$baseFolder}/processed");

            if (!is_dir($outputDir)) {
                mkdir($outputDir, 0755, true);
            }

            foreach ($documents as $doc) {
                $sourcePath = Storage::disk('admission')->path($doc->file_path);
                if (!file_exists($sourcePath)) continue;
                $label = strtoupper(str_replace('_', ' ', $doc->type));
                $ext   = strtolower(pathinfo($sourcePath, PATHINFO_EXTENSION));

                if (in_array($ext, ['jpg', 'jpeg', 'png'])) {
                    $outputPath = "{$outputDir}/{$doc->id}.jpg";
                    $this->normalizeImage($sourcePath, $outputPath);
                    $images[] = [
                        'label' => $label,
                        'path'  => $outputPath,
                        'image' => 'data:image/jpeg;base64,' . base64_encode(file_get_contents($outputPath)),
                    ];
                }

                if ($ext === 'pdf') {
                    $pdf = new PdfToImage($sourcePath);
                    $pdf->setResolution(200);
                    for ($i = 1; $i <= $pdf->getNumberOfPages(); $i++) {
                        $tmpPath   = "{$outputDir}/{$doc->id}_tmp_{$i}.jpg";
                        $finalPath = "{$outputDir}/{$doc->id}_page_{$i}.jpg";
                        $pdf->setPage($i)->saveImage($tmpPath);
                        $this->normalizeImage($tmpPath, $finalPath);
                        unlink($tmpPath);
                        $images[] = [
                            'label' => "{$label}",
                            'path'  => $finalPath,
                            'image'  => 'data:image/jpeg;base64,' . base64_encode(file_get_contents($finalPath)),
                        ];
                    }
                }
            }

            if (empty($images)) {
                return response()->json([
                    'message' => 'Tidak ada dokumen untuk diproses'
                ], 422);
            }

            
            $logoPath = public_path('assets/images/Logo-all-branch.png');
            $imageBase64 = imageToBase64($logoPath);

            $pdf = Pdf::loadView('pdf.student-file', [
                'data'=>$admission,
                'groups' => $images,
                'logo'=>$imageBase64
            ])
            ->setPaper('a4')
            ->setWarnings(false);

            $tempPath = storage_path('app/temp-' . uniqid() . '.pdf');
            $pdf->save($tempPath);

            $filename = "Documents-{$admission->applicant->fullname}";

            $finalPdfPath = Storage::disk('admission')->path("{$baseFolder}/{$filename}.pdf");
            $this->compressPdf($tempPath, $finalPdfPath);

            unlink($tempPath);
            
            $this->deleteFolder($outputDir);

            $admission->subject  = 'Submitted Documents Summary';
            $admission->template = 'email-template.student-file';

            setupMail($admission->branch_id);
            $emails = $admission->applicant->parents
                ->pluck('email')
                ->filter()
                ->unique()
                ->values()
                ->toArray();

            if (!empty($emails)) {
                Mail::to($emails)->send(
                    (new AdmissionEmail($admission))
                        ->attach($finalPdfPath, [
                            'as'   => "$filename.pdf",
                            'mime' => 'application/pdf',
                        ])
                );
            }

            $admission->activities()->create([
                'prospects_id' => $admission->enrolment->prospects_id,
                'note'=>"document upload completed on ". Carbon::now()->toDateTimeString(),
            ]);

            return response()->json([
                'message'  => 'Dokumen berhasil digabung menjadi 1 PDF',
                'pdf_path' => "admission/{$request->admission_id}/all-documents.pdf"
            ], 201);

        } catch (\Throwable $e) {
            return response()->json([
                'message' => 'Terjadi kesalahan saat memproses dokumen',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }


    /**
     * Display the specified resource.
     *
     * @param  \App\Models\AdmissionDocument  $admissionDocument
     * @return \Illuminate\Http\Response
     */
    public function show(AdmissionDocument $admissionDocument)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\AdmissionDocument  $admissionDocument
     * @return \Illuminate\Http\Response
     */
    public function edit(AdmissionDocument $admissionDocument)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\AdmissionDocument  $admissionDocument
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, AdmissionDocument $admissionDocument)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\AdmissionDocument  $admissionDocument
     * @return \Illuminate\Http\Response
     */
    public function destroy(AdmissionDocument $admissionDocument)
    {
        //
    }

    private function normalizeImage(
        string $inputPath,
        string $outputPath
    ): void {
        $img = new \Imagick($inputPath);

        $img->setBackgroundColor(new \ImagickPixel('white'));
        $img->mergeImageLayers(\Imagick::LAYERMETHOD_FLATTEN);

        // Rotate jika portrait
        if ($img->getImageHeight() > $img->getImageWidth()) {
            $img->rotateImage(new \ImagickPixel('white'), 90);
        }

        // Resize konsisten
        $img->resizeImage(2480,1650,\Imagick::FILTER_LANCZOS,1,true);

        $img->setImageFormat('jpg');
        $img->writeImage($outputPath);
        $img->clear();
    }

    private function compressPdf($source, $destination)
    {
        $source = escapeshellarg($source);
        $destination = escapeshellarg($destination);

        $command = "gs -sDEVICE=pdfwrite \
            -dCompatibilityLevel=1.4 \
            -dPDFSETTINGS=/ebook \
            -dDownsampleColorImages=true \
            -dColorImageResolution=100 \
            -dNOPAUSE -dQUIET -dBATCH \
            -sOutputFile={$destination} {$source}";

        exec($command);
    }
    
    private function deleteFolder($dir)
    {
        if (!is_dir($dir)) return;
    
        foreach (scandir($dir) as $file) {
            if ($file != '.' && $file != '..') {
                $path = $dir . '/' . $file;
                is_dir($path)
                    ? $this->deleteFolder($path)
                    : unlink($path);
            }
        }
    
        rmdir($dir);
    }

}
