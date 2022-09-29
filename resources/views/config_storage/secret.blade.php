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
            <td>{{$secret->getName()}}</td>
            <td>{{$secret->getNamespace()}}</td>
            <td>{{\Carbon\Carbon::createFromTimeString($secret->toArray()['metadata']['creationTimestamp'], 'UTC')->addHours(7)->toDayDateTimeString()}}</td>
            <td>{{$age}}</td>
            <td>{{$secret->getResourceUid()}}</td>
        </tr>
        <tr>
            <th colspan="5">Labels</th>
        </tr>
        <tr>
            <td colspan="5">
                @foreach($secret->toArray()['metadata']['labels']??json_decode('{"":""}') as $key => $label)
                    @if($key == "")
                        -
                    @else
                        {{$key}}: {{$label}}<br>
                    @endif
                @endforeach
            </td>
        </tr>
        <tr>
            <th colspan="5">Annotations</th>
        </tr>
        <tr>
            <td colspan="5">
                @foreach($secret->toArray()['metadata']['annotations']??json_decode('{"":""}') as $key => $value)
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
        @if(!is_null($secret->getData()) && count($secret->getData()) != 0)
            @foreach($secret->getData() as $key => $data)
            <tr>
                <th>{{$key}}</th>
            </tr>
            <tr>
                <td>
                    <div class="codecontainer" style="width: 80vw; height: 30vh; margin-left: auto; margin-right: auto">
                        @if(base64_encode(base64_decode($data, true)) === $data)
                            <div class="aceEditor" style="position: relative; height: 100%; width: 100%">{{base64_decode($data)}}<br>
</div>
                        @else
                            <div class="aceEditor" style="position: relative; height: 100%; width: 100%">{{$data}}<br>
</div>
                        @endif
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

            aceEditor[i].setOption('wrap', 80)
            aceEditor[i].setOption('indentedSoftWrap', false)
            aceEditor[i].setReadOnly(true)

        }

    </script>


@endsection
