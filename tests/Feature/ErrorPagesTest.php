<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Route;
use RuntimeException;
use Tests\TestCase;

class ErrorPagesTest extends TestCase
{
    public function test_missing_page_uses_the_branded_404(): void
    {
        $this->get('/page-that-does-not-exist')->assertNotFound()
            ->assertSee('A little off course.')->assertSee('Back to home')
            ->assertSee('noindex, nofollow');
    }

    public function test_server_error_uses_the_branded_500_without_leaking_details(): void
    {
        config(['app.debug' => false]);
        Route::get('/test-server-error', function () {
            throw new RuntimeException('Private failure details');
        });

        $this->get('/test-server-error')->assertStatus(500)
            ->assertSee('A pause, not the end.')->assertSee('Back to home')
            ->assertDontSee('Private failure details');
    }
}
