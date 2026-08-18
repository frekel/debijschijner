@php
    $hasChildren = count($item['children'] ?? []) > 0;
    $classes = ['menu-item', 'menu-item-type-post_type', 'menu-item-object-page'];

    if ($hasChildren) {
        $classes[] = 'menu-item-has-children';
    }

    if (!empty($item['isActive'])) {
        $classes[] = 'current-menu-item';
        $classes[] = 'current_page_item';
    }

    if (!empty($item['hasActiveDescendant'])) {
        $classes[] = 'current-menu-ancestor';
        $classes[] = 'current-menu-parent';
        $classes[] = 'current_page_parent';
        $classes[] = 'current_page_ancestor';
    }
@endphp

<li class="{{ implode(' ', $classes) }}">
    <a href="{{ $item['url'] }}" @if(!empty($item['isActive'])) aria-current="page" @endif>{{ $item['label'] }}</a>

    @if($hasChildren)
        <ul class="sub-menu">
            @foreach($item['children'] as $child)
                @include('cms.partials.menu-item', ['item' => $child])
            @endforeach
        </ul>
    @endif
</li>