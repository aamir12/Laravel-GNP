<?php

namespace Tests\Feature\Admin\EmailConfig;

use App\Models\EmailConfig;
use Tests\TestCase;

class GetEmailConfigTest extends TestCase
{
    private $url = '/api/admin/email-config/get';

    protected function setUp(): void
    {
        parent::setUp();
        $this->createUserAndLogin(true);
    }

    public function testGetEmailConfigById()
    {
        $id = EmailConfig::first()->id;
        $response = $this->getJson($this->url . '?id=' . $id);
        $response->assertOk();
        $response->assertJsonPath('data.id', $id);
    }

    public function testGetEmailConfigByType()
    {
        $emailType = EmailConfig::first()->email_type;
        $response = $this->getJson($this->url . '?email_type=' . $emailType);
        $response->assertOk();
        $response->assertJsonPath('data.email_type', $emailType);
    }
}
