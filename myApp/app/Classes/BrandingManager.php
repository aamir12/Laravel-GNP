<?php

namespace App\Classes;

use App\Models\Branding;
use App\Models\Meta;
use App\Services\StorageService;

class BrandingManager
{
    public static function update($fields)
    {
        if (isset($fields['logo'])) {
            $fileData = StorageService::storeImage($fields['logo']);
            $fields['logo'] = $fileData['image'];
            $fields['image_type'] = $fileData['image_type'];
        }
        $brand = Branding::firstOrFail();
        $brand->fill($fields);
        $brand->save();
        $brand->setMetadata(Meta::extractMetadata($fields));
        return $brand;
    }
}
