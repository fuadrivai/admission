<?php

namespace App\Services\Implement;

use App\Models\BankCharger;
use App\Services\BankChargerService;

class BankChargerImplement implements BankChargerService
{
    public function get($with= [])
    {
        return BankCharger::first();
    }

    public function show($id,$with=[])
    {
        return BankCharger::with($with)-> findOrFail($id);
    }

    public function post($data)
    {
        $bank = BankCharger::create([
            'price'     => $data['name'],
        ]);

        return $bank;
    }

    public function put($data)
    {
        $bank = BankCharger::findOrFail($data['id']);

        $bank->update([
            'price'     => $data['name'],
        ]);

        return $bank;
    }

    public function delete($id)
    {
        $bank = BankCharger::findOrFail($id);
        return $bank->delete();
    }
}
