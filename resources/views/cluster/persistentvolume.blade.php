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
            <td>{{$pv->getName()}}</td>
            <td>{{$pv->toArray()['metadata']['creationTimestamp']}}</td>
            <td>{{$age}}</td>
            <td>{{$pv->getResourceUid()}}</td>
        </tr>
        <tr>
            <th colspan="4">Labels</th>
        </tr>
        <tr>
            <td colspan="4">
                @foreach($pv->toArray()['metadata']['labels']??json_decode('{"":""}') as $key => $label)
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
                @foreach($pv->toArray()['metadata']['annotations']??json_decode('{"":""}') as $key => $value)
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
            <th>Claim</th>
            <th>Reclaim policy</th>
            <th>Storage class</th>
            <th>Access modes</th>
        </tr>
        <tr>
            <td>{{$pv->getPhase()}}</td>
            <td><a href="{{ route('pvc-details', ['name'=>$pv->getSpec('claimRef')['name'], 'namespace'=>$pv->getSpec('claimRef')['namespace']]) }}">{{$pv->getSpec('claimRef')['namespace'].'/'.$pv->getSpec('claimRef')['name']}}</a></td>
            <td>{{$pv->getSpec('persistentVolumeReclaimPolicy')??'-'}}</td>
            <td>{{$pv->getStorageClass()??'-'}}</td>
            <td>
                @foreach($pv->getAccessModes() as $accessMode)
                    {{$accessMode}}<br>
                @endforeach
            </td>
        </tr>
        </tbody>
    </table>


    <table class="table table-secondary table-borderless" style="padding-left: 30px">
        <thead>
        <h3 style="padding-left: 30px"id="deployment_table">Source</h3>
        </thead>
        <tbody>
        <tr>
            @foreach($types as $type)
                @if(!is_null($pv->getSpec($type)))
                    <th>Type</th>
                    @foreach($pv->getSpec($type) as $key => $value)
                        <th>{{ucwords($key)}}</th>
                    @endforeach
                @endif
            @endforeach
        </tr>
        <tr>
            @foreach($types as $type)
                @if(!is_null($pv->getSpec($type)))
                    <td>{{strtoupper($type)}}</td>
                    @foreach($pv->getSpec($type) as $key => $value)
                        <td>{{$value}}</td>
                    @endforeach
                @endif
            @endforeach
        </tr>
        </tbody>
    </table>


    <table class="table table-secondary table-borderless" style="padding-left: 30px">
        <thead>
        <h3 style="padding-left: 30px"id="deployment_table">Capacity</h3>
        </thead>
        <tbody>
        <tr>
            <th>Resource name</th>
            <th>Quantity</th>
        </tr>
        @foreach($pv->getSpec('capacity') as $name => $capacity)
            <tr>
                <td>{{$name}}</td>
                <td>{{$capacity}}</td>
            </tr>
        @endforeach
        </tbody>
    </table>



@endsection
