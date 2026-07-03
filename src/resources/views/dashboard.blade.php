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

        <!-- Sidebar Widgets -->
        <div class="sidebar-widgets-container">
          <!-- Rank Widget -->
          <div class="sidebar-widget rank-widget">
            <span class="widget-label">Your Rank</span>
            <div class="rank-display">
              @if(is_numeric($myRank))
                <span class="rank-hash">#</span>
              @endif
              <span class="rank-number" style="{{ !is_numeric($myRank) ? 'font-size: 1.6rem;' : '' }}">{{ $myRank }}</span>
            </div>
          </div>

          <!-- XP Widget -->
          <div class="sidebar-widget xp-widget">
            <div class="xp-header">
              <span class="level-badge">Lv. {{ $level }}</span>
              <span class="xp-text">{{ $xpInCurrentLevel }}/{{ $nextLevelXp }} XP</span>
            </div>
            <div class="xp-bar-bg">
              <div class="xp-bar" style="width: {{ $xpProgressPercent }}%"></div>
            </div>
          </div>

          <!-- Daily Chess Tip Widget -->
          <div class="sidebar-widget tip-widget">
            <div class="tip-header">
              <svg class="tip-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M15 14c.2-1 .7-1.7 1.5-2.5 1-.9 1.5-2.2 1.5-3.5A5 5 0 0 0 8 8c0 1 .3 2.2 1.5 3.5.7.7 1.3 1.5 1.5 2.5" />
                <line x1="9" y1="18" x2="15" y2="18" />
                <line x1="10" y1="22" x2="14" y2="22" />
              </svg>
              <span>Chess Tip</span>
            </div>
            <p class="tip-text">"{{ $dailyTip->tip }}"</p>
            @if($dailyTip->author)
              <span class="tip-author">&mdash; {{ $dailyTip->author }}</span>
            @endif
          </div>
        </div>
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
            <!-- Win Rate Card -->
            <div class="kpi-card">
              <div class="kpi-card-header">
                <span class="kpi-label">Win Rate</span>
                <span class="kpi-card-icon">🏆</span>
              </div>
              <div class="kpi-card-body">
                <span class="kpi-value">{{ $winrate }}%</span>
                <div class="kpi-trend {{ $winrateDiff >= 0 ? 'trend-up' : 'trend-down' }}">
                  @if($winrateDiff >= 0)
                    <span class="trend-arrow">&uarr;</span> +{{ $winrateDiff }}% vs overall
                  @else
                    <span class="trend-arrow">&darr;</span> {{ $winrateDiff }}% vs overall
                  @endif
                </div>
              </div>
              <div class="kpi-chart-wrapper">
                <svg class="kpi-sparkline" viewBox="0 0 100 30" preserveAspectRatio="none">
                  @php
                    $path = '';
                    $count = count($winratePoints);
                    foreach ($winratePoints as $i => $val) {
                        $x = ($i / ($count - 1)) * 100;
                        $y = 25 - ($val * 0.2); // map 0-100 to 25-5 range
                        $path .= ($i === 0 ? 'M' : 'L') . " {$x},{$y}";
                    }
                  @endphp
                  <path d="{{ $path }}" fill="none" stroke="var(--gold)" stroke-width="1.5" />
                </svg>
              </div>
            </div>

            <!-- Avg Duration Card -->
            <div class="kpi-card">
              <div class="kpi-card-header">
                <span class="kpi-label">Avg Duration</span>
                <span class="kpi-card-icon">⏱</span>
              </div>
              <div class="kpi-card-body">
                <span class="kpi-value">{{ $avgMinutes }}m</span>
                <div class="kpi-trend {{ $durationDiff <= 0 ? 'trend-up' : 'trend-down' }}">
                  @if($durationDiff <= 0)
                    <span class="trend-arrow">&uarr;</span> {{ $durationDiff }}m vs overall
                  @else
                    <span class="trend-arrow">&darr;</span> +{{ $durationDiff }}m vs overall
                  @endif
                </div>
              </div>
              <div class="kpi-chart-wrapper">
                <svg class="kpi-sparkline" viewBox="0 0 100 30" preserveAspectRatio="none">
                  @php
                    $path = '';
                    $count = count($durationPoints);
                    $maxDuration = max($durationPoints) ?: 10;
                    $minDuration = min($durationPoints) ?: 0;
                    $diffD = ($maxDuration - $minDuration) ?: 1;
                    foreach ($durationPoints as $i => $val) {
                        $x = ($i / ($count - 1)) * 100;
                        $y = 25 - (($val - $minDuration) / $diffD) * 20; // map value to Y (5-25)
                        $path .= ($i === 0 ? 'M' : 'L') . " {$x},{$y}";
                    }
                  @endphp
                  <path d="{{ $path }}" fill="none" stroke="var(--gold)" stroke-width="1.5" />
                </svg>
              </div>
            </div>

            <!-- Puzzles Solved Card -->
            <div class="kpi-card">
              <div class="kpi-card-header">
                <span class="kpi-label">Puzzles Solved</span>
                <span class="kpi-card-icon">🧩</span>
              </div>
              <div class="kpi-card-body">
                <span class="kpi-value">{{ $puzzlesSolved }}/{{ $puzzlesTotal }}</span>
                <div class="kpi-trend {{ $solvedToday > 0 ? 'trend-up' : 'trend-neutral' }}">
                  @if($solvedToday > 0)
                    <span class="trend-arrow">&uarr;</span> +{{ $solvedToday }} solved today
                  @else
                    0 solved today
                  @endif
                </div>
              </div>
              <div class="kpi-chart-wrapper">
                <svg class="kpi-sparkline" viewBox="0 0 100 30" preserveAspectRatio="none">
                  @php
                    $path = '';
                    $count = count($puzzlePoints);
                    $maxPuzzle = max($puzzlePoints) ?: 1;
                    foreach ($puzzlePoints as $i => $val) {
                        $x = ($i / ($count - 1)) * 100;
                        $y = 25 - ($val / $maxPuzzle) * 20;
                        $path .= ($i === 0 ? 'M' : 'L') . " {$x},{$y}";
                    }
                  @endphp
                  <path d="{{ $path }}" fill="none" stroke="var(--gold)" stroke-width="1.5" />
                </svg>
              </div>
            </div>
          </section>

          <!-- Section Recent Activity -->
          <section class="overview-section animate animate-3-5" style="margin-top: 2rem; width: 100%;">
            <div class="section-header" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem;">
              <div>
                <h2>Recent Activity</h2>
                <p>Preview of your last 3 matches. Link to history for details.</p>
              </div>
              <a href="/dashboard?tab=history" class="play-now-btn btn-small" style="background: transparent; color: var(--gold-lt); border: 1px solid var(--border); box-shadow: none;">View Full History &rarr;</a>
            </div>

            @if($recentMatchesForPreview->isEmpty())
              <div class="empty-history" style="padding: 2rem; border: 1px dashed rgba(201, 168, 76, 0.2); border-radius: 6px;">
                <p style="margin: 0; color: var(--muted); font-size: 0.9rem;">No matches played yet. Start your first match!</p>
              </div>
            @else
              <div class="recent-matches-list" style="display: flex; flex-direction: column; gap: 0.85rem; margin-top: 1rem;">
                @foreach($recentMatchesForPreview as $match)
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
                  <div class="recent-match-card" style="display: flex; align-items: center; justify-content: space-between; padding: 1rem 1.25rem; background: rgba(26, 22, 18, 0.4); border: 1px solid rgba(201, 168, 76, 0.12); border-radius: 6px; transition: border-color 0.2s ease;">
                    <div style="display: flex; align-items: center; gap: 1.25rem;">
                      @if($match->is_win)
                        <span class="badge badge-win" style="min-width: 80px; text-align: center;">Victory</span>
                      @else
                        <span class="badge badge-loss" style="min-width: 80px; text-align: center;">Defeat</span>
                      @endif
                      <div>
                        <div style="font-size: 0.88rem; font-weight: 500; color: var(--ivory);">vs Wukong AI</div>
                        <div style="font-size: 0.76rem; color: var(--muted);">Played {{ $match->created_at->diffForHumans() }}</div>
                      </div>
                    </div>
                    
                    <div style="display: flex; align-items: center; gap: 2rem;">
                      <div>
                        <div style="font-size: 0.72rem; text-transform: uppercase; color: var(--muted); letter-spacing: 0.08em; margin-bottom: 0.15rem;">Power Drafted</div>
                        <span class="power-badge {{ $match->power_type ?: 'no-power' }}">{{ $powerName }}</span>
                      </div>
                      <div style="text-align: right; min-width: 70px;">
                        <div style="font-size: 0.72rem; text-transform: uppercase; color: var(--muted); letter-spacing: 0.08em; margin-bottom: 0.15rem;">Duration</div>
                        <div style="font-size: 0.85rem; color: var(--cream); font-weight: 500;">{{ $minutes }}m {{ $seconds }}s</div>
                      </div>
                    </div>
                  </div>
                @endforeach
              </div>
            @endif
          </section>

          <!-- Powers Reference Panel -->
          <section class="overview-section animate animate-4" style="margin-top: 2.5rem; width: 100%;">
            <div class="section-header">
              <h2>Master the Privileges</h2>
              <p>Unlock the secrets of the chess war custom powers. Every card turns standard rules upside down.</p>
            </div>
            
            <div class="powers-guide-grid">
              <!-- Confused Pawn -->
              <div class="guide-card">
                <div class="guide-card-top">
                  <div class="guide-card-icon">♟</div>
                  <div>
                    <h4>Confused Pawn</h4>
                    <p>Can move 1 step forward or backward. Opponent pawns stay normal.</p>
                  </div>
                </div>
                <div class="mini-board-wrapper">
                  <div class="mini-board">
                    @for($i = 0; $i < 25; $i++)
                      @php
                        $row = floor($i / 5);
                        $col = $i % 5;
                        $isLight = ($row + $col) % 2 === 0;
                        $isHighlight = in_array($i, [7, 17]);
                      @endphp
                      <div class="mini-square {{ $isLight ? 'light' : 'dark' }} {{ $isHighlight ? 'highlight' : '' }}">
                        @if($i === 12) <span class="mini-piece">♟</span> @endif
                      </div>
                    @endfor
                  </div>
                </div>
              </div>

              <!-- Blink Knight -->
              <div class="guide-card">
                <div class="guide-card-top">
                  <div class="guide-card-icon">♞</div>
                  <div>
                    <h4>Blink Knight</h4>
                    <p>Can make standard L-jumps or double-length leaps to control the board.</p>
                  </div>
                </div>
                <div class="mini-board-wrapper">
                  <div class="mini-board">
                    @for($i = 0; $i < 25; $i++)
                      @php
                        $row = floor($i / 5);
                        $col = $i % 5;
                        $isLight = ($row + $col) % 2 === 0;
                        $isHighlight = in_array($i, [1, 3, 5, 9, 15, 19, 21, 23, 0, 4, 20, 24]);
                      @endphp
                      <div class="mini-square {{ $isLight ? 'light' : 'dark' }} {{ $isHighlight ? 'highlight' : '' }}">
                        @if($i === 12) <span class="mini-piece">♞</span> @endif
                      </div>
                    @endfor
                  </div>
                </div>
              </div>

              <!-- Grey Bishop -->
              <div class="guide-card">
                <div class="guide-card-top">
                  <div class="guide-card-icon">♝</div>
                  <div>
                    <h4>Grey Bishop</h4>
                    <p>Can slide sideways by 1 square before sliding diagonally from its new path.</p>
                  </div>
                </div>
                <div class="mini-board-wrapper">
                  <div class="mini-board">
                    @for($i = 0; $i < 25; $i++)
                      @php
                        $row = floor($i / 5);
                        $col = $i % 5;
                        $isLight = ($row + $col) % 2 === 0;
                        $isHighlight = in_array($i, [0, 4, 6, 8, 11, 13, 16, 18, 20, 24]);
                      @endphp
                      <div class="mini-square {{ $isLight ? 'light' : 'dark' }} {{ $isHighlight ? 'highlight' : '' }}">
                        @if($i === 12) <span class="mini-piece">♝</span> @endif
                      </div>
                    @endfor
                  </div>
                </div>
              </div>

              <!-- Super Rook -->
              <div class="guide-card">
                <div class="guide-card-top">
                  <div class="guide-card-icon">♜</div>
                  <div>
                    <h4>Super Rook</h4>
                    <p>Can move vertically/horizontally, and slide diagonally forward to attack.</p>
                  </div>
                </div>
                <div class="mini-board-wrapper">
                  <div class="mini-board">
                    @for($i = 0; $i < 25; $i++)
                      @php
                        $row = floor($i / 5);
                        $col = $i % 5;
                        $isLight = ($row + $col) % 2 === 0;
                        $isHighlight = in_array($i, [2, 7, 10, 11, 13, 14, 17, 22, 0, 4, 6, 8]);
                      @endphp
                      <div class="mini-square {{ $isLight ? 'light' : 'dark' }} {{ $isHighlight ? 'highlight' : '' }}">
                        @if($i === 12) <span class="mini-piece">♜</span> @endif
                      </div>
                    @endfor
                  </div>
                </div>
              </div>

              <!-- Omni Queen -->
              <div class="guide-card">
                <div class="guide-card-top">
                  <div class="guide-card-icon">♛</div>
                  <div>
                    <h4>Omni Queen</h4>
                    <p>Holds the combined movement of a Queen and a Knight jump.</p>
                  </div>
                </div>
                <div class="mini-board-wrapper">
                  <div class="mini-board">
                    @for($i = 0; $i < 25; $i++)
                      @php
                        $row = floor($i / 5);
                        $col = $i % 5;
                        $isLight = ($row + $col) % 2 === 0;
                        $isHighlight = $i !== 12;
                      @endphp
                      <div class="mini-square {{ $isLight ? 'light' : 'dark' }} {{ $isHighlight ? 'highlight' : '' }}">
                        @if($i === 12) <span class="mini-piece">♛</span> @endif
                      </div>
                    @endfor
                  </div>
                </div>
              </div>

              <!-- Undying King -->
              <div class="guide-card">
                <div class="guide-card-top">
                  <div class="guide-card-icon">♚</div>
                  <div>
                    <h4>Undying King</h4>
                    <p>Has 2 lives. The first capture will destroy the attacker and revive the King.</p>
                  </div>
                </div>
                <div class="mini-board-wrapper">
                  <div class="mini-board undying-king-board">
                    @for($i = 0; $i < 25; $i++)
                      @php
                        $row = floor($i / 5);
                        $col = $i % 5;
                        $isLight = ($row + $col) % 2 === 0;
                        $isHighlight = in_array($i, [6, 7, 8, 11, 13, 16, 17, 18]);
                      @endphp
                      <div class="mini-square {{ $isLight ? 'light' : 'dark' }} {{ $isHighlight ? 'highlight' : '' }}">
                        @if($i === 12) <span class="mini-piece">♚</span> @endif
                      </div>
                    @endfor
                  </div>
                </div>
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

              <!-- Difficulty Distribution Widget -->
              <div class="stats-card difficulty-card">
                <div class="card-header">
                  <h3>Difficulty Distribution</h3>
                </div>
                <div class="powers-usage-list">
                  @php
                    $diffNames = [
                      100 => 'Beginner',
                      500 => 'Intermediate',
                      1000 => 'Professional',
                      2500 => 'Master',
                      5000 => 'Grandmaster'
                    ];
                    $totalDifficultyMatches = array_sum($difficultyCounts ?? []);
                  @endphp
                  @foreach($diffNames as $time => $name)
                    @php
                      $count = $difficultyCounts[$time] ?? 0;
                      $percent = $totalDifficultyMatches > 0 ? round(($count / $totalDifficultyMatches) * 100) : 0;
                    @endphp
                    <div class="power-stat-item">
                      <div class="power-stat-info">
                        <span class="power-stat-name">{{ $name }}</span>
                        <span class="power-stat-count">{{ $count }}x ({{ $percent }}%)</span>
                      </div>
                      <div class="power-stat-bar-bg">
                        <div class="power-stat-bar" style="width: {{ $percent }}%; background: #c9a84c;"></div>
                      </div>
                    </div>
                  @endforeach
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
