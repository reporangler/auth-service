<?php
namespace App\Model;

use Illuminate\Database\Eloquent\Builder;

class CapabilityMap extends \RepoRangler\Entity\CapabilityMap
{
    protected $table = 'capability_map';
    protected $hidden = ['capability', 'entity'];
    protected $appends = ['name'];
    protected $casts = ['constraint' => 'array'];

    public function capability()
    {
        return $this->belongsTo(Capability::class);
    }

    public function entity()
    {
        return $this->morphTo();
    }

    public function setNameAttribute(string $name): void
    {
        $this->capability_id = Capability::where('name', $name)->firstOrFail()->id;
    }

    public function getNameAttribute(): string
    {
        return $this->capability['name'];
    }

    public function setAdminAttribute(bool $admin): void
    {
        $constraint = $this->constraint;
        $constraint['admin'] = $admin;
        $this->constraint = $constraint;
    }

    public function scopeAdminUser($query)
    {
        return $query->where('capability_id', Capability::IsAdminUser()->firstOrFail()->id);
    }
}
