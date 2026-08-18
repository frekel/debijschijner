<section class="cms-section cms-image-text cms-publication-detail">
    <div class="cms-container">
        <div class="grid-row">
            <div class="grid-col grid-col-6 grid-md-6">
                <div class="cms-publication-body">
                    @if($publication->title !== '')
                        <h3>"{{ $publication->title }}"</h3>
                    @endif

                    <div class="cms-richtext">{!! $publication->text !!}</div>
                </div>
            </div>
            <div class="grid-col grid-col-6 grid-md-6">
                @if($image)
                    <div class="cms-publication-hero">
                        <img src="{{ $image }}" alt="{{ $publication->title }}">
                    </div>
                @endif
            </div>
        </div>
    </div>
</section>
