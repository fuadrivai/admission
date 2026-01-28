<?php

namespace App\Http\Controllers;

use App\Models\Admission;
use App\Models\AdmissionDocument;
use App\Services\AdmissionDocumentService;
use Carbon\Carbon;
use Illuminate\Http\Request;

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
            'ktp_father' => 'father',
            'ktp_mother' => 'mother',
            'birth_certificate' => 'child',
            'family_card' => 'family',
        ];
        foreach ($mapping as $field => $type) {
            if ($request->hasFile($field)) {
                $file = $request->file($field);

                $path = $file->store(
                    "admission/{$request->admission_id}",
                    'public'
                );

                AdmissionDocument::updateOrCreate(
                    [
                        'admission_id' => $request->admission_id,
                        'type' => $field,
                    ],
                    [
                        'file_path'     => $path,
                        'original_name' => $file->getClientOriginalName(),
                        'mime_type'     => $file->getClientMimeType(),
                        'file_size'     => $file->getSize(),
                        'owner_role'    => $type,
                        'verified_at'   => Carbon::now()
                    ]
                );
            }
        }
        return response()->json(["message"=>"Data berhasil di simpan"],201);
    } catch (\Throwable $th) {
        return response()->json($th->getMessage(), $th->getCode());
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
}
