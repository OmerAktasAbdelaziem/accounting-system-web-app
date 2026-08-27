@props([
    'badge' => null,
    'title' => null,
    'description' => null,
])

<div class="products-hero">
    <div class="products-hero-grid">
        <div class="products-hero-copy">
            @if($badge)
                <div class="products-hero-badge">{!! $badge !!}</div>
            @endif

            <div>
                <h1 class="products-hero-title">{!! $title !!}</h1>
                @if($description)
                    <p class="products-hero-text">{!! $description !!}</p>
                @endif
            </div>

            <div class="products-hero-actions">
                {{ $actions ?? '' }}
            </div>
        </div>

        <div class="products-hero-panel">
            {{ $panel ?? '' }}
        </div>
    </div>
</div>

<style>
    /* Minimal hero styles bundled with component to ensure consistent layout */
    .products-hero { border-radius: 30px; padding: 24px; background: linear-gradient(135deg, rgba(17,24,39,0.96), rgba(31,41,55,0.92)); color:#fff; border:1px solid rgba(255,255,255,0.08); box-shadow:0 28px 70px rgba(15,23,42,0.2); max-width: 1180px; margin: 0 auto; }
    /* Use flexible left content with a fixed-width right panel to avoid large empty areas */
    .products-hero-grid { display:grid; grid-template-columns: 1fr 340px; gap:18px; align-items:stretch }
    .products-hero-copy { display:flex; flex-direction:column; justify-content:space-between; gap:18px }
    .products-hero-badge { display:inline-flex; align-items:center; gap:8px; padding:8px 14px; border-radius:999px; background:rgba(255,255,255,0.08); border:1px solid rgba(255,255,255,0.10); color:#fff; font-weight:700; font-size:12px; letter-spacing:.06em; text-transform:uppercase }
    .products-hero-title { margin:0; font-size:clamp(2rem,4vw,3.4rem); line-height:0.95; letter-spacing:-0.05em; font-weight:900; color:#fff }
    .products-hero-text { margin:0; color:rgba(255,255,255,0.78); font-size:15px; line-height:1.7; max-width:760px }
    .products-hero-actions { display:flex; flex-wrap:wrap; gap:10px; align-items:center }
    .products-hero-panel { border-radius:26px; padding:18px; background:rgba(255,255,255,0.08); border:1px solid rgba(255,255,255,0.10); display:grid; gap:12px; align-content:space-between }

    @media (min-width: 993px) { .products-hero-badge { display: none !important; } }
    @media (max-width: 992px) { .products-hero-grid { grid-template-columns: 1fr } .products-hero-actions .btn { width:100% } }
</style>
