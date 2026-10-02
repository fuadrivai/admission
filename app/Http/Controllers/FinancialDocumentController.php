<?php

namespace App\Http\Controllers;

use App\Models\AdmissionFinancialDocument;
use App\Services\FinancialDocumentService;
use Illuminate\Http\Request;

class FinancialDocumentController extends Controller
{
    private $financialDocumentService;

    public function __construct(FinancialDocumentService $financialDocumentService)
    {
        $this->financialDocumentService = $financialDocumentService;
    }

    public function index()
    {
        $documents = AdmissionFinancialDocument::with(['sections.items'])
            ->where('type', 'financial')
            ->orderBy('version', 'desc')
            ->get();

        return view('financial-document.index', compact('documents'));
    }

    public function create()
    {
        $document = new AdmissionFinancialDocument([
            'type' => 'financial',
            'name' => 'Financial Agreement',
            'version' => '1.0',
        ]);

        return view('financial-document.editor', ['document' => $document, 'editing' => false]);
    }

    public function store(Request $request)
    {
        $document = $this->financialDocumentService->saveDocument($this->validateDocument($request));

        return redirect()->route('setting.financial-document.edit', $document->id)
            ->with('success', 'Financial document draft created.');
    }

    public function edit(AdmissionFinancialDocument $document)
    {
        if ($document->status !== 'DRAFT') {
            return redirect()->route('setting.financial-document.preview', $document->id);
        }

        $document->load(['sections.items']);

        return view('financial-document.editor', ['document' => $document, 'editing' => true]);
    }

    public function update(Request $request, AdmissionFinancialDocument $document)
    {
        $this->financialDocumentService->saveDocument($this->validateDocument($request), $document->id);

        return redirect()->route('setting.financial-document.edit', $document->id)
            ->with('success', 'Financial document updated.');
    }

    public function preview(AdmissionFinancialDocument $document)
    {
        $document->load(['sections.items']);

        return view('financial-document.preview', compact('document'));
    }

    public function duplicate(AdmissionFinancialDocument $document)
    {
        $copy = $this->financialDocumentService->duplicateVersion($document->id);

        return redirect()->route('setting.financial-document.edit', $copy->id)
            ->with('success', 'A new financial document draft was created.');
    }

    public function publish(AdmissionFinancialDocument $document)
    {
        $this->financialDocumentService->publishDocument($document->id);

        return redirect()->route('setting.financial-document.index')
            ->with('success', 'Financial document published.');
    }

    public function archive(AdmissionFinancialDocument $document)
    {
        $this->financialDocumentService->archiveDocument($document->id);

        return redirect()->route('setting.financial-document.index')
            ->with('success', 'Financial document archived.');
    }

    public function destroy(AdmissionFinancialDocument $document)
    {
        $this->financialDocumentService->destroy($document->id);

        return redirect()->route('setting.financial-document.index')
            ->with('success', 'Financial document draft deleted.');
    }

    private function validateDocument(Request $request)
    {
        return $request->validate([
            'name' => 'required|string|max:255',
            'version' => 'required|string|max:50',
            'effective_at' => 'nullable|date',
            'description' => 'nullable|string',
            'sections' => 'required|array|min:1',
            'sections.*.id' => 'nullable|integer',
            'sections.*.title_en' => 'required|string|max:255',
            'sections.*.title_id' => 'required|string|max:255',
            'sections.*.sort_order' => 'required|integer',
            'sections.*.items' => 'required|array|min:1',
            'sections.*.items.*.id' => 'nullable|integer',
            'sections.*.items.*.number' => 'required|integer|min:0',
            'sections.*.items.*.text_en' => 'required|string',
            'sections.*.items.*.text_id' => 'required|string',
            'sections.*.items.*.sort_order' => 'required|integer',
            'sections.*.items.*.is_required' => 'nullable|boolean',
        ]);
    }
}
