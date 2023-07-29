<?php

namespace App\Models;

use App\Models\Traits\HasDates;
use App\Models\Traits\HasMetadata;
use App\Models\Traits\RecordsUserstamps;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Auditable as AuditableTrait;
use OwenIt\Auditing\Contracts\Auditable;

class Group extends Model implements Auditable
{
    use SoftDeletes;
    use HasDates;
    use HasFactory;
    use HasMetadata;
    use AuditableTrait;
    use RecordsUserstamps;

    protected $fillable = [
        'name',
        'parent_id',
        'is_default_group'
    ];

    protected $hidden = ['pivot'];

    /**
     * Get all subgroups of this group, including all subgroups of subgroups etc.
     *
     * @return \Illuminate\Support\Collection
     */
    public function getSubgroups()
    {
        $subgroups = $this->children;

        foreach ($this->children as $child) {
            $subgroups = $subgroups->merge($child->getSubgroups());
        }
        return $subgroups;
    }

    /**
     * Get all users within this group, including those within subgroups.
     *
     * @param bool $activatedOnly If true, will only return activated users.
     * @return \Illuminate\Support\Collection The users found in this group.
     */
    public function getAllUsersInSubgroups($activatedOnly = false)
    {
        // If group has no children, simply return users.
        if ($this->children->isEmpty()) {
            if ($activatedOnly) {
                return $this->users()->active()->get();
            }
            return $this->users;
        }

        // If group does have children, then recursively fetch users.
        $users = collect();
        foreach ($this->children as $child) {
            $users->push($child->getAllUsersInSubgroups($activatedOnly));
        }

        // Return accumulated users with duplicates removed.
        return $users->flatten()->unique();
    }

    /**
     * Get all activated users within this group, including those within subgroups.
     *
     * @return \Illuminate\Support\Collection The users found in this group.
     */
    public function getAllActivatedUsersInSubgroups()
    {
        return $this->getAllUsersInSubgroups(true);
    }

    /**
     * Determine if the group contains any users or subgroups.
     */
    public function isEmpty(): bool
    {
        return ! $this->users()->exists() && ! $this->children()->exists();
    }

    /**
     * Determine if the group is not empty.
     */
    public function isNotEmpty()
    {
        return ! $this->isEmpty();
    }

    public function moveContentsTo(Group $destinationGroup): void
    {
        if ($this->isEmpty()) {
            return;
        }

        if ($this->users()->exists()) {
            $this->moveUsersTo($destinationGroup);
        }

        if ($this->children()->exists()) {
            $this->moveSubgroupsTo($destinationGroup);
        }
    }

    /**
     * Removes users from this group and adds them to the destination group.
     *
     * @param Group $destinationGroup Group users will be moved to.
     *
     * @throws Exception If users cannot be moved for any reason.
     */
    public function moveUsersTo(Group $destinationGroup): void
    {
        if ($destinationGroup->children()->exists()) {
            throw new Exception();
        }

        $userIds = $this->users->pluck('id');
        $destinationGroup->users()->syncWithoutDetaching($userIds);
        $this->users()->detach($userIds);
    }

    /**
     * Removes subgroups from this group and adds them to the destination group.
     *
     * @param Group $destinationGroup Group subgroups will be moved to.
     *
     * @throws Exception If subgroups cannot be moved for any reason.
     */
    public function moveSubgroupsTo(Group $destinationGroup): void
    {
        if ($destinationGroup->users()->exists()) {
            throw new Exception();
        }
        $this->children()->update(['parent_id' => $destinationGroup->id]);
    }

    public function setAsDefault()
    {
        Group::query()->update(['is_default_group' => 0]);
        $this->update(['is_default_group' => 1]);
    }

    public function containsUser(User $user): bool
    {
        return $this->getAllUsersInSubgroups()->contains($user);
    }

    // Relationships
    public function users()
    {
        return $this->belongsToMany(User::class, 'group_user')->withTimestamps();
    }

    public function competitions()
    {
        return $this->belongsToMany(Group::class, 'competition_group')->withTimestamps();
    }

    public function parent()
    {
        return $this->belongsTo(Group::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Group::class, 'parent_id')->with('children', 'users');
    }
}
