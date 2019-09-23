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

    public function validateLoginHeaders(Request $request)
    {
        $headers = $this->flattenHeaders($request->headers->all());

        // TODO: Maybe login types could also be dynamically registered?
        $loginTypes = ['http-basic', 'database', 'ldap'];

        // TODO: This could request the metadata service to return a list of registered services?
        $repositoryTypes = ['php'];

        $validator = Validator::make($headers, [
            'reporangler-login-type' => 'string|in:'.implode(',', $loginTypes),
            'reporangler-login-username' => 'required|string',
            'reporangler-login-password' => 'required|string',
            'reporangler-login-repository-type' => 'string|in:'.implode(',', $repositoryTypes),
        ]);

        $data = $validator->validate();

        // We override this because we always want to check the database in this case
        if($data['reporangler-login-type'] === 'http-basic'){
            $data['reporangler-login-type'] = 'database';
        }

        // Default to database logins
        if(!array_key_exists('reporangler-login-type', $data)){
            $data['reporangler-login-type'] = 'database';
        }

        // Default to REST Api User Login
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

    public function login(string $type, string $username, string $password): User
    {
        if($type === 'database'){
            $auth = app(DatabaseAuthenticator::class);
            $user = $auth->login($username, $password);
        }else if($type === 'ldap') {
            $auth = app(LDAPAuthenticator::class);
            $user = $auth->login($username, $password);
        }else{
            abort(400, 'No Authorization attempt made');
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

    public function loginRepoUser(Request $request)
    {
        return false;
    }

    public function checkToken(string $token): User
    {
        $token = str_replace('Bearer','', $token);
        $token = trim($token);

        $token = UserToken::with([
            'user.package_groups',
            'user.access_tokens',
        ])->where(['token' => $token])->firstOrFail();

        return $token->user;
    }
}
