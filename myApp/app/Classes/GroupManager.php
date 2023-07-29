<?php

namespace App\Classes;

use App\Models\Group;
use App\Models\Meta;

class GroupManager
{
    public static function create($data)
    {
        if (isset($data['is_default_group']) && $data['is_default_group']) {
            Group::query()->update(['is_default_group' => 0]);
        }
        if (Group::count() === 0) {
            $data['is_default_group'] = 1;
        }

        $group = Group::find(Group::create($data)->id);
        $group->setMetadata(Meta::extractMetadata($data));
        $group->load('meta');
        return $group;
    }

    public static function update($req)
    {
        $group = Group::find($req->id);
        $group->fill($req->all());
        if ($group->isClean()) {
            return 'nothing-updated';
        }

        if ($req->is_default_group == 1) {
            Group::query()->update(['is_default_group' => 0]);
        }

        if (isset($req->default_group_id)) {
            $req->merge(['is_default_group' => 0]);
            Group::find($req->default_group_id)->update(['is_default_group' => 1]);
        }

        if (Group::count() == 1) {
            $req->merge(['is_default_group' => 1]);
        }

        $group->fill($req->all());
        $group->save();
        $group->setMetadata(Meta::extractMetadata($req->all()));
        $group->load('meta');

        return $group;
    }
}
