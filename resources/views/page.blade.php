<div class="panel panel-default" style="margin: 2em">
    <div class="panel-body ">
        <img class="device-icon-header pull-left img-circle" style="vertical-align:middle"
            src="https://avatars.githubusercontent.com/u/13834451?s=400&u=ff8417db6126da8d9ff82822ea0be5897ad744b3&v=4">

        <div>
            <span style="font-size: 18px;">{{ $info['title'] }}</span><br>
            <p>
                Author : {{ $info['author'] }}<br>
                Version : {{ $info['ver'] }}
            </p>
        </div>
        <div style="margin-top: 2em">
            <a href="{{ $info['settings'] }}" class="btn btn-primary btn-lg" role="button">Settings</a>
        </div>
    </div>
</div>
