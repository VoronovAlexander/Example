<?php

class PostTest extends TestCase
{
    
    public function testStore()
    {
        $user = factory('App\User')
            ->create();

        $anotherUser = factory('App\User')
            ->create();

        $this->actingAs($user)
            ->post('/api/v1/posts/', [
                'text' => $this->faker->text(200),
            ]);

        $this->seeJson(['status' => true])
            ->seeJsonStructure([
                'status',
                'data' => [
                    'id',
                    'text',
                    'created_at',
                ],
            ]);
    }

    public function testIndex()
    {
        $user = factory('App\User')
            ->create();

        $posts = factory('App\Post', 10)
            ->make();

        $posts->each(function ($post) use ($user) {
            $post->user_id = $user->id;
            $post->save();
        });

        $this->get("/api/v1/posts?user_id={$user->id}");

        $this->seeJson(['status' => true])
            ->seeJsonStructure([
                'status',
                'data' => [
                    'data' => [
                        '*' => [
                            'id',
                            'text',
                            'created_at',
                        ],
                    ],
                    'next_page_url',
                ],
            ]);
    }

    public function testWall()
    {
        $user = factory('App\User')
            ->create();

        $posts = factory('App\Post', 10)
            ->make();

        $posts->each(function ($post) use ($user) {
            $post->user_id = $user->id;
            $post->save();
        });

        $this->get("/api/v1/posts/wall");

        $this->seeJson(['status' => true])
            ->seeJsonStructure([
                'status',
                'data' => [
                    'data' => [
                        '*' => [
                            'id',
                            'text',
                            'created_at',
                        ],
                    ],
                    'next_page_url',
                ],
            ]);
    }

    public function testSearch()
    {
        $user = factory('App\User')
            ->create();

        $posts = factory('App\Post', 10)
            ->make();

        $posts->each(function ($post) use ($user) {
            $post->user_id = $user->id;
            $post->save();
        });

        $post = $posts->random(1)
            ->first();

        $limit = strlen($post->text);
        $query = $post->text[rand(0, $limit-1)];

        $this->get("/api/v1/posts/search?query={$query}");

        $this->seeJson(['status' => true])
            ->seeJsonStructure([
                'status',
                'data' => [
                    'data' => [
                        '*' => [
                            'id',
                            'text',
                            'created_at',
                        ],
                    ],
                    'next_page_url',
                ],
            ]);
    }

    public function testShow()
    {
        $user = factory('App\User')
            ->create();

        $post = factory('App\Post')
            ->make();

        $post->user_id = $user->id;
        $post->save();

        $this->actingAs($user)
            ->get("/api/v1/posts/show?id={$post->id}");

        $this->seeJson(['status' => true])
            ->seeJsonStructure([
                'status',
                'data' => [
                    'id',
                    'text',
                    'created_at',
                ],
            ]);
    }

    public function testUpdate()
    {
        $user = factory('App\User')
            ->create();

        $post = factory('App\Post')
            ->make();

        $post->user_id = $user->id;
        $post->save();

        $newText = $this->faker->text(200);

        $this->actingAs($user)
            ->put("/api/v1/posts/update?id={$post->id}", [
                'text' => $newText,
            ]);

        $this->seeJson(['status' => true])
            ->seeJsonContains([
                'text' => $newText,
            ])
            ->seeJsonStructure([
                'status',
                'data' => [
                    'id',
                    'text',
                    'created_at',
                ],
            ]);
    }

    public function testDelete()
    {
        $user = factory('App\User')
            ->create();

        $post = factory('App\Post')
            ->make();

        $post->user_id = $user->id;
        $post->save();

        $newText = $this->faker->text(200);

        $this->actingAs($user)
            ->delete("/api/v1/posts/delete?id={$post->id}");

        $this->seeJson(['status' => true]);
    }

}
