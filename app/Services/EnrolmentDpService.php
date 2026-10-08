<?php

namespace App\Services;

use App\Models\Enrolment;
use App\Models\EnrolmentTransaction;

interface EnrolmentDpService
{
    public function searchEnrolment(string $code): array;
    public function validateExistingEnrolment(Enrolment $enrolment): void;
    public function getRegistrationFee($academicYearId, $branchId, $levelId, $gradeId);
    public function createTransaction(array $data): EnrolmentTransaction;
}
