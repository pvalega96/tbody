<?php

namespace Tests\Unit;

use Tests\TestCase;

class AuthTest extends TestCase
{
    /**
     * A basic unit test example.
     *
     * @return void
     */
    public function testRegister_email_exist()
    {
        $test_register = [
            'name' => 'pedro',
            'email' => 'pv0@hotmail.com',
            'password' => '123456789',
            'admin' => 0
        ];
        $response = $this->json('POST','/api/v1/register',$test_register);

        $response
            ->assertStatus(400)
            ->assertJsonPath('message', 'Validation errors')
            ->assertJsonPath('data.email.0', 'The email has already been taken.');
    }

    public function testRegister_succes()
    {
        $test_register = [
            'name' => 'pedro',
            'email' => 'pv213@hotmail.com',
            'password' => '123456789',
            'admin' => 0
        ];
        $response = $this->json('POST','/api/v1/register',$test_register);
        $response
            ->assertStatus(200);
    }


    public function testLogin_succes()
    {
        $test_login = [
            'email' => 'pv213@hotmail.com',
            'password' => '123456789'
        ];
        $response = $this->json('POST','/api/v1/login',$test_login);

        $response
            ->assertStatus(200)
            ->assertJsonPath('token_type', 'Bearer');
    }

    public function testLogin_unauthorized()
    {
        $test_login = [
            'email' => 'pv0332@hotmail.com',
            'password' => '123123133'
        ];
        $response = $this->json('POST','/api/v1/login',$test_login);

        $response
            ->assertStatus(401)
            ->assertJsonPath('message', 'Unauthorized');
    }


}
