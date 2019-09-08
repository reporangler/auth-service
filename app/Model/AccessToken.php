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
/**
select
    access_tokens.*,
    access_token_user.user_id as pivot_user_id,
    access_token_user.access_token_id as pivot_access_token_id
from
    access_tokens
inner join
    access_token_user on access_tokens.id = access_token_user.access_token_id
where
    access_token_user.user_id in (1)
*/
