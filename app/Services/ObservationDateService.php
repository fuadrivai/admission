<?php

namespace App\Services;

interface ObservationDateService
{
    public function get();
    public function getByDate($date);
    public function getByDateAndDivision($date, $divisionId);
    public function show($id);
    public function post($data);
    public function put($data);
    public function active($id);
    public function delete($id);
}
