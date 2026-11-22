@php
    $pluginName = \DRP\DeviceBackups\PLUGIN_NAME;
    $links = [['Plugin', route('plugin.page', ['plugin' => $pluginName]), ['plugin.page']]];

    $admin = auth()->user()?->hasRole('admin');
    if ($admin && \DRP\DeviceBackups\PluginDb::isReady($info['settings'] ?? null)) {
        $links = array_merge($links, [
            ['Status', route('device-backups.backups.status'), ['device-backups.backups.status']],
            [
                'By Device',
                route('device-backups.backups.devices'),
                ['device-backups.backups.devices', 'device-backups.backups.history'],
            ],
            ['SSH Status', route('device-backups.backups.ssh.index'), ['device-backups.backups.ssh.*']],
            ['Schedule', route('device-backups.backups.schedule'), ['device-backups.backups.schedule']],
        ]);
    }
    if ($admin) {
        $links[] = ['Settings', route('device-backups.settings'), ['device-backups.settings']];
    }
@endphp

<nav class="tw:flex tw:justify-between" aria-label="Device Backups">
    <div class="tw:min-h-8 tw:p-2">
        <strong style="font-size: 1.1em;">{{ \DRP\DeviceBackups\PLUGIN_TITLE }}</strong> »
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
