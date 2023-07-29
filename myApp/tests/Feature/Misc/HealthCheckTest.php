<?php

namespace Tests\Feature\Misc;

use Tests\TestCase;

class HealthCheckTest extends TestCase
{
    public function testHealthCheckWorks()
    {
        $response = $this->get('/healthz');
        $response->assertOk();
    }
}