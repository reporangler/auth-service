<?php

namespace App\Http\Controllers;

use Laravel\Lumen\Routing\Controller as BaseController;
use Illuminate\Http\JsonResponse;

class Controller extends BaseController
{
    public function cors($args)
    {
        return $this->healthz();
    }

    public function healthz()
    {
        return new JsonResponse(["statusCode" => 200, "service" => config('app.url')], 200);
    }
}
