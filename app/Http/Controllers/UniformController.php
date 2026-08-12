<?php

namespace App\Http\Controllers;

use App\Exports\UniformOrderExport;
use App\Mail\AdmissionEmail;
use App\Models\Branch;
use App\Models\EmailSetting;
use App\Models\Grade;
use App\Models\Level;
use App\Models\UniformPrice;
use App\Models\UniformProduct;
use App\Models\UniformOrder;
use App\Models\UniformOrderDetail;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\Utilities\Request as UtilitiesRequest;

use function App\Helpers\codeGenerator;
use function App\Helpers\createXenditInvoice;



class UniformController extends Controller
{

    public function open(Request $request)
    {
        $query = UniformOrder::with(['branch', 'level', 'grade', 'details', 'pickupUser']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($orderQuery) use ($search) {
                $orderQuery->where('code', 'like', "%{$search}%")
                    ->orWhere('student_name', 'like', "%{$search}%")
                    ->orWhere('parent_name', 'like', "%{$search}%")
                    ->orWhere('parent_phone', 'like', "%{$search}%");
            });
        }

        if ($request->filled('branch') && $request->branch !== 'all') {
            $query->where('branch_id', $request->branch);
        }

        if ($request->filled('level') && $request->level !== 'all') {
            $query->where('level_id', $request->level);
        }

        if ($request->filled('product') && $request->product !== 'all') {
            $query->whereHas('details', function ($detailQuery) use ($request) {
                $detailQuery->where('uniform_product_id', $request->product);
            });
        }

        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('payment_status', $request->status);
        }

        if ($request->filled('order_date_from')) {
            $query->where('order_date', '>=', Carbon::parse($request->order_date_from)->startOfDay());
        }

        if ($request->filled('order_date_to')) {
            $query->where('order_date', '<=', Carbon::parse($request->order_date_to)->endOfDay());
        }

        if ($request->filled('payment_date_from')) {
            $query->where('payment_date', '>=', Carbon::parse($request->payment_date_from)->startOfDay());
        }

        if ($request->filled('payment_date_to')) {
            $query->where('payment_date', '<=', Carbon::parse($request->payment_date_to)->endOfDay());
        }

        $summaryOrders = (clone $query)->get();
        $orders = $query->latest()->paginate(12)->appends($request->query());

        return view('uniform.open', [
            'title' => 'Uniform Collection Monitor',
            'orders' => $orders,
            'branches' => Branch::with('levels')->orderBy('name')->get(),
            'products' => UniformProduct::orderBy('name')->get(),
            'summary' => [
                'total' => $summaryOrders->count(),
                'paid' => $summaryOrders->filter(function ($order) {
                    return in_array(strtoupper($order->payment_status), ['PAID', 'SETTLED', 'COMPLETED']);
                })->count(),
                'pickedUp' => $summaryOrders->whereNotNull('picked_up_at')->count(),
            ],
        ]);
    }

    public function confirmPickup(UniformOrder $uniform)
    {
        if (!in_array(strtoupper($uniform->payment_status), ['PAID', 'SETTLED', 'COMPLETED'])) {
            return response()->json([
                'message' => 'Only orders with a paid payment status can be marked as collected.',
            ], 422);
        }

        if ($uniform->picked_up_at) {
            return response()->json([
                'message' => 'This order has already been marked as collected.',
            ], 422);
        }

        $uniform->update([
            'picked_up_at' => now(),
            'picked_up_by' => auth()->id() ?? "User",
        ]);

        $confirmedOrder = $uniform->fresh('pickupUser');

        return response()->json([
            'message' => 'Uniform collection confirmed.',
            'picked_up_at' => $confirmedOrder->picked_up_at->format('d M Y, H:i'),
            'picked_up_by' => optional($confirmedOrder->pickupUser)->name ?? 'User #' . $confirmedOrder->picked_up_by,
        ]);
    }

    public function index(Request $request)
    {
        $query = UniformOrder::with(['branch', 'level', 'grade', 'details', 'pickupUser']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('code', 'like', "%{$search}%")
                  ->orWhere('student_name', 'like', "%{$search}%")
                  ->orWhere('parent_name', 'like', "%{$search}%")
                  ->orWhere('parent_email', 'like', "%{$search}%")
                  ->orWhere('parent_phone', 'like', "%{$search}%");
            });
        }

        if ($request->filled('start_date')) {
            $startDate = Carbon::parse($request->start_date)->startOfDay();
            $query->where('created_at', '>=', $startDate);
        }

        if ($request->filled('end_date')) {
            $endDate = Carbon::parse($request->end_date)->endOfDay();
            $query->where('created_at', '<=', $endDate);
        }

        if ($request->filled('branch') && $request->branch !== 'all') {
            $query->where('branch_id', $request->branch);
        }

        if ($request->filled('level') && $request->level !== 'all') {
            $query->where('level_id', $request->level);
        }

        if ($request->filled('product') && $request->product !== 'all') {
            $query->whereHas('details', function ($detailQuery) use ($request) {
                $detailQuery->where('uniform_product_id', $request->product);
            });
        }

        if ($request->filled('grade') && $request->grade !== 'all') {
            $query->where('grade_id', $request->grade);
        }

        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('payment_status', $request->status);
        }

        $query->orderBy('created_at', 'desc');

        $allOrders = (clone $query)->get();
        $summary = [
            'total'     => $allOrders->count(),
            'pending'   => $allOrders->whereIn('payment_status', ['PENDING', 'UNPAID', 'pending', 'unpaid'])->count(),
            'paid'      => $allOrders->whereIn('payment_status', ['PAID', 'paid', 'SETTLED', 'settled', 'COMPLETED', 'completed'])->count(),
            'expired'   => $allOrders->whereIn('payment_status', ['EXPIRED', 'expired'])->count(),
            'cancelled' => $allOrders->whereIn('payment_status', ['CANCEL', 'cancelled', 'CANCELLED'])->count(),
        ];

        $orders = $query->paginate($request->get('perpage', 10))->appends($request->query());

        if ($request->ajax()) {
            return view('uniform._list', compact('orders', 'summary'))->render();
        }

        return view('uniform.index', [
            'title'   => 'Uniform Orders List',
            'orders'  => $orders,
            'summary' => $summary,
        ]);
    }

    public function export(Request $request)
    {
        $query = UniformOrder::with(['branch', 'level', 'grade', 'details']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('code', 'like', "%{$search}%")
                  ->orWhere('student_name', 'like', "%{$search}%")
                  ->orWhere('parent_name', 'like', "%{$search}%")
                  ->orWhere('parent_email', 'like', "%{$search}%")
                  ->orWhere('parent_phone', 'like', "%{$search}%");
            });
        }

        if ($request->filled('start_date')) {
            $query->where('created_at', '>=', Carbon::parse($request->start_date)->startOfDay());
        }

        if ($request->filled('end_date')) {
            $query->where('created_at', '<=', Carbon::parse($request->end_date)->endOfDay());
        }

        if ($request->filled('branch') && $request->branch !== 'all') {
            $query->where('branch_id', $request->branch);
        }

        if ($request->filled('level') && $request->level !== 'all') {
            $query->where('level_id', $request->level);
        }

        if ($request->filled('product') && $request->product !== 'all') {
            $query->whereHas('details', function ($detailQuery) use ($request) {
                $detailQuery->where('uniform_product_id', $request->product);
            });
        }

        if ($request->filled('grade') && $request->grade !== 'all') {
            $query->where('grade_id', $request->grade);
        }

        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('payment_status', $request->status);
        }

        if ($request->filled('order_date_from')) {
            $query->where('order_date', '>=', Carbon::parse($request->order_date_from)->startOfDay());
        }

        if ($request->filled('order_date_to')) {
            $query->where('order_date', '<=', Carbon::parse($request->order_date_to)->endOfDay());
        }

        if ($request->filled('payment_date_from')) {
            $query->where('payment_date', '>=', Carbon::parse($request->payment_date_from)->startOfDay());
        }

        if ($request->filled('payment_date_to')) {
            $query->where('payment_date', '<=', Carbon::parse($request->payment_date_to)->endOfDay());
        }

        $orders = $query->orderBy('created_at', 'desc')->get();
        $timestamps = Carbon::now()->format('Ymd_His');

        return Excel::download(
            new UniformOrderExport($orders),
            'Uniform_Orders_Report_' . $timestamps . '.xlsx',
            \Maatwebsite\Excel\Excel::XLSX
        );
    }

    public function leaderboard(Request $request)
    {
        $branchId  = $request->get('branch', 'all');
        $status    = $request->get('status', 'PAID');
        $startDate = $request->get('start_date');
        $endDate   = $request->get('end_date');

        $orderQuery = UniformOrder::query();

        if ($branchId !== 'all' && !empty($branchId)) {
            $orderQuery->where('branch_id', $branchId);
        }

        if ($status !== 'all' && !empty($status)) {
            $orderQuery->where('payment_status', $status);
        }

        if (!empty($startDate)) {
            $orderQuery->where('created_at', '>=', Carbon::parse($startDate)->startOfDay());
        }

        if (!empty($endDate)) {
            $orderQuery->where('created_at', '<=', Carbon::parse($endDate)->endOfDay());
        }

        $orderIds = (clone $orderQuery)->pluck('id');

        // Overall KPI Metrics
        $totalOrders  = $orderIds->count();
        $totalRevenue = (clone $orderQuery)->sum('total_amount');
        $totalItems   = UniformOrderDetail::whereIn('uniform_order_id', $orderIds)->sum('qty');

        // Product Leaderboard (Ranked by quantity sold)
        $topProducts = UniformOrderDetail::select(
                'uniform_product_id',
                'product_name',
                'product_code',
                DB::raw('SUM(qty) as total_qty'),
                DB::raw('SUM(subtotal) as total_revenue'),
                DB::raw('COUNT(DISTINCT uniform_order_id) as order_count')
            )
            ->whereIn('uniform_order_id', $orderIds)
            ->groupBy('uniform_product_id', 'product_name', 'product_code')
            ->orderBy('total_qty', 'desc')
            ->get();

        foreach ($topProducts as $prod) {
            $sizeDist = UniformOrderDetail::select('size', DB::raw('SUM(qty) as size_qty'))
                ->whereIn('uniform_order_id', $orderIds)
                ->where('uniform_product_id', $prod->uniform_product_id)
                ->whereNotNull('size')
                ->where('size', '!=', '')
                ->groupBy('size')
                ->orderBy('size_qty', 'desc')
                ->first();
            $prod->top_size = $sizeDist ? $sizeDist->size : '-';
        }

        $topProductName = $topProducts->first()->product_name ?? '-';

        // Branch Leaderboard
        $branchLeaderboard = (clone $orderQuery)
            ->select(
                'branch_id',
                'branch_name',
                DB::raw('COUNT(id) as total_orders'),
                DB::raw('SUM(total_items) as total_items_sold'),
                DB::raw('SUM(total_amount) as total_revenue')
            )
            ->groupBy('branch_id', 'branch_name')
            ->orderBy('total_revenue', 'desc')
            ->get();

        // Level Leaderboard
        $levelLeaderboard = (clone $orderQuery)
            ->select(
                'level_id',
                'level_name',
                DB::raw('COUNT(id) as total_orders'),
                DB::raw('SUM(total_amount) as total_revenue')
            )
            ->groupBy('level_id', 'level_name')
            ->orderBy('total_revenue', 'desc')
            ->get();

        $branches = Branch::all();

        return view('uniform.leaderboard', [
            'title'             => 'Uniform Sales Leaderboard',
            'totalOrders'       => $totalOrders,
            'totalRevenue'      => $totalRevenue,
            'totalItems'        => $totalItems,
            'topProductName'    => $topProductName,
            'topProducts'       => $topProducts,
            'branchLeaderboard' => $branchLeaderboard,
            'levelLeaderboard'  => $levelLeaderboard,
            'branches'          => $branches,
            'filters'           => [
                'branch'     => $branchId,
                'status'     => $status,
                'start_date' => $startDate,
                'end_date'   => $endDate,
            ]
        ]);
    }

    public function list()
    {
        return redirect()->route('uniform.open');
    }
    public function show($id)
    {
        $order = UniformOrder::with(['branch', 'level', 'grade', 'details.product', 'pickupUser'])->findOrFail($id);

        return view('uniform.detail', [
            'title' => 'Uniform Order Detail - ' . $order->code,
            'order' => $order,
        ]);
    }

    public function setting()
    {
        $branches = Branch::with(['levels.grades'])->get();
        $products = UniformProduct::all();

        $stats = [
            'total_products' => UniformProduct::count(),
            'total_prices'   => UniformPrice::count(),
            'active_prices'  => UniformPrice::where('is_active', 1)->count(),
            'has_size'       => UniformProduct::where('has_size', 1)->count(),
        ];

        return view('uniform.setting', [
            'title'    => 'Uniform Master & Pricing',
            'branches' => $branches,
            'products' => $products,
            'stats'    => $stats,
        ]);
    }

    public function getProducts()
    {
        $products = UniformProduct::withCount('prices')->get();
        return response()->json($products);
    }

    public function productDatatables(UtilitiesRequest $request)
    {
        $query = UniformProduct::withCount('prices');

        if ($request->filled('unit_type')) {
            $query->where('unit_type', $request->unit_type);
        }

        if ($request->filled('has_size')) {
            $query->where('has_size', $request->has_size);
        }

        return datatables()->of($query)
            ->addColumn('price_count', function ($row) {
                return $row->prices_count;
            })
            ->make(true);
    }

    public function storeProduct(Request $request)
    {
        $validated = $request->validate([
            'code'      => 'required|string|max:50|unique:uniform_products,code',
            'name'      => 'required|string|max:255',
            'unit_type' => 'required|in:pcs,meter',
            'has_size'  => 'nullable|boolean',
        ]);

        $product = UniformProduct::create([
            'code'      => $validated['code'],
            'name'      => $validated['name'],
            'unit_type' => $validated['unit_type'],
            'has_size'  => isset($validated['has_size']) && $validated['has_size'] ? 1 : 0,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Master Product created successfully',
            'data'    => $product,
        ]);
    }

    public function showProduct($id)
    {
        $product = UniformProduct::with('prices')->find($id);
        if (!$product) {
            return response()->json(['success' => false, 'message' => 'Master Product not found'], 404);
        }
        return response()->json($product);
    }

    public function updateProduct(Request $request, $id)
    {
        $product = UniformProduct::find($id);
        if (!$product) {
            return response()->json(['success' => false, 'message' => 'Master Product not found'], 404);
        }

        $validated = $request->validate([
            'code'      => 'required|string|max:50|unique:uniform_products,code,' . $id,
            'name'      => 'required|string|max:255',
            'unit_type' => 'required|in:pcs,meter',
            'has_size'  => 'nullable|boolean',
        ]);

        $product->update([
            'code'      => $validated['code'],
            'name'      => $validated['name'],
            'unit_type' => $validated['unit_type'],
            'has_size'  => isset($validated['has_size']) && $validated['has_size'] ? 1 : 0,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Master Product updated successfully',
            'data'    => $product,
        ]);
    }

    public function destroyProduct($id)
    {
        $product = UniformProduct::find($id);
        if (!$product) {
            return response()->json(['success' => false, 'message' => 'Master Product not found or already deleted'], 404);
        }
        $product->delete();

        return response()->json([
            'success' => true,
            'message' => 'Master Product and its price rules removed successfully',
        ]);
    }

    public function priceDatatables(UtilitiesRequest $request)
    {
        $query = UniformPrice::select('uniform_prices.*')->with(['product', 'branch']);

        if ($request->filled('product_id')) {
            $query->where('uniform_product_id', $request->product_id);
        }

        if ($request->filled('branch_id')) {
            $query->where('branch_id', $request->branch_id);
        }

        if ($request->filled('is_active') && $request->is_active !== '') {
            $query->where('is_active', $request->is_active);
        }

        return datatables()->of($query)
            ->addColumn('product_name', function ($row) {
                return $row->product ? $row->product->name : '-';
            })
            ->addColumn('product_code', function ($row) {
                return $row->product ? $row->product->code : '-';
            })
            ->addColumn('unit_type', function ($row) {
                return $row->product ? $row->product->unit_type : '-';
            })
            ->addColumn('branch_name', function ($row) {
                return $row->branch ? $row->branch->name : 'All Branches';
            })
            ->make(true);
    }

    public function storePrice(Request $request)
    {
        $validated = $request->validate([
            'product_id'  => 'required|exists:uniform_products,id',
            'branch_id'   => 'required|exists:branches,id',
            'level_id'    => 'nullable|exists:levels,id',
            'size'        => 'nullable|string|max:50',
            'price'       => 'required',
            'is_active'   => 'nullable|boolean',
            'description' => 'nullable|string|max:255',
        ]);

        $rawPrice = str_replace(',', '', $validated['price']);
        $rawPrice = (float) str_replace('.', '', $rawPrice);

        $price = UniformPrice::updateOrCreate(
            [
                'uniform_product_id' => $validated['product_id'],
                'branch_id'          => $validated['branch_id'],
                'level_id'           => $validated['level_id'] ?? null,
                'size'               => $validated['size'] ?? null,
            ],
            [
                'price'              => $rawPrice,
                'is_active'          => isset($validated['is_active']) && $validated['is_active'] ? 1 : 0,
                'description'        => $validated['description'] ?? null,
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'Product Price rule saved successfully',
            'data'    => $price,
        ]);
    }

    public function showPrice($id)
    {
        $price = UniformPrice::with(['product', 'branch'])->find($id);
        if (!$price) {
            return response()->json(['success' => false, 'message' => 'Price rule not found'], 404);
        }
        return response()->json($price);
    }

    public function updatePrice(Request $request, $id)
    {
        $priceModel = UniformPrice::find($id);
        if (!$priceModel) {
            return response()->json(['success' => false, 'message' => 'Price rule not found'], 404);
        }

        $validated = $request->validate([
            'product_id'  => 'required|exists:uniform_products,id',
            'branch_id'   => 'required|exists:branches,id',
            'level_id'    => 'nullable|exists:levels,id',
            'size'        => 'nullable|string|max:50',
            'price'       => 'required',
            'is_active'   => 'nullable|boolean',
            'description' => 'nullable|string|max:255',
        ]);

        $rawPrice = str_replace(',', '', $validated['price']);
        $rawPrice = (float) str_replace('.', '', $rawPrice);

        $priceModel->update([
            'uniform_product_id'  => $validated['product_id'],
            'branch_id'           => $validated['branch_id'],
            'level_id'            => $validated['level_id'] ?? null,
            'size'                => $validated['size'] ?? null,
            'price'               => $rawPrice,
            'is_active'           => isset($validated['is_active']) && $validated['is_active'] ? 1 : 0,
            'description'         => $validated['description'] ?? null,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Product Price updated successfully',
            'data'    => $priceModel,
        ]);
    }

    public function destroyPrice($id)
    {
        $price = UniformPrice::find($id);
        if (!$price) {
            return response()->json([
                'success' => false,
                'message' => 'Product Price rule not found or already deleted',
            ], 404);
        }

        $price->delete();

        return response()->json([
            'success' => true,
            'message' => 'Product Price rule deleted successfully',
        ]);
    }

    public function togglePriceActive($id)
    {
        $price = UniformPrice::find($id);
        if (!$price) {
            return response()->json(['success' => false, 'message' => 'Price rule not found'], 404);
        }

        $price->is_active = !$price->is_active;
        $price->save();

        return response()->json([
            'success' => true,
            'message' => 'Price status updated to ' . ($price->is_active ? 'Active' : 'Inactive'),
            'is_active' => $price->is_active
        ]);
    }

    public function getLevelsByBranch($branchId)
    {
        $levels = Level::where('branch_id', $branchId)->get();
        return response()->json($levels);
    }

    public function getGradesByLevel($levelId)
    {
        $grades = Grade::where('level_id', $levelId)->get();
        return response()->json($grades);
    }

    public function form()
    {
        $branches = Branch::with(['levels.grades'])->get();
        $products = UniformProduct::with(['prices'])->get();

        return view('uniform.form', compact('branches', 'products'));
    }

    public function storeOrder(Request $request)
    {
        $validated = $request->validate([
            'student_name' => 'required|string|max:255',
            'parent_name'  => 'required|string|max:255',
            'parent_phone' => 'required|string|max:50',
            'parent_email' => 'required|email|max:255',
            'branch'       => 'required|exists:branches,id',
            'level'        => 'required|exists:levels,id',
            'grade_id'     => 'required|exists:grades,id',
            'items'        => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:uniform_products,id',
            'items.*.qty'        => 'required|numeric|min:0',
            'items.*.size'       => 'nullable|string',
            'bank_charger' => 'required|numeric|min:0',
            'grand_total'  => 'required|numeric|min:0', 
        ]);

        $branch = Branch::findOrFail($validated['branch']);
        $level  = Level::findOrFail($validated['level']);
        $grade  = Grade::findOrFail($validated['grade_id']);

        $totalSubtotal = 0;
        $totalItemsCount = 0;
        $orderDetailsData = [];

        foreach ($validated['items'] as $itemData) {
            $qty = (float) $itemData['qty'];
            if ($qty <= 0) continue;

            $product = UniformProduct::findOrFail($itemData['product_id']);
            $size = $itemData['size'] ?? null;

            $priceQuery = UniformPrice::where('uniform_product_id', $product->id)
                ->where('branch_id', $branch->id)
                ->where('is_active', 1);

            if ($size) {
                $priceQuery->where('size', $size);
            }

            $priceModel = $priceQuery->first();
            $unitPrice = $priceModel ? (float) $priceModel->price : 0;
            $subtotal  = $unitPrice * $qty;

            $totalSubtotal += $subtotal;
            $totalItemsCount += ($product->unit_type === 'pcs' ? (int)$qty : 1);

            $orderDetailsData[] = [
                'uniform_product_id' => $product->id,
                'product_name'       => $product->name,
                'product_code'       => $product->code,
                'unit_type'          => $product->unit_type,
                'size'               => $size,
                'qty'                => $qty,
                'price'              => $unitPrice,
                'subtotal'           => $subtotal,
            ];
        }

        if (empty($orderDetailsData)) {
            return response()->json([
                'success' => false,
                'message' => 'Please select at least one uniform item with quantity greater than 0.'
            ], 422);
        }
        $dataOrder = [
            'code'           => codeGenerator("uniform_orders","code","UORD-"),
            'student_name'   => $validated['student_name'],
            'parent_name'    => $validated['parent_name'],
            'parent_phone'   => $validated['parent_phone'],
            'parent_email'   => $validated['parent_email'],
            'branch_id'      => $branch->id,
            'branch_name'    => $branch->name,
            'level_id'       => $level->id,
            'level_name'     => $level->name,
            'grade_id'       => $grade->id,
            'grade_name'     => $grade->name,
            'subtotal'       => $totalSubtotal,
            'discount'       => 0,
            'bank_charger'   => $validated['bank_charger'],
            'total_amount'   => $validated['grand_total'],
            'total_items'    => $totalItemsCount,
            'order_date'     => now(),
        ];
        $payload = [
            "external_id"=> $dataOrder['code'],
            "amount"=> $dataOrder['total_amount'],
            "payer_email"=> $validated['parent_email'],
            "description"=> "Uniform Order Payment - ". $validated['student_name'] . " for " . $level->name . " " . $grade->name,
            "invoice_duration"=> (60*60*24*7)
        ];
        $xendit = createXenditInvoice($payload, $dataOrder['branch_name'] ?? "bintaro");
        $dataOrder['payment_status'] = $xendit['status'];
        $dataOrder['order_link'] = $xendit['invoice_url'];
        $dataOrder['expired_date_va'] = Carbon::parse($xendit['expiry_date']);

        $order = UniformOrder::create($dataOrder);

        foreach ($orderDetailsData as $detail) {
            $detail['uniform_order_id'] = $order->id;
            UniformOrderDetail::create($detail);
        }

        $order['subject'] = "Uniform Payment of $order->student_name - Mutiara Harapan Islamic School";
        $order['template'] = 'email-template.uniform-payment';
        $order['level_name'] = $level->name;
        $order['grade_name'] = $grade->name??"";

        $setting = EmailSetting::where('branch_id',$order['branch_id'])->first();
        Config::set('mail.default', 'smtp');
        Config::set('mail.mailers.smtp', [
            'transport' => $setting->mailer,
            'host' => $setting->host,
            'port' => $setting->port,
            'encryption' => $setting->encryption,
            'username' => $setting->username,
            'password' => $setting->app_password,
            'timeout' => null,
        ]);
        Config::set('mail.from', [
            'address' => $setting->from_address,
            'name' => $setting->from_name,
        ]);

        $order['items'] = $orderDetailsData;

        Mail::to($order->parent_email)->send(new AdmissionEmail($order));

        return response()->json([
            'success'    => true,
            'message'    => 'Uniform order submitted successfully!',
            'order_code' => $order->code,
            'data'       => $order,
        ]);
    }

    public function getProductsByBranchAndLevel(Request $request) {
        $branchId = $request->branch;

        $products = UniformProduct::with("prices")
            ->whereHas("prices",function($query) use ($branchId){
                $query->where("branch_id", $branchId)
                    ->where("is_active", 1);
            })
            ->get();    

        return response()->json($products);
    }
}
