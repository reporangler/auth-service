<?php
namespace App\Model;

class UserCapability extends \RepoRangler\Entity\UserCapability
{
    protected $table = 'user_capability';
    protected $hidden = ['capability'];
    protected $appends = ['name'];
    protected $casts = ['constraint' => 'array'];

    public function capability()
    {
        return $this->belongsTo(Capability::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function setNameAttribute(string $name): void
    {
        $this->name = $name;
        $this->attributes['capability_id'] = Capability::where('name', $name)->firstOrFail()->id;
    }

    public function getNameAttribute(): string
    {
        return $this->capability['name'];
    }
}
