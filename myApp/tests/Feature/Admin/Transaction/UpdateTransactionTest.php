<?php

namespace Tests\Feature\Admin\Transaction;

use App\Models\Transaction;
use Tests\TestCase;

class UpdateTransactionTest extends TestCase
{
    private $url = '/api/admin/transactions/update';

    protected function setUp(): void
    {
        parent::setUp();
        $this->createUserAndLogin(true);
    }

    /**
     * @dataProvider invalidIdProvider
     */
    public function testUpdateTransactionFailsWithInvalidId($id)
    {
        $requestData = $this->makeTransactionRequestData(['id' => $id]);
        $response = $this->postJson($this->url, $requestData);
        $response->assertUnprocessable();
    }

    /**
     * @dataProvider invalidStatusProvider
     */
    public function testUpdateTransactionFailsWithInvalidStatus($status)
    {
        $requestData = $this->makeTransactionRequestData(['status' => $status]);
        $response = $this->postJson($this->url, $requestData);
        $response->assertUnprocessable();
    }

    public function invalidStatusProvider(): array
    {
        return [
            'Empty status' => [ '' ],
            'Invalid status' => [ 'status' => 'Invalid' ],
        ];
    }

    public function testOnlyStatusAndReferenceCanBeUpdated()
    {
        $transaction = Transaction::factory()->create();
        $requestData = [
            'id' => $transaction->id,
            'user_id' => 10000,
            'amount' => 1000,
            'reference' => 'ZXY-999',
            'currency' => 'USD',
            'status' => 'complete',
        ];

        $response = $this->postJson($this->url, $requestData);
        $response->assertOk();

        $transaction = Transaction::find($transaction->id);

        $this->assertNotEquals($requestData['user_id'], $transaction->user_id);
        $this->assertNotEquals($requestData['amount'], $transaction->amount);
        $this->assertNotEquals($requestData['currency'], $transaction->currency);

        $this->assertEquals($requestData['reference'], $transaction->reference);
        $this->assertEquals($requestData['status'], $transaction->status);
    }

    public function testUpdateTransactionSucceedsWithValidData()
    {
        $requestData = $this->makeTransactionRequestData();
        $response = $this->postJson($this->url, $requestData);
        $response->assertOk();
    }

    private function makeTransactionRequestData(array $overrides = []): array
    {
        $defaults = [
            'id' => Transaction::factory()->create()->id,
            'status' => 'pending',
            'reference' => 'ZXY-999'
        ];
        return array_merge($defaults, $overrides);
    }
}