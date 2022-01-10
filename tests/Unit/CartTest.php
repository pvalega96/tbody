<?php

namespace Tests\Unit;

use Tests\TestCase;

class CartTest extends TestCase
{
    /**
     * A basic unit test example.
     *
     * @return void
     */
    public $token='eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJhdWQiOiIzIiwianRpIjoiOTViN2ZkZTBjNDcyMzBlMzhhZjUyZTRjNTkzZmVlMjAyNjNhMmZiZWQyODQ4YTcwMjQ2ZDI0ZTEwMGRiZjQzNGFlMTRiZmZkY2ExODNkMDciLCJpYXQiOjE2NDE4NDQwNzkuODAzMTYyLCJuYmYiOjE2NDE4NDQwNzkuODAzMTY3LCJleHAiOjE2NzMzODAwNzkuNzkyMDg3LCJzdWIiOiIyIiwic2NvcGVzIjpbXX0.qagpcVeJpM1Zf11QPuPSXk9TWnxLDj-X6QijTEeXI0lLK2BIzdM9z_nH2oaQUKHGpU_1zdyeON2vt4sw01l0JLSLM4BeMBxDkAs659IYL0IbniG95Ov-5d4EIZVXmh_eErbcmXTmjIluw9cb1SIKk2WhfSa9mkdSPLnkbhgrWGhrcpxX9UhF6rHG1hINBvaz2q2ScIyVOVSEVSPt20yKPEJshABK0zbwZhyLhCfoaaTqlbpwBMeEO3CM0yuJVNqx3SrSmuFzho_NvWY_t3vPU2Y09bFHmub_rqxxkju8WZ0x5WXD7iSeOY58F4K41DyBf6bgdSejo8FOpq9VqYQ7HWarm1aIHa1uFUeHVJQHy9dwHoENUrRW0XclmQlmthkVrc9lBu80PNVAMtFDP2OTCIHpFgy5CMABT1E1GqEXh7i48fAexXWEhBuLnu_AwdZnuczTN7jufGnTNn_XOkQ9IFsbJiBiLQfsim05aaTQXNpuupKJv_DJBt-kkufKpCi3LWOLOawPXH9st0CcMxlUCXaY1AapX0fq7-0wr2BedpKKJl3dAfNR7UdF_xGy0F9CKi8TuoPChqQumG7LwtlRc38gRArhJTnbjexr3bXmwW-N4JtxgWBG8aobW6fHRQA22iCdoGVP_YJ3WrfDeshahBfXz0wCxIx1vkUjjd_ot0U';
    public $test_cart = [];

    public function testStore_succes()
    {
        $this->test_cart  = [
            'product_id' => '1',
            'quantity' => '1',
            'discount' => '10'
        ];
        $response = $this->json('POST','/api/v1/cart',$this->test_cart , ['Accept' => 'application/json',  'HTTP_Authorization' => 'Bearer ' . $this->token]);
        $response
            ->assertStatus(201)
            ->assertJsonPath('message', 'Product add succesfully');
    }

    public function testStore_fail()
    {
        $this->test_cart  = [
            'product_id' => '1',
            'quantity' => '1d',
            'discount' => '10'
        ];
        $response = $this->json('POST','/api/v1/cart',$this->test_cart , ['Accept' => 'application/json',  'HTTP_Authorization' => 'Bearer ' . $this->token]);
        $response
            ->assertStatus(400)
            ->assertJsonPath('message', 'Validation errors')
            ->assertJsonPath('data.quantity.0', 'The quantity must be a number.');
    }

    public function testUpdate_fail()
    {
        $this->test_cart  = [
            'operation' => '0'
        ];
        $response = $this->json('PUT','/api/v1/cart/91',$this->test_cart , ['Accept' => 'application/json',  'HTTP_Authorization' => 'Bearer ' . $this->token]);
        $response
            ->assertStatus(404)
            ->assertJsonPath('err', 'ProductCart not found');
    }

    public function testUpdate_succes()
    {
        $this->test_cart  = [
            'operation' => '1'
        ];
        $response = $this->json('PUT','/api/v1/cart/1',$this->test_cart , ['Accept' => 'application/json',  'HTTP_Authorization' => 'Bearer ' . $this->token]);
        $response
            ->assertStatus(200)
            ->assertJsonPath('message', 'ProductCart Add');
    }

    public function testDestroy_fail()
    {

        $response = $this->json('DELETE','/api/v1/cart/91',$this->test_cart , ['Accept' => 'application/json',  'HTTP_Authorization' => 'Bearer ' . $this->token]);
        $response
            ->assertStatus(404)
            ->assertJsonPath('err', 'Product from cart not found');
    }
    public function testDestroy_succes()
    {
        $response = $this->json('DELETE','/api/v1/cart/1',$this->test_cart , ['Accept' => 'application/json',  'HTTP_Authorization' => 'Bearer ' . $this->token]);
        $response
            ->assertStatus(202)
            ->assertJsonPath('res', 'Deleted Product from cart');
    }


}
