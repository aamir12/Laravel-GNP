<?php

namespace App\Models\Traits;

use App\Models\Meta;

trait HasMetadata
{
    public function meta()
    {
        return $this->morphMany(Meta::class, 'meta');
    }

    public function setMetadata($metadata)
    {
        if (is_array($metadata) && count($metadata) > 0) {
            foreach ($metadata as $index => $item) {
                if (is_array($item)) { // multidimensional array
                    $this->setMetadata($item);
                } else {
                    $modelData = ['name' => $index, 'content' => $item];
                    $this->meta()->updateOrCreate(['name' => $index], $modelData);
                }
            }
        } else {
            $this->meta()->delete();
        }
    }
}
