<?php
namespace App\Tests;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use App\Entity\Cart;
use Hautelook\AliceBundle\PhpUnit\RefreshDatabaseTrait;

class CartTest extends ApiTestCase
{
    // This trait provided by AliceBundle will take care of refreshing the database content to a known state before each test
    use RefreshDatabaseTrait;

    public function testGetCollection(): void
    {
        // The client implements Symfony HttpClient's `HttpClientInterface`, and the response `ResponseInterface`
        $response = static::createClient()->request('GET', 'api/cart');
        $this->assertResponseIsSuccessful();

        $data = $response->toArray();

        // Asserts that the returned content type is JSON-LD (the default)
        $this->assertResponseHeaderSame('content-type', 'application/json');

        $arr = [];
        foreach($data['data'] as $key => $val){
            $arr[] = array(
                'id' => $val['id'],
                'quantity' => $val['quantity'],
                'product' => array(
                        'id' =>  $val['product']['id'],
                        'title' =>  $val['product']['title'],
                        'image' =>  $val['product']['image'],
                        'price' =>  $val['product']['price'],
                        'description' =>  $val['product']['description'],
                        'created_at' =>  $val['product']['created_at'],
                        'updated_at' =>  $val['product']['updated_at'],
                    ),
                'created_at' => $val['created_at'],
                'updated_at' => $val['updated_at']
            );

            
        }

        // Asserts that the returned JSON is a superset of this one
        $this->assertJsonContains([
            'status' => true,
            'msg' => '',
            'data' => $arr,
        ]);

        // Asserts that the returned JSON is validated by the JSON Schema generated for this resource by API Platform
        // This generated JSON Schema is also used in the OpenAPI spec!
        $this->assertMatchesResourceItemJsonSchema(Cart::class, '', 'json');
    }

    public function testAddToCart(): void
    {
        $response = static::createClient()->request('POST', 'api/cart', ['json' => [
            'productId' => '3',
            'quantity' => '4',
        ]]);

        $res = $response->toArray();
       
        // Asserts that the returned JSON is a superset of this one
        $this->assertJsonContains([
            'status' => true,
            'msg' => '',
            'data' => [
                'id' => $res['data']['id'],
                'productId' => array(
                    'id' =>  $res['data']['productId']['id'],
                    'title' =>  $res['data']['productId']['title'],
                    'image' =>  $res['data']['productId']['image'],
                    'price' =>  $res['data']['productId']['price'],
                    'description' =>  $res['data']['productId']['description'],
                    'created_at' =>  $res['data']['productId']['created_at'],
                    'updated_at' =>  $res['data']['productId']['updated_at'],
                ),
                'quantity' => $res['data']['quantity'],
                'created_at' => $res['data']['created_at'],
                'created_at' => $res['data']['updated_at']
            ]
        ]);

        $this->assertResponseStatusCodeSame(200);
        $this->assertResponseHeaderSame('content-type', 'application/json');
    }

    public function testGetData(): void
    {
        // The client implements Symfony HttpClient's `HttpClientInterface`, and the response `ResponseInterface`
        $response = static::createClient()->request('GET', 'api/cart/1');
        $this->assertResponseIsSuccessful();

        $res = $response->toArray();
        
        // Asserts that the returned content type is JSON-LD (the default)
        $this->assertResponseHeaderSame('content-type', 'application/json');

         // Asserts that the returned JSON is a superset of this one
         $this->assertJsonContains([
            'status' => true,
            'msg' => '',
            'data' => [
                'id' => $res['data']['id'],
                'product' => array(
                    'id' =>  $res['data']['product']['id'],
                    'title' =>  $res['data']['product']['title'],
                    'image' =>  $res['data']['product']['image'],
                    'price' =>  $res['data']['product']['price'],
                    'description' =>  $res['data']['product']['description'],
                    'created_at' =>  $res['data']['product']['created_at'],
                    'updated_at' =>  $res['data']['product']['updated_at'],
                ),
                'quantity' => $res['data']['quantity'],
                'created_at' => $res['data']['created_at'],
                'created_at' => $res['data']['updated_at']
            ]
        ]);

        // Asserts that the returned JSON is validated by the JSON Schema generated for this resource by API Platform
        // This generated JSON Schema is also used in the OpenAPI spec!
        $this->assertMatchesResourceItemJsonSchema(Cart::class, '', 'json');
    }

    
    public function testUpdateCart(): void
    {
        $client = static::createClient();
        // findIriBy allows to retrieve the IRI of an item by searching for some of its properties.
        // ISBN 9786644879585 has been generated by Alice when loading test fixtures.
        // Because Alice use a seeded pseudo-random number generator, we're sure that this ISBN will always be generated.
        $iri = $this->findIriBy(Cart::class, ['id' => '1']);

        $response = $client->request('PUT', $iri, ['json' => [
            'quantity' => 1,
        ]]);

        // Asserts that the returned content type is JSON-LD (the default)
        $this->assertResponseHeaderSame('content-type', 'application/json');

        $res = $response->toArray();

        $this->assertResponseIsSuccessful();
        // Asserts that the returned JSON is a superset of this one
        $this->assertJsonContains([
            'status' => true,
            'msg' => '',
            'data' => [
                'id' => $res['data']['id'],
                'product' => array(
                    'id' =>  $res['data']['product']['id'],
                    'title' =>  $res['data']['product']['title'],
                    'image' =>  $res['data']['product']['image'],
                    'price' =>  $res['data']['product']['price'],
                    'description' =>  $res['data']['product']['description'],
                    'created_at' =>  $res['data']['product']['created_at'],
                    'updated_at' =>  $res['data']['product']['updated_at'],
                ),
                'quantity' => $res['data']['quantity'],
                'created_at' => $res['data']['created_at'],
                'updated_at' => $res['data']['updated_at']
            ]
        ]);
    }

  public function testDeleteCart(): void
    {
        $client = static::createClient();
        $iri = $this->findIriBy(Cart::class, ['id' => '1']);

        $client->request('DELETE', $iri);

        $this->assertResponseStatusCodeSame(200);
        $this->assertJsonContains(['status' => true, 'msg' => 'Deleted a Cart successfully with id 1', 'data' => []]);
        // $this->assertNull(
        //     // Through the container, you can access all your services from the tests, including the ORM, the mailer, remote API clients...
        //     static::getContainer()->get('doctrine')->getRepository(Cart::class)->findOneBy(['id' => '1'])
        // );
    }

}
