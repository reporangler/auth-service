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

    public function scopeUser(Builder $query, ?User $user = null)
    {
        $fields = ['entity_type' => CapabilityMap::USER];

        if($user instanceof User){
            $fields['entity_id'] = $user->id;
        }

        return $query->where($fields);
    }

    public function scopePackageGroup(Builder $query, ?string $packageGroup = null, ?string $repository = null)
    {
        $fields = ['capability_id' => Capability::packageGroupAccess()->firstOrFail()->id];

        if(!empty($packageGroup)) $fields['constraint->package_group'] = $packageGroup;

        if(!empty($repository)) $fields['constraint->repository'] = $repository;

        return $query->where($fields);
    }

    public function scopeAdmin(Builder $query)
    {
        return $query->where([
            'constraint->admin' => true,
        ]);
    }

    public function scopeApprovals(Builder $query)
    {
        return $query->where([
            'constraint->approved' => false,
        ]);
    }
}
