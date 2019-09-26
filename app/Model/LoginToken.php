<?php
namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class LoginToken extends Model
{
    protected $fillable = ['user_id', 'expire_at', 'token'];

    public function __construct(array $attributes = [])
    {
        $attributes['token'] = sha1(microtime(true));

        parent::__construct($attributes);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
