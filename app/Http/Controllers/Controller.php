<?php

namespace App\Http\Controllers;

use App\Services\DatabaseAuthenticator;
use App\Services\LDAPAuthenticator;
use Illuminate\Http\Request;
use Laravel\Lumen\Routing\Controller as BaseController;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class Controller extends BaseController
{
    public function cors($args)
    {
        return $this->healthz();
    }

    public function healthz()
    {
        return new JsonResponse(["statusCode" => 200, "service" => config('app.auth_base_url')], 200);
    }

    public function auth(Request $request)
    {
        $authSchema = [
            'type' => 'required|in:http-basic,ldap',
            'username' => 'required|string',
            'password' => 'required|string',
        ];

        $data = $this->validate($request,$authSchema);

        switch($data['type']){
            case 'http-basic':
                $db = app(DatabaseAuthenticator::class);
                return new JsonResponse($db->auth($data['username'], $data['password']), 200);
                break;

            case 'ldap':
                $ldap = app(LDAPAuthenticator::class);
                return new JsonResponse($ldap->auth($data['username'], $data['password']), 200);
                break;
        }

        throw new BadRequestHttpException("No Authorization attempt made");
    }
}
