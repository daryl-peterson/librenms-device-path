<div style="margin-top:-12px; padding-bottom: 1em;">
    @includeIf('device-paths::layouts.flash-messages')
</div>
@component($info['name'] . '::layouts.panel')
    @includeIf('device-paths::partials.author')
@endcomponent


@push('scripts')
    <script>
        $(document).ready(function() {
            console.log("LibreNMS custom script running!");
            $(".alert").delay(5000).fadeOut(500, function() {
                $(this).alert('close');
            });
        });
    </script>
@endpush
