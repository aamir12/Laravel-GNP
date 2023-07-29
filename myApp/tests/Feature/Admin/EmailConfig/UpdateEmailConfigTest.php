<?php

namespace Tests\Feature\Admin\EmailConfig;

use App\Models\EmailConfig;
use Tests\TestCase;

class UpdateEmailConfigTest extends TestCase
{
    private $url = '/api/admin/email-config/update';

    protected function setUp(): void
    {
        parent::setUp();
        $this->createUserAndLogin(true);
    }

    /**
     * @dataProvider invalidIdProvider
     */
    public function testUpdateEmailConfigFailsWithInvalidId($invalidId)
    {
        $requestData = ['id' => $invalidId];
        $response = $this->postJson($this->url, $requestData);
        $response->assertUnprocessable();
        $response->assertJsonValidationErrors(['id']);
    }

    public function testUpdateEmailConfigFailsWithInvalidType()
    {
        $requestData = ['email_type' => 'invalid'];
        $response = $this->postJson($this->url, $requestData);
        $response->assertUnprocessable();
        $response->assertJsonValidationErrors(['email_type']);
    }

    /**
     * @dataProvider invalidEmailConfigDataProvider
     */
    public function testUpdateEmailConfigFailsWithInvalidData()
    {
        $emailConfig = EmailConfig::first();
        $requestData = ['id' => $emailConfig->id, 'is_enabled' => 0];
        $response = $this->postJson($this->url, $requestData);
        $response->assertOk();
        $response->assertJsonPath('data.is_enabled', 0);
        $response->assertJsonPath('message', __('email_config')['update_success']);
    }

    public function invalidEmailConfigDataProvider(): array
    {
        return [
            'Empty subject' => [['subject' => '']],
            'Empty body' => [['body' => '']],
            'Invalid resend_interval' => [['resend_interval' => 'Test']],
        ];
    }

    public function testUpdateEmailConfigSucceedsWithNothingUpdated()
    {
        $emailConfig = EmailConfig::first();
        $requestData = [
            'id' => $emailConfig->id,
            'is_enabled' => $emailConfig->is_enabled
        ];
        $response = $this->postJson($this->url, $requestData);
        $response->assertOk();
        $response->assertJsonPath('data.is_enabled', $emailConfig->is_enabled);
        $response->assertJsonPath('message', __('nothing_updated'));
    }

    public function testUpdateEmailConfigSucceedsWithValidData()
    {
        $emailConfig = EmailConfig::first();
        $requestData = ['id' => $emailConfig->id, 'is_enabled' => 0];
        $response = $this->postJson($this->url, $requestData);
        $response->assertOk();
        $response->assertJsonPath('data.is_enabled', 0);
        $response->assertJsonPath('message', __('email_config')['update_success']);
    }
}
