<section class="cms-section cms-image-text">
    <div class="cms-container ">
        <div class="grid-row">
            <div class="grid-col grid-col-6 grid-md-6">
                @if($image)
                    <div class="cms-image">
                        <div class="image-thumb">
                            <img src="{{ $image }}" alt="{{ $alt }}">
                        </div>
                    </div> 
                @endif 
            </div>
            <div class="grid-col grid-col-6 grid-md-6">
                <div class="cms-copy">
                    @if($heading !== '') <h2>{{ $heading }}</h2> @endif 
                    <div class="cms-richtext">{!! $body !!}</div>
                </div>
            </div>
        </div>
    </div>
</section>
