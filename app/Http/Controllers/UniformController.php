<?php

namespace App\Http\Controllers;

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
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Yajra\DataTables\Utilities\Request as UtilitiesRequest;

use function App\Helpers\codeGenerator;
use function App\Helpers\createXenditInvoice;



class UniformController extends Controller
{

    public function index()
    {
        return redirect()->route('uniform.uniform-setting');
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
        $xendit = createXenditInvoice($payload);
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

