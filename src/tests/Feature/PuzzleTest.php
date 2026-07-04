<?php

use App\Models\User;
use App\Models\Puzzle;
use App\Models\PuzzleAttempt;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('guest cannot access puzzle page', function () {
    $response = $this->get('/puzzle');
    $response->assertRedirect('/login');
});

test('authenticated user can load puzzle page with dynamic database puzzles', function () {
    $user = User::factory()->create();
    
    // Seed some puzzles
    $puzzle1 = Puzzle::create([
        'name' => "Scholar's Mate",
        'difficulty' => 'easy',
        'diff_label' => 'Mate in 1',
        'fen' => 'r1bqkb1r/pppp1ppp/2n2n2/4p2Q/2B1P3/8/PPPP1PPP/RNB1K1NR w KQkq - 4 4',
        'description' => 'White to move. Deliver checkmate in 1.',
        'side_to_move' => 'white',
        'solution' => ['h5f7'],
        'moves_limit' => 1,
    ]);

    $response = $this->actingAs($user)->get('/puzzle');
    $response->assertStatus(200);
    $response->assertViewHas('puzzles');
    
    $puzzles = $response->viewData('puzzles');
    expect($puzzles)->toHaveCount(1);
    expect($puzzles->first()->name)->toEqual("Scholar's Mate");
});

test('user puzzle progress api', function () {
    $user = User::factory()->create();
    
    $puzzle = Puzzle::create([
        'name' => 'Back Rank Mate',
        'difficulty' => 'easy',
        'diff_label' => 'Mate in 1',
        'fen' => '6k1/5ppp/8/8/8/8/8/4R1K1 w - - 0 1',
        'description' => 'White to move. Back rank mate in 1.',
        'side_to_move' => 'white',
        'solution' => ['e1e8'],
        'moves_limit' => 1,
    ]);

    // Initial progress should be empty
    $response = $this->actingAs($user)->getJson('/api/puzzle/progress');
    $response->assertStatus(200)
        ->assertJson([
            'success' => true,
            'solved_puzzles' => [],
        ]);

    // Complete the puzzle
    $response = $this->actingAs($user)->postJson('/api/puzzle/complete', [
        'puzzle_id' => $puzzle->id,
        'attempts' => 2,
    ]);
    
    $response->assertStatus(200)
        ->assertJson([
            'success' => true,
        ]);

    $this->assertDatabaseHas('puzzle_attempts', [
        'user_id' => $user->id,
        'puzzle_id' => $puzzle->id,
        'solved' => true,
        'attempts' => 2,
    ]);

    // Progress should now have the completed puzzle ID
    $response = $this->actingAs($user)->getJson('/api/puzzle/progress');
    $response->assertStatus(200)
        ->assertJson([
            'success' => true,
            'solved_puzzles' => [(string)$puzzle->id],
        ]);
});
