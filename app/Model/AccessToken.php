<?php
namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class AccessToken extends Model
{
    public function user()
    {
        return $this->hasOne(User::class);
    }
}
