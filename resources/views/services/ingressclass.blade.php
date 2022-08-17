@extends('layouts.app2', ['namespaces'=>$namespaces])

@section('content')

    <style>
        .table td, .table th {
            padding: 5px 0.4rem;
        }
    </style>


    <table class="table table-secondary table-borderless" style="padding-left: 30px">
        <thead>
        <h3 style="padding-left: 30px"id="deployment_table">Metadata</h3>
        </thead>
        <tbody>
        <tr>
            <th>Name</th>
            <th>Created</th>
            <th>Age</th>
            <th>UID</th>
        </tr>
        <tr>
            <td>{{$ingressclass['metadata']['name']}}</td>
            <td>{{$ingressclass['metadata']['creationTimestamp']}}</td>
            <td>{{$age}}</td>
            <td>{{$ingressclass['metadata']['uid']}}</td>
        </tr>
        <tr>
            <th colspan="4">Labels</th>
        </tr>
        <tr>
            <td colspan="4">
                @foreach($ingressclass['metadata']['labels']??json_decode('{"":""}') as $key => $label)
                    @if($key == "")
                        -
                    @else
                        {{$key}}: {{$label}}<br>
                    @endif
                @endforeach
            </td>
        </tr>
        <tr>
            <th colspan="4">Annotations</th>
        </tr>
        <tr>
            <td colspan="4">
                @foreach($ingressclass['metadata']['annotations']??json_decode('{"":""}') as $key => $value)
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
        <h3 style="padding-left: 30px"id="deployment_table">Resource Information</h3>
        </thead>
        <tbody>
        <tr>
            <th colspan="4">Controller</th>
        </tr>
        <tr>
            <td colspan="4">{{$ingressclass['spec']['controller']}}</td>
        </tr>
        </tbody>
    </table>

@endsection
