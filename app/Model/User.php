<?php
namespace App\Model;

use RepoRangler\Entity\PublicUser;

class User extends PublicUser
{
    protected $table = 'user';

    protected $hidden = ['password'];

    protected $appends = ['is_admin_user', 'is_rest_user'];

    public function setPassword(string $password): self
    {
        $this->password = password_hash($password, PASSWORD_BCRYPT);

        return $this;
    }

    public function checkPassword(string $password)
    {
        return password_verify($password, $this->password);
    }

    public function package_groups()
    {
        return $this->belongsToMany(PackageGroup::class, UserPackageGroup::class);
    }

    public function access_tokens()
    {
        return $this->hasMany(AccessToken::class);
    }

    public function capability()
    {
        return $this->hasMany(UserCapability::class);
    }
}
