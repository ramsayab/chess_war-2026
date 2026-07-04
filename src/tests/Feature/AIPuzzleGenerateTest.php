<?php

use App\Models\User;
use App\Models\Puzzle;
use Illuminate\Support\Facades\Http;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('guest cannot access AI puzzle generate endpoint', function () {
    $response = $this->postJson('/api/puzzle/generate', [
        'difficulty' => 'easy',
        'moves_limit' => 1,
    ]);

    $response->assertStatus(401);
});

test('non-admin user cannot access AI puzzle generate endpoint', function () {
    $user = User::factory()->create(['is_admin' => false]);

    $response = $this->actingAs($user)->postJson('/api/puzzle/generate', [
        'difficulty' => 'easy',
        'moves_limit' => 1,
    ]);

    $response->assertStatus(403);
});

test('admin user gets 500 if GEMINI_API_KEY is missing', function () {
    $user = User::factory()->create(['is_admin' => true]);
    
    // Ensure API key is empty
    config(['services.gemini.key' => '']);

    $response = $this->actingAs($user)->postJson('/api/puzzle/generate', [
        'difficulty' => 'easy',
        'moves_limit' => 1,
    ]);

    $response->assertStatus(500);
    $response->assertJsonFragment([
        'success' => false,
        'message' => 'Gemini API key is not configured.',
    ]);
});

test('admin user validation errors', function () {
    $user = User::factory()->create(['is_admin' => true]);
    config(['services.gemini.key' => 'mocked_key']);

    $response = $this->actingAs($user)->postJson('/api/puzzle/generate', [
        'difficulty' => 'invalid_diff',
        'moves_limit' => 4,
    ]);

    $response->assertStatus(422);
});

test('admin user successfully generates AI puzzle', function () {
    $user = User::factory()->create(['is_admin' => true]);
    config(['services.gemini.key' => 'mocked_key']);

    $mockedResponseText = json_encode([
        'name' => "Mocked Scholar's Mate",
        'difficulty' => 'easy',
        'diff_label' => 'Mate in 1',
        'fen' => 'r1bqkb1r/pppp1ppp/2n2n2/4p2Q/2B1P3/8/PPPP1PPP/RNB1K1NR w KQkq - 4 4',
        'description' => 'White to move. Deliver checkmate in 1.',
        'side_to_move' => 'white',
        'solution' => ['h5f7'],
        'moves_limit' => 1
    ]);

    Http::fake([
        'generativelanguage.googleapis.com/*' => Http::response([
            'candidates' => [
                [
                    'content' => [
                        'parts' => [
                            [
                                'text' => $mockedResponseText
                            ]
                        ]
                    ]
                ]
            ]
        ], 200)
    ]);

    $response = $this->actingAs($user)->postJson('/api/puzzle/generate', [
        'difficulty' => 'easy',
        'moves_limit' => 1,
    ]);

    $response->assertStatus(200);
    $response->assertJson([
        'success' => true,
    ]);

    $this->assertDatabaseHas('puzzles', [
        'name' => "Mocked Scholar's Mate",
        'difficulty' => 'easy',
        'fen' => 'r1bqkb1r/pppp1ppp/2n2n2/4p2Q/2B1P3/8/PPPP1PPP/RNB1K1NR w KQkq - 4 4',
        'side_to_move' => 'white',
        'moves_limit' => 1,
    ]);
});
