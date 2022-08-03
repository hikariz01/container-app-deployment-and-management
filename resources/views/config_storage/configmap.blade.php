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
            margin-left: -12.5%;
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
            <td>{{$configmap->getName()}}</td>
            <td>{{$configmap->getNamespace()}}</td>
            <td>{{$configmap->toArray()['metadata']['creationTimestamp']}}</td>
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
                @foreach($configmap->toArray()['metadata']['annotations']??json_decode('{"":""}') as $key => $value)
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
        @if(!is_null($configmap->getData()))
            <tr>
                <th>
                    <pre>
                        <code class="codebox">
                            {{json_encode($configmap->getData())}}
                        </code>
                    </pre>
                </th>
            </tr>
        @else
            <tr>
                <td>There is no data.</td>
            </tr>
        @endif
        </tbody>
    </table>


@endsection
