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
            <th>Namespace</th>
            <th>Created</th>
            <th>Age</th>
            <th>UID</th>
        </tr>
        <tr>
            <td>{{$pvc->getName()}}</td>
            <td>{{$pvc->getNamespace()}}</td>
            <td>{{$pvc->toArray()['metadata']['creationTimestamp']}}</td>
            <td>{{$age}}</td>
            <td>{{$pvc->getResourceUid()}}</td>
        </tr>
        <tr>
            <th colspan="5">Labels</th>
        </tr>
        <tr>
            <td colspan="5">
                @foreach($pvc->toArray()['metadata']['labels']??json_decode('{"":""}') as $key => $label)
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
                @foreach($pvc->toArray()['metadata']['annotations']??json_decode('{"":""}') as $key => $value)
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
            <th>Status</th>
            <th>Storage Class</th>
            <th>Volume Name</th>
        </tr>
        <tr>
            <td>{{$pvc->getStatus('phase')??'-'}}</td>
            <td>{{$pvc->getStorageClass()}}</td>
{{--            TODO ADD LINK TO VOLUME--}}
            <td><a href="#">{{$pvc->getSpec('volumeName')}}</a></td>
        </tr>
        <tr>
            <th colspan="3">Capacity</th>
        </tr>
        <tr>
            <td colspan="3">{{$pvc->getCapacity()}}</td>
        </tr>
        <tr>
            <th colspan="3">Access Modes</th>
        </tr>
        <tr>
            <td colspan="3">
                @foreach($pvc->getAccessModes() as $accessMode)
                    {{$accessMode}}<br>
                @endforeach
            </td>
        </tr>
        </tbody>
    </table>


@endsection
