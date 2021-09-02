<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function test_example()
    {
        $response = $this->get('/api/devnote/notes');

        $response->assertStatus(200);
    }

    public function test_example_post()
    {
        $response = $this->get('/api/devnote/notes');

        $response->assertStatus(200);
    }
}
