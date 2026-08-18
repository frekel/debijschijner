<section class="cms-section cms-quote">
    <div class="cms-container">
        <div class="element element-icon">
            <div class="element-icon-box"><i class="fas fa-quote-left"></i></div>
        </div>

        @if($quoteText && $quoteText !== '')
            <div class="section-cms-quote element element-advanced-text">
                <div class="text-wrap">
                    <div class="text"><br>{!! nl2br(e($quoteText)) !!}</div>
                </div>
            </div>
        @endif

        @if($quoteAuthor && $quoteAuthor !== '')
            <div class="element element-advanced-text">
                <div class="text-wrap"><p class="text">{{ $quoteAuthor }}</p></div>
            </div>
        @endif
    </div>
</section>
