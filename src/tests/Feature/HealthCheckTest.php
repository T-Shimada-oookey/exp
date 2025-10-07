<?php

namespace Tests\Feature;

use Tests\TestCase;

class HealthCheckTest extends TestCase
{
    /** @test */
    public function health_endpoint_returns_ok(): void
    {
        $res = $this->get('/health');
        $res->assertOk()
            ->assertJson(['status' => 'ok']);
    }
}