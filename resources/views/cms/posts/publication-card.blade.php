<article class="cms-publication-card">
    @if($image)
        <a class="cms-publication-card-image" href="{{ $url }}">
            <img src="{{ $image }}" alt="{{ $title }}">
        </a>
    @endif

    <div class="cms-publication-card-content">
        <h3><a href="{{ $url }}">{{ $title }}</a></h3>
        <div class="cms-publication-card-text">{!! $buttonText !!}</div>
        <a class="cms-publication-card-button" href="{{ $url }}">                
            <span>Lees meer</span>
            <i class="gutentor-button-icon fas fa-arrow-right"></i>
        </a>
    </div>
</article>