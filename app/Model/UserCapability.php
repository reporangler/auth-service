<?php
namespace App\Model;

use RepoRangler\Entity\UserCapability as UserCapabilityEntity;

class UserCapability extends UserCapabilityEntity
{
    protected $table = 'user_capability';
    protected $hidden = ['capability'];
    protected $appends = ['name'];

    public function capability()
    {
        return $this->belongsTo(Capability::class);
    }

    public function getNameAttribute(): string
    {
        return $this->capability['name'];
    }
}
