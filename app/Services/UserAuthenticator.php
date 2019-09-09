<?php

namespace App\Services;

use App\Model\User;
use App\Model\UserToken;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class UserAuthenticator
{
    public function loginRepoUser(Request $request)
    {
        $schema = [
            'type' => 'required|in:http-basic,database,ldap',
            'username' => 'required|string',
            'password' => 'required|string',
            'repository_type' => 'required|string',
        ];

        $data = Validator::make($request->all(),$schema)->validate();

        $user = User::where([
            'username' => $data['username'],
            'repository_type' => $data['repository_type'],
        ])->with([
            'package_groups',
            'access_tokens',
        ])->firstOrFail();

        switch($data['type']){
            case 'database':
            case 'http-basic':
                $auth = app(DatabaseAuthenticator::class);

                $user = $auth->login($user, $data['password']);
                break;

            case 'ldap':
                $ldap = app(LDAPAuthenticator::class);
                $user = $ldap->login($user, $data['password']);
                break;

            default:
                throw new BadRequestHttpException('No Authorization attempt made');
                break;
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

    public function checkToken(Request $request)
    {
        $token = $request->headers->get('Authorization');
        $token = str_replace('Bearer','', $token);
        $token = trim($token);

        $token = UserToken::with([
            'user.package_groups',
            'user.access_tokens'
        ])->where(['token' => $token])->firstOrFail();

        return $token->user;
    }
}
