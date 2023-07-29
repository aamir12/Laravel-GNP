<?php

namespace App\Services;

use App\Models\Prize;
use App\Models\Stock;
use App\Services\StorageService;

class PrizeService
{
    /**
     * Create a prize and its associated stock item.
     */
    public static function createPrize(array $data): Prize
    {
        if (isset($data['image'])) {
            $data = array_merge(
                $data,
                StorageService::storeImage($data['image'])
            );
        }

        $data['stock_id'] = Stock::create($data)->id;
        $data['reference'] = $data['reference'] ?? str_random(10);
        $prize = Prize::create($data);
        $prize->load('stock');

        return $prize;
    }

    /**
     * Bulk creates prizes and stock from the given array.
     */
    public static function createPrizes(array $data): array
    {
        $prizes = [];
        foreach($data['prizes'] as $prize) {
            $prizes[] = self::createPrize($prize);
        }

        return $prizes;
    }

    /**
     * Updates the given prize with the data provided.
     */
    public static function updatePrize(Prize $prize, array $data): Prize
    {
        if (isset($data['image'])) {
            $data = array_merge(
                $data,
                StorageService::storeImage($data['image'])
            );
        }

        $prize->stock->update($data);
        $prize->update($data);

        return $prize;
    }
}