<?php

namespace Tests\Feature;

use Tests\TestCase;

class EnrolmentDpFeatureTest extends TestCase
{
    public function test_dp_routes_require_authentication(): void
    {
        $this->get(route('enrolment.dp.index'))->assertRedirect('/auth');
        $this->post(route('enrolment.dp.store'))->assertRedirect('/auth');
        $this->get(route('enrolment.dp.search', ['code' => 'ENR-2027-000123']))
            ->assertRedirect('/auth');
    }

    public function test_dp_form_prefills_existing_enrolment_code(): void
    {
        $user = \App\Models\User::where('role', '!=', 'user')->first();
        if (!$user) {
            $this->markTestSkipped('No staff user available.');
        }

        $response = $this->actingAs($user)->get(route('enrolment.dp.index', ['code' => 'MHIS-TEST001']));

        $response->assertOk();
        $html = $response->getContent();
        $this->assertMatchesRegularExpression('/name="already_enrolment"\s+value="yes"\s+checked/s', $html);
        $this->assertStringContainsString('value="MHIS-TEST001"', $html);
    }}
