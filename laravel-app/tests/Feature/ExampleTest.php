<?php

namespace Tests\Feature;

use Tests\TestCase;

class ExampleTest extends TestCase
{
    /**
     * Test that the root URL redirects to login when not authenticated.
     */
    public function test_the_application_redirects_to_login()
    {
        $response = $this->get('/');
        $response->assertStatus(302); // يجب أن يعيد التوجيه
        $response->assertRedirect('/login');
    }
}
