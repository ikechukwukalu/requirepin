<?php

namespace Ikechukwukalu\Requirepin\Tests;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Route;
use Ikechukwukalu\Requirepin\Models\Book;
use Ikechukwukalu\Requirepin\Models\TestUser;

class PinTest extends TestCase
{
    use WithFaker;

   /**
     * A basic feature test example.
     *
     * @return void
     */

    public function testErrorValidationForChangePin()
    {
        Notification::fake();

        $user = TestUser::create([
            'name' => str::random(),
            'email' => Str::random(40) . '@example.com',
            'password' => Hash::make('password'),
            'pin' => Hash::make(config('requirepin.default', '0000'))
        ]); // Would still have the default pin

        $this->actingAs($user);

        $postData = [
            'current_pin' => '9090', //Wrong current pin
            'pin' => '1uu4', //Wrong pin format
            'pin_confirmation' => '1234' //None matching pins
        ];

        $response = $this->post('/test/change/pin', $postData);
        $response->assertStatus(302);
    }

    public function testChangePin()
    {
        $user = TestUser::create([
            'name' => str::random(),
            'email' => Str::random(40) . '@example.com',
            'password' => Hash::make('password'),
            'pin' => Hash::make(config('requirepin.default', '0000')),
        ]);

        $this->actingAs($user);

        $postData = [
            'current_pin' => config('requirepin.default', '0000'),
            'pin' => '1234',
            'pin_confirmation' => '1234'
        ];

        $this->assertTrue(Hash::check($postData['current_pin'], $user->pin));

        $response = $this->post('/test/change/pin', $postData, ['Accept' => 'application/json']);
        $responseArray = json_decode($response->getContent(), true);

        $this->assertEquals(200, $responseArray['status_code']);
        $this->assertEquals( 'success', $responseArray['status']);
    }

    public function testRequirePinMiddleWareForCreateBook()
    {
        $user = TestUser::create([
            'name' => str::random(),
            'email' => Str::random(40) . '@example.com',
            'password' => Hash::make('password'),
            'pin' => Hash::make('1234'),
            'default_pin' => 0
        ]);

        $this->actingAs($user);

        $this->assertTrue(Hash::check('1234', $user->pin));

        $postData = [
            'name' => $this->faker->sentence(rand(1,5)),
            'isbn' => $this->faker->unique()->isbn13(),
            'authors' => implode(",", [$this->faker->name(), $this->faker->name()]),
            'publisher' => $this->faker->name(),
            'number_of_pages' => rand(45,1500),
            'country' => $this->faker->countryISOAlpha3(),
            'release_date' => date('Y-m-d')
        ];

        $response = $this->post(route('createBookTest'), $postData, ['Accept' => 'application/json']);
        $responseArray = json_decode($response->getContent(), true);

        $this->assertEquals(200, $responseArray['status_code']);
        $this->assertEquals('success', $responseArray['status']);
        $this->assertTrue(isset($responseArray['data']['url']));

        $postData = [
            config('requirepin.input', '_pin') => '1234'
        ];
        $url = $responseArray['data']['url'];

        $response = $this->post($url, $postData, ['Accept' => 'application/json']);
        $responseArray = json_decode($response->getContent(), true);

        $this->assertEquals(200, $responseArray['status_code']);
        $this->assertEquals('success', $responseArray['status']);
    }

    public function testRequirePinMiddleWareForDeleteBook()
    {
        $user = TestUser::create([
            'name' => str::random(),
            'email' => Str::random(40) . '@example.com',
            'password' => Hash::make('password'),
            'pin' => Hash::make('1234'),
            'default_pin' => 0
        ]);

        $this->actingAs($user);

        $this->assertTrue(Hash::check('1234', $user->pin));

        if (Route::has('deleteBookTest')) {
            $book = Book::find(1);

            if (!isset($book->id)) {
                $book = Book::create([
                    'name' => $this->faker->sentence(rand(1,5)),
                    'isbn' => $this->faker->unique()->isbn13(),
                    'authors' => implode(",", [$this->faker->name(), $this->faker->name()]),
                    'publisher' => $this->faker->name(),
                    'number_of_pages' => rand(45,1500),
                    'country' => $this->faker->countryISOAlpha3(),
                    'release_date' => date('Y-m-d')
                ]);
            }

            $id = $book->id;

            $response = $this->json('DELETE', route('deleteBookTest', ['id' => $id]), ['Accept' => 'application/json']);
            $responseArray = json_decode($response->getContent(), true);

            $this->assertEquals(200, $responseArray['status_code']);
            $this->assertEquals('success', $responseArray['status']);
            $this->assertTrue(isset($responseArray['data']['url']));

            $postData = [
                config('requirepin.input', '_pin') => '1234'
            ];
            $url = $responseArray['data']['url'];

            $response = $this->post($url, $postData, ['Accept' => 'application/json']);
            $responseArray = json_decode($response->getContent(), true);

            $this->assertEquals(200, $responseArray['status_code']);
            $this->assertEquals('success', $responseArray['status']);

        } else {
            $this->assertTrue(true);
        }

    }
}
