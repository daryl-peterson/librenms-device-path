@extends('layouts.librenmsv1')

@section('content')
    <div id="device-paths-flash-messages" style="margin-top:-12px; padding-bottom: 1em;">
        @includeIf("$plugin::layouts.flash-messages")
    </div>

    @component("$plugin::layouts.panel")
        @includeIf("$plugin::partials.author")

        <form method="post">
            @csrf

            <div class="form-group">

                <label for="value-interval"><strong>Interval (hours) :</strong></label>
                <input type="text" id="value-interval" name="settings[interval]" class="form-control" value="{{ $settings['interval'] }}">

            </div>

            <div class="form-group">

                <label for="value-force_build"><strong>Force Build</strong></label>
                <select id="value-force_build" name="settings[force_build]" class="form-control" class="form-control">
                    @if (empty($settings['force_build']))
                        <option value="0" selected>Off</option>
                        <option value="1">On</option>
                    @else
                        <option value="0">Off</option>
                        <option value="1" selected>On</option>
                    @endif
                </select>
            </div>
            <button class="btn btn-primary" type="submit">Save</button>
        </form>
    @endcomponent
@endsection
