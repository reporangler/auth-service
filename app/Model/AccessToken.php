<?php
namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class AccessToken extends Model
{
    protected $fillable = ['user_id', 'type', 'token'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
