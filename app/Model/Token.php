<?php
namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Token extends Model{

    public function __construct(User $user, \DateTime $expireAt)
    {
        $this->token        = sha1(microtime(true));
        $this->user_id      = $user->id;
        $this->expire_at    = $expireAt;
    }
}
