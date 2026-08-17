<section class="cms-section"> <div class="cms-container cms-image-text"> @if($image) <div class="cms-image"><img src="{{ $image }}" alt="{{ $alt }}"></div> @endif <div class="cms-copy"> @if($heading !== '') <h2>{{ $heading }}</h2> @endif <div class="cms-richtext">{!! $body !!}</div> </div> </div>
</section>
