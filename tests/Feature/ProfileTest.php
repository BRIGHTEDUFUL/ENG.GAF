<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProfileTest extends TestCase
{
    use RefreshDatabase;

    public function test_profile_page_is_displayed(): void
    {
        $user = User::factory()->create();
        $response = $this->actingAs($user)->get('/profile');
        $response->assertStatus(200);
    }

    public function test_profile_information_can_be_updated(): void
    {
        $user = User::factory()->create();
        $response = $this->actingAs($user)->patch('/profile', [
            'name'  => 'Updated Name',
            'email' => 'updated@example.com',
        ]);
        $response->assertSessionHasNoErrors();
        $response->assertRedirect('/profile');
        $this->assertSame('Updated Name', $user->fresh()->name);
        $this->assertSame('updated@example.com', $user->fresh()->email);
    }

    public function test_email_verification_status_is_unchanged_when_the_email_address_is_unchanged(): void
    {
        $user = User::factory()->create();
        $response = $this->actingAs($user)->patch('/profile', [
            'name'  => $user->name,
            'email' => $user->email,
        ]);
        $response->assertSessionHasNoErrors();
        $this->assertNotNull($user->fresh()->email_verified_at);
    }

    public function test_user_can_delete_their_account(): void
    {
        $user = User::factory()->create();
        $response = $this->actingAs($user)->delete('/profile', [
            'password' => 'password',
        ]);
        $response->assertRedirect('/');
        $this->assertSoftDeleted($user);
    }

    public function test_correct_password_must_be_provided_to_delete_account(): void
    {
        $user = User::factory()->create();
        $response = $this->actingAs($user)->delete('/profile', [
            'password' => 'wrong-password',
        ]);
        // The destroy method uses validateWithBag('userDeletion', ...) so errors are in that bag
        $response->assertSessionHasErrorsIn('userDeletion', 'password');
        $this->assertNotNull($user->fresh());
    }
}
