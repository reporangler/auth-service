<?php

namespace App\Http\Controllers;

use App\Model\User;
use App\Model\AccessToken;
use App\Services\DatabaseAuthenticator;
use App\Services\LDAPAuthenticator;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Laravel\Lumen\Routing\Controller as BaseController;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class AccessTokenController extends BaseController
{
    public function add(Request $request, int $id)
    {
        $schema = [
            'type' => 'required|in:github',
            'token' => 'required|string',
        ];

        $data = $this->validate($request,$schema);

        $user = User::find($id)->firstOrFail();

        $token = new AccessToken();
        $token->user_id = $user->id;
        $token->type = $data['type'];
        $token->token = $data['token'];
        $token->save();

        return new JsonResponse($token, 200);
    }

    public function remove(int $userId, int $tokenId)
    {
        $accessToken = AccessToken::where([
            'id' => $tokenId,
            'user_id' => $userId,
        ])->firstOrFail();

        $deleted[] = $accessToken->toArray();

        $accessToken->delete();

        return new JsonResponse(['deleted' => $deleted], 200);
    }
}
