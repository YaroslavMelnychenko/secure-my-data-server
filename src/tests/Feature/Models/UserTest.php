<?php

namespace Tests\Feature\Models;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Event;
use Illuminate\Database\Eloquent\Model;
use Tests\TestCase;

use App\Models\User;
use App\Models\Encryption\Asymmetric;
use App\Http\Requests\AuthRegisterRequest;
use App\Helpers;

use App\Events\UserRegistered;

class UserTest extends TestCase
{
    use WithFaker;

    /**
     * Test user insertion
     *
     * @return void
     */
    public function testUserCreateTestAndKeyPairChallengeTest()
    {
        $request = new AuthRegisterRequest();

        $request->replace([
            'email' => $this->faker->safeEmail(),
            'password' => Str::random(10),
            'seed' => Helpers::randomHex(256)
        ]);

        $keyPair = Asymmetric::createKeyPair($request->seed);

        $initialEvent = Event::getFacadeRoot();
        Event::fake();
        Model::setEventDispatcher($initialEvent);

        $user = User::create($request, $keyPair);

        Event::assertDispatched(UserRegistered::class, function ($e) use ($user) {
            return $e->user->id === $user->id;
        });

        $this->assertDatabaseHas('users', [
            'id' => $user->id
        ]);

        $this->assertTrue($user->asymmetricChallenge($keyPair));
    }
}
