@php
    $reviewerSlug = \Illuminate\Support\Str::slug(\Illuminate\Support\Str::lower($reviewerName));
@endphp

<article class="review-card">
    <div class="review-card-media" @if($image) style="background-image: url('{{ $image }}');" @endif>
        <div class="review-card-overlay">
            <p class="review-card-name">{{ \Illuminate\Support\Str::upper($reviewerName) }}</p>
            @if($buttonText !== '')
                <p class="review-card-button-text">"{{ $buttonText }}..."</p>
            @endif
            <br/>
            <a class="review-card-button" href="/ervaringen/{{ $reviewerSlug }}">
                <span>Lees meer</span>
                <i class="gutentor-button-icon fas fa-arrow-right"></i>
            </a>
        </div>
    </div>

</article>
