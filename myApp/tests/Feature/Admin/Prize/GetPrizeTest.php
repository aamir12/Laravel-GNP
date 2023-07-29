<?php

namespace Tests\Feature\Admin\Prize;

use App\Models\Competition;
use App\Models\Prize;
use App\Models\Stock;
use Tests\TestCase;

class GetPrizeTest extends TestCase
{
    private $url = '/api/admin/prizes/get';

    protected function setUp(): void
    {
        parent::setUp();
        $this->createUserAndLogin(true);
    }

    /**
     * @dataProvider invalidIdProvider
     */
    public function testGetPrizeFailsWithInvalidPrizeId($invalidId)
    {
        $this->seedPrize();
        $response = $this->getJson($this->url . '?id=' . $invalidId);
        $response->assertUnprocessable();
    }

    public function testGetPrizeSucceedsWithValidId()
    {
        $prize = $this->seedPrize();
        $response = $this->getJson($this->url . '?id=' . $prize->id);
        $response->assertOk();
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