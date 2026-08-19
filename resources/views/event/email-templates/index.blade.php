@extends('main-layout.index')

@section('content-child')
    <section class="section">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Email Templates - {{ $event->title }}</h5>
                <div class="d-flex gap-2">
                    <a href="{{ route('event.edit', $event) }}" class="btn btn-outline-secondary btn-sm">
                        <i class="fa fa-arrow-left"></i> Back
                    </a>
                    <a href="{{ route('event.email-templates.create', $event) }}" class="btn btn-success btn-sm">Add
                        Template</a>
                </div>
            </div>
            <div class="card-body">
                @if ($templates->isEmpty())
                    <div class="alert alert-info mb-0">No templates found.</div>
                @else
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Key</th>
                                    <th>Subject</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($templates as $template)
                                    <tr>
                                        <td>{{ $template->key }}</td>
                                        <td>{{ $template->subject }}</td>
                                        <td>
                                            <a href="{{ route('event.email-templates.edit', [$event, $template]) }}"
                                                class="btn btn-sm btn-primary">Edit</a>
                                            <form method="POST"
                                                action="{{ route('event.email-templates.destroy', [$event, $template]) }}"
                                                style="display:inline;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger"
                                                    onclick="return confirm('Delete this template?')">Delete</button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </section>
@endsection
