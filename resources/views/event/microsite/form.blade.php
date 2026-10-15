@extends('main-layout.index')

@section('content-child')
    <section class="section">
        <div class="card">
            <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-2">
                <div>
                    <h5 class="card-title mb-1">Microsite: {{ $event->title }}</h5>
                    <div class="small text-muted">
                        Status:
                        @if ($publishedRevision)
                            <span class="badge bg-success">Published</span>
                        @else
                            <span class="badge bg-secondary">Draft</span>
                        @endif
                    </div>
                </div>
                @if ($publishedRevision)
                    <a class="btn btn-sm btn-outline-primary" href="{{ $micrositeUrl }}" target="_blank" rel="noopener">
                        <i class="fa fa-external-link"></i> View published page
                    </a>
                @endif
            </div>
            <div class="card-body">
                @if (session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <div>Please review the highlighted fields.</div>
                        <ul class="mb-0 mt-1">
                            @foreach ($errors->all() as $message)
                                <li>{{ $message }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="alert alert-info">
                    <strong>Public URL:</strong>
                    <a href="{{ $micrositeUrl }}" target="_blank" rel="noopener">{{ $micrositeUrl }}</a>
                    <div class="small mt-1">The published URL is public and does not require an account. Saving a draft does not change it.</div>
                </div>

                @if ($publishedRevision)
                    <div class="border rounded p-3 mb-4">
                        <h6>Public check-in QR code</h6>
                        <p class="small text-muted mb-2">Display this QR code at the reception desk. It opens the public check-in page.</p>
                        <img src="{{ route('microsite.qr', $event->slug) }}" alt="QR code for {{ $event->title }} check-in"
                            width="220" height="220">
                        <div>
                            <a href="{{ route('microsite.qr', $event->slug) }}" download="event-check-in-{{ $event->slug }}.svg">
                                Download QR code (SVG)
                            </a>
                        </div>
                    </div>
                @endif

                <form method="POST" action="{{ route('event.microsite.update', $event) }}" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <h6 class="mb-3">Event information</h6>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label" for="title">Page title</label>
                            <input class="form-control @error('title') is-invalid @enderror" id="title" name="title"
                                value="{{ old('title', $revision->title ?? $event->title) }}" maxlength="255" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="tagline">Tagline</label>
                            <input class="form-control @error('tagline') is-invalid @enderror" id="tagline" name="tagline"
                                value="{{ old('tagline', $revision->tagline ?? '') }}" maxlength="255">
                        </div>
                        <div class="col-12">
                            <label class="form-label" for="description">Introduction</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description"
                                rows="4" maxlength="5000">{{ old('description', $revision->description ?? '') }}</textarea>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="starts_at">Starts at</label>
                            <input class="form-control @error('starts_at') is-invalid @enderror" type="datetime-local"
                                id="starts_at" name="starts_at" value="{{ old('starts_at', optional($revision->starts_at ?? null)->format('Y-m-d\TH:i')) }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="ends_at">Ends at</label>
                            <input class="form-control @error('ends_at') is-invalid @enderror" type="datetime-local"
                                id="ends_at" name="ends_at" value="{{ old('ends_at', optional($revision->ends_at ?? null)->format('Y-m-d\TH:i')) }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="location">Location</label>
                            <input class="form-control @error('location') is-invalid @enderror" id="location" name="location"
                                value="{{ old('location', $revision->location ?? '') }}" maxlength="255">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="map_url">Map link (HTTPS)</label>
                            <input class="form-control @error('map_url') is-invalid @enderror" type="url" id="map_url"
                                name="map_url" value="{{ old('map_url', $revision->map_url ?? '') }}"
                                placeholder="https://maps.google.com/...">
                        </div>
                        <div class="col-12">
                            <hr class="my-2">
                            <div class="d-flex flex-wrap justify-content-between align-items-center gap-2">
                                <div>
                                    <h6 class="mb-1">Rundown schedule</h6>
                                    <div class="form-text">Add each agenda item with its start and end time.</div>
                                </div>
                                <button class="btn btn-sm btn-outline-primary" type="button" id="add-rundown-item">
                                    <i class="fa fa-plus"></i> Add schedule item
                                </button>
                            </div>
                            <div id="rundown-items" class="mt-3">
                                @foreach (old('rundown_items', $rundownItems) as $index => $item)
                                    <div class="card mb-3 rundown-item">
                                        <div class="card-body">
                                            <div class="d-flex justify-content-between align-items-center mb-3">
                                                <strong class="rundown-item-heading">Schedule item</strong>
                                                <button class="btn btn-sm btn-outline-danger remove-item" type="button">Remove</button>
                                            </div>
                                            <div class="row g-3">
                                                <div class="col-12">
                                                    <label class="form-label">Title</label>
                                                    <input class="form-control" name="rundown_items[{{ $index }}][title]"
                                                        value="{{ $item['title'] ?? '' }}" maxlength="255" required>
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label">Start time</label>
                                                    <input class="form-control" type="time" name="rundown_items[{{ $index }}][starts_at]"
                                                        value="{{ $item['starts_at'] ?? '' }}">
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label">End time</label>
                                                    <input class="form-control" type="time" name="rundown_items[{{ $index }}][ends_at]"
                                                        value="{{ $item['ends_at'] ?? '' }}">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            @error('rundown_items')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-12">
                            <hr class="my-2">
                            <div class="d-flex flex-wrap justify-content-between align-items-center gap-2">
                                <div>
                                    <h6 class="mb-1">Speakers</h6>
                                    <div class="form-text">Add speakers separately from the schedule. Photos are optional (JPEG, PNG, or WebP, max 2 MB).</div>
                                </div>
                                <button class="btn btn-sm btn-outline-primary" type="button" id="add-speaker">
                                    <i class="fa fa-plus"></i> Add speaker
                                </button>
                            </div>
                            <div id="speakers" class="row g-3 mt-1">
                                @foreach (old('speakers', $speakers) as $index => $speaker)
                                    @php
                                        $photoUrl = $speaker['photo_url'] ?? (isset($speaker['photo_path']) ? \Illuminate\Support\Facades\Storage::disk('public')->url($speaker['photo_path']) : null);
                                    @endphp
                                    <div class="col-md-6 speaker-item">
                                        <div class="card h-100">
                                            <div class="card-body">
                                                <div class="d-flex justify-content-between align-items-center mb-3">
                                                    <strong>Speaker</strong>
                                                    <button class="btn btn-sm btn-outline-danger remove-item" type="button">Remove</button>
                                                </div>
                                                <label class="form-label">Name</label>
                                                <input class="form-control mb-3" name="speakers[{{ $index }}][name]"
                                                    value="{{ $speaker['name'] ?? '' }}" maxlength="255" required>
                                                <label class="form-label">Position</label>
                                                <input class="form-control mb-3" name="speakers[{{ $index }}][position]"
                                                    value="{{ $speaker['position'] ?? '' }}" maxlength="255">
                                                <label class="form-label">Photo</label>
                                                <input class="form-control speaker-photo" type="file"
                                                    name="speakers[{{ $index }}][photo]" accept="image/jpeg,image/png,image/webp">
                                                @if ($photoUrl && empty($speaker['remove_photo']))
                                                    <div class="speaker-photo-preview mt-2">
                                                        <img src="{{ $photoUrl }}" alt="{{ $speaker['name'] ?? 'Speaker' }}"
                                                            style="width:72px;height:72px;object-fit:cover;border-radius:50%">
                                                        <button class="btn btn-sm btn-link text-danger remove-photo" type="button">Remove photo</button>
                                                    </div>
                                                    <input type="hidden" name="speakers[{{ $index }}][remove_photo]"
                                                        value="{{ $speaker['remove_photo'] ?? 0 }}">
                                                @else
                                                    <div class="speaker-photo-preview mt-2"></div>
                                                    <input type="hidden" name="speakers[{{ $index }}][remove_photo]"
                                                        value="{{ $speaker['remove_photo'] ?? 0 }}">
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            @error('speakers')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-12">
                            <hr class="my-2">
                            <div class="d-flex flex-wrap justify-content-between align-items-center gap-2">
                                <div>
                                    <h6 class="mb-1">Frequently asked questions</h6>
                                    <div class="form-text">Add each question with its answer. Line breaks are supported.</div>
                                </div>
                                <button class="btn btn-sm btn-outline-primary" type="button" id="add-faq">
                                    <i class="fa fa-plus"></i> Add FAQ
                                </button>
                            </div>
                            <div id="faq-items" class="mt-3">
                                @foreach (old('faq_items', $faqItems) as $index => $faqItem)
                                    <div class="card mb-3 faq-item">
                                        <div class="card-body">
                                            <div class="d-flex justify-content-between align-items-center mb-3">
                                                <strong>FAQ item</strong>
                                                <button class="btn btn-sm btn-outline-danger remove-item" type="button">Remove</button>
                                            </div>
                                            <label class="form-label">Question</label>
                                            <input class="form-control mb-3" name="faq_items[{{ $index }}][question]"
                                                value="{{ $faqItem['question'] ?? '' }}" maxlength="500" required>
                                            <label class="form-label">Answer</label>
                                            <textarea class="form-control" name="faq_items[{{ $index }}][answer]"
                                                rows="4" maxlength="10000" required>{{ $faqItem['answer'] ?? '' }}</textarea>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            @error('faq_items')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="student_name_field_id">Student name field</label>
                            <select class="form-select @error('student_name_field_id') is-invalid @enderror"
                                id="student_name_field_id" name="student_name_field_id">
                                <option value="">Not configured</option>
                                @foreach ($greetingFields as $field)
                                    <option value="{{ $field->id }}"
                                        {{ (string) old('student_name_field_id', $revision->student_name_field_id ?? '') === (string) $field->id ? 'selected' : '' }}>
                                        {{ $field->label }}
                                    </option>
                                @endforeach
                            </select>
                            <div class="form-text">Used for the personalized greeting after a family code is verified.</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="parent_name_field_id">Parent name field</label>
                            <select class="form-select @error('parent_name_field_id') is-invalid @enderror"
                                id="parent_name_field_id" name="parent_name_field_id">
                                <option value="">Not configured</option>
                                @foreach ($greetingFields as $field)
                                    <option value="{{ $field->id }}"
                                        {{ (string) old('parent_name_field_id', $revision->parent_name_field_id ?? '') === (string) $field->id ? 'selected' : '' }}>
                                        {{ $field->label }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <hr class="my-4">
                    <h6 class="mb-3">Microsite colors</h6>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label" for="primary_color">Primary color</label>
                            <input class="form-control form-control-color @error('primary_color') is-invalid @enderror"
                                type="color" id="primary_color" name="primary_color"
                                value="{{ old('primary_color', $revision->primary_color ?? '#174A83') }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="secondary_color">Secondary color</label>
                            <input class="form-control form-control-color @error('secondary_color') is-invalid @enderror"
                                type="color" id="secondary_color" name="secondary_color"
                                value="{{ old('secondary_color', $revision->secondary_color ?? '#D9B66F') }}" required>
                        </div>
                    </div>

                    <div class="d-flex flex-wrap gap-2 mt-4">
                        <button type="submit" class="btn btn-primary"><i class="fa fa-save"></i> Save draft</button>
                    </div>
                </form>

                <form method="POST" action="{{ route('event.microsite.publish', $event) }}" class="mt-2"
                    onsubmit="return confirm('Publish the saved Microsite draft? The current public page will be replaced.');">
                    @csrf
                    <button type="submit" class="btn btn-success">
                        <i class="fa fa-globe"></i> Publish Microsite
                    </button>
                </form>
            </div>
        </div>
    </section>

    <template id="rundown-item-template">
        <div class="card mb-3 rundown-item">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <strong class="rundown-item-heading">Schedule item</strong>
                    <button class="btn btn-sm btn-outline-danger remove-item" type="button">Remove</button>
                </div>
                <div class="row g-3">
                    <div class="col-12">
                        <label class="form-label">Title</label>
                        <input class="form-control" name="rundown_items[__INDEX__][title]" maxlength="255" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Start time</label>
                        <input class="form-control" type="time" name="rundown_items[__INDEX__][starts_at]">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">End time</label>
                        <input class="form-control" type="time" name="rundown_items[__INDEX__][ends_at]">
                    </div>
                </div>
            </div>
        </div>
    </template>

    <template id="speaker-template">
        <div class="col-md-6 speaker-item">
            <div class="card h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <strong>Speaker</strong>
                        <button class="btn btn-sm btn-outline-danger remove-item" type="button">Remove</button>
                    </div>
                    <label class="form-label">Name</label>
                    <input class="form-control mb-3" name="speakers[__INDEX__][name]" maxlength="255" required>
                    <label class="form-label">Position</label>
                    <input class="form-control mb-3" name="speakers[__INDEX__][position]" maxlength="255">
                    <label class="form-label">Photo</label>
                    <input class="form-control speaker-photo" type="file" name="speakers[__INDEX__][photo]"
                        accept="image/jpeg,image/png,image/webp">
                    <div class="speaker-photo-preview mt-2"></div>
                    <input type="hidden" name="speakers[__INDEX__][remove_photo]" value="0">
                </div>
            </div>
        </div>
    </template>

    <template id="faq-template">
        <div class="card mb-3 faq-item">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <strong>FAQ item</strong>
                    <button class="btn btn-sm btn-outline-danger remove-item" type="button">Remove</button>
                </div>
                <label class="form-label">Question</label>
                <input class="form-control mb-3" name="faq_items[__INDEX__][question]" maxlength="500" required>
                <label class="form-label">Answer</label>
                <textarea class="form-control" name="faq_items[__INDEX__][answer]" rows="4" maxlength="10000" required></textarea>
            </div>
        </div>
    </template>

    <script>
        (function () {
            const form = document.querySelector('form[enctype="multipart/form-data"]');
            if (!form) return;

            const containers = {
                rundown_items: document.getElementById('rundown-items'),
                speakers: document.getElementById('speakers'),
                faq_items: document.getElementById('faq-items')
            };

            function nextIndex(container, prefix) {
                let next = 0;
                container.querySelectorAll('[name]').forEach(function (field) {
                    const match = field.name.match(new RegExp('^' + prefix + '\\\\[(\\d+)\\\\]'));
                    if (match) next = Math.max(next, Number(match[1]) + 1);
                });
                return next;
            }

            function addItem(container, templateId, prefix) {
                const index = nextIndex(container, prefix);
                const markup = document.getElementById(templateId).innerHTML.replace(/__INDEX__/g, index);
                container.insertAdjacentHTML('beforeend', markup);
            }

            document.getElementById('add-rundown-item').addEventListener('click', function () {
                addItem(containers.rundown_items, 'rundown-item-template', 'rundown_items');
            });
            document.getElementById('add-speaker').addEventListener('click', function () {
                addItem(containers.speakers, 'speaker-template', 'speakers');
            });
            document.getElementById('add-faq').addEventListener('click', function () {
                addItem(containers.faq_items, 'faq-template', 'faq_items');
            });

            form.addEventListener('click', function (event) {
                const remove = event.target.closest('.remove-item');
                if (remove) {
                    remove.closest('.rundown-item, .speaker-item, .faq-item').remove();
                    return;
                }

                const removePhoto = event.target.closest('.remove-photo');
                if (removePhoto) {
                    const speaker = removePhoto.closest('.speaker-item');
                    speaker.querySelector('[name$="[remove_photo]"]').value = '1';
                    speaker.querySelector('.speaker-photo-preview').innerHTML = '<span class="small text-muted">Photo will be removed when saved.</span>';
                    removePhoto.remove();
                }
            });

            form.addEventListener('change', function (event) {
                if (!event.target.matches('.speaker-photo') || !event.target.files.length) return;
                const speaker = event.target.closest('.speaker-item');
                const removePhoto = speaker.querySelector('[name$="[remove_photo]"]');
                removePhoto.value = '0';
                const preview = speaker.querySelector('.speaker-photo-preview');
                preview.innerHTML = '';
                const image = document.createElement('img');
                image.src = URL.createObjectURL(event.target.files[0]);
                image.alt = 'Speaker photo preview';
                image.style.cssText = 'width:72px;height:72px;object-fit:cover;border-radius:50%';
                preview.appendChild(image);
            });
        })();
    </script>
@endsection
