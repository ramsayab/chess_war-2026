<html>
  <head>
    <title>ChessWar</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@500;600&family=Cormorant+Garamond:ital,wght@0,500;1,500&family=Jost:wght@300;400;500;600&display=swap" rel="stylesheet">

    <!-- JQuery -->
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    
    <!-- Bootstrap -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
    
    <!-- chessboardjs -->
    <link rel="stylesheet" href="css/chessboard-1.0.0.min.css">
    <script src="js/chessboard-1.0.0.min.js"></script>
    
    <!-- WukongJS chess engine -->
    <script src="js/wukong.js"></script>

    <style>
      :root {
        --game-bg: #09111f;
        --game-bg-alt: #111c2d;
        --game-panel: rgba(10, 16, 28, 0.82);
        --game-border: rgba(201, 168, 76, 0.28);
        --game-gold: #c9a84c;
        --game-text: #f4efe3;

        --void: #07080d;
        --panel: #0d111d;
        --panel-2: #131829;
        --card: #0e1220;
        --card-hi: #161c30;
        --gold: #c9a44c;
        --gold-bright: #f0d38a;
        --gold-dim: rgba(201,164,76,0.28);
        --ember: #7c3b34;
        --ivory: #ece4d0;
        --slate: #9096a6;
        --slate-dim: #5c6172;
      }

      * {
        box-sizing: border-box;
      }

      html, body {
        min-height: 100%;
      }

      body.game-page {
        margin: 0;
        font-family: 'Jost', sans-serif;
        color: var(--game-text);
        background:
          radial-gradient(circle at 20% 20%, rgba(201, 168, 76, 0.18), transparent 30%),
          radial-gradient(circle at 85% 10%, rgba(105, 131, 191, 0.16), transparent 24%),
          linear-gradient(180deg, var(--game-bg) 0%, var(--game-bg-alt) 48%, #060b14 100%);
        overflow-x: hidden;
      }

      body.game-page::before {
        content: '';
        position: fixed;
        inset: 0;
        pointer-events: none;
        opacity: 0.15;
        background-image:
          linear-gradient(45deg, rgba(255,255,255,0.06) 25%, transparent 25%, transparent 75%, rgba(255,255,255,0.06) 75%, rgba(255,255,255,0.06)),
          linear-gradient(45deg, rgba(255,255,255,0.06) 25%, transparent 25%, transparent 75%, rgba(255,255,255,0.06) 75%, rgba(255,255,255,0.06));
        background-position: 0 0, 18px 18px;
        background-size: 36px 36px;
      }

      body.game-page::after {
        content: '';
        position: fixed;
        inset: auto -12% -18% auto;
        width: 360px;
        height: 360px;
        border-radius: 50%;
        background: radial-gradient(circle, rgba(201, 168, 76, 0.22), transparent 68%);
        filter: blur(10px);
        pointer-events: none;
      }

      .game-scene {
        position: relative;
        min-height: 100vh;
        padding: 40px 16px 28px;
        display: flex;
        align-items: center;
        justify-content: center;
      }

      .game-scene::before,
      .game-scene::after {
        content: '';
        position: absolute;
        width: 180px;
        height: 180px;
        border: 1px solid rgba(201, 168, 76, 0.18);
        border-radius: 28px;
        transform: rotate(12deg);
        pointer-events: none;
      }

      .game-scene::before {
        top: 36px;
        left: 18px;
        box-shadow: 0 0 0 1px rgba(201, 168, 76, 0.08) inset;
      }

      .game-scene::after {
        right: 24px;
        bottom: 34px;
        transform: rotate(-10deg);
      }

      .game-shell {
        position: relative;
        width: min(820px, 100%);
        padding: 30px 22px 24px;
        border: 1px solid var(--game-border);
        border-radius: 30px;
        background: var(--game-panel);
        box-shadow: 0 22px 60px rgba(0, 0, 0, 0.45);
        backdrop-filter: blur(12px);
        -webkit-backdrop-filter: blur(12px);
      }

      .game-shell::before {
        content: '';
        position: absolute;
        inset: 12px;
        border: 1px solid rgba(255, 255, 255, 0.05);
        border-radius: 22px;
        pointer-events: none;
      }

      .game-kicker {
        margin: 0 0 6px;
        letter-spacing: 0.24em;
        text-transform: uppercase;
        font-size: 0.74rem;
        color: rgba(201, 168, 76, 0.88);
      }

      .game-title {
        margin: 0;
        font-family: 'Cormorant Garamond', serif;
        font-size: clamp(2rem, 4vw, 3rem);
        line-height: 0.95;
        color: #fff7e4;
      }

      .game-subtitle {
        margin: 10px auto 0;
        max-width: 520px;
        color: rgba(244, 239, 227, 0.74);
      }

      .power-panel {
        width: min(640px, 100%);
        margin: 22px auto 10px;
        padding: 16px;
        border: 1px solid rgba(201, 168, 76, 0.18);
        border-radius: 22px;
        background: rgba(6, 10, 18, 0.55);
        box-shadow: 0 14px 32px rgba(0, 0, 0, 0.22);
      }

      .power-panel__header {
        margin-bottom: 12px;
      }

      .power-panel__label {
        margin: 0;
        font-size: 0.74rem;
        letter-spacing: 0.2em;
        text-transform: uppercase;
        color: rgba(201, 168, 76, 0.82);
      }

      .power-panel__title {
        margin: 4px 0 0;
        font-family: 'Cormorant Garamond', serif;
        font-size: 1.55rem;
        line-height: 1;
        color: #fff7e4;
      }

      .power-panel__hint {
        margin: 6px 0 0;
        color: rgba(244, 239, 227, 0.72);
        font-size: 0.95rem;
      }

      .power-grid {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 12px;
      }

      .power-card {
        position: relative;
        display: block;
        margin: 0;
        padding: 14px 14px 13px;
        border: 1px solid rgba(255, 255, 255, 0.08);
        border-radius: 18px;
        background:
          linear-gradient(180deg, rgba(255,255,255,0.05), rgba(255,255,255,0.02)),
          rgba(14, 21, 34, 0.92);
        color: var(--game-text);
        text-align: left;
        cursor: pointer;
        transition: transform 160ms ease, border-color 160ms ease, box-shadow 160ms ease, background 160ms ease;
      }

      .power-card:hover {
        transform: translateY(-2px);
        border-color: rgba(201, 168, 76, 0.32);
        box-shadow: 0 14px 26px rgba(0, 0, 0, 0.24);
      }

      .power-card.active {
        border-color: rgba(201, 168, 76, 0.75);
        background:
          linear-gradient(180deg, rgba(201, 168, 76, 0.18), rgba(201, 168, 76, 0.05)),
          rgba(14, 21, 34, 0.96);
        box-shadow: 0 0 0 1px rgba(201, 168, 76, 0.18) inset, 0 18px 34px rgba(0, 0, 0, 0.28);
      }

      .power-card__radio {
        position: absolute;
        opacity: 0;
        pointer-events: none;
      }

      .power-card__badge {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 54px;
        padding: 0.2rem 0.55rem;
        border-radius: 999px;
        font-size: 0.7rem;
        letter-spacing: 0.12em;
        text-transform: uppercase;
        color: #111;
        background: var(--game-gold);
      }

      .power-card__name {
        display: block;
        margin-top: 12px;
        font-family: 'Cormorant Garamond', serif;
        font-size: 1.6rem;
        line-height: 1;
        color: #fff7e4;
      }

      .power-card__desc {
        display: block;
        margin-top: 8px;
        color: rgba(244, 239, 227, 0.72);
        font-size: 0.92rem;
        line-height: 1.45;
      }

      .power-state {
        margin-top: 10px;
        color: rgba(244, 239, 227, 0.62);
        font-size: 0.9rem;
      }

      .power-state strong {
        color: #fff7e4;
      }

      #chessboard {
        width: min(400px, 100%);
        margin: 26px auto 14px;
        border: 1px solid rgba(201, 168, 76, 0.3);
        border-radius: 18px;
        overflow: hidden;
        box-shadow: 0 18px 40px rgba(0, 0, 0, 0.35);
      }

      .game-controls {
        width: min(427px, 100%);
        margin: 0 auto;
      }

      .game-controls .btn {
        border-radius: 999px;
        padding: 0.45rem 1rem;
        color: #f5f1e8;
        border-color: rgba(201, 168, 76, 0.35);
      }

      .game-controls .btn:hover,
      .game-controls .btn:focus {
        color: #111;
        background: var(--game-gold);
        border-color: var(--game-gold);
        box-shadow: none;
      }

      .game-controls .btn-group {
        gap: 0.5rem;
        flex-wrap: wrap;
        justify-content: center;
      }

      .game-controls .btn-group > .btn {
        flex: 1 1 90px;
      }

      @media (max-width: 768px) {
        .power-grid {
          grid-template-columns: repeat(2, minmax(0, 1fr));
        }
      }

      @media (max-width: 576px) {
        .game-scene {
          padding: 20px 12px;
        }

        .game-shell {
          padding: 22px 14px 18px;
          border-radius: 24px;
        }

        .game-controls .btn-group > .btn {
          flex-basis: calc(50% - 0.5rem);
        }

        .power-grid {
          grid-template-columns: 1fr;
        }
      }

      .power-card.mystery {
        border-color: rgba(201, 168, 76, 0.45);
        background: linear-gradient(135deg, rgba(16, 26, 43, 0.95) 0%, rgba(8, 14, 24, 0.98) 100%);
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.3);
      }
      
      .power-card.mystery:hover {
        transform: translateY(-4px);
        border-color: var(--game-gold);
        box-shadow: 0 12px 28px rgba(201, 168, 76, 0.25);
      }

      .power-card.revealed {
        animation: cardFlip 0.5s ease-out;
      }

      @keyframes cardFlip {
        0% { transform: scale(0.9) rotateY(90deg); opacity: 0; }
        100% { transform: scale(1) rotateY(0deg); opacity: 1; }
      }

      /* Square highlight guides for valid moves */
      .square-55d63.highlight-hint::after {
        content: '';
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        width: 18px;
        height: 18px;
        border-radius: 50%;
        background: rgba(201, 168, 76, 0.65);
        pointer-events: none;
        z-index: 99;
        box-shadow: 0 0 8px rgba(201, 168, 76, 0.4);
      }

      /* Capture guide (ring surrounding piece) */
      .square-55d63.highlight-hint.has-piece::after {
        width: 84%;
        height: 84%;
        border-radius: 50%;
        border: 4px solid rgba(201, 168, 76, 0.8);
        background: transparent;
        box-shadow: 0 0 10px rgba(201, 168, 76, 0.3);
      }

      /* Highlight for selected square */
      .square-55d63.selected-square {
        box-shadow: inset 0 0 3px 3px var(--game-gold) !important;
      }

      /* Premium Card Selection Layout Styles */
      .corner { position: absolute; width: 34px; height: 34px; opacity: 0.7; }
      .corner svg { width: 100%; height: 100%; }
      .corner.tl { top: 14px; left: 14px; }
      .corner.tr { top: 14px; right: 14px; transform: scaleX(-1); }
      .corner.bl { bottom: 14px; left: 14px; transform: scaleY(-1); }
      .corner.br { bottom: 14px; right: 14px; transform: scale(-1,-1); }

      .eyebrow {
        text-align: center;
        font-size: 11px;
        letter-spacing: 0.32em;
        text-transform: uppercase;
        color: var(--gold);
        margin: 0 0 14px;
        font-weight: 500;
      }
      .eyebrow::before, .eyebrow::after { content: "◆"; font-size: 6px; margin: 0 12px; color: var(--gold-dim); vertical-align: middle; }

      .sub {
        text-align: center;
        font-family: 'Cormorant Garamond', serif;
        font-style: italic;
        font-size: 19px;
        color: var(--slate);
        max-width: 520px;
        margin: 0 auto 40px;
        line-height: 1.5;
      }

      .divider {
        display: flex;
        align-items: center;
        gap: 14px;
        max-width: 360px;
        margin: 0 auto 40px;
      }
      .divider span { flex: 1; height: 1px; background: linear-gradient(90deg, transparent, var(--gold-dim), transparent); }
      .divider i { width: 6px; height: 6px; background: var(--gold); transform: rotate(45deg); }

      .privilege {
        background: radial-gradient(ellipse at 50% -20%, rgba(201,164,76,0.06), transparent 60%), var(--panel-2);
        border: 1px solid var(--gold-dim);
        padding: 40px 40px 34px;
      }
      .privilege .tag {
        text-align: center;
        font-size: 10.5px;
        letter-spacing: 0.28em;
        text-transform: uppercase;
        color: var(--gold);
        margin: 0 0 10px;
      }
      .privilege h2 {
        text-align: center;
        font-family: 'Cinzel', serif;
        font-weight: 500;
        font-size: 24px;
        margin: 0 0 10px;
        color: var(--ivory);
      }
      .privilege p.desc {
        text-align: center;
        font-size: 14px;
        color: var(--slate);
        margin: 0 0 34px;
      }

      .cards {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 22px;
      }
      .card {
        position: relative;
        background: #000000 !important;
        border: 1px solid rgba(201,168,76,0.20) !important;
        padding: 30px 22px 26px;
        cursor: pointer;
        text-align: center;
        transition: transform 0.35s cubic-bezier(0.2,0.8,0.2,1), border-color 0.35s, box-shadow 0.35s, background 0.35s;
        opacity: 0;
        animation: cardIn 0.6s cubic-bezier(0.2,0.8,0.2,1) forwards;
      }
      .card:nth-child(1) { animation-delay: 0.15s; }
      .card:nth-child(2) { animation-delay: 0.28s; }
      .card:nth-child(3) { animation-delay: 0.41s; }
      @keyframes cardIn {
        from { opacity: 0; transform: translateY(14px); }
        to { opacity: 1; transform: translateY(0); }
      }

      .card:hover {
        transform: translateY(-6px);
        border-color: rgba(240,211,138,0.55) !important;
        background: linear-gradient(160deg, var(--card-hi) 0%, #0a0d18 100%) !important;
        box-shadow: 0 18px 40px -18px rgba(0,0,0,0.7), 0 0 0 1px rgba(240,211,138,0.08);
      }
      .card.selected {
        border-color: var(--gold-bright) !important;
        background: linear-gradient(160deg, var(--card-hi) 0%, #0d1120 100%) !important;
        box-shadow: 0 0 0 1px rgba(240,211,138,0.5), 0 18px 44px -16px rgba(0,0,0,0.8);
        transform: translateY(-6px);
      }
      .card .beam {
        position: absolute; top: 0; left: 12%; right: 12%; height: 2px;
        background: linear-gradient(90deg, transparent, var(--gold-bright), transparent);
        opacity: 0; transition: opacity 0.35s;
      }
      .card.selected .beam { opacity: 1; }

      .seal {
        position: absolute; top: -11px; right: -11px;
        width: 26px; height: 26px; border-radius: 50%;
        background: radial-gradient(circle at 35% 30%, var(--gold-bright), var(--gold) 70%);
        display: flex; align-items: center; justify-content: center;
        font-size: 13px; color: #1a1204; font-weight: 600;
        opacity: 0; transform: scale(0.4);
        transition: opacity 0.3s, transform 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
        box-shadow: 0 4px 10px rgba(0,0,0,0.5);
      }
      .card.selected .seal { opacity: 1; transform: scale(1); }

      .glyph {
        font-size: 44px;
        line-height: 1;
        margin: 6px 0 16px;
        color: var(--gold);
        transition: color 0.35s, text-shadow 0.35s;
      }
      .card:hover .glyph, .card.selected .glyph {
        color: var(--gold-bright);
        text-shadow: 0 0 22px rgba(240,211,138,0.35);
      }

      .card .rank {
        font-size: 10px;
        letter-spacing: 0.22em;
        text-transform: uppercase;
        color: var(--slate-dim);
        margin: 0 0 6px;
      }
      .card h3 {
        font-family: 'Cinzel', serif;
        font-weight: 500;
        font-size: 18px;
        margin: 0 0 12px;
        color: var(--ivory);
      }

      .footer {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 20px;
        margin-top: 32px;
        padding-top: 24px;
        border-top: 1px solid rgba(201,164,76,0.15);
        flex-wrap: wrap;
      }
      .footer .status {
        font-size: 12.5px;
        letter-spacing: 0.06em;
        color: var(--slate);
      }
      .footer .status b {
        color: var(--gold-bright);
        font-weight: 500;
      }
      button.begin {
        font-family: 'Jost', sans-serif;
        font-size: 13px;
        letter-spacing: 0.14em;
        text-transform: uppercase;
        font-weight: 600;
        padding: 14px 30px;
        border: 1px solid var(--gold-dim);
        background: transparent;
        color: var(--slate-dim);
        cursor: not-allowed;
        transition: all 0.35s;
      }
      button.begin.active {
        color: #171106;
        background: linear-gradient(135deg, var(--gold-bright), var(--gold));
        border-color: var(--gold-bright);
        cursor: pointer;
        box-shadow: 0 10px 26px -10px rgba(201,164,76,0.55);
      }
      button.begin.active:hover { filter: brightness(1.08); transform: translateY(-1px); }
      button.begin.active:active { transform: translateY(0); }

      .game-shell-compact {
        width: min(440px, 100%) !important;
        padding: 24px 22px !important;
        margin: 0 !important;
      }

      .game-arena-grid {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 24px;
        margin: 15px auto;
        width: 100%;
        max-width: 440px;
      }

    </style>
    
  </head>
  <body class="game-page">
    <div class="col mt-4 game-scene">
      <div class="row justify-content-center" style="width: 100%; max-width: 900px; margin: 0 auto;">
        
        <!-- ==================== PRE-GAME AREA (Header + Cards) ==================== -->
        <div align="center" class="col-12 game-shell" id="game-pre-area" style="position: relative; padding: 64px 56px 48px; border: 1px solid var(--gold-dim); background: linear-gradient(180deg, var(--panel) 0%, #0a0d17 100%);">
          <!-- Corners -->
          <div class="corner tl"><svg viewBox="0 0 34 34" fill="none"><path d="M2 32V6a4 4 0 0 1 4-4h26" stroke="#c9a44c" stroke-width="1.2"/><circle cx="6" cy="6" r="2" fill="#c9a44c"/></svg></div>
          <div class="corner tr"><svg viewBox="0 0 34 34" fill="none"><path d="M2 32V6a4 4 0 0 1 4-4h26" stroke="#c9a44c" stroke-width="1.2"/><circle cx="6" cy="6" r="2" fill="#c9a44c"/></svg></div>
          <div class="corner bl"><svg viewBox="0 0 34 34" fill="none"><path d="M2 32V6a4 4 0 0 1 4-4h26" stroke="#c9a44c" stroke-width="1.2"/><circle cx="6" cy="6" r="2" fill="#c9a44c"/></svg></div>
          <div class="corner br"><svg viewBox="0 0 34 34" fill="none"><path d="M2 32V6a4 4 0 0 1 4-4h26" stroke="#c9a44c" stroke-width="1.2"/><circle cx="6" cy="6" r="2" fill="#c9a44c"/></svg></div>

          <p class="eyebrow">Chess War</p>
          <h1 style="font-family:'Cinzel',serif; font-weight:600; font-size:clamp(32px,5vw,46px); letter-spacing:.01em; margin:0 0 14px; color:var(--gold-bright); text-shadow:0 0 28px rgba(240,211,138,0.18);">Battle Arena</h1>
          <p class="sub">Choose one royal privilege before the match begins. Your opponent plays it straight.</p>

          <div class="divider"><span></span><i></i><span></span></div>

          <div class="privilege">
            <p class="tag">User Privilege</p>
            <h2>Select 1 Active Power</h2>
            <p class="desc">Choose one card to reveal your secret power. The bot stays standard.</p>

            <div class="cards" id="power-grid" role="radiogroup" aria-label="Choose active power">
              <!-- Dynamically populated cards -->
            </div>

            <div class="footer">
              <p class="status" id="shuffle-status">No power selected — choose one card above.</p>
              <button class="begin" id="beginBtn" disabled>Begin Battle →</button>
            </div>
          </div>
        </div>

        <!-- ==================== ACTIVE GAMEPLAY AREA ==================== -->
        <div class="col-12" id="game-arena-wrapper" style="display: none; text-align: center; width: 100%;">
          
          <!-- 1. Active Power Header (Outside Board Box) -->
          <div id="active-power-header" style="display: none; margin-bottom: 24px; padding: 12px 24px; border-radius: 16px; background: rgba(201, 168, 76, 0.08); border: 1px solid rgba(201, 168, 76, 0.18); text-align: center; max-width: 400px; margin-left: auto; margin-right: auto;">
              <span style="font-size: 0.75rem; letter-spacing: 0.22em; text-transform: uppercase; color: var(--game-gold); display: block; margin-bottom: 4px;">Active Power</span>
              <h2 id="active-power-title" style="font-family: 'Cormorant Garamond', serif; font-size: 2.1rem; color: #fff7e4; margin: 0; line-height: 1.1;">-</h2>
              <p id="active-power-desc" style="color: rgba(244, 239, 227, 0.74); font-size: 0.95rem; margin: 6px 0 0;"></p>
              <div id="king-lives-indicator" style="display: none; margin-top: 8px; font-size: 0.9rem; color: #ff5252; font-weight: 500;"></div>
          </div>

          <!-- 3. Side-by-Side Flex Layout (Board Box) -->
          <div class="game-arena-grid">
              
              <!-- Left side: The Board Box (Compact game-shell) -->
              <div class="game-shell game-shell-compact">
                  <!-- chessboard -->
                  <div id="chessboard" style="width: 400px;"></div>
                  
                  <!-- controls inside board box -->
                  <div class="row game-controls mt-4">
                      @if(auth()->user()->is_admin || auth()->user()->hasRole('super_admin'))
                      <div class="col btn-group">
                        <button id="newgame" class="btn btn-outline-secondary">New</button>
                        <button id="makemove" class="btn btn-outline-secondary">Move</button>
                        <button id="takeback" class="btn btn-outline-secondary">Undo</button>
                        <button id="flipboard" class="btn btn-outline-secondary">Flip</button>
                      </div>
                      @endif

                      <div class="col btn-group mt-3" style="width: 100%; display: flex; justify-content: center; gap: 0.5rem;">
                        <button id="save-game-btn" class="btn btn-outline-secondary" style="background: rgba(40, 167, 69, 0.12); border-color: rgba(40, 167, 69, 0.35); color: #fff;">Save Game</button>
                        <a href="/dashboard" class="btn btn-outline-secondary">Exit to Dashboard</a>
                      </div>
                  </div>
              </div>

          </div>

        </div>

      </div>
    </div>    
  </body>
</html>

<script>
  window.isAdmin = {{ (auth()->user()?->is_admin || auth()->user()?->hasRole('super_admin')) ? 'true' : 'false' }};
  let selectedSquare = null;

  function removeHighlights() {
    $('.square-55d63').removeClass('highlight-hint has-piece selected-square');
  }

  function highlightSquareMoves(square) {
    removeHighlights();
    
    if (!window.powerSelected) return;
    
    // Convert algebraic (e.g. 'e2') to 0x88 index
    const srcSq = square[0].charCodeAt() - "a".charCodeAt() + (8 - (square[1].charCodeAt() - "0".charCodeAt())) * 16;
    const pc = engine.getPiece(srcSq);
    if (pc === 0) return;
    
    const activeSide = engine.getSide();
    const isPlayerPiece = (activeSide === 0 && pc >= 1 && pc <= 6) || (activeSide === 1 && pc >= 7 && pc <= 12);
    if (!isPlayerPiece) return;
    
    const legalMoves = engine.generateLegalMoves();
    legalMoves.forEach(lm => {
      const mv = lm.move;
      if (engine.getMoveSource(mv) === srcSq) {
        const tgtSq = engine.getMoveTarget(mv);
        const tgtStr = engine.squareToString(tgtSq);
        
        const tgtEl = $('.square-' + tgtStr);
        tgtEl.addClass('highlight-hint');
        
        if (engine.getPiece(tgtSq) !== 0) {
          tgtEl.addClass('has-piece');
        }
      }
    });
  }

  function onMouseoverSquare (square, piece) {
    if (selectedSquare) return;
    highlightSquareMoves(square);
  }

  function onMouseoutSquare (square, piece) {
    if (selectedSquare) return;
    removeHighlights();
  }

  /****************************\
   ============================
   
        USER INPUT HANDLERS

   ============================              
  \****************************/
  
  
  let gameStartTime = null;

  function startTimer() {
    gameStartTime = Date.now();
  }

  function saveMatchResult(isWin, duration) {
    $.ajax({
      url: '/matches',
      type: 'POST',
      data: {
        _token: '{{ csrf_token() }}',
        is_win: isWin ? 1 : 0,
        total_time: duration,
        power_type: window.activePlayerPower
      },
      success: function(response) {
        console.log('Match history saved successfully:', response);
      },
      error: function(xhr) {
        console.error('Failed to save match history:', xhr.responseText);
      }
    });
  }

  function checkGameStatus() {
    if (!window.engine) return false;
    
    const legalMoves = engine.generateLegalMoves();
    const side = engine.getSide(); // 0 = white, 1 = black
    const inCheck = engine.inCheck(side);
    
    let isGameOver = false;
    let userWon = false;
    let draw = false;
    let reason = "";

    if (legalMoves.length === 0) {
      isGameOver = true;
      if (inCheck) {
        // Checkmate! The side to move has no moves and is in check.
        if (side === 0) {
          // User is checkmated (user lost)
          userWon = false;
          reason = "Checkmate! Bot wins.";
        } else {
          // Bot is checkmated (user won)
          userWon = true;
          reason = "Checkmate! You win!";
        }
      } else {
        // Stalemate
        draw = true;
        reason = "Draw by Stalemate.";
      }
    } else if (engine.isMaterialDraw()) {
      isGameOver = true;
      draw = true;
      reason = "Draw by Insufficient Material.";
    } else if (engine.isRepetition()) {
      isGameOver = true;
      draw = true;
      reason = "Draw by Repetition.";
    } else if (engine.getFifty() >= 100) {
      isGameOver = true;
      draw = true;
      reason = "Draw by 50-move rule.";
    }

    if (isGameOver) {
      const endTime = Date.now();
      const duration = gameStartTime ? Math.round((endTime - gameStartTime) / 1000) : 0;
      
      alert("Game Over: " + reason);
      
      // Save to database
      saveMatchResult(userWon && !draw, duration);
      return true;
    }
    return false;
  }

  // handle new game button click
  $('#newgame').on('click', function() {
    // reset engine
    engine.setBoard(engine.START_FEN);
    
    // Reset power selection
    window.powerSelected = false;
    window.activePlayerPower = '';
    window.moveCounter = 0;
    


    // Hide gameplay arena wrapper
    $('#game-arena-wrapper').hide();
    $('#active-power-header').hide();
    
    // Show pre-game area
    $('#game-pre-area').fadeIn(300);
    $('#power-panel').show();

    // Start shuffling animation again!
    runShufflingAnimation();
  });

  window.powerSelected = false;
  window.isShuffling = true;
  window.currentShuffledPowers = [];

  const powersList = [
    {
      value: 'confused_pawn',
      name: 'Confused Pawn',
      desc: 'Pawn can move backward too, making file control much more chaotic.'
    },
    {
      value: 'blink_knight',
      name: 'Blink Knight',
      desc: 'Knight jumps with a longer reach, doubling the usual movement patterns.'
    },
    {
      value: 'super_rook',
      name: 'Super Rook',
      desc: 'Rook keeps straight lines and gains one-step forward diagonals.'
    },
    {
      value: 'undying_king',
      name: 'Undying King',
      desc: 'King has 2 lives. The enemy piece that captures the King dies, and the King is restored.'
    },
    {
      value: 'omni_queen',
      name: 'Omni Queen',
      desc: 'Queen can move like a Queen and jump like a Knight.'
    },
    {
      value: 'grey_bishop',
      name: 'Grey Bishop',
      desc: 'Bishop can shift 1 step left/right (changing square color) and then slide diagonally.'
    }
  ];

  let lastKingLives = 2;
  function updateKingLivesUI() {
    if (window.engine && typeof window.engine.getKingLives === 'function') {
      const lives = window.engine.getKingLives();
      if (window.activePlayerPower === 'undying_king') {
        $('#king-lives-indicator').show().text(`Lives remaining: ${lives} / 2`);
        if (lives === 1 && lastKingLives === 2) {
          alert("Your Undying King lost a life! The attacking piece has been destroyed.");
        }
      } else {
        $('#king-lives-indicator').hide();
      }
      lastKingLives = lives;
    }
  }

  // Shuffle function
  function shuffle(array) {
    let currentIndex = array.length, randomIndex;
    while (currentIndex != 0) {
      randomIndex = Math.floor(Math.random() * currentIndex);
      currentIndex--;
      [array[currentIndex], array[randomIndex]] = [array[randomIndex], array[currentIndex]];
    }
    return array;
  }

  const powerGlyphs = {
    'blink_knight': '♞',
    'super_rook': '♜',
    'confused_pawn': '♟',
    'undying_king': '♚',
    'omni_queen': '♛',
    'grey_bishop': '♝'
  };

  function runShufflingAnimation() {
    window.isShuffling = false;
    const grid = $('#power-grid');
    grid.empty();
    
    // Reset the begin battle button
    $('#beginBtn').attr('disabled', 'disabled').removeClass('active');
    chosenPower = null;
    
    if (window.isAdmin) {
      // Admin flow: show all 6 powers with their actual names, glyphs, and descriptions
      window.currentShuffledPowers = [...powersList];
      
      window.currentShuffledPowers.forEach((power, index) => {
        const glyph = powerGlyphs[power.value] || '♟';
        grid.append(`
          <div class="card" data-power="${power.name}" data-index="${index}">
            <div class="beam"></div>
            <div class="seal">✓</div>
            <p class="rank">Admin Choice</p>
            <div class="glyph">${glyph}</div>
            <h3>${power.name}</h3>
            <p class="desc-text" style="font-size:13px; line-height:1.6; color:#9096a6; margin:0;">${power.desc}</p>
          </div>
        `);
      });
      
      $('#shuffle-status').text('Admin Privilege: Choose any power to activate.');
    } else {
      // Normal user flow: show 3 secret cards (CARD 1 - 3, full black, gold outline, no description)
      const shuffled = shuffle([...powersList]);
      window.currentShuffledPowers = shuffled.slice(0, 3);
      
      window.currentShuffledPowers.forEach((power, index) => {
        grid.append(`
          <div class="card" data-power="CARD ${index + 1}" data-index="${index}">
            <div class="beam"></div>
            <div class="seal">✓</div>
            <p class="rank">Secret</p>
            <div class="glyph">?</div>
            <h3>CARD ${index + 1}</h3>
          </div>
        `);
      });

      $('#shuffle-status').text('No power selected — choose one card above.');
    }
  }

  let chosenPower = null;

  $('#power-grid').on('click', '.card', function() {
    if (window.powerSelected) return; // Only allow selecting once

    $('.card').removeClass('selected');
    $(this).addClass('selected');
    
    const chosenIndex = $(this).data('index');
    chosenPower = window.currentShuffledPowers[chosenIndex];
    if (!chosenPower) return;

    // Enable the Begin Battle button
    $('#beginBtn').removeAttr('disabled').addClass('active');

    if (window.isAdmin) {
      $('#shuffle-status').html('Power selected — <b>' + chosenPower.name + '</b>');
    } else {
      $('#shuffle-status').html('Card selected — <b>CARD ' + (chosenIndex + 1) + '</b>');
    }
  });

  $('#beginBtn').on('click', function() {
    if (!$(this).hasClass('active')) return;
    if (window.powerSelected) return;
    if (!chosenPower) return;

    window.powerSelected = true;
    startTimer(); // Start the game timer
    
    window.activePlayerPower = chosenPower.value;
    if (window.engine && typeof window.engine.setPlayerPower === 'function') {
      window.engine.setPlayerPower(chosenPower.value);
    }
    
    lastKingLives = 2;

    // Populate active header details
    $('#active-power-title').text(chosenPower.name);
    $('#active-power-desc').text(chosenPower.desc);
    updateKingLivesUI();

    // Transition to gameplay
    $('#game-pre-area').fadeOut(300, function() {
      $('#active-power-header').show();
      $('#game-arena-wrapper').fadeIn(300, function() {
        if (!board) {
          board = Chessboard('chessboard', config);
        } else {
          board.position('start');
        }
        engine.setBoard(engine.START_FEN);
      });
    });
  });
  
  // handle make move button click
  $('#makemove').on('click', function() {
    // make computer move
    makeMove();
  });
  
  // handle take back button click
  $('#takeback').on('click', function() {
    // take move back
    engine.takeBack();
    
    // update board position
    board.position(engine.generateFen());
  });
  
  // handle flip board button click
  $('#flipboard').on('click', function() {
    // flip board
    board.flip();
  });
  
  // handle select move time option
  $('#move_time').on('change', function() {
    // disable fixed depth
    $('#fixed_depth').val('0');
  });
  
  // handle select fixed depth option
  $('#fixed_depth').on('change', function() {
    // disable fixed depth
    $('#move_time').val('0');
  });
  
  // handle set FEN button click
  $('#set_fen').on('click', function() {
    // set user FEN
    
    // FEN parsed
    if (game.load($('#fen').val()))
      // set board position
      board.position(game.fen());
    
    // FEN is not parsed
    else
      alert('Illegal FEN!');
  });
  
  // prevent scrolling on touch devices
  $('#chessboard').on('scroll touchmove touchend touchstart contextmenu', function(e) {
    e.preventDefault();
  });


  /****************************\
   ============================
   
      USER CONTROL FUNCTIONS

   ============================              
  \****************************/

  // make engine move
  function makeMove() {
    // make computer move
    setTimeout(function() {
      let bestMove = engine.searchTime(1000); // search for 1 second
      engine.makeMove(bestMove);
      
      // Update King Lives UI if Undying King triggered
      updateKingLivesUI();
      
      let fen = engine.generateFen();
      board.position(fen);

      // Check if engine's move ended the game
      checkGameStatus();
    }, 300);
  }



  // on dropping piece
  function onDrop (source, target) {
    if (source === target) return 'snapback';

    removeHighlights();
    selectedSquare = null;

    let promotedPiece = (engine.getSide() ? (5 + 6): 5); // queen promotion only for now
    let move = source + target + engine.promotedToString(promotedPiece);
    let validMove = engine.moveFromString(move);

    console.log('user move', promotedPiece);
    
    // invalid move
    if (validMove == 0) return 'snapback';
    
    let legalMoves = engine.generateLegalMoves();
    let isLegal = 0;
    
    for (let count = 0; count < legalMoves.length; count++) {
      if (validMove == legalMoves[count].move) isLegal = 1;  
    }
    
    // illegal move
    if (isLegal == 0) return 'snapback';
    
    // make user move
    engine.makeMove(validMove);    
    engine.printBoard();

    // Check if user's move ended the game
    if (checkGameStatus()) return;
    
    // make engine move
    makeMove();
    
    // TODO: update game status
    // isGameOver();
  }

  // update the board position after the piece snap
  // for castling, en passant, pawn promotion
  function onSnapEnd () {
    board.position(engine.generateFen());
  }

  
  /****************************\
   ============================
   
           MAIN DRIVER

   ============================              
  \****************************/

  // on drag start
  function onDragStart (source, piece, position, orientation) {
    if (!window.powerSelected) {
      alert("Please select a mystery card first to unlock your secret power!");
      return false;
    }

    // Only allow dragging if it is White's turn (user's turn) and the piece is White
    if (engine.getSide() !== 0 || piece.search(/^w/) === -1) {
      return false;
    }

    selectedSquare = source;
    highlightSquareMoves(source);
    $('.square-' + source).addClass('selected-square');
  }

  // chess board configuration
  var config = {
    draggable: true,
    position: 'start',
    onDragStart: onDragStart,
    onDrop: onDrop,
    onSnapEnd: onSnapEnd,
    onMouseoverSquare: onMouseoverSquare,
    onMouseoutSquare: onMouseoutSquare
  }
  
  // create chess board widget instance
  // create chess board widget instance
  let board = null;

  // Bind pointerdown event for valid move highlights and click-to-move
  $('#chessboard').on('pointerdown', '.square-55d63', function(e) {
    if (!window.powerSelected) return;
    if (!board) return;
    
    // Only allow moves if it is White's turn (user's turn)
    if (engine.getSide() !== 0) return;
    
    const classList = $(this).attr('class').split(/\s+/);
    const squareClass = classList.find(c => c.startsWith('square-') && c !== 'square-55d63');
    if (!squareClass) return;
    
    const square = squareClass.substring(7); // e.g. 'e2'
    
    // If a piece is already selected, try to move to the clicked square
    if (selectedSquare && selectedSquare !== square) {
      let promotedPiece = (engine.getSide() ? (5 + 6): 5); // queen promotion only for now
      let move = selectedSquare + square + engine.promotedToString(promotedPiece);
      let validMove = engine.moveFromString(move);
      
      let isLegal = 0;
      if (validMove !== 0) {
        let legalMoves = engine.generateLegalMoves();
        for (let count = 0; count < legalMoves.length; count++) {
          if (validMove == legalMoves[count].move) {
            isLegal = 1;
            break;
          }
        }
      }
      
      if (isLegal) {
        // Make user move in engine
        engine.makeMove(validMove);    
        engine.printBoard();
        
        // Update UI board position
        board.position(engine.generateFen());

        selectedSquare = null;
        removeHighlights();

        // Check if user's move ended the game
        if (checkGameStatus()) return;
        
        // Make engine move
        makeMove();
        return;
      }
    }
    
    const srcSq = square[0].charCodeAt() - "a".charCodeAt() + (8 - (square[1].charCodeAt() - "0".charCodeAt())) * 16;
    const pc = engine.getPiece(srcSq);
    const activeSide = engine.getSide();
    const isPlayerPiece = (activeSide === 0 && pc >= 1 && pc <= 6) || (activeSide === 1 && pc >= 7 && pc <= 12);
    
    if (isPlayerPiece) {
      if (selectedSquare === square) {
        selectedSquare = null;
        removeHighlights();
      } else {
        selectedSquare = square;
        highlightSquareMoves(square);
        $('.square-' + square).addClass('selected-square');
      }
    } else {
      selectedSquare = null;
      removeHighlights();
    }
  });
  
  // create WukongJS engine instance
  const engine = new Engine();
  window.engine = engine;
  // Initialize with no power active until one is selected
  window.activePlayerPower = '';

  // Save Game Event Listener
  $('#save-game-btn').on('click', function() {
    if (!window.powerSelected) {
      alert('Please select a card first to start the game before saving.');
      return;
    }
    
    const lives = (engine.getKingLives && typeof engine.getKingLives === 'function') ? engine.getKingLives() : 2;
    const currentFen = engine.generateFen() + ' KQkq - 0 1 ' + lives;
    const currentPower = window.activePlayerPower;
    
    $.ajax({
      url: '/api/game/save',
      type: 'POST',
      data: {
        _token: '{{ csrf_token() }}',
        fen: currentFen,
        power_type: currentPower
      },
      success: function(response) {
        alert('Game state saved successfully! You can resume this match later from the dashboard.');
      },
      error: function(xhr) {
        alert('Failed to save game state: ' + xhr.responseText);
      }
    });
  });

  // Resume Game Logic on load
  const urlParams = new URLSearchParams(window.location.search);
  if (urlParams.get('resume') === 'true') {
    $.ajax({
      url: '/api/game/resume',
      type: 'GET',
      success: function(response) {
        if (response.success && response.saved_game) {
          const savedGame = response.saved_game;
          
          // Set active player power
          window.activePlayerPower = savedGame.power_type;
          window.powerSelected = true;
          
          if (typeof engine.setPlayerPower === 'function') {
            engine.setPlayerPower(savedGame.power_type);
          }
          
          const power = powersList.find(p => p.value === savedGame.power_type);
          const powerName = power ? power.name : 'None';
          const powerDesc = power ? power.desc : '';
          
          // Populate active power header
          $('#active-power-title').text(powerName);
          $('#active-power-desc').text(powerDesc);
          
          const lives = (engine.getKingLives && typeof engine.getKingLives === 'function') ? engine.getKingLives() : 2;
          lastKingLives = lives;
          updateKingLivesUI();
          
          // Hide pre-game area immediately
          $('#game-pre-area').hide();
          
          // Show active power header and gameplay arena wrapper
          $('#active-power-header').show();
          $('#game-arena-wrapper').show();
          
          // Set board FEN in engine and board UI
          let fenToLoad = savedGame.fen;
          if (fenToLoad.split(' ').length < 6) {
            fenToLoad += ' KQkq - 0 1';
          }
          engine.setBoard(fenToLoad);
          
          // Initialize board widget since container is now visible
          board = Chessboard('chessboard', config);
          board.position(fenToLoad);
          
          // Start the play duration timer
          startTimer();
        }
      },
      error: function(xhr) {
        console.error('Failed to resume game:', xhr.responseText);
      }
    });
  } else {
    // New game flow
    runShufflingAnimation();
  }
</script>
