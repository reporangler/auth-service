<?php
namespace App\Model;

use Illuminate\Database\Eloquent\Builder;

class CapabilityMap extends \RepoRangler\Entity\CapabilityMap
{
    protected $table    = 'capability_map';
    protected $hidden   = ['capability', 'entity'];
    protected $appends  = ['name'];
    protected $casts    = ['constraint' => 'array'];

    const USER          = 'user';
    const PACKAGE_GROUP = 'package-group';
    const REPOSITORY    = 'repository';

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

    public function scopeAdminUser(Builder $query)
    {
        return $query->where('capability_id', Capability::IsAdminUser()->firstOrFail()->id);
    }

    public function scopePackageGroupAdmin(Builder $query, User $user)
    {
        return $query->where([
            'entity_type' => CapabilityMap::USER,
            'entity_type' => $user->id,
            'capability_id' => Capability::packageGroupAccess()->firstOrFail()->id,
            'constraint->admin' => true,
        ]);
    }
}
