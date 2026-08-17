<section class="cms-section cms-hero"> <div class="cms-container"> <h1>{{ $heading }}</h1> @if($subheading !== '') <p class="cms-subheading">{{ $subheading }}</p> @endif @if($buttonText !== '') <p><a class="cms-button" href="{{ $buttonUrl }}">{{ $buttonText }}</a></p> @endif </div>
</section>
