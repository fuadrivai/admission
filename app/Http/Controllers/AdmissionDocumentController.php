<?php

namespace App\Http\Controllers;

use App\Models\Admission;
use App\Models\AdmissionDocument;
use App\Services\AdmissionDocumentService;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Spatie\PdfToImage\Pdf as PdfToImage;
use Illuminate\Http\Request;
use Imagick;
use ImagickPixel;

class AdmissionDocumentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    private AdmissionDocumentService $admissionDocumentService;

    public function __construct(AdmissionDocumentService $admissionDocumentService)
    {
        $this->admissionDocumentService = $admissionDocumentService;
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
            $mapping = [
                'ktp_father'        => 'father',
                'ktp_mother'        => 'mother',
                'birth_certificate' => 'child',
                'family_card'       => 'family',
            ];

            foreach ($mapping as $field => $owner) {
                if (!$request->hasFile($field)) continue;

                $file = $request->file($field);

                $path = $file->store(
                    "admission/{$request->admission_id}/originals",
                    'public'
                );

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
            $outputDir = storage_path("app/public/admission/{$request->admission_id}/processed");

            if (!is_dir($outputDir)) {
                mkdir($outputDir, 0755, true);
            }

            foreach ($documents as $doc) {

                $sourcePath = storage_path('app/public/' . $doc->file_path);
                if (!file_exists($sourcePath)) continue;

                $label = strtoupper(str_replace('_', ' ', $doc->type));
                $ext   = strtolower(pathinfo($sourcePath, PATHINFO_EXTENSION));

                if (in_array($ext, ['jpg', 'jpeg', 'png'])) {

                    $outputPath = "{$outputDir}/{$doc->id}.jpg";
                    $this->normalizeImage($sourcePath, $outputPath);

                    $images[] = [
                        'label' => $label,
                        'path'  => $outputPath,
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
                            'label' => "{$label} (Page {$i})",
                            'path'  => $finalPath,
                        ];
                    }
                }
            }

            if (empty($images)) {
                return response()->json([
                    'message' => 'Tidak ada dokumen untuk diproses'
                ], 422);
            }

            $groups = array_chunk($images, 2);

            $pdf = Pdf::loadView('pdf.student-file', [
                'groups' => $groups
            ])
            ->setPaper('a4')
            ->setWarnings(false);

            $finalPdfPath = storage_path(
                "app/public/admission/{$request->admission_id}/all-documents.pdf"
            );

            $pdf->save($finalPdfPath);

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

}
