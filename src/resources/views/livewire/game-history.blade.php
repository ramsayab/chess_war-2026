<div>
  @if(session()->has('message'))
    <div style="background: rgba(16, 185, 129, 0.15); border: 1px solid rgba(16, 185, 129, 0.3); color: #34d399; padding: 0.75rem 1rem; border-radius: 8px; margin-bottom: 1.5rem; font-size: 0.9rem;">
      {{ session('message') }}
    </div>
  @endif

  @if($savedGame)
    <!-- Active Saved Game Card -->
    <div style="background: rgba(201, 168, 76, 0.04); border: 1px dashed rgba(201, 168, 76, 0.3); border-radius: 12px; padding: 1.5rem; margin-bottom: 2rem; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
      <div>
        <h4 style="margin: 0; color: var(--gold-lt); font-size: 1.1rem; font-weight: 600; display: flex; align-items: center; gap: 0.5rem;">
          <span>⚔️</span> Active Game in Progress
        </h4>
        @php
          $powersMap = [
            'blink_knight' => 'Blink Knight',
            'super_rook' => 'Super Rook',
            'confused_pawn' => 'Confused Pawn',
            'undying_king' => 'Undying King',
            'omni_queen' => 'Omni Queen',
            'grey_bishop' => 'Grey Bishop',
          ];
          $powerName = $powersMap[$savedGame->power_type] ?? 'None';
          $difficultyNamesMap = [
            100 => 'Beginner',
            500 => 'Intermediate',
            1000 => 'Professional',
            2500 => 'Master',
            5000 => 'Grandmaster'
          ];
          $diffName = $difficultyNamesMap[(int)$savedGame->difficulty] ?? 'Professional';
        @endphp
        <p style="margin: 0.5rem 0 0 0; font-size: 0.88rem; color: #9ca3af;">
          Power: <span class="power-badge {{ $savedGame->power_type ?: 'no-power' }}" style="font-size: 0.75rem;">{{ $powerName }}</span> | Difficulty: <span style="color: var(--gold-lt);">{{ $diffName }}</span>
        </p>
      </div>
      <div style="display: flex; gap: 0.75rem;">
        <a href="/game?resume=true" class="play-now-btn" style="padding: 0.5rem 1rem; font-size: 0.8rem; box-shadow: none;">Resume Match</a>
        <button wire:click="deleteSavedGame" wire:confirm="Are you sure you want to discard this saved game progress?" class="play-now-btn" style="background: transparent; color: #ef4444; border: 1px solid rgba(239, 68, 68, 0.3); box-shadow: none; padding: 0.5rem 1rem; font-size: 0.8rem; cursor: pointer;">
          Discard Save
        </button>
      </div>
    </div>
  @endif

  @if($matches->isEmpty())
    <div class="empty-history" style="text-align: center; padding: 3rem 1.5rem; background: #111; border-radius: 12px; border: 1px solid var(--border);">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" style="width: 48px; height: 48px; color: #4b5563; margin-bottom: 1rem;">
        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" />
        <polyline points="14 2 14 8 20 8" />
        <line x1="16" y1="13" x2="8" y2="13" />
        <line x1="16" y1="17" x2="8" y2="17" />
      </svg>
      <p style="color: #9ca3af; margin-bottom: 1.5rem;">You haven't played any matches yet.</p>
      <a class="play-now-btn btn-small" href="/game">Start Match</a>
    </div>
  @else
    <div class="table-container">
      <table class="history-table">
        <thead>
          <tr>
            <th>No.</th>
            <th>Result</th>
            <th>Drafted Power</th>
            <th>Difficulty</th>
            <th>Duration</th>
            <th>Played At</th>
          </tr>
        </thead>
        <tbody>
          @foreach($matches as $index => $match)
            @php
              $minutes = floor($match->total_time / 60);
              $seconds = $match->total_time % 60;
              
              $powersMap = [
                'blink_knight' => 'Blink Knight',
                'super_rook' => 'Super Rook',
                'confused_pawn' => 'Confused Pawn',
                'undying_king' => 'Undying King',
                'omni_queen' => 'Omni Queen',
                'grey_bishop' => 'Grey Bishop',
              ];
              $powerName = $powersMap[$match->power_type] ?? 'None';
              $difficultyNamesMap = [
                100 => 'Beginner',
                500 => 'Intermediate',
                1000 => 'Professional',
                2500 => 'Master',
                5000 => 'Grandmaster'
              ];
              $diffName = $difficultyNamesMap[(int)$match->difficulty] ?? 'Professional';
              
              $rowNumber = $matches->total() - (($matches->currentPage() - 1) * $matches->perPage()) - $index;
            @endphp
            <tr>
              <td>{{ $rowNumber }}</td>
              <td>
                @if($match->is_win)
                  <span class="badge badge-win">Victory</span>
                @else
                  <span class="badge badge-loss">Defeat</span>
                @endif
              </td>
              <td>
                <span class="power-badge {{ $match->power_type ?: 'no-power' }}">
                  {{ $powerName }}
                </span>
              </td>
              <td>
                <span class="power-badge" style="background: rgba(201, 168, 76, 0.1); border: 1px solid rgba(201, 168, 76, 0.25); color: var(--gold-lt);">
                  {{ $diffName }}
                </span>
              </td>
              <td class="duration-cell">{{ $minutes }}m {{ $seconds }}s</td>
              <td class="date-cell">{{ $match->created_at->format('d M Y, H:i') }}</td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>

    <!-- Livewire Pagination Links -->
    <div style="margin-top: 1.5rem; display: flex; justify-content: center;">
      {{ $matches->links('livewire::simple-bootstrap') }}
    </div>
  @endif
</div>
