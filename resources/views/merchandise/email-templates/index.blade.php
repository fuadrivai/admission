@extends('main-layout.index')

@section('content-child')
    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">Merchandise Email Templates</h5>
        </div>
        <div class="card-body">
            <p class="text-muted">These templates are sent when an order is created and after Xendit confirms payment.</p>
            <div class="table-responsive">
                <table class="table table-striped align-middle">
                    <thead>
                        <tr>
                            <th>Template</th>
                            <th>Email subject</th>
                            <th>Sent when</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($templates as $template)
                            <tr>
                                <td>{{ $template->name }}</td>
                                <td>{{ $template->subject }}</td>
                                <td>{{ $template->key === 'invoice' ? 'Xendit invoice is created' : 'Payment is confirmed' }}</td>
                                <td class="text-end">
                                    <a class="btn btn-sm btn-primary"
                                        href="{{ route('merchandise.email-templates.edit', $template->key) }}">Edit</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
