<?php

namespace App\Services;

use App\Model\User;
use App\Model\UserToken;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class UserAuthenticator
{
    public function loginRepoUser(string $type, string $username, string $password, string $repoType): User
    {
        if($repoType === 'http-basic'){
            $repoType = 'database';
        }

        $user = User::where([
            'username' => $username,
            'repository_type' => $repoType,
        ])->with([
            'package_groups',
            'access_tokens',
        ])->firstOrFail();

        if($repoType === 'database'){
            $auth = app(DatabaseAuthenticator::class);
            $user = $auth->login($user, $password);
        }else if($repoType === 'ldap') {
            $auth = app(LDAPAuthenticator::class);
            $user = $auth->login($user, $password);
        }else{
            throw new BadRequestHttpException('No Authorization attempt made');
        }

        $hours = config('app.token_life_hours');
        $lifetime = new \DateInterval("PT{$hours}H");
        $expireAt = new \DateTime();
        $expireAt->add($lifetime);

        $token = new UserToken();
        $token->user_id = $user->id;
        $token->expire_at = $expireAt;
        $token->save();

        $user->token = $token->token;

        return $user;
    }

    public function loginApiUser(Request $request)
    {
        return false;
    }

    public function checkToken(string $token): User
    {
        $token = str_replace('Bearer','', $token);
        $token = trim($token);

        $token = UserToken::with([
            'user.package_groups',
            'user.access_tokens'
        ])->where(['token' => $token])->firstOrFail();

        return $token->user;
    }
}
