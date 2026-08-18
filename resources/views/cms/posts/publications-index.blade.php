<section class="cms-section cms-publications-index">
    <div class="cms-container">
        <div class="cms-publications-grid">
            @foreach($publications as $publication)
                @include('cms.posts.publication-card', [
                    'title' => (string) ($publication->title ?? ''),
                    'buttonText' => (string) ($publication->button_text ?? ''),
                    'image' => $resolveImageUrl($publication->image),
                    'url' => $resolvePublicationUrl($publication),
                ])
            @endforeach
        </div>
    </div>
</section>