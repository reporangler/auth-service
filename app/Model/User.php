<?php
namespace App\Model;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class User extends \RepoRangler\Entity\User
{
    protected $table = 'user';

    protected $with = ['access_tokens'];

    public function setPasswordAttribute(string $password): void
    {
        $this->attributes['password'] = password_hash($password, PASSWORD_BCRYPT);
    }

    public function checkPassword(string $password): bool
    {
        return password_verify($password, $this->attributes['password']);
    }

    public function access_tokens(): HasMany
    {
        return $this->hasMany(AccessToken::class);
    }

    public function capability(): MorphMany
    {
        return $this->morphMany(CapabilityMap::class, 'entity');
    }

    public function scopeAdmin(Builder $query)
    {
        return $query->whereHas('capability', function(Builder $query){
            $query->adminUser();
        });
    }

    public function scopePackageGroupAdmin(Builder $query, User $user)
    {
        return $query->whereHas('capability', function(Builder $query) use ($user) {
            $query->packageGroupAdmin($user);
        });
    }

    public function giveAdmin(): CapabilityMap
    {
        return CapabilityMap::create([
            'entity_type' => CapabilityMap::USER,
            'entity_id' => $this->id,
            'name' => Capability::IS_ADMIN_USER,
            'constraint' => [],
        ]);
    }

    public function removeAdmin(): void
    {
        foreach($this->capability as $cap){
            if($cap->name === Capability::IS_ADMIN_USER){
                $cap->delete();
            }
        }
    }
}
