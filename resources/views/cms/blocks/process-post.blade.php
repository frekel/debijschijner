<div class="process-post-item single-item single-item-{{ $itemIndex }} {{ $parityClass }}">
    <div class="timeline-item">
        <div class="single-item-wrap">
            <div class="timeline-item-circle"></div>
            @if($time !== '')
                <span class="timeline-item-time">{{ $time }}</span>
            @endif
            <div class="single-item-wrapper">
                <div class="single-item-content timeline-item-content">
                    <h3 class="single-item-title">{{ $title }}</h3>
                    <p class="single-item-desc">{{ $text }}</p>
                </div>
            </div>
        </div>
    </div>
</div>
