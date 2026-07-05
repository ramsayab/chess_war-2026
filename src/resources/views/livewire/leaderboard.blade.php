<section class="history-section leaderboard-section animate animate-2">
  <div class="history-header" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem; margin-bottom: 2rem;">
    <div>
      <h2 style="margin: 0;">Top Commanders</h2>
      <p style="margin: 0.25rem 0 0 0; color: #9ca3af;">Top rankings of Chess War players based on total wins.</p>
    </div>
    <div style="position: relative;">
      <input type="text" wire:model.live.debounce.250ms="search" placeholder="Search commander..." class="ai-dialog-select" style="padding: 0.5rem 1rem; font-size: 0.85rem; width: 220px; border-radius: 8px; background: #0d0d0d; border: 1px solid rgba(255, 255, 255, 0.15); color: white; outline: none;" />
    </div>
  </div>

  <div class="table-container">
    <table class="history-table leaderboard-table">
      <thead>
        <tr>
          <th>Rank</th>
          <th>Player</th>
          <th>Matches</th>
          <th>Wins</th>
          <th>Win Rate</th>
        </tr>
      </thead>
      <tbody>
        @forelse($leaderboard as $player)
          @php
            $isCurrentUser = $player->id === auth()->id();
            $rankBadge = '';
            if ($player->rank === 1) $rankBadge = '🥇';
            elseif ($player->rank === 2) $rankBadge = '🥈';
            elseif ($player->rank === 3) $rankBadge = '🥉';
          @endphp
          <tr class="{{ $isCurrentUser ? 'current-user-row' : '' }} {{ $player->rank <= 3 ? 'top-three-row' : '' }}">
            <td>
              <span class="rank-badge rank-{{ $player->rank }}">
                {!! $rankBadge ?: $player->rank !!}
              </span>
            </td>
            <td>
              <strong>{{ $player->name }}</strong>
              @if($isCurrentUser)
                <span class="you-badge">(You)</span>
              @endif
            </td>
            <td>{{ $player->total_matches }}</td>
            <td>{{ $player->won_matches }}</td>
            <td style="color: var(--gold-lt); font-weight: 500;">{{ $player->winrate }}%</td>
          </tr>
        @empty
          <tr>
            <td colspan="5" style="text-align: center; color: #9ca3af; padding: 2rem;">No commanders found matching your search.</td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>
</section>
