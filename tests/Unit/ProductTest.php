<?php

namespace Tests\Unit;

use Tests\TestCase;

class ProductTest extends TestCase
{
    /**
     * A basic unit test example.
     *
     * @return void
     */
    public $token='eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJhdWQiOiIzIiwianRpIjoiOTViN2ZkZTBjNDcyMzBlMzhhZjUyZTRjNTkzZmVlMjAyNjNhMmZiZWQyODQ4YTcwMjQ2ZDI0ZTEwMGRiZjQzNGFlMTRiZmZkY2ExODNkMDciLCJpYXQiOjE2NDE4NDQwNzkuODAzMTYyLCJuYmYiOjE2NDE4NDQwNzkuODAzMTY3LCJleHAiOjE2NzMzODAwNzkuNzkyMDg3LCJzdWIiOiIyIiwic2NvcGVzIjpbXX0.qagpcVeJpM1Zf11QPuPSXk9TWnxLDj-X6QijTEeXI0lLK2BIzdM9z_nH2oaQUKHGpU_1zdyeON2vt4sw01l0JLSLM4BeMBxDkAs659IYL0IbniG95Ov-5d4EIZVXmh_eErbcmXTmjIluw9cb1SIKk2WhfSa9mkdSPLnkbhgrWGhrcpxX9UhF6rHG1hINBvaz2q2ScIyVOVSEVSPt20yKPEJshABK0zbwZhyLhCfoaaTqlbpwBMeEO3CM0yuJVNqx3SrSmuFzho_NvWY_t3vPU2Y09bFHmub_rqxxkju8WZ0x5WXD7iSeOY58F4K41DyBf6bgdSejo8FOpq9VqYQ7HWarm1aIHa1uFUeHVJQHy9dwHoENUrRW0XclmQlmthkVrc9lBu80PNVAMtFDP2OTCIHpFgy5CMABT1E1GqEXh7i48fAexXWEhBuLnu_AwdZnuczTN7jufGnTNn_XOkQ9IFsbJiBiLQfsim05aaTQXNpuupKJv_DJBt-kkufKpCi3LWOLOawPXH9st0CcMxlUCXaY1AapX0fq7-0wr2BedpKKJl3dAfNR7UdF_xGy0F9CKi8TuoPChqQumG7LwtlRc38gRArhJTnbjexr3bXmwW-N4JtxgWBG8aobW6fHRQA22iCdoGVP_YJ3WrfDeshahBfXz0wCxIx1vkUjjd_ot0U';
    public $test_product = [];

    public function testStore_fail()
    {
        $this->test_product = [
            'name' => 'computador',
            'price' => '150000'
        ];
        $response = $this->json('POST','/api/v1/product',$this->test_product , ['Accept' => 'application/json']);
        $response
            ->assertStatus(401)
            ->assertJsonPath('message', 'Unauthenticated.');
    }

    public function testStore_succes()
    {
        $this->test_product  = [
            'name' => 'computador',
            'price' => '150000'
        ];
        $response = $this->json('POST','/api/v1/product',$this->test_product , ['Accept' => 'application/json',  'HTTP_Authorization' => 'Bearer ' . $this->token]);
        $response
            ->assertStatus(201)
            ->assertJsonPath('message', 'Product create succesfully');
    }

    public function testUpdate_fail()
    {

        $this->test_product  = [
            'name' => 'computador',
            'price' => '15df0000'
        ];
        $response = $this->json('PUT','/api/v1/product/1',$this->test_product , ['Accept' => 'application/json',  'HTTP_Authorization' => 'Bearer ' . $this->token]);
        $response
            ->assertStatus(400)
            ->assertJsonPath('message', 'Validation errors');
    }

    public function testUpdate_succes()
    {

        $this->test_product  = [
            'name' => 'computador',
            'price' => '1000'
        ];
        $response = $this->json('PUT','/api/v1/product/1',$this->test_product , ['Accept' => 'application/json',  'HTTP_Authorization' => 'Bearer ' . $this->token]);
        $response
            ->assertStatus(200);
    }

    public function testDestroy_fail()
    {
        $response = $this->json('DELETE','/api/v1/product/111',$this->test_product , ['Accept' => 'application/json',  'HTTP_Authorization' => 'Bearer ' . $this->token]);
        $response
            ->assertStatus(404)
            ->assertJsonPath('err', 'Product not found');
    }

    public function testDestroy_succes()
    {
        $response = $this->json('DELETE','/api/v1/product/2',$this->test_product , ['Accept' => 'application/json',  'HTTP_Authorization' => 'Bearer ' . $this->token]);
        $response
            ->assertStatus(202)
            ->assertJsonPath('res', 'Deleted Product');
    }



}
