<body class="page-template-form page active no-sidebar">

    @include('cms.partials.notification')

    @include('cms.partials.header')

    <div class="clearfix"></div>
    <div class="inner-main-title">
        <header class="entry-header">
            <h1 class="entry-title">{{ $title ?? 'De Bijschijner' }} - Verlicht jouw onderwijs!</h1>
        </header>
    </div>

    <div id="content" class="site-content">
        <div id="primary">
            <main id="main" class="site-main" role="main">
                <article class="hentry">
                    <div class="single-feat clearfix"></div>
                    <div class="content-wrapper">
                        <section class="cms-section cms-form-template">
                            <div class="cms-container">
                                @if(($page->form_title ?? '') !== '')
                                    <div class="cms-form-block-header">
                                        <h2>{{ $page->form_title }}</h2>
                                    </div>
                                @endif

                                <div class="cms-form-block-grid">
                                    <div class="cms-form-block-form">
                                        @yield('content')
                                    </div>

                                    <aside class="cms-form-block-sidebar">
                                        <div class="cms-form-info-cards">
                                            <article class="cms-form-info-card">
                                                <h3>Email:</h3>
                                                <div class="cms-form-info-card-text">debora@debijschijner.nl</div>
                                            </article>
                                            <article class="cms-form-info-card">
                                                <h3>Telefoon/Whatsapp:</h3>
                                                <div class="cms-form-info-card-text">06-24881874</div>
                                            </article>
                                            <article class="cms-form-info-card">
                                                <h3>Adres</h3>
                                                <div class="cms-form-info-card-text">
                                                    Zuidgors 20<br/>
                                                    2134WE Hoofddorp
                                                </div>
                                            </article>
                                            <article class="cms-form-info-card">
                                                <h3>Overige</h3>
                                                <div class="cms-form-info-card-text">KVK: 95218661</div>
                                            </article>
                                            <article class="cms-form-info-card">
                                                <div class="cms-form-info-card-text">
                                                    Jouw contactgegevens gebruik ik alleen om contact met je op te nemen. Zie hiervoor ook mijn <a href="/privacy-policy/" data-type="page" data-id="3">privacyverklaring</a>
                                                </div>
                                            </article>
                                        </div>
                                    </aside>
                                </div>
                            </div>
                        </section>
                    </div>
                </article>
            </main>
        </div>
    </div>

    <div class="clearfix"></div>

    @include('cms.partials.footer')

    @include('cms.partials.footer-js')

</body>