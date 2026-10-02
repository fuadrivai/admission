<?php

namespace App\Services;

interface FinancialDocumentService
{
    public function getPublishedDocument();
    public function getDocumentById($id);
    public function saveDocument(array $payload, $id = null);
    public function duplicateVersion($id);
    public function publishDocument($id);
    public function archiveDocument($id);
    public function destroy($id);
}
