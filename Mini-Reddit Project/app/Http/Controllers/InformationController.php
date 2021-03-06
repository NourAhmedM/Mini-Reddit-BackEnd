<?php

namespace App\Http\Controllers;

use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Illuminate\Http\Request;
use App\Blocking;
use App\User;

/**
 * @group User Information
 */
class InformationController extends Controller
{
    /**
     * Show user's private information.
     *
     * @authenticated
     * @response 200 {
     *  "success": "true",
     *  "email": "john_bb@gmail"
     * }
     *
     * @response 401 {
     *  "success": "false",
     * 	"error": "UnAuthorized"
     * }
     */
    public function viewPrivateUserInfo()
    {
        $user = auth()->user();

        if (!$user) {
            return response()->json([
                'success' => 'false',
                'error' => 'UnAuthorized',
            ], 401);
        }

        return response()->json([
            'success' => 'true',
            'email' => $user->email,
        ], 200);
    }

    /**
     * Show user's public information.
     *
     * @bodyParam username string required username to show his public info
     * @response 200 {
     *  "success": "true",
     *  "username": "john",
     *  "name": "John Smith",
     *  "karma":500,
     *  "cake_day":"March 8, 2019",
     *  "about":"be or not to be",
     *  "photo_path" : "storage/app/avater.jpg",
     *  "cover_path" : "storage/app/bannar.jpg"
     *
     * }
     *
     * @response 403 {
     *  "success": "false",
     * 	"error": "username doesn't exist"
     * }
     */
    public function viewPublicUserInfo(Request $request)
    {
        if (!$request->username || !User::userExist($request->username)) {
            return response()->json([
                'success' => 'false',
                'error' => "username doesn't exist",
            ], 403);
        }

        $Auth = 1;
        try {
            $tokenFetch = JWTAuth::parseToken()->authenticate();
        } catch (JWTException $e) {
            $Auth = 0;
        }

        if ($Auth) {
            $user = auth()->user();

            if (Blocking::blockedOrBlocker($user->username, $request->username)) {
                return response()->json([
                    'success' => 'false',
                    'error' => "username doesn't exist",
                ], 403);
            }
        }

        $selected_user = User::getUserWholeRecord($request->username);

        return response()->json([
            'success' => 'true',
            'username' => $selected_user->username,
            'name' => $selected_user->display_name,
            'karma' => $selected_user->karma,
            'cake_day' => $selected_user->cake_date,
            'about' => $selected_user->about,
            'photo_path' => $selected_user->photo_url,
            'cover_path' => $selected_user->cover_url,
        ], 200);
    }

    /**
     * Show user's username.
     *
     * @authenticated
     * @response 200 {
     *  "success": "true",
     *  "username": "john"
     * }
     *
     * @response 401 {
     *  "success": "false",
     * 	"error": "UnAuthorized"
     * }
     */
    public function getUsername()
    {
        return response()->json([
            'success' => 'true',
            'username' => auth()->user()->username,
        ]);
    }
}
