<?php

namespace App\Services\Implement;

use App\Models\AdmissionFinancialDocument;
use App\Models\AdmissionFinancialItem;
use App\Models\AdmissionFinancialSection;
use App\Services\FinancialDocumentService;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class FinancialDocumentImplement implements FinancialDocumentService
{
    public function getPublishedDocument()
    {
        return AdmissionFinancialDocument::where('type', 'financial')
            ->where('status', 'PUBLISHED')
            ->with(['sections.items'])
            ->first();
    }

    public function getDocumentById($id)
    {
        return AdmissionFinancialDocument::with(['sections.items'])->findOrFail($id);
    }

    public function saveDocument(array $payload, $id = null)
    {
        $document = $id ? AdmissionFinancialDocument::findOrFail($id) : new AdmissionFinancialDocument();

        if ($document->exists && $document->status === 'PUBLISHED') {
            throw ValidationException::withMessages([
                'status' => ['A published financial document cannot be edited directly.'],
            ]);
        }

        $versionExists = AdmissionFinancialDocument::where('type', 'financial')
            ->where('version', $payload['version'])
            ->when($document->exists, function ($query) use ($document) {
                $query->where('id', '<>', $document->id);
            })
            ->exists();

        if ($versionExists) {
            throw ValidationException::withMessages([
                'version' => ['This financial document version already exists.'],
            ]);
        }

        return DB::transaction(function () use ($document, $payload) {
            $document->type = 'financial';
            $document->name = $payload['name'];
            $document->version = $payload['version'];
            $document->effective_at = $payload['effective_at'] ?? null;
            $document->description = $payload['description'] ?? null;
            $document->status = $document->exists ? $document->status : 'DRAFT';
            $document->save();

            $incomingSectionIds = [];
            foreach ($payload['sections'] as $sectionIndex => $sectionPayload) {
                $section = !empty($sectionPayload['id'])
                    ? $document->sections()->find($sectionPayload['id'])
                    : null;

                if (!$section) {
                    $section = new AdmissionFinancialSection();
                }

                $section->document_id = $document->id;
                $section->title_en = $sectionPayload['title_en'];
                $section->title_id = $sectionPayload['title_id'];
                $section->sort_order = $sectionPayload['sort_order'] ?? ($sectionIndex + 1);
                $section->save();
                $incomingSectionIds[] = $section->id;

                $incomingItemIds = [];
                foreach ($sectionPayload['items'] as $itemIndex => $itemPayload) {
                    $item = !empty($itemPayload['id'])
                        ? $section->items()->find($itemPayload['id'])
                        : null;

                    if (!$item) {
                        $item = new AdmissionFinancialItem();
                    }

                    $item->section_id = $section->id;
                    $item->number = $itemPayload['number'] ?? ($itemIndex + 1);
                    $item->text_en = $itemPayload['text_en'];
                    $item->text_id = $itemPayload['text_id'];
                    $item->sort_order = $itemPayload['sort_order'] ?? ($itemIndex + 1);
                    $item->is_required = (bool) ($itemPayload['is_required'] ?? true);
                    $item->save();
                    $incomingItemIds[] = $item->id;
                }

                $section->items()->whereNotIn('id', $incomingItemIds)->delete();
            }

            $document->sections()->whereNotIn('id', $incomingSectionIds)->delete();

            return $document->fresh(['sections.items']);
        });
    }

    public function duplicateVersion($id)
    {
        $source = $this->getDocumentById($id);
        $version = $this->nextVersion($source->version);

        while (AdmissionFinancialDocument::where('type', 'financial')->where('version', $version)->exists()) {
            $version = $this->nextVersion($version);
        }

        return DB::transaction(function () use ($source, $version) {
            $duplicate = AdmissionFinancialDocument::create([
                'type' => 'financial',
                'name' => $source->name,
                'version' => $version,
                'status' => 'DRAFT',
                'effective_at' => $source->effective_at,
                'description' => $source->description,
            ]);

            foreach ($source->sections as $section) {
                $newSection = AdmissionFinancialSection::create([
                    'document_id' => $duplicate->id,
                    'title_en' => $section->title_en,
                    'title_id' => $section->title_id,
                    'sort_order' => $section->sort_order,
                ]);

                foreach ($section->items as $item) {
                    AdmissionFinancialItem::create([
                        'section_id' => $newSection->id,
                        'number' => $item->number,
                        'text_en' => $item->text_en,
                        'text_id' => $item->text_id,
                        'sort_order' => $item->sort_order,
                        'is_required' => $item->is_required,
                    ]);
                }
            }

            return $duplicate->fresh(['sections.items']);
        });
    }

    public function publishDocument($id)
    {
        $document = $this->getDocumentById($id);

        if ($document->status !== 'DRAFT') {
            throw ValidationException::withMessages([
                'status' => ['Only a draft financial document can be published.'],
            ]);
        }

        if ($document->sections->isEmpty() || $document->sections->contains(function ($section) {
            return $section->items->isEmpty();
        })) {
            throw ValidationException::withMessages([
                'sections' => ['Every financial document section must contain at least one item.'],
            ]);
        }

        return DB::transaction(function () use ($document) {
            AdmissionFinancialDocument::where('type', 'financial')
                ->where('status', 'PUBLISHED')
                ->where('id', '<>', $document->id)
                ->update(['status' => 'ARCHIVED']);

            $document->status = 'PUBLISHED';
            $document->effective_at = $document->effective_at ?: now()->toDateString();
            $document->save();

            return $document->fresh(['sections.items']);
        });
    }

    public function archiveDocument($id)
    {
        $document = $this->getDocumentById($id);
        $document->status = 'ARCHIVED';
        $document->save();

        return $document;
    }

    public function destroy($id)
    {
        $document = $this->getDocumentById($id);

        if ($document->status !== 'DRAFT' || $document->financialAgreements()->exists()) {
            throw ValidationException::withMessages([
                'status' => ['A financial document version in use or outside DRAFT status cannot be deleted.'],
            ]);
        }

        return $document->delete();
    }

    private function nextVersion($version)
    {
        preg_match('/^(\d+)(?:\.(\d+))?$/', (string) $version, $matches);
        $major = isset($matches[1]) ? (int) $matches[1] : 1;

        return ($major + 1) . '.0';
    }
}
