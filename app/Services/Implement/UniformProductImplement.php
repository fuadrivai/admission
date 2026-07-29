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
            'name' => $data['name'],
            'category_id' => $data['category_id'],
            'has_size' => $data['has_size']??0,
            'is_active' => $data['is_active']??0,
        ]);

        return $uniformProduct;
    }

    public function put($data)
    {
        $uniformProduct = UniformProduct::findOrFail($data['id']);
        $uniformProduct->update([
            'name'     => $data['name'],
            'category_id' => $data['category_id'],
            'has_size' => $data['has_size']??0,
            'is_active' => $data['is_active']??0,
        ]);

        return $uniformProduct;
    }

    public function delete($id)
    {
        $uniformProduct = UniformProduct::findOrFail($id);
        return $uniformProduct->delete();
    }
}
