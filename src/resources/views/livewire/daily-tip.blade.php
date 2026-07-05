<div class="sidebar-widget tip-widget" style="position: relative;">
  <div class="tip-header" style="display: flex; justify-content: space-between; align-items: center; width: 100%;">
    <div style="display: flex; align-items: center; gap: 0.5rem;">
      <svg class="tip-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width: 18px; height: 18px;">
        <path d="M15 14c.2-1 .7-1.7 1.5-2.5 1-.9 1.5-2.2 1.5-3.5A5 5 0 0 0 8 8c0 1 .3 2.2 1.5 3.5.7.7 1.3 1.5 1.5 2.5" />
        <line x1="9" y1="18" x2="15" y2="18" />
        <line x1="10" y1="22" x2="14" y2="22" />
      </svg>
      <span style="font-weight: 500;">Chess Tip</span>
    </div>
    <button wire:click="refreshTip" class="refresh-tip-btn" style="background: none; border: none; color: var(--gold-lt); cursor: pointer; display: flex; align-items: center; padding: 2px;" title="Get new tip">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="width: 16px; height: 16px;">
        <path d="M21.5 2v6h-6M21.34 15.57a10 10 0 1 1-.57-8.38l5.67-5.67" />
      </svg>
    </button>
  </div>
  <p class="tip-text" style="margin-top: 0.75rem; font-style: italic; color: #d1d5db; line-height: 1.4;">"{{ $tipText }}"</p>
  @if($author)
    <span class="tip-author" style="display: block; text-align: right; font-size: 0.75rem; color: #9ca3af; margin-top: 0.25rem;">&mdash; {{ $author }}</span>
  @endif
</div>
