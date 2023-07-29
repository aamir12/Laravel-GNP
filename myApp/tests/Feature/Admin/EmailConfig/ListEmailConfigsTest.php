<?php

namespace Tests\Feature\Admin\EmailConfig;

use Tests\TestCase;

class ListEmailConfigsTest extends TestCase
{
    private $url = '/api/admin/email-config/list';

    protected function setUp(): void
    {
        parent::setUp();
        $this->createUserAndLogin(true);
    }

    public function testGetEmailConfigList()
    {
        $response = $this->getJson($this->url);
        $response->assertOk();
    }
}
