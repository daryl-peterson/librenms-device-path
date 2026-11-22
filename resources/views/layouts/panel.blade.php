<div class="container-fluid">
    <div class="col-sm-12 {{ ($wide ?? false) ? 'col-lg-8' : 'col-lg-6' }}">
        <div class="panel panel-default">
            <div class="panel-heading">
                @include('device-backups::partials.breadcrumb')
            </div>
            <div class="panel-body">
                {{ $slot }}
            </div>
        </div>
    </div>
</div>
