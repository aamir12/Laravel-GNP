<?php

namespace Tests\Feature\Admin\Transaction;

use App\Models\Transaction;
use Tests\TestCase;

class BulkUpdateTransactionsTest extends TestCase
{
    private $url = '/api/admin/transactions/bulk-update';

    protected function setUp(): void
    {
        parent::setUp();
        $this->createUserAndLogin(true);
    }

    /**
     * @dataProvider invalidIdProvider
     */
    public function testBulkUpdateTransactionsFailsWithInvalidId($id)
    {
        $requestData = [
            'transactions' => [
                $this->makeTransactionRequestData(['id' => $id])
            ]
        ];
        $response = $this->postJson($this->url, $requestData);
        $response->assertUnprocessable();
    }

    /**
     * @dataProvider invalidStatusProvider
     */
    public function testBulkUpdateTransactionFailsWithInvalidStatus($status)
    {
        $requestData = [
            'transactions' => [
                $this->makeTransactionRequestData(['status' => $status])
            ]
        ];
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

        $transactionData = [
            'id' => $transaction->id,
            'user_id' => 10000,
            'amount' => 1000,
            'reference' => 'ZXY-999',
            'currency' => 'USD',
            'status' => 'complete',
        ];

        $requestData = [
            'transactions' => [ $transactionData ]
        ];

        $response = $this->postJson($this->url, $requestData);
        $response->assertOk();

        $transaction = Transaction::find($transaction->id);

        $this->assertNotEquals($transactionData['user_id'], $transaction->user_id);
        $this->assertNotEquals($transactionData['amount'], $transaction->amount);
        $this->assertNotEquals($transactionData['currency'], $transaction->currency);

        $this->assertEquals($transactionData['reference'], $transaction->reference);
        $this->assertEquals($transactionData['status'], $transaction->status);
    }

    public function testBulkUpdateTransactionSucceedsWithValidData()
    {
        $id = Transaction::factory()->create()->id;
        $requestData = [
            'transactions' => [
                $this->makeTransactionRequestData(),
                $this->makeTransactionRequestData(['id' => $id]),
            ]
        ];
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