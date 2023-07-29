<?php

namespace App\Classes\Validators;
use App\Models\Group;

class GroupValidator 
{
    public function validateParentId($attribute, $value, $parameters, $validator) {
        $next_parent_id = $value;
        $id = $validator->getData()['id'];

        while ($next_parent_id != 0) {
            if ($next_parent_id == $id) {
                return false;
            }
            $next_parent_id = Group::find($next_parent_id)->parent_id;
        }
        
        return true;
    }
}
