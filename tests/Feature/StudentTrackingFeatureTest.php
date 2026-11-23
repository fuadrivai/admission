<?php

namespace Tests\Feature;

use Tests\TestCase;

class StudentTrackingFeatureTest extends TestCase
{
    public function test_student_tracking_requires_authentication(): void
    {
        $this->get(route('student-tracking.show', ['prospect' => 1]))
            ->assertRedirect('/auth');
    }
}
