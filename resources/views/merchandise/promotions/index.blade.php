@extends('main-layout.index')

@section('content-child')
    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if ($errors->any())
        <div class="alert alert-danger"><ul class="mb-0">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
    @endif

    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
        <p class="text-muted mb-0">Kelola diskon, voucher, hadiah, dan paket merchandise. Satu promo berlaku per pesanan.</p>
        <a href="{{ route('merchandise.promotions.create') }}" class="btn btn-primary"><i class="bi bi-plus-lg"></i> Buat Promo</a>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr><th>Promo</th><th>Jenis</th><th>Aturan</th><th>Periode</th><th>Kuota</th><th>Status</th><th class="text-end">Aksi</th></tr>
                    </thead>
                    <tbody>
                        @forelse ($promotions as $promotion)
                            @php
                                $typeLabels = [
                                    'minimum_purchase' => 'Minimum Purchase',
                                    'quantity_discount' => 'Quantity Discount',
                                    'buy_x_get_y' => 'Buy X Get Y',
                                    'free_gift' => 'Free Gift',
                                    'bundle' => 'Bundle',
                                    'voucher' => 'Voucher',
                                ];
                                $rule = $promotion->code ? 'Kode ' . $promotion->code : '';
                                if (in_array($promotion->type, ['minimum_purchase', 'voucher']) && $promotion->discount_type) {
                                    $rule .= ($rule ? ' · ' : '') . ($promotion->minimum_purchase ? 'Min. Rp' . number_format($promotion->minimum_purchase, 0, ',', '.') . ' · ' : '')
                                        . ($promotion->discount_type === 'percentage' ? $promotion->discount_value . '%' : 'Rp' . number_format($promotion->discount_value, 0, ',', '.'));
                                } elseif ($promotion->type === 'quantity_discount') {
                                    $rule = 'Min. ' . $promotion->min_quantity . ' item · ' . ($promotion->discount_type === 'percentage' ? $promotion->discount_value . '%' : 'Rp' . number_format($promotion->discount_value, 0, ',', '.'));
                                } elseif ($promotion->type === 'buy_x_get_y') {
                                    $rule = 'Beli ' . $promotion->buy_quantity . ', gratis ' . $promotion->get_quantity;
                                } elseif ($promotion->type === 'free_gift') {
                                    $rule = 'Min. ' . $promotion->min_quantity . ' item · ' . $promotion->get_quantity . ' hadiah';
                                } elseif ($promotion->type === 'bundle') {
                                    $rule = 'Harga paket Rp' . number_format($promotion->bundle_price, 0, ',', '.');
                                }
                            @endphp
                            <tr>
                                <td class="fw-semibold">{{ $promotion->name }}</td>
                                <td>{{ $typeLabels[$promotion->type] ?? $promotion->type }}</td>
                                <td>{{ $rule ?: 'Semua produk' }}</td>
                                <td class="text-nowrap">
                                    {{ $promotion->valid_from ? $promotion->valid_from->format('d M Y H:i') : 'Tanpa awal' }}
                                    <br><small class="text-muted">{{ $promotion->valid_until ? 's.d. ' . $promotion->valid_until->format('d M Y H:i') : 'Tanpa akhir' }}</small>
                                </td>
                                <td>{{ $promotion->usage_limit ? number_format($promotion->usage_count) . ' / ' . number_format($promotion->usage_limit) : number_format($promotion->usage_count) . ' / ∞' }}</td>
                                <td><span class="badge {{ $promotion->is_active ? 'bg-success' : 'bg-secondary' }}">{{ $promotion->is_active ? 'Aktif' : 'Nonaktif' }}</span></td>
                                <td class="text-end text-nowrap">
                                    <a class="btn btn-sm btn-outline-primary" href="{{ route('merchandise.promotions.edit', $promotion->id) }}">Ubah</a>
                                    <form method="POST" action="{{ route('merchandise.promotions.destroy', $promotion->id) }}" class="d-inline" onsubmit="return confirm('Hapus promo ini? Riwayat pesanan tetap menyimpan nama dan potongan promo.')">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-sm btn-outline-danger" type="submit">Hapus</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="7" class="text-center text-muted py-4">Belum ada promo.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            {{ $promotions->links() }}
        </div>
    </div>
@endsection
