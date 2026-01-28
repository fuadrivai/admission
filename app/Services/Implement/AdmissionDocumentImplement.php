<?php

namespace App\Services\Implement;

use App\Models\AdmissionDocument;
use App\Services\AdmissionDocumentService;

class AdmissionDocumentImplement implements AdmissionDocumentService
{
    public function get($with= [])
    {
    }

    public function getByAdmissionId($id)
    {
        $document = AdmissionDocument::where('admission_id', $id)->get();
        return $document;
    }

    public function show($id,$with=[])
    {
    }

    public function post($data)
    {
    }

    public function put($data)
    {
    }

    public function delete($id)
    {
    }
}
