<?php
namespace App\Model;

use Illuminate\Database\Eloquent\Builder;

class UserCapability extends \RepoRangler\Entity\UserCapability
{
    protected $table = 'user_capability';
    protected $hidden = ['capability', 'user'];
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
        $this->capability_id = Capability::where('name', $name)->firstOrFail()->id;
    }

    public function getNameAttribute(): string
    {
        return $this->capability['name'];
    }

    public function setPackageGroupAttribute(PackageGroup $packageGroup): void
    {
        $this->name = Capability::PACKAGE_GROUP_ACCESS;
        $this->constraint = ['name' => $packageGroup->name];
    }

    static public function whereUserHasPackageGroup(User $user, PackageGroup $packageGroup): ?UserCapability
    {
        return UserCapability::whereHas('capability', function (Builder $query){
            $query->where('name', Capability::PACKAGE_GROUP_ACCESS);
        })->where([
            'user_id' => $user->id,
            'constraint->name' => $packageGroup->name,
        ])->first();
    }
}
