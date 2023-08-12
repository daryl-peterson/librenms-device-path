<div class="container-fluid" style="margin: 15px;">


    <!-- Example of free-form settings, real plugins should use specific fields -->
    <!-- All input fields should be in the settings array (settings[]) -->

    <div class="row">
        <div class="col-sm-6">

            <div class="panel panel-default">
                <div class="panel-heading">
                    <h4>{{ $plugin_name }} Settings</h4>
                </div>
                <div class="panel-body">

                    <form method="post">
                        @csrf

                        <div class="form-group">

                            <label for="value-interval"><strong>Interval (hours) :</strong></label>
                            <input type="text" id="value-interval" name="settings[interval]" class="form-control"
                                value="{{ $settings['interval'] }}">

                        </div>

                        <div class="form-group">

                            <label for="value-force_build"><strong>Force Build</strong></label>
                            <select id="value-force_build" name="settings[force_build]" class="form-control" class="form-control">
                                @if (!isset($settings['force_build']))
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
                </div>
            </div>
        </div>
    </div>
</div>
