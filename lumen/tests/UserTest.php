<?php

use Illuminate\Support\Facades\Hash;

class UserTest extends TestCase
{

    public function testMe()
    {
        $user = factory('App\User')
            ->create();

        $this->actingAs($user)
            ->get('/api/v1/users/me');

        $this->seeJson(['status' => true])
            ->seeJsonStructure([
                'status',
                'data' => [
                    'id',
                    'username',
                    'created_at',
                ],
            ]);
    }

    public function testShow()
    {
        $user = factory('App\User')
            ->create();

        $anotherUser = factory('App\User')
            ->create();

        $this->actingAs($user)
            ->get("/api/v1/users/show?id={$anotherUser->id}");

        $this->seeJson(['status' => true])
            ->seeJsonStructure([
                'status',
                'data' => [
                    'id',
                    'username',
                    'created_at',
                ],
            ]);
    }

    public function testUpdate()
    {
        $user = factory('App\User')
            ->create();

        $newPassword = $this->faker->password(8);

        $this->actingAs($user)
            ->put("/api/v1/users/update", [
                'password' => $newPassword,
            ]);

        $this->seeJson(['status' => true]);

        $user->fresh();
        $this->assertTrue(Hash::check($newPassword, $user->password));
    }

    public function testSearch()
    {
        $users = factory('App\User', 10)
            ->create();

        $user = $users->random(1)
            ->first();

        $username = $user->username;
        $limit = strlen($username);

        $query = $username[rand(0, $limit - 1)];

        $this->get("/api/v1/users/search?query={$query}");
        $this->seeJson(['status' => true]);
        $this->seeJsonStructure([
            'status',
            'data' => [
                'data' => [
                    '*' => [
                        'id',
                        'username',
                    ],
                ],
            ],
        ]);

    }

}
