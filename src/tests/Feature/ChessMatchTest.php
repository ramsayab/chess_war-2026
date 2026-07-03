<?php

use App\Models\User;
use App\Models\ChessMatch;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('guest cannot post matches', function () {
    $response = $this->postJson('/matches', [
        'is_win' => true,
        'total_time' => 120,
        'power_type' => 'omni_queen',
    ]);
    
    $response->assertStatus(401);
});

test('authenticated user can save match history', function () {
    $user = User::factory()->create([
        'name' => 'John Doe',
        'email' => 'john@doe.com',
    ]);
    
    $response = $this->actingAs($user)->postJson('/matches', [
        'is_win' => true,
        'total_time' => 240,
        'power_type' => 'blink_knight',
    ]);
    
    $response->assertStatus(200);
    $response->assertJson([
        'success' => true,
    ]);
    
    $this->assertDatabaseHas('matches', [
        'user_id' => $user->id,
        'is_win' => true,
        'total_time' => 240,
        'power_type' => 'blink_knight',
    ]);
});
