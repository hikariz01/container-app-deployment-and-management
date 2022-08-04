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
            <td>{{$storageclass->getName()}}</td>
            <td>{{$storageclass->toArray()['metadata']['creationTimestamp']}}</td>
            <td>{{$age}}</td>
            <td>{{$storageclass->getResourceUid()}}</td>
        </tr>
        <tr>
            <th colspan="4">Labels</th>
        </tr>
        <tr>
            <td colspan="4">
                @foreach($storageclass->toArray()['metadata']['labels']??json_decode('{"":""}') as $key => $label)
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
                @foreach($storageclass->toArray()['metadata']['annotations']??json_decode('{"":""}') as $key => $value)
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
            <th>Provisioner</th>
        </tr>
        <tr>
            <td>{{$storageclass->toArray()['provisioner']}}</td>
        </tr>
        <tr>
            <th>Parameters</th>
        </tr>
        <tr>
            <td>
                @foreach($storageclass->getParameters()??[''=>''] as $key => $value)
                    @if($key === '')
                        -
                    @else
                        {{$key}}: {{$value}}<br>
                    @endif
                @endforeach
            </td>
        </tr>
        </tbody>
    </table>


    <table class="table table-secondary" style="padding-left: 30px" >
        <thead>
        <h3 style="padding-left: 30px" id=persistentvolumes_table">Persistent Volumes</h3>
        </thead>
        <tbody>
        <tr>
            <td>Name</td>
            <td>Capacity</td>
            <td>Access Modes</td>
            <td>Reclaim Policy</td>
            <td>Status</td>
            <td>Claim</td>
            <td>Storage Class</td>
            <td>Reason</td>
            <td>Create Time</td>
        </tr>
        @foreach($persistentvolumes as $persistentvolume)
            <tr>
                <td>{{$persistentvolume->getName()}}</td>
                <td>{{$persistentvolume->getCapacity()}}</td>
                <td>
                    @foreach($persistentvolume->getAccessModes() as $accessMode)
                        {{$accessMode}}<br>
                    @endforeach
                </td>
                <td>{{$persistentvolume->getSpec('persistentVolumeReclaimPolicy')}}</td>
                <td>{{$persistentvolume->getStatus('phase')}}</td>
                <td><a href="{{ route('pvc-details', ['name'=>$persistentvolume->getSpec('claimRef')['name'], 'namespace'=>$persistentvolume->getSpec('claimRef')['namespace']]) }}">{{$persistentvolume->getSpec('claimRef')['namespace'].'/'.$persistentvolume->getSpec('claimRef')['name']}}</a></td>
                <td>{{$persistentvolume->getSpec('storageClassName')}}</td>
                <td>{{$persistentvolume->getStatus('reason')??'-'}}</td>
                <td>{{$persistentvolume->toArray()['metadata']['creationTimestamp']}}</td>
            </tr>
        @endforeach

        </tbody>
    </table>


@endsection
