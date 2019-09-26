<?php
namespace App\Model;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;
use RepoRangler\Entity\PublicUser;

class User extends PublicUser
{
    protected $table = 'user';

    protected $hidden = ['password'];

    protected $with = ['capability', 'access_tokens'];

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

    public function capability(): HasMany
    {
        return $this->hasMany(UserCapability::class);
    }
}
