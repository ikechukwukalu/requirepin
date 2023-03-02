<?php

namespace Ikechukwukalu\Requirepin\Tests;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Ikechukwukalu\Requirepin\Models\TestUser;
use Ikechukwukalu\Requirepin\Notifications\PinChange;

class NotificationsTest extends TestCase
{
    use RefreshDatabase;

    public function test_fires_pin_change_notification(): void
    {
        Notification::fake();

        Notification::assertNothingSent();

        $user = TestUser::create([
            'name' => str::random(),
            'email' => Str::random(40) . '@example.com',
            'password' => Hash::make('password'),
            'pin' => Hash::make('0000'),
        ]);

        $this->actingAs($user);
        $user->notify(new PinChange());

        Notification::assertSentTo(
            [$user], PinChange::class
        );

        Notification::assertCount(1);
    }
}
