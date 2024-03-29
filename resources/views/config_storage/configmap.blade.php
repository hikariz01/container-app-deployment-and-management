@extends('layouts.app2', ['namespaces'=>$namespaces])

@section('content')

    <style>
        .table td, .table th {
            padding: 5px 0.4rem;
        }
        .codebox {
            border:1px solid black;
            background-color:#EEEEFF;
            white-space: pre-line;
            padding:10px;
            font-size:0.9em;
            display: inline-block;
        }

        code {
            margin-left: -13vw;
            width: 80vw;
        }
    </style>

    @include('layouts.resourceNav')

    <table class="table table-secondary table-borderless" style="padding-left: 30px">
        <thead>
        <tr>
            <td colspan="10">
                <h3 style="padding-left: 30px"id="deployment_table">Metadata</h3>
            </td>
        </tr>
        </thead>
        <tbody>
        <tr>
            <th>Name</th>
            <th>Namespace</th>
            <th>Created</th>
            <th>Age</th>
            <th>UID</th>
        </tr>
        <tr>
            <td>{{$configmap->getName()}}</td>
            <td>{{$configmap->getNamespace()}}</td>
            <td>{{\Carbon\Carbon::createFromTimeString($configmap->toArray()['metadata']['creationTimestamp'], 'UTC')->addHours(7)->toDayDateTimeString()}}</td>
            <td>{{$age}}</td>
            <td>{{$configmap->getResourceUid()}}</td>
        </tr>
        <tr>
            <th colspan="5">Labels</th>
        </tr>
        <tr>
            <td colspan="5">
                @foreach($configmap->toArray()['metadata']['labels']??json_decode('{"":""}') as $key => $label)
                    @if($key == "")
                        -
                    @else
                        <div class="badge badge-pill bg-primary">
                            {{$key}}: {{$label}}
                        </div>
                    @endif
                @endforeach
            </td>
        </tr>
        <tr>
            <th colspan="5">Annotations</th>
        </tr>
        <tr>
            <td colspan="5">
                @foreach($configmap->toArray()['metadata']['annotations']??json_decode('{"":""}') as $key => $value)
                    @if($key == "")
                        -
                    @elseif(is_array(json_decode($value, true)))
                        {{$key}}: <button class="btn btn-outline-primary rounded-pill" data-bs-toggle="modal" data-bs-target="#annoJSON" type="button" onclick="updateJSON(this)" value="{{$key}}">JSON</button><br>
                        <div class="{{$key}}" style="display: none">{{$value}}</div>
                    @else
                        {{$key}}: {{$value}}<br>
                    @endif
                @endforeach
            </td>
        </tr>
        </tbody>
    </table>

    @include('layouts.jsonViewModal')

    <table class="table table-secondary table-borderless" style="padding-left: 30px">
        <thead>
        <tr>
            <td colspan="10">
                <h3 style="padding-left: 30px"id="deployment_table">Data</h3>
            </td>
        </tr>
        </thead>
        <tbody>
        @if(!is_null($configmap->getData()))
            @foreach($configmap->getData() as $key => $data)
                <tr>
                    <th>{{$key}}</th>
                </tr>
                <tr>
                    <td>
{{--                    <pre>--}}
{{--                        <code class="codebox">--}}
{{--                            @if(base64_encode(base64_decode($data, true)) === $data)--}}
{{--                                {{base64_decode($data)}}--}}
{{--                            @else--}}
{{--                                {{$data}}--}}
{{--                            @endif--}}
{{--                        </code>--}}
{{--                    </pre>--}}
                        <div class="codecontainer" style="width: 80vw; height: 30vh; margin-left: auto; margin-right: auto">
{{--                            @if($data === 'true' || $data === 'false')--}}
{{--                                <div class="aceEditor" style="position: relative; height: 100%; width: 100%">{{$data === 'true' ? 'true' : 'false'}}<br>--}}
{{--</div>--}}
{{--                            @elseif(base64_encode(base64_decode($data, true)) === $data)--}}
{{--                                <div class="aceEditor" style="position: relative; height: 100%; width: 100%">{{base64_decode($data)}}<br>--}}
{{--</div>--}}
{{--                            @else--}}
                                <div class="aceEditor" style="position: relative; height: 100%; width: 100%">{{$data}}<br>
</div>
{{--                            @endif--}}
                        </div>
                    </td>
                </tr>
            @endforeach
        @else
            <tr>
                <td>There is no data.</td>
            </tr>
        @endif
        </tbody>
    </table>

    <div id="data" style="display: none">{{\Symfony\Component\Yaml\Yaml::dump($configmap->toArray(), 512, 2)}}</div>

    @include('layouts.editFormModal')

    @include('layouts.deleteFormModal')

@endsection

@section('js')

    @include('layouts.jsonEditor')

    <script>

        let aceEditors = document.getElementsByClassName('aceEditor')
        let aceEditor = []

        for (let i=0; i < aceEditors.length; i++) {
            aceEditor[i] = ace.edit(aceEditors[i])

            aceEditor[i].setTheme('ace/theme/monokai')
            aceEditor[i].session.setMode("ace/mode/text");

            aceEditor[i].setReadOnly(true)

        }

        let aceData = document.querySelector('#data').innerHTML

        let editor = document.querySelector('#editor')
        let aceEditor1 = ace.edit("editor");

        aceEditor1.setTheme('ace/theme/monokai')
        aceEditor1.session.setMode("ace/mode/yaml");

        aceEditor1.session.setValue(aceData)

        function updateData() {
            document.querySelector('input[name="value"]').value = aceEditor1.session.getValue()
        }

        let kind = '{{$configmap->getKind()}}';
        let namespace = '{{$configmap->getNamespace()}}';
        let name = '{{$configmap->getName()}}';

        document.getElementById('deleteValue').value = kind + ' ' + namespace + ' ' + name

        function updateDownloadData() {
            let downloadName = document.querySelector('#resourceName')
            let downloadData = document.querySelector('#downloadData')

            downloadName.value = name
            downloadData.value = aceData
        }

    </script>
@endsection
