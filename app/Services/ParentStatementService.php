<?php

namespace App\Services;

interface ParentStatementService
{
    public function getPublishedParentDocument();
    public function getDocumentById($id);
    public function saveDocument(array $payload, $id = null);
    public function duplicateVersion($id);
    public function publishDocument($id);
    public function archiveDocument($id);
    public function destroy($id);
    public function saveParentAgreement($admissionStatementId, array $submittedItemIds, $request);
    public function getPublishedRequiredItemIds();
}
