<?php

namespace App\Services;

use App\Model\User;
use App\Model\UserToken;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class UserAuthenticator
{
    private function flattenHeaders(array $headers)
    {
        foreach($headers as $key => $value){
            $headers[$key] = is_array($value) ? current($value) : $value;
        }

        return $headers;
    }

    public function validateLoginRequest(Request $request)
    {
        $headers = $this->flattenHeaders($request->headers->all());

        $validator = Validator::make($headers, [
            'reporangler-login-type' => 'required|in:http-basic,database,ldap',
            'reporangler-login-username' => 'required|string',
            'reporangler-login-password' => 'required|string',
            'reporangler-login-repository-type' => 'string',
        ]);

        $data = $validator->validate();

        if(!array_key_exists('reporangler-login-repository-type', $data)){
            $data['reporangler-login-repository-type'] = null;
        }

        return $data;
    }

    public function validateTokenRequest(Request $request)
    {
        $headers = $this->flattenHeaders($request->headers->all());

        $validator = Validator::make($headers, [
            'authorization' => 'required|string'
        ]);

        $data = $validator->validate();

        return $data['authorization'];
    }

    public function loginRepoUser(string $type, string $username, string $password, ?string $repoType): User
    {
        if($type === 'http-basic'){
            $type = 'database';
        }

        $user = User::where([
            'username' => $username,
            'repository_type' => $repoType,
        ])->with([
            'package_groups',
            'access_tokens',
        ])->firstOrFail();

        if($type === 'database'){
            $auth = app(DatabaseAuthenticator::class);
            $user = $auth->login($user, $password);
        }else if($type === 'ldap') {
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
