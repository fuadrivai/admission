@extends('main-layout.index')

@section('content-child')
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
        </div>
    @endif

    @if ($variants->isEmpty())
        <div class="alert alert-warning">Belum ada varian merchandise yang dapat dihitung.</div>
    @else
        <form method="POST" action="{{ route('merchandise.stock-opnames.store') }}">
            @csrf
            <div class="card mb-4">
                <div class="card-header"><h5 class="mb-0">Informasi Stock Opname</h5></div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label" for="opname-date">Tanggal perhitungan</label>
                            <input id="opname-date" name="opname_date" class="form-control" type="date" required value="{{ old('opname_date', now()->toDateString()) }}">
                        </div>
                        <div class="col-md-8">
                            <label class="form-label" for="opname-notes">Catatan (opsional)</label>
                            <input id="opname-notes" name="notes" class="form-control" maxlength="1000" value="{{ old('notes') }}" placeholder="Contoh: Stock opname bulanan">
                        </div>
                    </div>
                </div>
            </div>

            <div class="alert alert-info">
                Isi jumlah fisik yang benar-benar tersedia di gudang. Stok sistem tidak mengurangi order pending yang belum dibayar karena barangnya belum keluar secara fisik; Stock Overview mengurangi reservasi tersebut untuk menampilkan stok yang masih dapat dipesan.
                Setelah disimpan, selisih langsung dicatat sebagai penyesuaian stok dan tidak dapat diubah dari halaman ini.
            </div>

            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <h5 class="mb-0">Hitung Stok Fisik</h5>
                    <input id="variant-search" class="form-control" type="search" placeholder="Cari produk atau varian" aria-label="Cari produk atau varian" style="max-width:320px">
                </div>
                <div class="px-3 pt-2 text-muted small">Pencarian hanya menandai varian yang cocok; semua varian tetap wajib dihitung.</div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead>
                                <tr><th>Produk</th><th>Varian</th><th class="text-end">Stok sistem</th><th style="min-width:180px" class="text-end">Hitung fisik</th></tr>
                            </thead>
                            <tbody id="opname-variants">
                                @foreach ($variants as $variant)
                                    <tr data-variant-row>
                                        <td>{{ $variant->product->name }}</td>
                                        <td>{{ $variant->color ? $variant->color . ' / ' : '' }}{{ $variant->name }}</td>
                                        <td class="text-end">{{ number_format($variant->system_quantity) }} pcs</td>
                                        <td>
                                            <input name="items[{{ $variant->id }}][counted_quantity]" class="form-control text-end" type="number" min="0" max="999999" step="1" required value="{{ old('items.' . $variant->id . '.counted_quantity') }}" placeholder="Jumlah fisik" aria-label="Jumlah fisik {{ $variant->product->name }} {{ $variant->name }}">
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="d-flex gap-2">
                <button class="btn btn-success" type="submit"><i class="bi bi-check-lg"></i> Simpan dan Sesuaikan Stok</button>
                <a href="{{ route('merchandise.stock-opnames') }}" class="btn btn-light-secondary">Batal</a>
            </div>
        </form>
    @endif
@endsection

@section('content-script')
    <script>
        (function () {
            var search = document.getElementById('variant-search');
            var rows = document.querySelectorAll('[data-variant-row]');
            if (!search) return;

            search.addEventListener('input', function () {
                var term = search.value.toLocaleLowerCase();
                rows.forEach(function (row) {
                    row.classList.toggle('table-warning', term !== '' && row.textContent.toLocaleLowerCase().includes(term));
                });
            });
        })();
    </script>
@endsection
