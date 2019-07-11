<?php

use Illuminate\Support\Facades\Hash;

class SignTest extends TestCase
{

    public function testSignup()
    {

        $this->post('/api/v1/users/signup', [
            'username' => $this->faker->name,
            'password' => $this->faker->password,
        ]);

        $this->seeJson(['status' => true]);

        $this->seeJsonStructure([
            'status',
            'data' => [
                'id',
                'username',
                'created_at',
            ],
        ]);
    }

    /**
     * Create user
     * 
     * @return string token
     */
    private function signedUser() : string
    {
        $password = $this->faker->password;

        $user = factory('App\User')->make();
        $user->password = Hash::make($password);
        $user->save();

        $this->post('/api/v1/users/signin', [
            'username' => $user->username,
            'password' => $password,
        ]);

        $this->seeJson(['status' => true]);

        $this->seeJsonStructure([
            'status',
            'data' => [
                'user' => [
                    'id',
                    'username',
                    'created_at',
                ],
                'token',
            ],
        ]);
        
        $response = $this->response->getContent();
        $jsonResponse = json_decode($response);

        return $jsonResponse->data->token;
    }

    public function testSignin()
    {
        $token = $this->signedUser();
    }

    public function testSignout()
    {
        $token = $this->signedUser();

        $this->delete('/api/v1/users/signout', [], ['Authorization' => $token]);

        $this->seeJson(['status' => true]);
    }

}
