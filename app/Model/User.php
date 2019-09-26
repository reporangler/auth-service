<?php
namespace App\Model;

use RepoRangler\Entity\PublicUser;

class User extends PublicUser
{
    protected $table = 'user';

    protected $hidden = ['password'];

    public function setPassword(string $password): self
    {
        $this->password = password_hash($password, PASSWORD_BCRYPT);

        return $this;
    }

    public function checkPassword(string $password)
    {
        return password_verify($password, $this->password);
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
