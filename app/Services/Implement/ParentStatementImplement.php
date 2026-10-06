<?php

namespace App\Services\Implement;

use App\Models\AdmissionStatement;
use App\Models\AdmissionStatementDocument;
use App\Models\AdmissionStatementItem;
use App\Models\AdmissionStatementSection;
use App\Models\StatementAgreement;
use App\Services\ParentStatementService;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ParentStatementImplement implements ParentStatementService
{
    public function getPublishedParentDocument()
    {
        return AdmissionStatementDocument::where('type', 'parent')
            ->where('status', 'PUBLISHED')
            ->with([
                'sections' => function ($query) {
                    $query->orderBy('sort_order');
                },
                'sections.items' => function ($query) {
                    $query->orderBy('sort_order')->orderBy('number');
                },
            ])
            ->first();
    }

    public function getDocumentById($id)
    {
        return AdmissionStatementDocument::with([
            'sections' => function ($query) {
                $query->orderBy('sort_order');
            },
            'sections.items' => function ($query) {
                $query->orderBy('sort_order')->orderBy('number');
            },
        ])->findOrFail($id);
    }

    public function saveDocument(array $payload, $id = null)
    {
        $document = $id ? AdmissionStatementDocument::findOrFail($id) : new AdmissionStatementDocument();

        if ($document->exists && $document->status === 'PUBLISHED') {
            throw ValidationException::withMessages([
                'status' => ['A published parent statement cannot be edited directly.'],
            ]);
        }

        return DB::transaction(function () use ($document, $payload) {
            $document->type = strtolower($payload['type'] ?? 'parent');
            $document->name = $payload['name'] ?? 'Parent Statement';
            $document->version = $payload['version'] ?? '1.0';
            $document->effective_at = $payload['effective_at'] ?? null;
            $document->description_en = $payload['description_en'] ?? null;
            $document->description_id = $payload['description_id'] ?? null;
            $document->description = null;
            $document->status = $payload['status'] ?? ($document->exists ? $document->status : 'DRAFT');
            $document->save();

            $incomingSectionIds = [];
            $sectionPayloads = $payload['sections'] ?? [];

            foreach ($sectionPayloads as $sectionIndex => $sectionPayload) {
                $section = !empty($sectionPayload['id'])
                    ? $document->sections()->find($sectionPayload['id'])
                    : null;

                if (!$section) {
                    $section = new AdmissionStatementSection();
                }

                $section->document_id = $document->id;
                $section->title_en = $sectionPayload['title_en'] ?? '';
                $section->title_id = $sectionPayload['title_id'] ?? '';
                $section->sort_order = $sectionPayload['sort_order'] ?? ($sectionIndex + 1);
                $section->is_required = (bool) ($sectionPayload['is_required'] ?? false);
                $section->save();

                $incomingSectionIds[] = $section->id;

                $incomingItemIds = [];
                $itemPayloads = $sectionPayload['items'] ?? [];

                foreach ($itemPayloads as $itemIndex => $itemPayload) {
                    $item = !empty($itemPayload['id'])
                        ? $section->items()->find($itemPayload['id'])
                        : null;

                    if (!$item) {
                        $item = new AdmissionStatementItem();
                    }

                    $item->section_id = $section->id;
                    $item->number = $itemPayload['number'] ?? ($itemIndex + 1);
                    $item->text_en = $itemPayload['text_en'] ?? '';
                    $item->text_id = $itemPayload['text_id'] ?? '';
                    $item->sort_order = $itemPayload['sort_order'] ?? ($itemIndex + 1);
                    $item->is_required = (bool) ($itemPayload['is_required'] ?? false);
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

        $nextVersion = $this->nextVersion($source->version);

        return DB::transaction(function () use ($source, $nextVersion) {
            $duplicate = AdmissionStatementDocument::create([
                'type' => $source->type,
                'name' => $source->name,
                'version' => $nextVersion,
                'status' => 'DRAFT',
                'effective_at' => $source->effective_at,
                'description_en' => $source->description_en ?? $source->description,
                'description_id' => $source->description_id,
            ]);

            foreach ($source->sections as $section) {
                $newSection = AdmissionStatementSection::create([
                    'document_id' => $duplicate->id,
                    'title_en' => $section->title_en,
                    'title_id' => $section->title_id,
                    'sort_order' => $section->sort_order,
                    'is_required' => $section->is_required,
                ]);

                foreach ($section->items as $item) {
                    AdmissionStatementItem::create([
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
                'status' => ['Only a draft document can be published.'],
            ]);
        }

        if ($document->sections()->count() < 1) {
            throw ValidationException::withMessages([
                'sections' => ['The document must contain at least one section.'],
            ]);
        }

        $invalidSections = $document->sections->filter(function ($section) {
            return $section->items()->count() < 1;
        });

        if ($invalidSections->isNotEmpty()) {
            throw ValidationException::withMessages([
                'items' => ['Each section must contain at least one item.'],
            ]);
        }

        return DB::transaction(function () use ($document) {
            AdmissionStatementDocument::where('type', $document->type)
                ->where('status', 'PUBLISHED')
                ->update(['status' => 'ARCHIVED']);

            $document->status = 'PUBLISHED';
            $document->effective_at = $document->effective_at ?? now()->toDateString();
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

        if ($document->status !== 'DRAFT') {
            throw ValidationException::withMessages([
                'status' => ['Only a draft document can be deleted.'],
            ]);
        }

        return $document->delete();
    }

    public function saveParentAgreement($admissionStatementId, array $submittedItemIds, $request)
    {
        $document = $this->getPublishedParentDocument();

        if (!$document) {
            throw ValidationException::withMessages([
                'document' => ['No published parent statement document is available.'],
            ]);
        }

        $requiredItems = $document->sections()->with('items')->get()->flatMap(function ($section) {
            $required = collect();

            if ($section->is_required && $section->items->isNotEmpty()) {
                $required->push($section->items->first());
            }

            $required->push(...$section->items->filter(fn ($item) => (bool) $item->is_required));

            return $required;
        })->unique('id');

        $requiredIds = $requiredItems->pluck('id')->map(fn ($id) => (int) $id)->values()->all();
        $submittedIds = collect($submittedItemIds)->map(fn ($id) => (int) $id)->unique()->values()->all();

        $missing = array_diff($requiredIds, $submittedIds);

        if (!empty($missing)) {
            throw ValidationException::withMessages([
                'statement_item_id' => ['All required parent statement items must be checked before continuing.'],
            ]);
        }

        $admissionStatement = AdmissionStatement::findOrFail($admissionStatementId);

        return DB::transaction(function () use ($admissionStatement, $document, $requiredItems, $submittedIds, $request) {
            foreach ($requiredItems as $item) {
                StatementAgreement::updateOrCreate(
                    [
                        'admission_statement_id' => $admissionStatement->id,
                        'type' => 'Parent',
                        'statement_item_id' => $item->id,
                    ],
                    [
                        'statement_document_id' => $document->id,
                        'agreed' => true,
                        'agreed_at' => now(),
                        'ip_address' => $request->ip(),
                        'user_agent' => $request->userAgent(),
                    ]
                );
            }

            StatementAgreement::where('admission_statement_id', $admissionStatement->id)
                ->where('type', 'Parent')
                ->whereNotIn('statement_item_id', $requiredItems->pluck('id')->all())
                ->delete();

            return StatementAgreement::where('admission_statement_id', $admissionStatement->id)
                ->where('type', 'Parent')
                ->get();
        });
    }

    public function getPublishedRequiredItemIds()
    {
        $document = $this->getPublishedParentDocument();

        if (!$document) {
            return [];
        }

        return $document->sections()->with('items')->get()->flatMap(function ($section) {
            $required = collect();

            if ($section->is_required && $section->items->isNotEmpty()) {
                $required->push($section->items->first());
            }

            $required->push(...$section->items->filter(fn ($item) => (bool) $item->is_required));

            return $required;
        })->unique('id')
            ->pluck('id')
            ->map(fn ($id) => (int) $id)
            ->values()
            ->all();
    }

    protected function nextVersion($version)
    {
        preg_match('/^(\d+)(?:\.(\d+))?$/', (string) $version, $matches);
        $major = isset($matches[1]) ? (int) $matches[1] : 1;

        return ($major + 1) . '.0';
    }
}
