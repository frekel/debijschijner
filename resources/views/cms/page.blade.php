<!DOCTYPE html>
<html lang="nl-NL">
<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="profile" href="/xfn/11">
    <link rel="pingback" href="/xmlrpc.php">
    <title>{{ $title ?? 'Verlicht jouw onderwijs!' }}</title>
    <meta name='robots' content='max-image-preview:large'/>

    <script type="text/javascript" src="/js/debijschijner/bijschijner.js" defer></script>

    <!-- Open Graph Meta Tags - Dynamic from CMS -->
    <meta property="og:title" content="{{ $title ?? 'Verlicht jouw onderwijs!' }}"/>
    <meta property="og:description" content="{{ Illuminate\Support\Str::limit(strip_tags($metaDescription ?? ''), 160, '') }}"/>
    <meta property="og:url" content="{{ $canonicalUrl ?? url('/') }}"/>
    @if($ogImage)
    <meta property="og:image" content="{{ $ogImage }}"/>
    <meta property="og:image:type" content="image/jpeg"/>
    @endif
    <meta property="og:type" content="article"/>
    @if(isset($page) && $page->created_at)
    <meta property="og:article:published_time" content="{{ $page->created_at->toIso8601String() }}"/>
    @endif
    @if(isset($page) && $page->updated_at)
    <meta property="og:article:modified_time" content="{{ $page->updated_at->toIso8601String() }}"/>
    @endif

    <!-- Twitter Card - Dynamic from CMS -->
    <meta name="twitter:card" content="summary">
    <meta name="twitter:title" content="{{ $title ?? 'Verlicht jouw onderwijs!' }}"/>
    <meta name="twitter:description" content="{{ Illuminate\Support\Str::limit(strip_tags($metaDescription ?? ''), 160, '') }}"/>
    @if($ogImage)
    <meta name="twitter:image" content="{{ $ogImage }}"/>
    @endif

    <meta name="author" content="admin"/>

    <link rel="alternate" type="application/rss+xml" title="Verlicht jouw onderwijs! &raquo; feed" href="/feed/"/>
    <link rel="alternate" type="application/rss+xml" title="Verlicht jouw onderwijs! &raquo; reactiesfeed" href="/comments/feed/"/>

    <link rel='stylesheet' id='slick-css' href='/css/gutentor/assets/library/slick/slick.min.css?ver=1.8.1' type='text/css' media='all'/>

    <link rel='stylesheet' id='fontawesome-css' href='/css/gutentor/assets/library/fontawesome/css/all.min.css?ver=5.12.0' type='text/css' media='all'/>
    <link rel='stylesheet' id='wpness-grid-css' href='/css/gutentor/assets/library/wpness-grid/wpness-grid.min.css?ver=1.0.0' type='text/css' media='all'/>
    <link rel='stylesheet' id='animate-css' href='/css/gutentor/assets/library/animatecss/animate.min.css?ver=3.7.2' type='text/css' media='all'/>
    <link rel='stylesheet' id='bootstrap-css' href='/css/debijschijner/assets/library/bootstrap/css/bootstrap.min.css?ver=3.3.6' type='text/css' media='all'/>
    <link rel='stylesheet' id='jquery-bxslider-css' href='/css/debijschijner/assets/library/bxslider/css/jquery.bxslider.css?ver=4.2.5' type='text/css' media='all'/>
    <link rel='stylesheet' id='ebijschijner-easy-notification-bar-css' href='/css/debijschijner/assets/library/easy-notification-bar/css/front.css?ver=1.6.1' type='text/css' media='all'/>
    <link rel='stylesheet' id='debijschijner-custom-css' href='/css/debijschijner/bijschijner.css' type='text/css' media='all'/>

    <link rel='stylesheet' id='missing-global-css' href='/css/gutentor/assets/css/global/global.css?ver=4.0.5' type='text/css' media='all' />
    <link rel='stylesheet' id='missing-widget-global-css' href='/css/gutentor/assets/css/widget/widget-global.css?ver=4.0.5' type='text/css' media='all' />
    <link rel='stylesheet' id='missing-content-box-css' href='/css/gutentor/assets/css/widget/content-box.css?ver=4.0.5' type='text/css' media='all' />
    <link rel='stylesheet' id='missing-social-css' href='/css/gutentor/assets/css/widget/social.css?ver=4.0.5' type='text/css' media='all' />

    <script type="text/javascript" src="/js/includes/js/jquery/jquery.min.js?ver=3.7.1" id="jquery-core-js"></script>
    <script type="text/javascript" src="/js/includes/js/jquery/jquery-migrate.min.js?ver=3.4.1" id="jquery-migrate-js"></script>
    <script type="text/javascript" src="/js/strato-assistant/js/cookies.js?ver=1724239156" id="strato-assistant-wp-cookies-js"></script>
    <!--[if lt IE 9]>
    <script type="text/javascript" src="/js/debijschijner/assets/library/html5shiv/html5shiv.min.js?ver=3.7.3" id="html5-js"></script>
    <![endif]-->
    <!--[if lt IE 9]>
    <script type="text/javascript" src="/js/debijschijner/assets/library/respond/respond.min.js?ver=1.1.2" id="respond-js"></script>
    <![endif]-->

    <!-- Google Analytics snippet -->
    <script type="text/javascript" src="/js/external/googletag/gtag.js" id="google_gtagjs-js" async></script>
    <link rel="https://api.w.org/" href="/wp-json/"/>
    <link rel="EditURI" type="application/rsd+xml" title="RSD" href="/xmlrpc.php?rsd"/>
    <meta name="generator" content="WordPress 6.6.2"/>
    @if($canonicalUrl)
    <link rel='canonical' href="{{ $canonicalUrl }}"/>
    @endif

</head>

    @extends($layoutView ?? 'cms.layouts.default')

    @section('content')
        {!! $contentHtml !!}
    @endsection

</html>
