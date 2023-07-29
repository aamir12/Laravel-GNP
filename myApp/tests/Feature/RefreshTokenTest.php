<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Faker\Factory as Faker;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Artisan as Artisan;
use File;

class RefreshTokenTest extends TestCase
{
    use RefreshDatabase;
    
    public function setUp(): void {
        parent::setUp();
        $file_path = base_path("bootstrap\cache\config.php");
        if(File::exists($file_path)){
            File::delete($file_path);
        }
        Artisan::call('config:clear');
        Artisan::call('cache:clear');
    }

    public static function setUpBeforeClass(): void {
        putenv ("OPEN_REGISTRATION_ALLOWED=true");
        echo PHP_EOL.'RefreshTokenTest';
    }

    public function createUserProvider(){
        $faker = Faker::create();
        return [
                    [
                        'user' => [
                            'email' => $faker->unique()->safeEmail,
                            'password' => 'FakePass@08',
                            'username' => $faker->username,
                            'name'  => $faker->firstname.' '.$faker->lastname,
                            'first_name' => $faker->firstname,
                            'last_name' => $faker->lastname,
                            'timezone'  => $faker->timezone,
                            'phone' => '+44 44445557777',//$faker->mobileNumber,
                            'dob'   => $faker->date('d M Y', '2002-07-20'),
                            //'activation_code'   => 'WE123',//$faker->vatId,
                            'external_id'   => $faker->url,
                            'is_activated'    => 1,
                        ], true
                    ]
            ];
    }

    /**
     * @dataProvider createUserProvider
     * */
    public function testValidRefreshToken($user)
    {
        \App\Models\OauthClient::truncate();
        Artisan::call('passport:install');

        $result = $this->withHeaders([
            'Accept' => 'application/json',
            'localization' => 'en'
        ])
        ->json('POST', '/api/auth/register', $user);
        $response = $this->withHeaders([
            "Authorization" => "Bearer ".json_decode($result->getContent(), true)['data']['access_token'],
            "Content-Type" => "application/json",
        ])
        ->json('POST', '/api/auth/refresh-token', [
            "refresh_token" => json_decode($result->getContent(), true)['data']['refresh_token'],
        ]);
       // $response->dumpHeaders();
        //$response->dump();
        $response
        ->assertJson([
            'status' => 'success',
        ]);
    }

    /**
     * @dataProvider createUserProvider
     * */
    public function testInValidRefreshToken($user)
    {
        \App\Models\OauthClient::truncate();
        Artisan::call('passport:install');

        $result = $this->withHeaders([
            'Accept' => 'application/json',
            'localization' => 'en'
        ])
        ->json('POST', '/api/auth/register', $user);

        $response = $this->withHeaders([
            "Authorization" => "Bearer ".json_decode($result->getContent(), true)['data']['access_token'].'INVALID',
            "Content-Type" => "application/json",
        ])
        ->json('POST', '/api/auth/refresh-token', [
            "refresh_token" => 'Wrong-refresh-token',
        ]);
       // $response->dumpHeaders();
        //$response->dump();
        $response
        ->assertJson([
            'status' => 'error',
        ]);
    }

    /**
     * @dataProvider createUserProvider
     * */
    public function testValidateRefreshToken($user) {
        \App\Models\OauthClient::truncate();
        Artisan::call('passport:install');

        $result = $this->withHeaders([
            'Accept' => 'application/json',
            'localization' => 'en'
        ])->json('POST', '/api/auth/register', $user);

        $response = $this->withHeaders([
            "Authorization" => "Bearer ".json_decode($result->getContent(), true)['data']['access_token'],
            "Content-Type" => "application/json",
        ])
        ->json('POST', '/api/auth/refresh-token');
       // $response->dumpHeaders();
        //$response->dump();
        $response
        ->assertJson([
            'status' => 'error',
        ]);
    }
}
