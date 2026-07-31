@extends('main-layout.index')

@section('content-style')
    <style>
        .leaderboard-kpi-card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }
        .leaderboard-kpi-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
        }
        .kpi-icon-wrapper {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.4rem;
        }
        .rank-badge {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-weight: 800;
            font-size: 0.9rem;
        }
        .rank-1 { background-color: #ffd700; color: #855900; }
        .rank-2 { background-color: #e0e0e0; color: #424242; }
        .rank-3 { background-color: #cd7f32; color: #ffffff; }
        .rank-other { background-color: #f1f3f5; color: #495057; }
    </style>
@endsection

@section('content-child')
    <section class="section">
        <!-- Filter Card -->
        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <form method="GET" action="{{ route('uniform.leaderboard') }}" id="filterForm">
                    <div class="row align-items-end g-3">
                        <div class="col-md-3">
                            <label class="form-label font-semibold text-secondary small mb-1">
                                <i class="bi bi-building me-1"></i> Branch
                            </label>
                            <select name="branch" class="form-select">
                                <option value="all" {{ ($filters['branch'] ?? 'all') == 'all' ? 'selected' : '' }}>All Branches</option>
                                @foreach($branches as $b)
                                    <option value="{{ $b->id }}" {{ ($filters['branch'] ?? '') == $b->id ? 'selected' : '' }}>{{ $b->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label font-semibold text-secondary small mb-1">
                                <i class="bi bi-credit-card me-1"></i> Payment Status
                            </label>
                            <select name="status" class="form-select">
                                <option value="all" {{ ($filters['status'] ?? '') == 'all' ? 'selected' : '' }}>All Status</option>
                                <option value="PAID" {{ ($filters['status'] ?? 'PAID') == 'PAID' ? 'selected' : '' }}>Paid Only</option>
                                <option value="PENDING" {{ ($filters['status'] ?? '') == 'PENDING' ? 'selected' : '' }}>Pending / Unpaid</option>
                                <option value="EXPIRED" {{ ($filters['status'] ?? '') == 'EXPIRED' ? 'selected' : '' }}>Expired</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label font-semibold text-secondary small mb-1">
                                <i class="bi bi-calendar-event me-1"></i> Start Date
                            </label>
                            <input type="date" name="start_date" class="form-control" value="{{ $filters['start_date'] ?? '' }}">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label font-semibold text-secondary small mb-1">
                                <i class="bi bi-calendar-event me-1"></i> End Date
                            </label>
                            <input type="date" name="end_date" class="form-control" value="{{ $filters['end_date'] ?? '' }}">
                        </div>
                        <div class="col-md-2 text-end">
                            <a href="{{ route('uniform.leaderboard') }}" class="btn btn-outline-secondary me-1" title="Reset Filters">
                                <i class="bi bi-arrow-counterclockwise"></i>
                            </a>
                            <button type="submit" class="btn btn-primary fw-bold">
                                <i class="bi bi-filter me-1"></i> Filter
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- KPI Summary Cards -->
        <div class="row g-3 mb-4">
            <div class="col-12 col-sm-6 col-xl-3">
                <div class="card leaderboard-kpi-card bg-white h-100">
                    <div class="card-body p-3">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <span class="text-muted font-semibold small d-block mb-1">Best Selling Product</span>
                                <h5 class="fw-bold text-dark mb-0 text-truncate" style="max-width: 180px;" title="{{ $topProductName }}">
                                    {{ $topProductName }}
                                </h5>
                            </div>
                            <div class="kpi-icon-wrapper bg-warning text-white shadow-sm">
                                🥇
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-xl-3">
                <div class="card leaderboard-kpi-card bg-white h-100">
                    <div class="card-body p-3">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <span class="text-muted font-semibold small d-block mb-1">Total Revenue</span>
                                <h4 class="fw-extrabold text-success mb-0">
                                    Rp {{ number_format($totalRevenue, 0, ',', '.') }}
                                </h4>
                            </div>
                            <div class="kpi-icon-wrapper bg-success text-white shadow-sm">
                                <i class="bi bi-cash-stack"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-xl-3">
                <div class="card leaderboard-kpi-card bg-white h-100">
                    <div class="card-body p-3">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <span class="text-muted font-semibold small d-block mb-1">Total Items Sold</span>
                                <h4 class="fw-extrabold text-primary mb-0">
                                    {{ number_format($totalItems) }} <span class="fs-6 fw-normal text-muted">pcs</span>
                                </h4>
                            </div>
                            <div class="kpi-icon-wrapper bg-primary text-white shadow-sm">
                                <i class="bi bi-box-seam-fill"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-6 col-xl-3">
                <div class="card leaderboard-kpi-card bg-white h-100">
                    <div class="card-body p-3">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <span class="text-muted font-semibold small d-block mb-1">Total Orders</span>
                                <h4 class="fw-extrabold text-info mb-0">
                                    {{ number_format($totalOrders) }} <span class="fs-6 fw-normal text-muted">orders</span>
                                </h4>
                            </div>
                            <div class="kpi-icon-wrapper bg-info text-white shadow-sm">
                                <i class="bi bi-cart-check-fill"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Leaderboard Main Content -->
        <div class="row g-4">
            <!-- Left Column: Product Leaderboard -->
            <div class="col-lg-8">
                <div class="card shadow-sm border-0 rounded-3">
                    <div class="card-header bg-transparent border-bottom py-3 d-flex align-items-center justify-content-between">
                        <h5 class="fw-bold text-dark mb-0">
                            <i class="bi bi-trophy-fill text-warning me-2"></i> Top Selling Uniform Products
                        </h5>
                        <span class="badge bg-light-primary text-primary font-semibold">
                            Ranked by Units Sold
                        </span>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light text-secondary small">
                                    <tr>
                                        <th class="text-center" style="width: 60px;">Rank</th>
                                        <th>Product Info</th>
                                        <th class="text-center">Top Size</th>
                                        <th class="text-center">Orders</th>
                                        <th class="text-center">Units Sold</th>
                                        <th class="text-end">Revenue</th>
                                        <th class="text-center" style="width: 130px;">Sales Share</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($topProducts as $index => $prod)
                                        @php
                                            $rank = $index + 1;
                                            $rankClass = 'rank-other';
                                            $badgeText = "#{$rank}";
                                            if ($rank === 1) { $rankClass = 'rank-1'; $badgeText = '🥇'; }
                                            elseif ($rank === 2) { $rankClass = 'rank-2'; $badgeText = '🥈'; }
                                            elseif ($rank === 3) { $rankClass = 'rank-3'; $badgeText = '🥉'; }
                                            
                                            $sharePercent = $totalItems > 0 ? round(($prod->total_qty / $totalItems) * 100, 1) : 0;
                                        @endphp
                                        <tr>
                                            <td class="text-center">
                                                <span class="rank-badge {{ $rankClass }}">{{ $badgeText }}</span>
                                            </td>
                                            <td>
                                                <div class="fw-bold text-dark">{{ $prod->product_name }}</div>
                                                <small class="text-muted font-monospace">{{ $prod->product_code }}</small>
                                            </td>
                                            <td class="text-center">
                                                @if($prod->top_size && $prod->top_size !== '-')
                                                    <span class="badge bg-light-secondary text-dark px-2 py-1">{{ $prod->top_size }}</span>
                                                @else
                                                    <span class="text-muted small">-</span>
                                                @endif
                                            </td>
                                            <td class="text-center fw-semibold text-dark">
                                                {{ number_format($prod->order_count) }}
                                            </td>
                                            <td class="text-center">
                                                <span class="badge bg-light-primary text-primary fw-extrabold fs-6 px-3 py-1">
                                                    {{ number_format($prod->total_qty) }}
                                                </span>
                                            </td>
                                            <td class="text-end fw-bold text-success">
                                                Rp {{ number_format($prod->total_revenue, 0, ',', '.') }}
                                            </td>
                                            <td class="text-center">
                                                <div class="small fw-semibold mb-1">{{ $sharePercent }}%</div>
                                                <div class="progress" style="height: 6px;">
                                                    <div class="progress-bar bg-primary" role="progressbar" style="width: {{ $sharePercent }}%" aria-valuenow="{{ $sharePercent }}" aria-valuemin="0" aria-valuemax="100"></div>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="text-center text-muted py-4">
                                                <i class="bi bi-inbox fs-3 d-block mb-1 text-secondary"></i>
                                                No uniform product sales recorded for the selected filter.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column: Branch & Level Leaderboards -->
            <div class="col-lg-4">
                <!-- Branch Leaderboard -->
                <div class="card shadow-sm border-0 rounded-3 mb-4">
                    <div class="card-header bg-transparent border-bottom py-3">
                        <h6 class="fw-bold text-dark mb-0">
                            <i class="bi bi-building text-primary me-2"></i> Performance by Branch
                        </h6>
                    </div>
                    <div class="card-body p-0">
                        <ul class="list-group list-group-flush">
                            @forelse($branchLeaderboard as $bRank => $branch)
                                @php
                                    $branchShare = $totalRevenue > 0 ? round(($branch->total_revenue / $totalRevenue) * 100, 1) : 0;
                                @endphp
                                <li class="list-group-item p-3">
                                    <div class="d-flex align-items-center justify-content-between mb-1">
                                        <div class="fw-bold text-dark">
                                            <span class="badge bg-light text-dark border me-1">#{{ $bRank + 1 }}</span>
                                            {{ $branch->branch_name }}
                                        </div>
                                        <span class="fw-bold text-success small">
                                            Rp {{ number_format($branch->total_revenue, 0, ',', '.') }}
                                        </span>
                                    </div>
                                    <div class="d-flex align-items-center justify-content-between text-muted small mb-1">
                                        <span>{{ number_format($branch->total_orders) }} Orders ({{ number_format($branch->total_items_sold) }} items)</span>
                                        <span>{{ $branchShare }}%</span>
                                    </div>
                                    <div class="progress" style="height: 5px;">
                                        <div class="progress-bar bg-success" role="progressbar" style="width: {{ $branchShare }}%" aria-valuenow="{{ $branchShare }}" aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                </li>
                            @empty
                                <li class="list-group-item text-center text-muted py-3">No branch sales data available.</li>
                            @endforelse
                        </ul>
                    </div>
                </div>

                <!-- Level Leaderboard -->
                <div class="card shadow-sm border-0 rounded-3">
                    <div class="card-header bg-transparent border-bottom py-3">
                        <h6 class="fw-bold text-dark mb-0">
                            <i class="bi bi-diagram-3 text-info me-2"></i> Performance by Level
                        </h6>
                    </div>
                    <div class="card-body p-0">
                        <ul class="list-group list-group-flush">
                            @forelse($levelLeaderboard as $lRank => $lvl)
                                @php
                                    $lvlShare = $totalRevenue > 0 ? round(($lvl->total_revenue / $totalRevenue) * 100, 1) : 0;
                                @endphp
                                <li class="list-group-item p-3">
                                    <div class="d-flex align-items-center justify-content-between mb-1">
                                        <div class="fw-bold text-dark">
                                            <span class="badge bg-light text-dark border me-1">#{{ $lRank + 1 }}</span>
                                            {{ $lvl->level_name }}
                                        </div>
                                        <span class="fw-bold text-info small">
                                            Rp {{ number_format($lvl->total_revenue, 0, ',', '.') }}
                                        </span>
                                    </div>
                                    <div class="d-flex align-items-center justify-content-between text-muted small mb-1">
                                        <span>{{ number_format($lvl->total_orders) }} Orders</span>
                                        <span>{{ $lvlShare }}%</span>
                                    </div>
                                    <div class="progress" style="height: 5px;">
                                        <div class="progress-bar bg-info" role="progressbar" style="width: {{ $lvlShare }}%" aria-valuenow="{{ $lvlShare }}" aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                </li>
                            @empty
                                <li class="list-group-item text-center text-muted py-3">No level sales data available.</li>
                            @endforelse
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
