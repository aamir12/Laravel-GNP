<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;

class StorageService
{
    public static function storeImage($file): array
    {
        return [
            'image' => url('image/' . basename(Storage::putFile(null, $file))),
            'image_type' => config('filesystems.default') === 'local' ? 'local' : 'S3',
        ];
    }

    public static function replaceImage($file, $name): array
    {
        return [
            'image' => url('image/' . basename(Storage::putFileAs(null, $file, $name))),
            'image_type' => config('filesystems.default') === 'local' ? 'local' : 'S3',
        ];
    }
}