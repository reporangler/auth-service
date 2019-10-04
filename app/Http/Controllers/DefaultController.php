<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Laravel\Lumen\Routing\Controller as BaseController;

class DefaultController extends BaseController
{
    public function cors()
    {
        return $this->healthz();
    }

    public function healthz()
    {
        return new JsonResponse(["statusCode" => 200, "service" => config('app.auth_base_url')], 200);
    }
}
