<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('puzzles', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('difficulty');
            $table->string('diff_label');
            $table->text('fen');
            $table->text('description');
            $table->string('side_to_move');
            $table->json('solution');
            $table->integer('moves_limit');
            $table->timestamps();
        });

        // Update puzzle_attempts table
        Schema::table('puzzle_attempts', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
        });

        Schema::table('puzzle_attempts', function (Blueprint $table) {
            $table->dropUnique(['user_id', 'puzzle_id']);
            $table->dropColumn('puzzle_id');
        });

        Schema::table('puzzle_attempts', function (Blueprint $table) {
            $table->foreignId('puzzle_id')->after('user_id')->constrained('puzzles')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->unique(['user_id', 'puzzle_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('puzzle_attempts', function (Blueprint $table) {
            $table->dropUnique(['user_id', 'puzzle_id']);
            $table->dropForeign(['puzzle_id']);
            $table->dropForeign(['user_id']);
            $table->dropColumn('puzzle_id');
        });

        Schema::table('puzzle_attempts', function (Blueprint $table) {
            $table->string('puzzle_id')->after('user_id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->unique(['user_id', 'puzzle_id']);
        });

        Schema::dropIfExists('puzzles');
    }
};
