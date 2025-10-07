<?php

namespace Tests\Feature;

use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\Attributes\Group;

final class HealthCheckTest extends TestCase
{
    #[Test]
    #[Group('fast')]
    #[TestDox('GET /health returns 200 and {"status":"ok"}')]
    public function health_endpoint_returns_ok(): void
    {
        $res = $this->get('/health');

        $res->assertOk()
            ->assertJson(['status' => 'ok']);
    }
}