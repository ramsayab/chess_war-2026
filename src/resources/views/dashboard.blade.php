<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard — Chess War</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@300;400;500;600;700&family=Jost:wght@300;400;500&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="css/style.css?v={{ time() }}">
  <link rel="stylesheet" href="css/dashboard.css?v={{ time() }}">
</head>
<body>
  @php
    $user = auth()->user();
    $displayName = $user?->name ?: ($user?->username ?: 'Player');
    $initial = strtoupper(substr($displayName, 0, 1));
  @endphp

  <nav class="dash-nav">
    <a class="dash-logo" href="/dashboard">Chess War</a>
    <div class="dash-nav-right">
      @if($user?->is_admin || $user?->hasRole('super_admin'))
        <a class="admin-panel-link" href="/admin" style="color: var(--gold-lt); text-decoration: none; font-size: 0.78rem; letter-spacing: 0.12em; text-transform: uppercase; margin-right: 1.5rem; font-weight: 500; border-bottom: 1px dashed var(--gold);">Admin Panel</a>
      @endif
      <div class="user-badge">
        <div class="user-avatar">{{ $initial }}</div>
        <span>{{ $displayName }}</span>
      </div>
      <form action="/logout" method="POST">
        @csrf
        <button class="logout-link" type="submit">Log out</button>
      </form>
    </div>
  </nav>

  <div class="dash-wrapper">
    <div class="dash-container">
      
      <!-- SIDEBAR MENU -->
      <aside class="dash-sidebar animate animate-1">
        <ul class="sidebar-menu">
          <li>
            <a href="/dashboard?tab=overview" class="sidebar-link {{ $tab === 'overview' ? 'active' : '' }}">
              <svg class="sidebar-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <rect x="3" y="3" width="7" height="9" />
                <rect x="14" y="3" width="7" height="5" />
                <rect x="14" y="12" width="7" height="9" />
                <rect x="3" y="16" width="7" height="5" />
              </svg>
              <span>Dashboard</span>
            </a>
          </li>
          <li>
            <a href="/dashboard?tab=history" class="sidebar-link {{ $tab === 'history' ? 'active' : '' }}">
              <svg class="sidebar-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="12" cy="12" r="10" />
                <polyline points="12 6 12 12 16 14" />
              </svg>
              <span>History</span>
            </a>
          </li>
          <li>
            <a href="/dashboard?tab=leaderboard" class="sidebar-link {{ $tab === 'leaderboard' ? 'active' : '' }}">
              <svg class="sidebar-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-dasharray="" stroke-dashoffset="" stroke-width="2">
                <path d="M6 9H4.5a2.5 2.5 0 0 1 0-5H6" />
                <path d="M18 9h1.5a2.5 2.5 0 0 0 0-5H18" />
                <path d="M4 22h16" />
                <path d="M10 14.66V17c0 .55-.45 1-1 1H4v2h16v-2h-5c-.55 0-1-.45-1-1v-2.34" />
                <path d="M12 2a5 5 0 0 1 5 5v2a5 5 0 0 1-10 0V7a5 5 0 0 1 5-5z" fill="none" stroke="currentColor" />
              </svg>
              <span>Leaderboard</span>
            </a>
          </li>
        </ul>
      </aside>

      <!-- MAIN CONTENT -->
      <main class="dash-content">
        @if($tab === 'overview')
          <!-- TAB 1: OVERVIEW & STATISTICS -->
          <section class="dash-hero animate animate-2">
            <p class="dash-kicker">Welcome Back</p>
            <h1>Welcome, {{ $displayName }}</h1>
            <p>Ready for your next match? Jump in, draft your mystery power, and claim victory. You have solved <strong>{{ $puzzlesSolved }} / {{ $puzzlesTotal }}</strong> puzzles.</p>
            <div style="display: flex; gap: 1rem; justify-content: flex-start; margin-top: 1.5rem; flex-wrap: wrap;">
              <a class="play-now-btn" href="/game">Play Now</a>
              <a class="play-now-btn" href="/puzzle" style="background: transparent; color: var(--gold); border: 1px solid var(--gold); box-shadow: none;">Play Puzzles</a>
              @if(isset($savedGame) && $savedGame)
                <a class="play-now-btn" href="/game?resume=true" style="background: transparent; color: var(--gold-lt); border: 1px solid var(--border); box-shadow: none;">Resume Game</a>
              @endif
            </div>
          </section>

          <section class="kpi-grid animate animate-3">
            <div class="kpi-card">
              <span class="kpi-label">Win Rate</span>
              <span class="kpi-value">{{ $winrate }}%</span>
            </div>
            <div class="kpi-card">
              <span class="kpi-label">Avg Duration</span>
              <span class="kpi-value">{{ $avgMinutes }}m</span>
            </div>
            <div class="kpi-card">
              <span class="kpi-label">Puzzles Solved</span>
              <span class="kpi-value">{{ $puzzlesSolved }}/{{ $puzzlesTotal }}</span>
            </div>
          </section>

          <!-- Powers Reference Panel -->
          <section class="overview-section animate animate-4" style="margin-top: 2.5rem; width: 100%;">
            <div class="section-header">
              <h2>Master the Privileges</h2>
              <p>Unlock the secrets of the chess war custom powers. Every card turns standard rules upside down.</p>
            </div>
            
            <div class="powers-guide-grid">
              <div class="guide-card">
                <div class="guide-card-icon">♟</div>
                <h4>Confused Pawn</h4>
                <p>Can move 1 step forward or backward. Opponent pawns stay normal.</p>
              </div>
              <div class="guide-card">
                <div class="guide-card-icon">♞</div>
                <h4>Blink Knight</h4>
                <p>Can make standard jumps or double-length leaps to control the board.</p>
              </div>
              <div class="guide-card">
                <div class="guide-card-icon">♝</div>
                <h4>Grey Bishop</h4>
                <p>Can slide sideways by 1 square before sliding diagonally from its new path.</p>
              </div>
              <div class="guide-card">
                <div class="guide-card-icon">♜</div>
                <h4>Super Rook</h4>
                <p>Can move vertically/horizontally, and slide diagonally forward to attack.</p>
              </div>
              <div class="guide-card">
                <div class="guide-card-icon">♛</div>
                <h4>Omni Queen</h4>
                <p>Holds the combined movement of a Queen and a Knight jump.</p>
              </div>
              <div class="guide-card">
                <div class="guide-card-icon">♚</div>
                <h4>Undying King</h4>
                <p>Has 2 lives. The first capture will destroy the attacker and revive the King.</p>
              </div>
            </div>
          </section>

        @elseif($tab === 'history')
          <!-- TAB 2: GAME HISTORY -->
          <section class="history-section animate animate-2">
            <div class="history-header">
              <h2>Match History & Statistics</h2>
              <p>Track your performance metrics, power usage, and historical matches.</p>
            </div>

            <!-- Detailed Statistics Grid -->
            <section class="stats-grid animate animate-3 mb-4" style="margin-bottom: 2.5rem;">
              <!-- Winrate Widget -->
              <div class="stats-card winrate-card">
                <div class="card-header">
                  <h3>Win Rate</h3>
                </div>
                <div class="winrate-visual">
                  <svg viewBox="0 0 36 36" class="circular-chart">
                    <path class="circle-bg"
                      d="M18 2.0845
                        a 15.9155 15.9155 0 0 1 0 31.831
                        a 15.9155 15.9155 0 0 1 0 -31.831"
                    />
                    <path class="circle"
                      stroke-dasharray="{{ $winrate }}, 100"
                      d="M18 2.0845
                        a 15.9155 15.9155 0 0 1 0 31.831
                        a 15.9155 15.9155 0 0 1 0 -31.831"
                    />
                    <text x="18" y="20.35" class="percentage">{{ $winrate }}%</text>
                  </svg>
                </div>
                <div class="card-footer">
                  <p>Won <strong>{{ $wonMatches }}</strong> out of <strong>{{ $totalMatches }}</strong> matches</p>
                </div>
              </div>

              <!-- Average Duration Widget -->
              <div class="stats-card duration-card">
                <div class="card-header">
                  <h3>Average Duration</h3>
                </div>
                <div class="duration-display">
                  <div class="clock-icon-wrapper">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                      <circle cx="12" cy="12" r="10" />
                      <polyline points="12 6 12 12 16 14" />
                    </svg>
                  </div>
                  <div class="duration-value">
                    <span class="number">{{ $avgMinutes }}</span>
                    <span class="unit">mins</span>
                  </div>
                </div>
                <div class="card-footer">
                  <p>Average time per chess match</p>
                </div>
              </div>

              <!-- Power Usage Stats Widget -->
              <div class="stats-card powers-card">
                <div class="card-header">
                  <h3>Power Usage</h3>
                </div>
                <div class="powers-usage-list">
                  @php
                    $powers = [
                      'blink_knight' => 'Blink Knight',
                      'super_rook' => 'Super Rook',
                      'confused_pawn' => 'Confused Pawn',
                      'undying_king' => 'Undying King',
                      'omni_queen' => 'Omni Queen',
                      'grey_bishop' => 'Grey Bishop'
                    ];
                    $totalPowersUsed = array_sum($powerCounts);
                  @endphp
                  @foreach($powers as $key => $name)
                    @php
                      $count = $powerCounts[$key] ?? 0;
                      $percent = $totalPowersUsed > 0 ? round(($count / $totalPowersUsed) * 100) : 0;
                    @endphp
                    <div class="power-stat-item">
                      <div class="power-stat-info">
                        <span class="power-stat-name">{{ $name }}</span>
                        <span class="power-stat-count">{{ $count }}x ({{ $percent }}%)</span>
                      </div>
                      <div class="power-stat-bar-bg">
                        <div class="power-stat-bar" style="width: {{ $percent }}%"></div>
                      </div>
                    </div>
                  @endforeach
                  
                  @php
                    $noneCount = ($powerCounts[''] ?? 0) + ($powerCounts[null] ?? 0);
                    $nonePercent = $totalPowersUsed > 0 ? round(($noneCount / $totalPowersUsed) * 100) : 0;
                  @endphp
                  @if($noneCount > 0)
                    <div class="power-stat-item">
                      <div class="power-stat-info">
                        <span class="power-stat-name">No Power / Standard</span>
                        <span class="power-stat-count">{{ $noneCount }}x ({{ $nonePercent }}%)</span>
                      </div>
                      <div class="power-stat-bar-bg">
                        <div class="power-stat-bar" style="width: {{ $nonePercent }}%; background: #6b6355;"></div>
                      </div>
                    </div>
                  @endif
                </div>
              </div>
            </section>

            @if($matches->isEmpty())
              <div class="empty-history">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                  <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" />
                  <polyline points="14 2 14 8 20 8" />
                  <line x1="16" y1="13" x2="8" y2="13" />
                  <line x1="16" y1="17" x2="8" y2="17" />
                  <polyline points="10 9 9 9 8 9" />
                </svg>
                <p>You haven't played any matches yet.</p>
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
                      @endphp
                      <tr>
                        <td>{{ $matches->count() - $index }}</td>
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
                        <td class="duration-cell">{{ $minutes }}m {{ $seconds }}s</td>
                        <td class="date-cell">{{ $match->created_at->format('d M Y, H:i') }}</td>
                      </tr>
                    @endforeach
                  </tbody>
                </table>
            @endif
          </section>

        @elseif($tab === 'leaderboard')
          <!-- TAB 3: LEADERBOARD -->
          <section class="history-section leaderboard-section animate animate-2">
            <div class="history-header">
              <h2>Top Commanders</h2>
              <p>Top rankings of Chess War players based on total wins.</p>
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
                  @foreach($leaderboard as $player)
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
                  @endforeach
                </tbody>
              </table>
            </div>
          </section>
        @endif
      </main>
      
    </div>
  </div>

  <script src="js/main.js"></script>
</body>
</html>
