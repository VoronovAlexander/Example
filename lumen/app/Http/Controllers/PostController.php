<?php

namespace App\Http\Controllers;

use App\Post;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class PostController extends Controller
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

    public function store(Request $request)
    {
        $this->validate($request, [
            'text' => 'required|string|min:3|max:255',
        ]);

        $user = Auth::user();

        $post = $user->posts()
            ->create($request->input());

        Cache::forget('wallFirstPage');

        return [
            'status' => true,
            'data' => $post,
        ];
    }

    public function wall(Request $request)
    {
        $query = Post::with('user')
            ->orderByDesc('id');

        $posts = $request->input('page', 1) === 1
        ? Cache::rememberForever('wallFirstPage', function () use ($query) {
            return $query->paginate();
        })
        : $query->paginate();

        return [
            'status' => true,
            'data' => $posts,
        ];
    }

    public function search(Request $request)
    {
        $this->validate($request, [
            'query' => 'required|string|min:1|max:255',
        ]);

        $posts = Post::with('user')
            ->where('text', 'like', "%" . $request->input('query') . "%")
            ->orderByDesc('id')
            ->paginate();

        return [
            'status' => true,
            'data' => $posts,
        ];
    }

    public function index(Request $request)
    {
        $this->validate($request, [
            'user_id' => 'required|integer|min:1|exists:users,id',
            'page' => 'sometimes|integer',
        ]);

        $user = User::find($request->user_id);

        $posts = $user->posts()
            ->orderByDesc('id')
            ->paginate();

        return [
            'status' => true,
            'data' => $posts,
        ];

    }

    public function show(Request $request)
    {
        $this->validate($request, [
            'id' => 'required|integer|min:1|exists:posts,id,deleted_at,NULL',
        ]);

        $post = Post::find($request->id);

        return [
            'status' => true,
            'data' => $post,
        ];
    }

    public function update(Request $request)
    {
        $this->validate($request, [
            'id' => 'required|integer|min:1|exists:posts,id,deleted_at,NULL',
        ]);

        $post = Post::find($request->id);

        $this->authorize('update', $post);

        $post->update($request->input());

        return [
            'status' => true,
            'data' => $post,
        ];
    }

    public function delete(Request $request)
    {
        $this->validate($request, [
            'id' => 'required|integer|min:1|exists:posts,id,deleted_at,NULL',
        ]);

        $post = Post::find($request->id);

        $this->authorize('delete', $post);

        $post->delete();

        return ['status' => true];

    }

    //
}
