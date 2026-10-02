<?php

namespace App\Http\Controllers;

use App\Models\AdmissionStatementDocument;
use App\Services\ParentStatementService;
use Illuminate\Http\Request;

class AdmissionStatementDocumentController extends Controller
{
    protected ParentStatementService $parentStatementService;

    public function __construct(ParentStatementService $parentStatementService)
    {
        $this->parentStatementService = $parentStatementService;
    }

    public function index()
    {
        $documents = AdmissionStatementDocument::with(['sections.items'])
            ->orderBy('version', 'desc')
            ->get();

        return view('statement.index', compact('documents'));
    }

    public function create()
    {
        return view('statement.create', ['document' => new AdmissionStatementDocument()]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'type' => 'required|string',
            'name' => 'required|string',
            'version' => 'required|string',
            'effective_at' => 'nullable|date',
            'description' => 'nullable|string',
            'sections' => 'required|array|min:1',
            'sections.*.title_en' => 'required|string',
            'sections.*.title_id' => 'required|string',
            'sections.*.sort_order' => 'required|integer',
            'sections.*.is_required' => 'nullable|boolean',
            'sections.*.items' => 'required|array|min:1',
            'sections.*.items.*.number' => 'required|integer',
            'sections.*.items.*.text_en' => 'required|string',
            'sections.*.items.*.text_id' => 'required|string',
            'sections.*.items.*.sort_order' => 'required|integer',
            'sections.*.items.*.is_required' => 'nullable|boolean',
        ]);

        $document = $this->parentStatementService->saveDocument($validated);

        return redirect()->route('setting.statement.edit', $document->id)->with('success', 'Parent statement created.');
    }

    public function edit(AdmissionStatementDocument $document)
    {
        if ($document->status === 'PUBLISHED') {
            return redirect()->route('setting.statement.preview', $document->id);
        }

        return view('statement.edit', compact('document'));
    }

    public function update(Request $request, AdmissionStatementDocument $document)
    {
        $validated = $request->validate([
            'type' => 'required|string',
            'name' => 'required|string',
            'version' => 'required|string',
            'effective_at' => 'nullable|date',
            'description' => 'nullable|string',
            'sections' => 'required|array|min:1',
            'sections.*.title_en' => 'required|string',
            'sections.*.title_id' => 'required|string',
            'sections.*.sort_order' => 'required|integer',
            'sections.*.is_required' => 'nullable|boolean',
            'sections.*.items' => 'required|array|min:1',
            'sections.*.items.*.number' => 'required|integer',
            'sections.*.items.*.text_en' => 'required|string',
            'sections.*.items.*.text_id' => 'required|string',
            'sections.*.items.*.sort_order' => 'required|integer',
            'sections.*.items.*.is_required' => 'nullable|boolean',
        ]);

        $this->parentStatementService->saveDocument($validated, $document->id);

        return redirect()->route('setting.statement.edit', $document->id)->with('success', 'Parent statement updated.');
    }

    public function preview(AdmissionStatementDocument $document)
    {
        return view('statement.preview', compact('document'));
    }

    public function duplicate(AdmissionStatementDocument $document)
    {
        $newDocument = $this->parentStatementService->duplicateVersion($document->id);

        return redirect()->route('setting.statement.edit', $newDocument->id)->with('success', 'A new draft version was created.');
    }

    public function publish(AdmissionStatementDocument $document)
    {
        $this->parentStatementService->publishDocument($document->id);

        return redirect()->route('setting.statement.index')->with('success', 'Document has been published.');
    }

    public function archive(AdmissionStatementDocument $document)
    {
        $this->parentStatementService->archiveDocument($document->id);

        return redirect()->route('setting.statement.index')->with('success', 'Document archived.');
    }

    public function destroy(AdmissionStatementDocument $document)
    {
        $this->parentStatementService->destroy($document->id);

        return redirect()->route('setting.statement.index')->with('success', 'Draft deleted.');
    }
}
