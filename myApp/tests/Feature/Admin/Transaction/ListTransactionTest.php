<?php

namespace Tests\Feature\Admin\Transaction;

use App\Models\Transaction;
use App\Models\User;
use Tests\TestCase;

class ListTransactionTest extends TestCase
{
    private $url = '/api/admin/transactions/list';

    protected function setUp(): void
    {
        parent::setUp();
        $this->createUserAndLogin(true);
    }

    public function testListTransactionSucceedsWithAnEmptyList()
    {
        $response = $this->getJson($this->url);
        $response->assertOk();
        $this->assertDatabaseCount('transactions', 0);
    }

    public function testListTransactionsSucceeds()
    {
        Transaction::factory()->count(3)->create();
        $response = $this->getJson($this->url);
        $response->assertOk();
        $response->assertJsonCount(3, 'data');
    }

    public function testListTransactionsIncludesCorrectUserData()
    {
        $user = User::factory()->hasTransactions(3)->create();

        $response = $this->getJson($this->url);
        $response->assertOk();

        $response->assertJsonPath('data.0.user.name', $user->name);
        $response->assertJsonPath('data.0.user.paypal_email', $user->paypal_email);
    }
}
