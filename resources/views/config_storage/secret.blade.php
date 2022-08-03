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
        <h3 style="padding-left: 30px"id="deployment_table">Metadata</h3>
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
            <td>{{$secret->toArray()['metadata']['creationTimestamp']}}</td>
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
                    @else
                        {{$key}}: {{$value}}<br>
                    @endif
                @endforeach
            </td>
        </tr>
        </tbody>
    </table>


    <table class="table table-secondary table-borderless" style="padding-left: 30px">
        <thead>
        <h3 style="padding-left: 30px"id="deployment_table">Data</h3>
        </thead>
        <tbody>
        @if(!is_null($secret->getData()))
            @foreach($secret->getData() as $key => $data)
            <tr>
                <th>{{$key}}</th>
            </tr>
            <tr>
                <td>
                    <pre>
                        <code class="codebox">
                            @if(base64_encode(base64_decode($data, true)) === $data)
                                {{base64_decode($data)}}
                            @else
                                {{$data}}
                            @endif
                        </code>
                    </pre>
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
