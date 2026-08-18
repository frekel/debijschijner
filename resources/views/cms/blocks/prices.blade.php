<article class="price-card">
    <div class="price-card-media">
        <img
            fetchpriority="high"
            decoding="async"
            class="price-card-image"
            src="http://debijschijner.nl/wp-content/uploads/2024/09/logo-e1729264008286.png"
            alt=""
            height="300"
            width="465"
        >
        <div class="price-card-price"><span>&euro;</span><strong>{{ $price }}</strong></div>
    </div>
    <h3 class="price-card-title">{{ $title }}</h3>
    <p class="price-card-text">{!! nl2br(e($text)) !!}</p>
    <a class="price-card-button" href="/aanvraag" rel="/aanvraag">
        <span>Vraag nu aan!</span>
        <i class="gutentor-button-icon fas fa-arrow-right"></i>
    </a>
</article>
