<section class="cms-section cms-image-text cms-reviewer-detail">
    <div class="cms-container">
        <div class="grid-row">
            <div class="grid-col grid-col-6 grid-md-6">
                <div class="cms-copy">
                    @if($title !== '')
                        <h3>"{{ $title }}"</h3>
                    @endif

                    <div class="cms-richtext">{!! $text !!}</div>
                </div>
            </div>
            <div class="grid-col grid-col-6 grid-md-6">
                @if($image)
                    <div class="cms-image">
                        <div class="image-thumb">
                            <img src="{{ $image }}" alt="{{ $reviewerName }}">
                        </div>
                    </div>
                @endif
            </div>
        </div>

        @if($previousReviewer || $nextReviewer)
            <div class="cms-reviewer-detail-nav">
                @if($previousReviewer)
                    <a class="cms-reviewer-detail-nav-button cms-reviewer-detail-nav-prev" href="{{ $previousReviewer['url'] }}">
                        <span class="cms-reviewer-detail-nav-arrow">&larr;</span>
                        <span>{{ $previousReviewer['name'] }}</span>
                    </a>
                @endif

                @if($nextReviewer)
                    <a class="cms-reviewer-detail-nav-button cms-reviewer-detail-nav-next" href="{{ $nextReviewer['url'] }}">
                        <span>{{ $nextReviewer['name'] }}</span>
                        <span class="cms-reviewer-detail-nav-arrow">&rarr;</span>
                    </a>
                @endif
            </div>
        @endif
    </div>
</section>
