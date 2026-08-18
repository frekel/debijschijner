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
    </div>
</section>
