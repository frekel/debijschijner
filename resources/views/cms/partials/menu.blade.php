<div class="main-navigation navbar-collapse collapse">
    <div class="menu-desktop-menu-container">
        <ul id="primary-menu" class="nav navbar-nav navbar-right animated fromthecity-normal-page">
            @foreach(($menuItems ?? []) as $item)
                @include('cms.partials.menu-item', ['item' => $item])
            @endforeach
        </ul>
    </div>
</div>
