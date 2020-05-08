<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;

class UserTableTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testUserInsertionTest()
    {
        $user = factory(User::class)->make();
        $user->save();

        $this->uuidRegTest($user->id);
    }
}
