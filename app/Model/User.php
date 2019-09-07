<?php
namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    protected $hidden = ['password'];

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function setPassword(string $password): self
    {
        $this->password = password_hash($password, PASSWORD_BCRYPT);

        return $this;
    }

    public function checkPassword(string $password)
    {
        return password_verify($password, $this->password);
    }

    public function setRepositoryType(string $repository_type): self
    {
        $this->repository_type = $repository_type;

        return $this;
    }

    public function getRepositoryType(): string
    {
        return $this->repository_type;
    }

    public function packageGroups()
    {
        return $this->belongsToMany(PackageGroup::class,UserPackageGroup::class);
    }
}
