<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    public function test_root_redirects_guests_toward_login(): void
    {
        $this->get('/')
            ->assertRedirect('/plan');

        $this->get('/plan')
            ->assertRedirect(route('login'));
    }
}
