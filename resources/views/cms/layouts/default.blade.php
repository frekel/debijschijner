<body class="page-template-default page active no-sidebar">

    @include('cms.partials.notification')

    @include('cms.partials.header')

    @include('cms.partials.header-title')

    <div id="content" class="site-content">
        <div id="primary" >
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