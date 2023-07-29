<?php

namespace Tests\Feature\Admin\Branding;

use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class UpdateBrandingTest extends TestCase
{
    private $url = '/api/admin/branding/update';

    protected function setUp(): void
    {
        parent::setUp();
        $this->createUserAndLogin(true);
    }

    /**
     * @dataProvider invalidBrandingDataProvider
     */
    public function testUpdateBrandingFailsWithInvalidData($requestData)
    {
        $response = $this->postJson($this->url, $requestData);
        $response->assertUnprocessable();
        // TODO: Assert that branding record is unchanged.
    }


    /**
     * @dataProvider validBrandingDataProvider
     */
    public function testUpdateBrandingSucceedsWithValidData($overrides = [])
    {
        $requestData = $this->makeBrandingRequestData($overrides);
        $response = $this->postJson($this->url, $requestData);
        $response->assertOk();
    }

    public function invalidBrandingDataProvider(): array
    {
        return [
            'Non-hex primary color' => [['primary_color' => 'invalid']],
            'Invalid hex primary color' => [['primary_color' => '#zzzzzz']],
            'Image non-file' => [['logo' => 'not-an-image.png']],
        ];
    }

    public function validBrandingDataProvider(): array
    {
        return [
            'Valid data' => [[]],
            'Valid data with image' => [['logo' => UploadedFile::fake()->image('test.png')]],
        ];
    }

    private function makeBrandingRequestData($overrides = []): array
    {
        $defaults = [
            'company_name' => '',
            'primary_color' => '#123456',
            'company_address' => '',
            'terms_url' => '',
            'privacy_url' => '',
            'support_email' => '',
        ];

        return array_merge($defaults, $overrides);
    }
}
