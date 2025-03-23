<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LeadExportTest extends TestCase
{
use RefreshDatabase;

    public function test_lead_export()
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'sanctum');
        $response = $this->get('/api/export');
        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    }
}
