<?php

namespace App\Http\Controllers;

use App\Model\User;
use App\Model\AccessToken;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Gate;
use Illuminate\Routing\Controller as BaseController;

class AccessTokenController extends BaseController
{
    public function findByUserId(Request $request, int $userId)
    {
        Gate::authorize('user-list-token');

        $list = AccessToken::where('user_id', $userId)->get();

        return new JsonResponse([
            'count' => count($list),
            'data' => $list
        ], count($list) ? 200 : 404);
    }

    public function add(Request $request, int $id)
    {
        Gate::authorize('user-add-token');

        $types = ['github'];

        $schema = [
            'type' => 'required|in:'.implode(',',$types),
            'token' => 'required|string',
        ];

        $data = $request->validate($schema);

        $user = User::findOrFail($id);

        $token = new AccessToken();
        $token->user_id = $user->id;
        $token->type = $data['type'];
        $token->token = $data['token'];
        $token->save();

        return new JsonResponse($token);
    }

    public function remove(Request $request, int $userId, int $tokenId)
    {
        Gate::authorize('user-remove-token');

        $accessToken = AccessToken::where([
            'id' => $tokenId,
            'user_id' => $userId,
        ])->firstOrFail();

        $deleted[] = $accessToken->toArray();

        $accessToken->delete();

        return new JsonResponse(['deleted' => $deleted]);
    }
}
