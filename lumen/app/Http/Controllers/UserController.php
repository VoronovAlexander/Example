<?php

namespace App\Http\Controllers;

use App\Jobs\OptimizeAvatar;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redis;

class UserController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    public function signup(Request $request)
    {
        $this->validate($request, [
            'username' => 'required|string|min:3|unique:users',
            'password' => 'required|string|min:6',
            'avatar' => 'sometimes|nullable|image|dimensions:min_width=1024,min_height=1024|max:2048',
        ]);

        $data = $request->input();

        $data['password'] = Hash::make($request->password);

        if ($request->file('avatar')) {
            $data['avatar'] = $request->file('avatar')
                ->store('avatars');
        }

        $user = User::create($data);

        if ($request->file('avatar')) {
            dispatch(new OptimizeAvatar($user));
        }

        return [
            'status' => true,
            'data' => $user,
        ];
    }

    public function signin(Request $request)
    {
        $this->validate($request, [
            'username' => 'required|string|min:3|exists:users',
            'password' => 'required|string|min:6',
        ]);

        $user = User::whereUsername($request->username)
            ->first();

        if (!$user) {
            return [
                'status' => false,
            ];
        }

        if (!Hash::check($request->password, $user->password)) {
            return [
                'status' => false,
            ];
        }

        $token = Hash::make(uniqid($user->id));

        Cache::put($token, $user->id, Carbon::now()->addDays(14));

        return [
            'status' => true,
            'data' => [
                'user' => $user,
                'token' => $token,
            ],
        ];
    }

    public function me(Request $request)
    {
        $user = Auth::user();
        return [
            'status' => true,
            'data' => $user,
        ];
    }

    public function show(Request $request)
    {
        $this->validate($request, [
            'id' => 'required|integer|min:1|exists:users',
        ]);

        $user = User::find($request->id);

        return [
            'status' => true,
            'data' => $user,
        ];
    }

    public function update(Request $request)
    {
        $this->validate($request, [
            'password' => 'required|string|min:6',
        ]);

        $user = Auth::user();

        $data = $request->input();
        $data['password'] = Hash::make($request->password);

        $user->update($data);

        return ['status' => true];
    }

    public function search(Request $request)
    {
        $this->validate($request, [
            'query' => 'required|string|min:1|max:255',
        ]);
        
        $users = User::where('username', 'like', "%" . $request->input('query') . "%")
            ->paginate();

        return [
            'status' => true,
            'data' => $users,
        ];
    }

    public function signout(Request $request)
    {
        Redis::del($request->header('Authorization'));
        return ['status' => true];
    }

}
