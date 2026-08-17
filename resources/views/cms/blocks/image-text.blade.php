<section class="cms-section">
    <div class="cms-container cms-image-text grid-container">
        <div class="grid-row">
            <div class="grid-col grid-col-6 grid-md-6">
                @if($image) 
                    <div class="single-item-image-box cms-image">
                        <div class="image-thumb">
                            <img src="{{ $image }}" alt="{{ $alt }}">
                        </div>
                    </div> 
                @endif 
            </div>
            <div class="grid-col grid-col-6 grid-md-6">
                <div class="cms-copy single-item-content"> 
                    @if($heading !== '') <h2>{{ $heading }}</h2> @endif 
                    <div class="cms-richtext single-item-desc">{!! $body !!}</div>
                </div>
            </div>
        </
    </div>
</section>
