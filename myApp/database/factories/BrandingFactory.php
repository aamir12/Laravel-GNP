<?php

namespace Database\Factories;

use App\Models\Branding;
use Illuminate\Database\Eloquent\Factories\Factory;

class BrandingFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Branding::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'logo' => 'test.png',
            'primary_color' => '#' . str_pad(dechex(rand(0x000000, 0xFFFFFF)), 6, 0, STR_PAD_LEFT),
            'company_address' => $this->faker->address(),
            'terms_url' => $this->faker->url(),
            'privacy_url' => $this->faker->url(),
            'support_email' => $this->faker->email(),
            'image_type' => 'local'
        ];
    }
}