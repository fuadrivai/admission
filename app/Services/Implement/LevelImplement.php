<?php

namespace App\Services\Implement;

use App\Models\Level;
use App\Services\LevelService;
use Illuminate\Support\Facades\DB;

class LevelImplement implements LevelService
{
    public function get()
    {
        return Level::with(['grades', 'division', 'branch'])->get();
    }

    public function getByBranch($branchId)
    {
        return Level::with(['grades', 'division', 'branch'])
            ->where('branch_id', $branchId)
            ->get();
    }

    public function show($id)
    {
        return Level::with('grades')->findOrFail($id);
    }

    public function post($data)
    {
        return DB::transaction(function () use ($data) {
            $level = Level::create([
                'name' => $data['name'],
                'division_id' => $data['division'],
                'branch_id' => $data['branch'],
                'branch_name' => $data['branch_name'],
                'principal' => $data['principal'],
                'branch_code' => $data['code'],
            ]);

            $grades = collect($data['grades'])->map(function ($grade) use ($level) {
                return [
                    'level_id' => $level->id,
                    'name'   => $grade['name'],
                ];
            })->toArray();
            $level->grades()->createMany($grades);
            return $level->load('grades');
        });
    }

    public function put($data)
    {
        return DB::transaction(function () use ($data) {
            $level = Level::findOrFail($data['id']);

            $level->update([
                'name' => $data['name'],
                'division_id' => $data['division'],
                'branch_id' => $data['branch'],
                'branch_name' => $data['branch_name'],
                'branch_code' => $data['code'],
                'principal' => $data['principal'],
            ]);

            $existingGradeIds = $level->grades()->pluck('id')->toArray();
            $newGradeIds = collect($data['grades'])->pluck('id')->filter()->toArray();

            $toDelete = array_diff($existingGradeIds, $newGradeIds);
            if (!empty($toDelete)) {
                $level->grades()->whereIn('id', $toDelete)->delete();
            }

            foreach ($data['grades'] as $grade) {
                if (!empty($grade['id'])) {
                    $level->grades()
                        ->where('id', $grade['id'])
                        ->update([
                            'name' => $grade['name'],
                        ]);
                } else {
                    $level->grades()->create([
                        'name' => $grade['name'],
                    ]);
                }
            }
            return $level->load('grades');
        });
    }

    public function delete($id)
    {
        $level = Level::findOrFail($id);

        $level->grades()->delete();

        return $level->delete();
    }
}
