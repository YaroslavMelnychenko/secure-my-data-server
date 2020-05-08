<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

use Illuminate\Support\Facades\Mail;
use App\Mail\TestMail;

class EmailServiceTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testSendTestMail()
    {
        Mail::fake();

        Mail::send(new TestMail());

        Mail::assertSent(TestMail::class);   
    }
}
