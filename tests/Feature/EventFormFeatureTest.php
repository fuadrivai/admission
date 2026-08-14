<?php

namespace Tests\Feature;

use Tests\TestCase;

class EventFormFeatureTest extends TestCase
{
    public function test_event_form_related_controllers_exist(): void
    {
        $this->assertTrue(class_exists(\App\Http\Controllers\EventFormController::class));
        $this->assertTrue(class_exists(\App\Http\Controllers\EventFieldController::class));
        $this->assertTrue(class_exists(\App\Http\Controllers\EventEmailTemplateController::class));
    }
}
