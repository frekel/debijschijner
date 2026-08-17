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
    <link rel="alternate" type="application/rss+xml" title="Verlicht jouw onderwijs! &raquo; reactiesfeed"
          href="/comments/feed/"/>
    <link rel='stylesheet' id='wp-block-library-css' href='/css/includes/css/dist/block-library/style.min.css?ver=6.6.2' type='text/css' media='all'/>
    <link rel='stylesheet' id='magnific-popup-css' href='/css/gutentor/assets/library/magnific-popup/magnific-popup.min.css?ver=1.8.0' type='text/css' media='all'/>
    <link rel='stylesheet' id='slick-css' href='/css/gutentor/assets/library/slick/slick.min.css?ver=1.8.1' type='text/css' media='all'/>
    <link rel='stylesheet' id='fontawesome-css' href='/css/gutentor/assets/library/fontawesome/css/all.min.css?ver=5.12.0' type='text/css' media='all'/>
    <link rel='stylesheet' id='wpness-grid-css' href='/css/gutentor/assets/library/wpness-grid/wpness-grid.min.css?ver=1.0.0' type='text/css' media='all'/>
    <link rel='stylesheet' id='animate-css' href='/css/gutentor/assets/library/animatecss/animate.min.css?ver=3.7.2' type='text/css' media='all'/>
    <link rel='stylesheet' id='wp-components-css' href='/css/includes/css/dist/components/style.min.css?ver=6.6.2' type='text/css' media='all'/>
    <link rel='stylesheet' id='wp-preferences-css' href='/css/includes/css/dist/preferences/style.min.css?ver=6.6.2' type='text/css' media='all'/>
    <link rel='stylesheet' id='wp-block-editor-css' href='/css/includes/css/dist/block-editor/style.min.css?ver=6.6.2' type='text/css' media='all'/>
    <link rel='stylesheet' id='wp-reusable-blocks-css' href='/css/includes/css/dist/reusable-blocks/style.min.css?ver=6.6.2' type='text/css' media='all'/>
    <link rel='stylesheet' id='wp-patterns-css' href='/css/includes/css/dist/patterns/style.min.css?ver=6.6.2' type='text/css' media='all'/>
    <link rel='stylesheet' id='wp-editor-css' href='/css/includes/css/dist/editor/style.min.css?ver=6.6.2' type='text/css' media='all'/>
    <link rel='stylesheet' id='dashicons-css' href='/css/includes/css/dashicons.min.css?ver=6.6.2' type='text/css' media='all'/>
    <link rel='stylesheet' id='css' href='/css/gutentor/dist/blocks.style.build.css?ver=4.0.5' type='text/css' media='all'/>
    <link rel='stylesheet' id='me-spr-block-styles-css' href='/css/simple-post-redirect/css/block-styles.min.css?ver=6.6.2' type='text/css' media='all'/>
    <link rel='stylesheet' id='debijschijner-googleapis-css' href='/css/external/fonts/google/ea95bddfec72.css' type='text/css' media='all'/>
    <link rel='stylesheet' id='bootstrap-css' href='/css/debijschijner/assets/library/bootstrap/css/bootstrap.min.css?ver=3.3.6' type='text/css' media='all'/>
    <link rel='stylesheet' id='font-awesome-css' href='/css/debijschijner/assets/library/Font-Awesome/css/font-awesome.min.css?ver=4.7.0' type='text/css' media='all'/>
    <link rel='stylesheet' id='jquery-bxslider-css' href='/css/debijschijner/assets/library/bxslider/css/jquery.bxslider.min.css?ver=4.2.5' type='text/css' media='all'/>
    <link rel='stylesheet' id='debijschijner-style-css' href='/css/debijschijner/style.css?ver=1.0.1' type='text/css' media='all'/>
    <link rel='stylesheet' id='debijschijner-block-front-styles-css' href='/css/debijschijner/fromthecity/gutenberg/gutenberg-front.css?ver=1.0' type='text/css' media='all'/>
    <link rel='stylesheet' id='easy-notification-bar-css' href='/css/easy-notification-bar/assets/css/front.css?ver=1.6.1' type='text/css' media='all'/>
    <link rel='stylesheet' id='debijschijner-custom-extracted-css' href='/css/debijschijner/bijschijner.css' type='text/css' media='all'/>
    <link rel='stylesheet' id='google-fonts-css' href='/css/external/fonts/google/e02796c9075c.css' type='text/css' media='all'/>
    
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
<body class="page-template-default page active no-sidebar">

    @include('cms.partials.notification')

    @include('cms.partials.header')
  
    <div class="clearfix"></div>
    
    @yield('content')
  
    <div class="clearfix"></div>
  
    @include('cms.partials.footer')

    <link rel='stylesheet' id='missing-global-css' href='/css/gutentor/assets/css/global/global.css?ver=4.0.5' type='text/css' media='all' />
    <link rel='stylesheet' id='missing-widget-global-css' href='/css/gutentor/assets/css/widget/widget-global.css?ver=4.0.5' type='text/css' media='all' />
    <link rel='stylesheet' id='missing-content-box-css' href='/css/gutentor/assets/css/widget/content-box.css?ver=4.0.5' type='text/css' media='all' />
    <link rel='stylesheet' id='missing-social-css' href='/css/gutentor/assets/css/widget/social.css?ver=4.0.5' type='text/css' media='all' />
    
    <script type="text/javascript" src="/js/gutentor/assets/library/wow/wow.min.js?ver=1.2.1" id="wow-js"></script>
    <script type="text/javascript" src="/js/debijschijner/fromthecity/core/js/skip-link-focus-fix.js?ver=20130115" id="debijschijner-skip-link-focus-fix-js"></script>
    <script type="text/javascript" src="/js/debijschijner/assets/library/bootstrap/js/bootstrap.min.js?ver=3.3.6" id="bootstrap-js"></script>
    <script type="text/javascript" src="/js/debijschijner/assets/library/bxslider/js/jquery.bxslider.js?ver=4.2.5.1" id="jquery-bxslider-js"></script>
    <script type="text/javascript" src="/js/debijschijner/assets/library/jquery-parallax/jquery.parallax.js?ver=1.1.3" id="parallax-js"></script>
    <script type="text/javascript" src="/js/debijschijner/assets/js/debijschijner-custom.js?ver=1.0.2" id="debijschijner-custom-js"></script>
    <script type="text/javascript" src="/js/google-site-kit/dist/assets/js/googlesitekit-consent-mode-86cb52dcb9f2b27ed244.js" id="googlesitekit-consent-mode-js"></script>
    <script type="text/javascript" src="/js/wp-consent-api/assets/js/wp-consent-api.min.js?ver=2.0.1" id="consent-api-js"></script>
    <script type="text/javascript" src="/js/gutentor/assets/js/gutentor.min.js?ver=4.0.5" id="block-js"></script>
</body>
</html>
