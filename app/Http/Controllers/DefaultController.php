<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Laravel\Lumen\Routing\Controller as BaseController;

class DefaultController extends BaseController
{
    public function healthz()
    {
        return new JsonResponse(["statusCode" => 200, "service" => config('app.auth_base_url')], 200);
    }

    public function cors()
    {
        $this->healthz();
    }
}
