<?php
namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Token extends Model
{
    public function __construct()
    {
        $this->token = sha1(microtime(true));
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
