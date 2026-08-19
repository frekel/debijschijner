<body class="page-template-homepage page active no-sidebar">

    @include('cms.partials.notification')

    @include('cms.partials.header')

    <div class="home-bxslider full-screen-bg">
        <section id="at-banner-slider" class="home-fullscreen  at-parallax num- full-screen-bg " style="background-image: url(&quot;/images/uploads/2024/09/cropped-Meer-Primair-foto.jpg&quot;); background-repeat: no-repeat; background-size: cover; background-attachment: fixed; background-position: center bottom;">
            <div class="slide at-slide-wrap">
                <div class="bx-wrapper" style="max-width: 100%;"><div class="bx-viewport" aria-live="polite" style="width: 100%; overflow: hidden; position: relative; height: 561px;"><ul class="text-slider at-featured-text-slider clearfix" style="display: block; width: auto; position: relative;">
                    <li class="clearfix" style="float: none; list-style: none; position: absolute; width: 1847px; z-index: 50; display: block;" aria-hidden="false">
                        <div class="at-overlay">
                            <div class="container text-slider-wrapper">
                                <span class="lead banner-title init-animate fadeInRight" style="visibility: visible; animation-name: fadeInRight;">Welkom bij De Bijschijner</span>
                                <div class="banner-title-line line init-animate fadeInLeft" style="visibility: visible; animation-name: fadeInLeft;"><span></span></div>
                                <div class="text-slider-caption init-animate fadeInDown" style="visibility: visible; animation-name: fadeInDown;">
                                </div>
                            </div>
                        </div>
                    </li>
                </ul></div><div class="bx-controls bx-has-controls-direction"><div class="bx-controls-direction"><a class="bx-prev disabled" href=""><i class="fa fa-angle-left fa-3x"></i></a><a class="bx-next disabled" href=""><i class="fa fa-angle-right fa-3x"></i></a></div></div></div>
            </div>
        </section>
    </div>
    
    <div class="clearfix"></div>

    <div id="content" class="site-content">
        <div id="primary">
            <main id="main" class="site-main" role="main">
                <article class="hentry">
                    <div class="single-feat clearfix"></div><!-- .single-feat-->
                    <div class="content-wrapper">
                        @yield('content')
                    </div><!-- .entry-content -->
                </article><!-- #post-## -->
            </main><!-- #main -->
        </div><!-- #primary -->
    </div><!-- #content -->

    <div class="clearfix"></div>

    @include('cms.partials.footer')

    @include('cms.partials.footer-js')

</body>
