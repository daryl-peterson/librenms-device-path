@php
    $pluginName = \DRP\DevicePaths\PLUGIN_NAME;
    $links = [['Plugin', route('plugin.page', ['plugin' => $pluginName]), ['plugin.page']]];

    $admin = auth()->user()?->hasRole('admin');

    if ($admin) {
        $links[] = ['Settings', route('device-paths.settings'), ['device-paths.settings']];
    }
@endphp

<nav class="tw:flex tw:justify-between" aria-label="Device Paths">
    <div class="tw:min-h-8 tw:p-2">
        <strong style="font-size: 1.1em;">{{ \DRP\DevicePaths\PLUGIN_TITLE }}</strong> »
        @foreach ($links as [$label, $url, $routes])
            @if (!$loop->first)
                <span aria-hidden="true"> | </span>
            @endif
            @php($selected = request()->routeIs(...$routes))
            <span @class(['pagemenu-selected' => $selected])>
                <a href="{{ $url }}" class="sync-filter-url"
                    @if ($selected) aria-current="page" @endif>{{ $label }}</a>
            </span>
        @endforeach
    </div>
</nav>
