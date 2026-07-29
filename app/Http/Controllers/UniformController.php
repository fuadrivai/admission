<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Grade;
use App\Models\Level;
use App\Models\UniformPrice;
use App\Models\UniformProduct;
use App\Services\UniformProductService;
use Illuminate\Http\Request;
use Yajra\DataTables\Utilities\Request as UtilitiesRequest;

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
        $query = UniformPrice::select('uniform_prices.*')->with(['product', 'branch', 'level']);

        if ($request->filled('product_id')) {
            $query->where('uniform_product_id', $request->product_id);
        }

        if ($request->filled('branch_id')) {
            $query->where('branch_id', $request->branch_id);
        }

        if ($request->filled('level_id')) {
            $query->where('level_id', $request->level_id);
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
            ->addColumn('level_name', function ($row) {
                return $row->level ? $row->level->name : 'All Levels';
            })
            ->addColumn('formatted_price', function ($row) {
                return 'Rp ' . number_format($row->price, 0, ',', '.');
            })
            ->make(true);
    }

    public function storePrice(Request $request)
    {
        $validated = $request->validate([
            'product_id'  => 'required|exists:uniform_products,id',
            'branch_id'   => 'required|exists:branches,id',
            'level_id.*'  => 'required|exists:levels,id',   
            'size'        => 'nullable|string|max:50',
            'price'       => 'required',
            'is_active'   => 'nullable|boolean',
            'description' => 'nullable|string|max:255',
        ]);

        $rawPrice = str_replace(',', '', $validated['price']);
        $rawPrice = (float) str_replace('.', '', $rawPrice);

        $levelIds = is_array($validated['level_id']) ? $validated['level_id'] : [$validated['level_id']];
        $createdPrices = [];

        foreach ($levelIds as $lvlId) {
            $price = UniformPrice::updateOrCreate(
                [
                    'uniform_product_id' => $validated['product_id'],
                    'branch_id'          => $validated['branch_id'],
                    'level_id'           => $lvlId,
                    'size'               => $validated['size'] ?? null,
                ],
                [
                    'price'              => $rawPrice,
                    'is_active'          => isset($validated['is_active']) && $validated['is_active'] ? 1 : 0,
                    'description'        => $validated['description'] ?? null,
                ]
            );
            $createdPrices[] = $price;
        }

        return response()->json([
            'success' => true,
            'message' => count($createdPrices) . ' Product Price rule(s) saved successfully',
            'data'    => $createdPrices,
        ]);
    }

    public function showPrice($id)
    {
        $price = UniformPrice::with(['product', 'branch', 'level'])->find($id);
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
            'level_id'    => 'required',
            'size'        => 'nullable|string|max:50',
            'price'       => 'required',
            'is_active'   => 'nullable|boolean',
            'description' => 'nullable|string|max:255',
        ]);

        $rawPrice = str_replace(',', '', $validated['price']);
        $rawPrice = (float) str_replace('.', '', $rawPrice);
        $lvlId = is_array($validated['level_id']) ? $validated['level_id'][0] : $validated['level_id'];

        $priceModel->update([
            'uniform_product_id'  => $validated['product_id'],
            'branch_id'           => $validated['branch_id'],
            'level_id'            => $lvlId,
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
}

