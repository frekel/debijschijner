<section class="cms-section cms-quote"> <div class="cms-container"> <div class="cms-quote-icon"><i class="fas fa-quote-left"></i></div> @if($quoteText !== '') <blockquote class="cms-quote-text">{!! nl2br(e($quoteText)) !!}</blockquote> @endif @if($quoteAuthor !== '') <p class="cms-quote-author">{{ $quoteAuthor }}</p> @endif </div>
</section>
