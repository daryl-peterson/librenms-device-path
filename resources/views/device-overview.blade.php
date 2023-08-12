<div class="row">
    <div class="col-md-12">
        <div class="panel panel-default panel-condensed">
            <div class="panel-heading">
                <i class="fa fa-road fa-lg" aria-hidden="true"></i>
                <strong>{{ $title }}</strong>
            </div>
            <div class="panel-body">
                <div class="row">
                    <div class="col-sm-12">
                        <table class="table table-condensed">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Device</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($device_path as $name => $id)
                                    <tr>
                                        <td><a href="{{ url('') }}/device/{{ $id }}" target="_blank"
                                                title="Show device">{{ $id }}</a></td>
                                        <td>{{ $name }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <a href="{{ $device_tree }}" target="_blank" class="btn btn-primary">Device Tree</a>
                        <a href="{{ $group_tree }}" target="_blank" class="btn btn-primary">Group Tree</a>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
