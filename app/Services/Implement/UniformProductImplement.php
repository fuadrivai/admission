<?php

namespace App\Services\Implement;

use App\Models\UniformProduct;
use App\Services\UniformProductService;

class UniformProductImplement implements UniformProductService
{
    public function get()
    {
        return UniformProduct::all();
    }
    public function show($id)
    {
        return UniformProduct::findOrFail($id);
    }


    public function post($data)
    {
        $uniformProduct = UniformProduct::create([
            'code'      => $data['code'] ?? null,
            'name'      => $data['name'],
            'unit_type' => $data['unit_type'] ?? 'pcs',
            'has_size'  => $data['has_size'] ?? 0,
        ]);

        return $uniformProduct;
    }

    public function put($data)
    {
        $uniformProduct = UniformProduct::findOrFail($data['id']);
        $uniformProduct->update([
            'code'      => $data['code'] ?? $uniformProduct->code,
            'name'      => $data['name'],
            'unit_type' => $data['unit_type'] ?? $uniformProduct->unit_type,
            'has_size'  => $data['has_size'] ?? 0,
        ]);

        return $uniformProduct;
    }

    public function delete($id)
    {
        $uniformProduct = UniformProduct::findOrFail($id);
        return $uniformProduct->delete();
    }
}
