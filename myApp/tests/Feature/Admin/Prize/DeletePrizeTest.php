<?php

namespace Tests\Feature\Admin\Prize;

use App\Models\Competition;
use App\Models\Prize;
use App\Models\Stock;
use Tests\TestCase;

class DeletePrizeTest extends TestCase
{
    private $url = '/api/admin/prizes/delete';

    protected function setUp(): void
    {
        parent::setUp();
        $this->createUserAndLogin(true);
    }

    /**
     * @dataProvider invalidIdProvider
     */
    public function testDeletePrizeFailsWithInvalidPrizeId($invalidId)
    {
        $this->seedPrize();
        $response = $this->postJson($this->url, ['id' => $invalidId]);
        $response->assertUnprocessable();
    }

    public function testDeletePrizeSucceedsWithValidPrizeId()
    {
        $prize = $this->seedPrize();
        $response = $this->postJson($this->url, ['id' => $prize->id]);
        $response->assertOk();
        $this->assertSoftDeleted('prizes', ['id' => $prize->id]);
    }

    private function seedPrize()
    {
        $competition = Competition::factory()->create();
        $stock = Stock::factory()->create(['image' => 'test.jpg']);
        $prize = Prize::factory()->create([
            'stock_id' => $stock->id,
            'competition_id' => $competition->id
        ]);
        return $prize;
    }
}