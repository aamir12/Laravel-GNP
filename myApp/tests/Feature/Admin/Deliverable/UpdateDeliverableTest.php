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

class UpdateDeliverableTest extends TestCase
{
    private $url = '/api/admin/deliverables/update';

    protected function setUp(): void
    {
        parent::setUp();
        $this->createUserAndLogin(true);
    }

    /**
     * @dataProvider invalidIdProvider
     */
    public function testUpdateDeliverableFailsWithInvalidId($invalidId)
    {
        $this->createDeliverable();
        $requestData = ['id' => $invalidId];
        $response = $this->postJson($this->url, $requestData);
        $response->assertUnprocessable();
        $response->assertJsonValidationErrors(['id']);
    }

    public function testUpdateDeliverableSucceedsWithNothingUpdated()
    {
        $deliverable = Deliverable::factory()->create();
        $requestData = [
            'id' => $deliverable->id,
            'is_shipped' => $deliverable->is_shipped
        ];
        $response = $this->postJson($this->url, $requestData);
        $response->assertOk();
        $response->assertJsonPath('data.is_shipped', $deliverable->is_shipped);
        $response->assertJsonPath('message', __('nothing_updated'));
    }

    public function testUpdateDeliverableSucceedsWithValidData()
    {
        $deliverable = Deliverable::factory()->create(['is_shipped' => 0]);
        $requestData = ['id' => $deliverable->id, 'is_shipped' => 1];
        $response = $this->postJson($this->url, $requestData);
        $response->assertOk();
        $response->assertJsonPath('data.is_shipped', 1);
        $response->assertJsonPath('message', __('deliverable')['update_success']);
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
