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
        min-height: 100vh;
      }

      body.game-page {
        margin: 0;
        font-family: 'Jost', sans-serif;
        color: var(--game-text);
        background:
          radial-gradient(circle at 20% 20%, rgba(201, 168, 76, 0.18), transparent 30%),
          radial-gradient(circle at 85% 10%, rgba(105, 131, 191, 0.16), transparent 24%),
          linear-gradient(180deg, var(--game-bg) 0%, var(--game-bg-alt) 48%, #060b14 100%);
        overflow-y: auto;
      }

      /* Lock viewport only when active game is showing */
      body.game-page.game-active {
        overflow: hidden;
        height: 100vh;
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
        padding: 40px 16px;
        display: flex;
        align-items: center;
        justify-content: center;
      }

      body.game-page.game-active .game-scene {
        height: 100vh;
        min-height: 100vh;
        padding: 16px;
        overflow: hidden;
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
        .cards {
          grid-template-columns: repeat(2, 1fr);
        }
      }

      @media (max-width: 576px) {
        .game-scene {
          padding: 20px 12px;
        }

        .game-shell {
          padding: 22px 14px 18px !important;
          border-radius: 24px;
        }

        .game-controls .btn-group > .btn {
          flex-basis: calc(50% - 0.5rem);
        }

        .cards {
          grid-template-columns: 1fr;
          gap: 16px;
        }

        .card {
          padding: 20px 16px;
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
        width: 610px !important;
        max-width: 610px !important;
        padding: 15px !important;
        margin: 0 auto !important;
      }

      #chessboard {
        width: 580px !important;
        height: 580px !important;
        margin: 0 auto;
        border: 1px solid rgba(201, 168, 76, 0.3);
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 12px 36px rgba(0, 0, 0, 0.45);
      }

      /* Ensure dragging and animated chess pieces are always visible above the board layout */
      body > .piece-417db {
        z-index: 999999 !important;
        pointer-events: none;
      }

      .game-arena-grid {
        width: 100%;
        display: flex;
        flex-direction: column;
        align-items: center;
      }

      /* 3-Column Layout */
      .game-arena-columns {
        display: grid;
        grid-template-columns: 260px 610px 280px;
        gap: 24px;
        align-items: center;
        justify-content: center;
        width: 100%;
        position: relative;
        z-index: 1;
      }
      .arena-col-left {
        display: flex;
        flex-direction: column;
        gap: 20px;
        width: 260px;
      }
      .arena-col-middle {
        display: flex;
        flex-direction: column;
        gap: 8px;
        width: 610px;
        align-items: center;
      }
      .arena-col-right {
        display: flex;
        flex-direction: column;
        gap: 16px;
        width: 280px;
      }

      /* Player Info Cards & Timers */
      .player-info-card {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 8px 14px;
        background: rgba(14, 21, 34, 0.75);
        border: 1px solid rgba(201, 168, 76, 0.15);
        border-radius: 12px;
        width: 100%;
        max-width: 610px;
        margin: 0 auto;
        transition: all 0.3s ease;
      }
      .player-info-card.active-turn {
        border-color: var(--game-gold);
        box-shadow: 0 0 15px rgba(201, 168, 76, 0.25);
        background: rgba(201, 168, 76, 0.08);
      }
      .player-info-card__left {
        display: flex;
        align-items: center;
        gap: 10px;
        flex: 1;
      }
      .player-avatar {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        background: radial-gradient(circle, var(--gold-bright), var(--gold));
        color: #111;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
        font-size: 13px;
        border: 1px solid rgba(255,255,255,0.1);
        box-shadow: 0 2px 6px rgba(0,0,0,0.3);
        transition: transform 0.3s;
      }
      .player-info-card.active-turn .player-avatar {
        transform: scale(1.08);
        box-shadow: 0 0 8px var(--game-gold);
      }
      .player-name {
        font-family: 'Cinzel', serif;
        font-size: 13px;
        color: var(--ivory);
        font-weight: 500;
      }
      .player-clock {
        font-family: monospace;
        font-size: 15px;
        color: var(--gold-bright);
        background: rgba(0, 0, 0, 0.4);
        padding: 2px 8px;
        border-radius: 6px;
        border: 1px solid rgba(251, 191, 36, 0.15);
        min-width: 54px;
        text-align: center;
      }
      .player-info-card.active-turn .player-clock {
        color: #fff;
        border-color: var(--game-gold);
      }

      /* Compact Captured Pieces Tray */
      .captured-tray-compact {
        display: flex;
        flex-wrap: wrap;
        gap: 3px;
        min-height: 18px;
        align-items: center;
        margin-left: 8px;
      }
      .captured-tray-compact .captured-piece {
        font-size: 14px;
        line-height: 1;
        opacity: 0.85;
      }
      .captured-tray-compact .captured-piece.black-piece {
        color: #8b949e;
        text-shadow: 0 0 1px #000;
      }
      .advantage-badge {
        font-size: 10px;
        background: rgba(52, 211, 153, 0.15);
        color: #34d399;
        border: 1px solid rgba(52, 211, 153, 0.3);
        padding: 0px 4px;
        border-radius: 4px;
        font-weight: 600;
        margin-left: 6px;
        display: inline-block;
      }

      /* Active Power Side Panel */
      .active-power-side-panel {
        background: rgba(14, 21, 34, 0.85);
        border: 1px solid rgba(201, 168, 76, 0.2);
        border-radius: 18px;
        padding: 14px;
        width: 100%;
        box-shadow: 0 12px 30px rgba(0, 0, 0, 0.4);
        text-align: center;
      }
      .power-avatar-display {
        width: 50px;
        height: 50px;
        border-radius: 12px;
        background: radial-gradient(circle, var(--card-hi), #000);
        border: 1px solid var(--gold-dim);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 28px;
        margin: 0 auto 10px;
        color: var(--gold-bright);
        box-shadow: 0 4px 12px rgba(0,0,0,0.5);
        position: relative;
      }
      .power-lives-badge {
        position: absolute;
        bottom: -6px;
        right: -6px;
        background: #ff5252;
        color: white;
        font-size: 9px;
        padding: 1px 4.5px;
        border-radius: 4px;
        font-weight: bold;
        box-shadow: 0 2px 6px rgba(0,0,0,0.4);
        display: none;
      }

      /* Move History / Notation scrolling list */
      .move-history-panel {
        background: rgba(14, 21, 34, 0.85);
        border: 1px solid rgba(201, 168, 76, 0.2);
        border-radius: 18px;
        padding: 14px;
        width: 100%;
        box-shadow: 0 12px 30px rgba(0, 0, 0, 0.4);
      }
      .move-list-scroll {
        height: 160px;
        overflow-y: auto;
        padding-right: 4px;
        font-size: 13px;
      }
      .move-list-scroll::-webkit-scrollbar {
        width: 4px;
      }
      .move-list-scroll::-webkit-scrollbar-track {
        background: rgba(0,0,0,0.1);
      }
      .move-list-scroll::-webkit-scrollbar-thumb {
        background: var(--gold-dim);
        border-radius: 2px;
      }
      .move-row {
        display: flex;
        padding: 4px 0;
        border-bottom: 1px solid rgba(255,255,255,0.03);
      }
      .move-number {
        width: 32px;
        color: var(--slate-dim);
        font-weight: 500;
      }
      .move-white, .move-black {
        flex: 1;
        color: var(--ivory);
        padding: 1px 4px;
        border-radius: 3px;
      }

      /* Action controls separation */
      .controls-group-title {
        font-size: 9px;
        letter-spacing: 0.14em;
        text-transform: uppercase;
        color: var(--slate-dim);
        margin-top: 10px;
        margin-bottom: 6px;
        text-align: center;
        font-weight: 600;
        border-bottom: 1px solid rgba(255,255,255,0.05);
        padding-bottom: 4px;
      }

      .game-control-panel {
        background: rgba(14, 21, 34, 0.85);
        border: 1px solid rgba(201, 168, 76, 0.2);
        border-radius: 18px;
        padding: 14px;
        width: 100%;
        box-shadow: 0 12px 30px rgba(0, 0, 0, 0.4);
      }
      .game-btn-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 8px;
        width: 100%;
      }
      .btn-chess-control {
        background: rgba(201, 168, 76, 0.04);
        border: 1px solid rgba(201, 168, 76, 0.25);
        color: var(--game-gold);
        font-family: 'Jost', sans-serif;
        font-size: 12.5px;
        font-weight: 500;
        padding: 7px 12px;
        border-radius: 8px;
        cursor: pointer;
        transition: all 0.2s ease;
        text-align: center;
        text-decoration: none;
      }
      .btn-chess-control:hover {
        background: rgba(201, 168, 76, 0.12);
        border-color: var(--game-gold);
        color: #fff;
      }
      .btn-chess-primary {
        background: var(--game-gold);
        border: 1px solid var(--game-gold);
        color: #0b0f19;
        font-family: 'Jost', sans-serif;
        font-size: 13px;
        font-weight: 600;
        padding: 9px 12px;
        border-radius: 8px;
        cursor: pointer;
        transition: all 0.2s ease;
        text-align: center;
        display: block;
        width: 100%;
        text-decoration: none;
        border-style: none;
      }
      .btn-chess-primary:hover {
        background: var(--gold-bright);
        box-shadow: 0 0 12px rgba(240, 211, 138, 0.4);
      }
      .btn-chess-danger {
        background: rgba(239, 68, 68, 0.04);
        border: 1px solid rgba(239, 68, 68, 0.25);
        color: #f87171;
        font-family: 'Jost', sans-serif;
        font-size: 13px;
        font-weight: 500;
        padding: 9px 12px;
        border-radius: 8px;
        cursor: pointer;
        transition: all 0.2s ease;
        text-align: center;
        display: block;
        width: 100%;
        text-decoration: none;
      }
      .btn-chess-danger:hover {
        background: rgba(239, 68, 68, 0.12);
        border-color: #ef4444;
        color: #fff;
      }

      /* Check Highlights */
      .square-55d63.in-check {
        animation: checkPulse 1.2s infinite alternate;
      }
      @keyframes checkPulse {
        0% { box-shadow: inset 0 0 12px 4px rgba(239, 68, 68, 0.5) !important; }
        100% { box-shadow: inset 0 0 20px 8px rgba(239, 68, 68, 0.95) !important; }
      }

      /* Status Toast Alert */
      .game-status-banner {
        margin: 0 auto 12px;
        padding: 6px 14px;
        border-radius: 8px;
        font-weight: 500;
        font-size: 13px;
        letter-spacing: 0.05em;
        text-align: center;
        width: 100%;
        max-width: 440px;
        display: none;
        animation: slideIn 0.3s ease-out;
      }
      .game-status-banner.status-check {
        background: rgba(239, 68, 68, 0.12);
        border: 1px solid rgba(239, 68, 68, 0.35);
        color: #f87171;
      }
      .game-status-banner.status-turn {
        background: rgba(52, 211, 153, 0.1);
        border: 1px solid rgba(52, 211, 153, 0.3);
        color: #34d399;
      }
      .game-status-banner.status-thinking {
        background: rgba(251, 191, 36, 0.1);
        border: 1px solid rgba(251, 191, 36, 0.3);
        color: #fbbf24;
      }
      @keyframes slideIn {
        from { opacity: 0; transform: translateY(-10px); }
        to { opacity: 1; transform: translateY(0); }
      }

      /* Watermark Chess pieces background decoration */
      .chess-decor-watermark {
        position: fixed;
        font-size: 20vw;
        color: rgba(201, 168, 76, 0.015);
        user-select: none;
        pointer-events: none;
        z-index: 0;
      }
      .chess-decor-watermark.left-piece {
        left: -3%;
        top: 25%;
        transform: rotate(-12deg);
      }
      .chess-decor-watermark.right-piece {
        right: -3%;
        bottom: 15%;
        transform: rotate(12deg);
      }

      /* Power Grid Vector visual illustration */
      .vector-board {
        display: grid;
        grid-template-columns: repeat(5, 1fr);
        width: 120px;
        height: 120px;
        border: 1.5px solid var(--game-gold);
        border-radius: 8px;
        overflow: hidden;
        margin: 10px auto;
        box-shadow: 0 4px 10px rgba(0,0,0,0.4);
      }
      .vector-sq {
        width: 24px;
        height: 24px;
        display: flex;
        align-items: center;
        justify-content: center;
        position: relative;
      }
      .vector-sq.light {
        background: #f4efe3;
      }
      .vector-sq.dark {
        background: #c9a84c;
      }
      .vector-piece {
        font-size: 16px;
        color: #111;
        z-index: 2;
      }
      .vector-dot {
        width: 6px;
        height: 6px;
        border-radius: 50%;
        background: #10b981;
        box-shadow: 0 0 6px rgba(16, 185, 129, 0.8);
      }
      .vector-line {
        width: 6px;
        height: 6px;
        border-radius: 50%;
        background: #3b82f6;
        box-shadow: 0 0 6px rgba(59, 130, 246, 0.8);
      }

      /* Mobile Layout adaptation */
      @media (max-width: 991px) {
        .game-arena-columns {
          display: flex;
          flex-direction: column;
          align-items: center;
        }
        .arena-col-left {
          order: 2;
          max-width: 440px;
          width: 100%;
          flex-direction: row;
          justify-content: space-between;
        }
        .arena-col-middle {
          order: 1;
          width: 100%;
        }
        .arena-col-right {
          order: 3;
          max-width: 440px;
          width: 100%;
        }
        .captured-panel, .active-power-side-panel {
          flex: 1;
          max-width: none;
        }
        #chessboard {
          width: min(100vw - 64px, 580px) !important;
          height: min(100vw - 64px, 580px) !important;
        }
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

            <!-- Pre-game Difficulty Selection -->
            <div style="margin: 24px auto 32px; max-width: 380px; text-align: left; background: rgba(201, 168, 76, 0.03); border: 1px solid rgba(201, 168, 76, 0.15); border-radius: 12px; padding: 18px 24px;">
              <label for="pre-game-difficulty" style="font-family: 'Cinzel', serif; font-size: 11px; letter-spacing: 0.12em; text-transform: uppercase; color: var(--gold-bright); display: block; margin-bottom: 8px; font-weight: 600;">Select AI Difficulty</label>
              <select id="pre-game-difficulty" class="btn-chess-control" style="width: 100%; text-align: left; background: rgba(10, 16, 28, 0.95); color: var(--game-gold); outline: none; border: 1px solid var(--game-border); padding: 10px 14px; border-radius: 8px; font-size: 14px; font-weight: 500; cursor: pointer; transition: all 0.2s;">
                <option value="100" style="background: #0d111a; color: var(--game-gold);">Beginner (100ms) • Normal XP</option>
                <option value="500" style="background: #0d111a; color: var(--game-gold);">Intermediate (500ms) • +50% XP</option>
                <option value="1000" selected style="background: #0d111a; color: var(--game-gold);">Professional (1000ms) • Double XP</option>
                <option value="2500" style="background: #0d111a; color: var(--game-gold);">Master (2500ms) • Triple XP</option>
                <option value="5000" style="background: #0d111a; color: var(--game-gold);">Grandmaster (5000ms) • 5x XP</option>
              </select>
            </div>

            <div class="footer">
              <p class="status" id="shuffle-status">No power selected — choose one card above.</p>
              <button class="begin" id="beginBtn" disabled>Begin Battle →</button>
            </div>
          </div>
        </div>

        <!-- ==================== ACTIVE GAMEPLAY AREA ==================== -->
        <div class="col-12" id="game-arena-wrapper" style="display: none; text-align: center; width: 100%;">
          
          <!-- Watermark Chess Pieces -->
          <div class="chess-decor-watermark left-piece">♞</div>
          <div class="chess-decor-watermark right-piece">♛</div>

          <div class="game-arena-columns">
              
              <!-- LEFT COLUMN: Active Power -->
              <div class="arena-col-left">
                  <!-- Active Power side panel -->
                  <div class="active-power-side-panel">
                      <span style="font-size: 0.7rem; letter-spacing: 0.18em; text-transform: uppercase; color: var(--game-gold); display: block; margin-bottom: 4px; font-weight: 500;">Active Privilege</span>
                      <div class="power-avatar-display">
                          <span id="power-avatar-icon">♟</span>
                          <span class="power-lives-badge" id="king-lives-badge">2</span>
                      </div>
                      <h4 id="active-power-title" style="font-family: 'Cinzel', serif; font-size: 1.25rem; color: #fff7e4; margin: 0 0 6px;">-</h4>
                      <p id="active-power-desc" style="color: rgba(244, 239, 227, 0.7); font-size: 0.82rem; margin: 0 auto; line-height: 1.45;"></p>
                      
                      <!-- Visual Grid movement vectors -->
                      <div class="vector-board" id="power-vector-board"></div>
                  </div>
              </div>
              
              <!-- MIDDLE COLUMN: Player Info & Chessboard -->
              <div class="arena-col-middle">
                  
                  <!-- Toast banner status messages -->
                  <div id="status-banner" class="game-status-banner">Your Turn</div>

                  <!-- Opponent Info Card -->
                  <div class="player-info-card" id="bot-info-card">
                      <div class="player-info-card__left">
                          <div class="player-avatar" style="background: radial-gradient(circle, #f87171, #ef4444); flex-shrink: 0;">AI</div>
                          <div style="display: flex; flex-direction: column; align-items: flex-start;">
                              <span class="player-name">Wukong AI</span>
                              <!-- Bot captures (White pieces captured by Bot) next to name -->
                              <div class="captured-tray-compact" id="bot-captured-tray"></div>
                          </div>
                          <span id="bot-captured-adv"></span>
                      </div>
                      <div class="player-clock" id="bot-time">00:00</div>
                  </div>

                  <!-- The Chessboard inside compact shell container -->
                  <div class="game-shell game-shell-compact">
                      <div id="chessboard"></div>
                  </div>

                  <!-- Player Info Card -->
                  <div class="player-info-card" id="player-info-card">
                      <div class="player-info-card__left">
                          <div class="player-avatar" style="flex-shrink: 0;">{{ strtoupper(substr(auth()->user()->name, 0, 2)) }}</div>
                          <div style="display: flex; flex-direction: column; align-items: flex-start;">
                              <span class="player-name">{{ auth()->user()->name }}</span>
                              <!-- Player captures (Black pieces captured by Player) next to name -->
                              <div class="captured-tray-compact" id="player-captured-tray"></div>
                          </div>
                          <span id="player-captured-adv"></span>
                      </div>
                      <div class="player-clock" id="player-time">00:00</div>
                  </div>
              </div>
              
              <!-- RIGHT COLUMN: Move History & Controls -->
              <div class="arena-col-right">
                  
                  <!-- Move History / Notation -->
                  <div class="move-history-panel">
                      <h3 style="font-size: 10px; letter-spacing: 0.16em; text-transform: uppercase; color: var(--gold); margin-bottom: 8px; text-align: center; border-bottom: 1px solid rgba(201,164,76,0.15); padding-bottom: 5px;">Move History</h3>
                      <div class="move-list-scroll" id="move-list-scroll">
                          <!-- Filled dynamically via javascript -->
                      </div>
                  </div>

                  <!-- Grouped Controls panel -->
                  <div class="game-control-panel">
                      <div class="controls-group-title" style="margin-top: 0; border-bottom: none; padding-bottom: 0;">Game Controls</div>
                      <div class="game-btn-grid" style="margin-bottom: 12px;">
                          @php
                              $user = auth()->user();
                              $isAdminUser = ($user->is_admin == 1) || $user->roles()->whereIn('name', ['admin', 'super_admin'])->exists();
                          @endphp
                          <button id="newgame" class="btn-chess-control">New</button>
                          @if($isAdminUser)
                              <button id="makemove" class="btn-chess-control">Move</button>
                              <button id="takeback" class="btn-chess-control">Undo</button>
                          @endif
                          <button id="flipboard" class="btn-chess-control">Flip</button>
                      </div>



                      <div class="controls-group-title" style="border-bottom: none; padding-bottom: 0;">Session</div>
                      <div style="display: flex; flex-direction: column; gap: 8px; width: 100%;">
                          <button id="save-game-btn" class="btn-chess-primary">Save Match</button>
                          <a href="/dashboard" class="btn-chess-danger">Exit Arena</a>
                      </div>
                  </div>

              </div>
              
          </div>

        </div></div>

      </div>
    </div>    
  </body>
</html>

<script>
  window.isAdmin = {{ (auth()->user()?->is_admin || auth()->user()?->hasRole('super_admin')) ? 'true' : 'false' }};
  let selectedSquare = null;

  // New In-game logic variables
  window.gameMoves = [];
  let playerTime = 0;
  let botTime = 0;
  let timerInterval = null;

  const powerVectors = {
    confused_pawn: [
      { r: 1, c: 2, t: 'dot' },
      { r: 3, c: 2, t: 'dot' }
    ],
    blink_knight: [
      { r: 0, c: 1, t: 'dot' }, { r: 0, c: 3, t: 'dot' },
      { r: 1, c: 0, t: 'dot' }, { r: 1, c: 4, t: 'dot' },
      { r: 3, c: 0, t: 'dot' }, { r: 3, c: 4, t: 'dot' },
      { r: 4, c: 1, t: 'dot' }, { r: 4, c: 3, t: 'dot' }
    ],
    super_rook: [
      { r: 0, c: 2, t: 'line' }, { r: 1, c: 2, t: 'line' },
      { r: 3, c: 2, t: 'line' }, { r: 4, c: 2, t: 'line' },
      { r: 2, c: 0, t: 'line' }, { r: 2, c: 1, t: 'line' },
      { r: 2, c: 3, t: 'line' }, { r: 2, c: 4, t: 'line' },
      { r: 1, c: 1, t: 'dot' }, { r: 1, c: 3, t: 'dot' }
    ],
    undying_king: [
      { r: 1, c: 1, t: 'dot' }, { r: 1, c: 2, t: 'dot' }, { r: 1, c: 3, t: 'dot' },
      { r: 2, c: 1, t: 'dot' },                          { r: 2, c: 3, t: 'dot' },
      { r: 3, c: 1, t: 'dot' }, { r: 3, c: 2, t: 'dot' }, { r: 3, c: 3, t: 'dot' }
    ],
    omni_queen: [
      { r: 0, c: 0, t: 'line' }, { r: 0, c: 2, t: 'line' }, { r: 0, c: 4, t: 'line' },
      { r: 1, c: 1, t: 'line' }, { r: 1, c: 2, t: 'line' }, { r: 1, c: 3, t: 'line' },
      { r: 2, c: 0, t: 'line' }, { r: 2, c: 1, t: 'line' },                          { r: 2, c: 3, t: 'line' }, { r: 2, c: 4, t: 'line' },
      { r: 3, c: 1, t: 'line' }, { r: 3, c: 2, t: 'line' }, { r: 3, c: 3, t: 'line' },
      { r: 4, c: 0, t: 'line' }, { r: 4, c: 2, t: 'line' }, { r: 4, c: 4, t: 'line' },
      { r: 0, c: 1, t: 'dot' }, { r: 0, c: 3, t: 'dot' },
      { r: 1, c: 0, t: 'dot' }, { r: 1, c: 4, t: 'dot' },
      { r: 3, c: 0, t: 'dot' }, { r: 3, c: 4, t: 'dot' },
      { r: 4, c: 1, t: 'dot' }, { r: 4, c: 3, t: 'dot' }
    ],
    grey_bishop: [
      { r: 0, c: 0, t: 'line' }, { r: 0, c: 4, t: 'line' },
      { r: 1, c: 1, t: 'line' }, { r: 1, c: 3, t: 'line' },
      { r: 3, c: 1, t: 'line' }, { r: 3, c: 3, t: 'line' },
      { r: 4, c: 0, t: 'line' }, { r: 4, c: 4, t: 'line' },
      { r: 2, c: 1, t: 'dot' }, { r: 2, c: 3, t: 'dot' }
    ]
  };

  function renderPowerBoard(powerType) {
    const container = $('#power-vector-board');
    container.empty();
    
    const vectors = powerVectors[powerType] || [];
    
    for (let r = 0; r < 5; r++) {
      for (let c = 0; c < 5; c++) {
        const isDark = (r + c) % 2 === 1;
        const isCenter = r === 2 && c === 2;
        
        let content = '';
        if (isCenter) {
          const glyph = powerGlyphs[powerType] || '♟';
          content = `<span class="vector-piece">${glyph}</span>`;
        } else {
          const vector = vectors.find(v => v.r === r && v.c === c);
          if (vector) {
            if (vector.t === 'dot') {
              content = `<div class="vector-dot"></div>`;
            } else if (vector.t === 'line') {
              content = `<div class="vector-line"></div>`;
            }
          }
        }
        
        container.append(`
          <div class="vector-sq ${isDark ? 'dark' : 'light'}">
            ${content}
          </div>
        `);
      }
    }
  }

  function startClocks() {
    if (timerInterval) clearInterval(timerInterval);
    timerInterval = setInterval(() => {
      if (!window.powerSelected) return;
      const side = engine.getSide(); // 0 = White, 1 = Black
      if (side === 0) {
        playerTime++;
        updateClockUI(0);
      } else {
        botTime++;
        updateClockUI(1);
      }
    }, 1000);
  }

  function stopClocks() {
    if (timerInterval) clearInterval(timerInterval);
  }

  function resetClocks() {
    playerTime = 0;
    botTime = 0;
    updateClockUI(0);
    updateClockUI(1);
  }

  function formatTime(sec) {
    const mins = Math.floor(sec / 60);
    const secs = sec % 60;
    return (mins < 10 ? '0' : '') + mins + ':' + (secs < 10 ? '0' : '') + secs;
  }

  function updateClockUI(side) {
    if (side === 0) {
      $('#player-time').text(formatTime(playerTime));
    } else {
      $('#bot-time').text(formatTime(botTime));
    }
  }

  function addMoveToHistory(moveStr) {
    window.gameMoves.push(moveStr);
    renderMoveHistory();
  }

  function renderMoveHistory() {
    const container = $('#move-list-scroll');
    container.empty();
    
    for (let i = 0; i < window.gameMoves.length; i += 2) {
      const moveNum = Math.floor(i / 2) + 1;
      const whiteMove = window.gameMoves[i];
      const blackMove = window.gameMoves[i + 1] || '';
      
      container.append(`
        <div class="move-row">
          <div class="move-number">${moveNum}.</div>
          <div class="move-white">${whiteMove}</div>
          <div class="move-black">${blackMove}</div>
        </div>
      `);
    }
    
    if (container[0]) {
      container.scrollTop(container[0].scrollHeight);
    }
  }

  function formatMove(moveStr) {
    if (!moveStr || moveStr.length < 4) return moveStr;
    const from = moveStr.substring(0, 2);
    const to = moveStr.substring(2, 4);
    const promo = moveStr.length > 4 ? '=' + moveStr.substring(4).toUpperCase() : '';
    return from + '-' + to + promo;
  }

  function highlightLastMove(from, to) {
    // Empty to remove trail highlight/shadows of the last move
  }

  function updateTurnHighlight() {
    if (!window.engine) return;
    const side = engine.getSide();
    if (side === 0) {
      $('#player-info-card').addClass('active-turn');
      $('#bot-info-card').removeClass('active-turn');
      
      $('#status-banner').removeClass('status-thinking status-check').addClass('status-turn').text('Your Turn').fadeIn(200);
    } else {
      $('#bot-info-card').addClass('active-turn');
      $('#player-info-card').removeClass('active-turn');
      
      $('#status-banner').removeClass('status-turn status-check').addClass('status-thinking').text('AI is thinking...').fadeIn(200);
    }
    
    // Check if in check
    const inCheck = engine.inCheck(side);
    $('.square-55d63').removeClass('in-check');
    if (inCheck) {
      $('#status-banner').removeClass('status-turn status-thinking').addClass('status-check').text('Check!').fadeIn(200);
      
      // Highlight king square
      let kingSquareName = "";
      for (let sq = 0; sq < 128; sq++) {
        if ((sq & 0x88) === 0) {
          const pc = engine.getPiece(sq);
          if (side === 0 && pc === 1) { // White King
            kingSquareName = engine.squareToString(sq);
            break;
          }
          if (side === 1 && pc === 7) { // Black King
            kingSquareName = engine.squareToString(sq);
            break;
          }
        }
      }
      if (kingSquareName) {
        $('.square-' + kingSquareName).addClass('in-check');
      }
    }
  }

  function updateCapturedPiecesUI() {
    if (!window.engine) return;
    const currentCounts = {
      P: 0, N: 0, B: 0, R: 0, Q: 0, K: 0,
      p: 0, n: 0, b: 0, r: 0, q: 0, k: 0
    };
    
    for (let sq = 0; sq < 128; sq++) {
      if ((sq & 0x88) === 0) {
        const pc = engine.getPiece(sq);
        if (pc === 1) currentCounts.K++;
        else if (pc === 2) currentCounts.P++;
        else if (pc === 3) currentCounts.N++;
        else if (pc === 4) currentCounts.B++;
        else if (pc === 5) currentCounts.R++;
        else if (pc === 6) currentCounts.Q++;
        else if (pc === 7) currentCounts.k++;
        else if (pc === 8) currentCounts.p++;
        else if (pc === 9) currentCounts.n++;
        else if (pc === 10) currentCounts.b++;
        else if (pc === 11) currentCounts.r++;
        else if (pc === 12) currentCounts.q++;
      }
    }
    
    const whiteCaptured = [];
    const blackCaptured = [];
    
    let whiteScore = 0;
    let blackScore = 0;
    
    // White pieces captured by Black
    for (let i = 0; i < 8 - currentCounts.P; i++) { whiteCaptured.push('♟'); blackScore += 1; }
    for (let i = 0; i < 2 - currentCounts.N; i++) { whiteCaptured.push('♞'); blackScore += 3; }
    for (let i = 0; i < 2 - currentCounts.B; i++) { whiteCaptured.push('♝'); blackScore += 3; }
    for (let i = 0; i < 2 - currentCounts.R; i++) { whiteCaptured.push('♜'); blackScore += 5; }
    for (let i = 0; i < 1 - currentCounts.Q; i++) { whiteCaptured.push('♛'); blackScore += 9; }
    
    // Black pieces captured by White
    for (let i = 0; i < 8 - currentCounts.p; i++) { blackCaptured.push('♟'); whiteScore += 1; }
    for (let i = 0; i < 2 - currentCounts.n; i++) { blackCaptured.push('♞'); whiteScore += 3; }
    for (let i = 0; i < 2 - currentCounts.b; i++) { blackCaptured.push('♝'); whiteScore += 3; }
    for (let i = 0; i < 2 - currentCounts.r; i++) { blackCaptured.push('♜'); whiteScore += 5; }
    for (let i = 0; i < 1 - currentCounts.q; i++) { blackCaptured.push('♛'); whiteScore += 9; }
    
    const playerTray = $('#player-captured-tray');
    playerTray.empty();
    blackCaptured.forEach(char => {
      playerTray.append(`<span class="captured-piece black-piece">${char}</span>`);
    });
    
    const botTray = $('#bot-captured-tray');
    botTray.empty();
    whiteCaptured.forEach(char => {
      botTray.append(`<span class="captured-piece">${char}</span>`);
    });
    
    $('#player-captured-adv').empty();
    $('#bot-captured-adv').empty();
    if (whiteScore > blackScore) {
      $('#player-captured-adv').html(`<span class="advantage-badge">+${whiteScore - blackScore}</span>`);
    } else if (blackScore > whiteScore) {
      $('#bot-captured-adv').html(`<span class="advantage-badge">+${blackScore - whiteScore}</span>`);
    }
  }

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
        power_type: window.activePlayerPower,
        difficulty: window.gameDifficulty || 1000
      },
      success: function(response) {
        console.log('Match history saved successfully:', response);
        alert("Match results saved to Leaderboard!");
      },
      error: function(xhr) {
        console.error('Failed to save match history:', xhr.responseText);
        alert("Failed to save match result: " + (xhr.responseJSON?.message || xhr.statusText));
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
      stopClocks();
      $('#status-banner').hide();
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
    
    stopClocks();
    resetClocks();
    window.gameMoves = [];
    renderMoveHistory();
    removeHighlights();
    $('.square-55d63').removeClass('last-move-highlight in-check');
    $('#status-banner').hide();
    
    // Disable full-screen mode for card selection
    $('body').removeClass('game-active');

    // Hide gameplay arena wrapper
    $('#game-arena-wrapper').hide();
    
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
        $('#king-lives-badge').show().text(lives);
        if (lives === 1 && lastKingLives === 2) {
          alert("Your Undying King lost a life! The attacking piece has been destroyed.");
        }
      } else {
        $('#king-lives-badge').hide();
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
    
    window.gameDifficulty = parseInt($('#pre-game-difficulty').val()) || 1000;

    window.activePlayerPower = chosenPower.value;
    if (window.engine && typeof window.engine.setPlayerPower === 'function') {
      window.engine.setPlayerPower(chosenPower.value);
    }
    
    lastKingLives = 2;

    // Populate active side panel details
    $('#active-power-title').text(chosenPower.name);
    $('#active-power-desc').text(chosenPower.desc);
    $('#power-avatar-icon').text(powerGlyphs[chosenPower.value] || '♟');
    renderPowerBoard(chosenPower.value);
    updateKingLivesUI();

    // Start clocks & reset history
    resetClocks();
    startClocks();
    window.gameMoves = [];
    renderMoveHistory();
    $('.square-55d63').removeClass('last-move-highlight in-check');
    removeHighlights();

    // Transition to gameplay
    $('#game-pre-area').fadeOut(300, function() {
      $('body').addClass('game-active');
      $('#game-arena-wrapper').fadeIn(300, function() {
        if (!board) {
          board = Chessboard('chessboard', config);
        } else {
          board.position('start');
        }
        engine.setBoard(engine.START_FEN);
        updateTurnHighlight();
        updateCapturedPiecesUI();
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
    // take move back twice (bot move + player move)
    engine.takeBack();
    engine.takeBack();
    
    // update board position
    board.position(engine.generateFen(), true);

    // update move log
    window.gameMoves.pop();
    window.gameMoves.pop();
    renderMoveHistory();

    // Reset square highlights
    $('.square-55d63').removeClass('last-move-highlight in-check');
    removeHighlights();

    // update panels
    updateTurnHighlight();
    updateCapturedPiecesUI();
    updateKingLivesUI();
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
      const searchTime = window.gameDifficulty || 1000;
      let bestMove = engine.searchTime(searchTime);
      
      let sourceStr = engine.squareToString(engine.getMoveSource(bestMove));
      let targetStr = engine.squareToString(engine.getMoveTarget(bestMove));
      
      engine.makeMove(bestMove);
      
      addMoveToHistory(formatMove(sourceStr + targetStr));
      highlightLastMove(sourceStr, targetStr);
      updateTurnHighlight();
      updateCapturedPiecesUI();

      // Update King Lives UI if Undying King triggered
      updateKingLivesUI();
      
      let fen = engine.generateFen();
      board.position(fen, true);

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

    addMoveToHistory(formatMove(source + target));
    highlightLastMove(source, target);
    updateTurnHighlight();
    updateCapturedPiecesUI();

    // Check if user's move ended the game
    if (checkGameStatus()) return;
    
    // make engine move
    makeMove();
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
    onMouseoutSquare: onMouseoutSquare,
    moveSpeed: 300,
    snapbackSpeed: 300,
    snapSpeed: 250
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
        
        addMoveToHistory(formatMove(selectedSquare + square));
        highlightLastMove(selectedSquare, square);
        updateTurnHighlight();
        updateCapturedPiecesUI();

        // Update UI board position
        board.position(engine.generateFen(), true);

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
        power_type: currentPower,
        difficulty: window.gameDifficulty || 1000
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
          window.gameDifficulty = savedGame.difficulty || 1000;
          
          if (typeof engine.setPlayerPower === 'function') {
            engine.setPlayerPower(savedGame.power_type);
          }
          
          const power = powersList.find(p => p.value === savedGame.power_type);
          const powerName = power ? power.name : 'None';
          const powerDesc = power ? power.desc : '';
          
          // Populate active power panel
          $('#active-power-title').text(powerName);
          $('#active-power-desc').text(powerDesc);
          $('#power-avatar-icon').text(powerGlyphs[savedGame.power_type] || '♟');
          renderPowerBoard(savedGame.power_type);
          updateKingLivesUI();
          
          // Hide pre-game area immediately
          $('#game-pre-area').hide();
          
          // Lock viewport for gameplay
          $('body').addClass('game-active');
          
          // Show gameplay arena wrapper
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
          
          // Start clocks & reset history
          resetClocks();
          startClocks();
          window.gameMoves = [];
          renderMoveHistory();
          $('.square-55d63').removeClass('last-move-highlight in-check');
          removeHighlights();
          updateTurnHighlight();
          updateCapturedPiecesUI();
          
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
