<?php

namespace App\Http\Controllers;

use App\Model\User;
use App\Model\UserToken;
use App\Services\DatabaseAuthenticator;
use App\Services\LDAPAuthenticator;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Laravel\Lumen\Routing\Controller as BaseController;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class LoginController extends BaseController
{
    /**
     * @param Request $request
     * @return JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function login(Request $request)
    {
        $schema = [
            'type' => 'required|in:http-basic,database,ldap',
            'username' => 'required|string',
            'password' => 'required|string',
            'repository_type' => 'required|string',
        ];

        $data = $this->validate($request,$schema);

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

        return new JsonResponse($user, 200);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function check(Request $request)
    {
        $token = $request->headers->get('Authorization');
        $token = str_replace('Bearer','', $token);
        $token = trim($token);

        $token = UserToken::with([
            'user.package_groups',
            'user.access_tokens'
        ])->where(['token' => $token])->firstOrFail();

        return new JsonResponse($token->user, 200);
    }
}
