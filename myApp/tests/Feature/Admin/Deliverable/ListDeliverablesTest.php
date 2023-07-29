<?php

namespace Tests\Feature\Admin\Deliverable;

use App\Models\Achievement;
use App\Models\AchievementWinner;
use App\Models\Competition;
use App\Models\Deliverable;
use App\Models\Prize;
use App\Models\Stock;
use App\Models\User;
use Tests\TestCase;

class ListDeliverablesTest extends TestCase
{
    private $url = '/api/admin/deliverables/list';

    protected function setUp(): void
    {
        parent::setUp();
        $this->createUserAndLogin(true);
    }

    public function testListDeliverables()
    {
        $this->createDeliverable();
        $response = $this->getJson($this->url);
        $response->assertOk();
        $response->assertJsonCount(1, 'data');
    }

    private function createDeliverable()
    {
        $stock = Stock::factory()->create(['image' => 'test.png']);
        $achievement = Achievement::factory()->create([
            'stock_id' => $stock->id,
            'name' => 'test',
            'description' => 'test description',
        ]);
        $competition = Competition::factory()->create();
        Prize::factory()->create([
            'stock_id' => $stock->id,
            'competition_id' => $competition->id,
        ]);
        $user = User::factory()->create([
            'email' => 'test1@test.com',
        ]);
        $deliverable = Deliverable::factory()->create();
        AchievementWinner::factory()->create([
            'achievement_id' => $achievement->id,
            'user_id' => $user->id,
            'deliverable_id' => $deliverable->id,
        ]);

        return $deliverable->id;
    }
}
