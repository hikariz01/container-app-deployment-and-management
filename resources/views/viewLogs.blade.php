@extends('layouts.app2', ['namespaces'=>$namespaces])


@section('content')
    <div class="container">
        <div class="row mt-4 mb-2">
            <div class="col-12" style="display: inline-flex">
                <h4 style="margin: auto 10px auto 0">Logs of </h4>
                <a href="{{ url()->previous() }}">
                    <button class="btn btn-primary">
                        {{$resource->getKind()}} [{{$resource->getName()}}]
                    </button>
                </a>
            </div>
        </div>
        <div style="width: 100%; height: calc(100vh - 130px)">
            <div id="logs" style="position: relative; height: 100%; width: 100%">{{$message}}</div>
        </div>
    </div>

@endsection

@section('js')

    <script>
        let logsViewer = document.querySelector('#logs')
        let logsViewCodeBox = ace.edit(logsViewer);

        logsViewCodeBox.setTheme('ace/theme/monokai')
        logsViewCodeBox.session.setMode("ace/mode/logtalk");

        logsViewCodeBox.setReadOnly(true)

    </script>

@endsection
