<?php

namespace Tests\Feature\Admin\Branding;

use App\Models\Branding;
use Tests\TestCase;

class GetBrandingTest extends TestCase
{
    private $url = '/api/admin/branding/get';

    protected function setUp(): void
    {
        parent::setUp();
        $this->createUserAndLogin(true);
    }

    public function testGetBranding()
    {
        Branding::factory()->create();
        $response = $this->getJson($this->url);
        $response->assertOk();
    }
}
