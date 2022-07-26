@extends('layouts.app2', ["namespaces"=>$namespaces])

@section('content')

    @if(!is_null($configmaps) && count($configmaps) != 0)
        <table class="table table-secondary" style="padding-left: 30px" >
            <thead>
            <h3 style="padding-left: 30px" id="configmaps_table">Config Maps</h3>
            </thead>
            <tbody>
            <tr>
                <td>Name</td>
                <td>Labels</td>
                <td>Create Time</td>
            </tr>
            @foreach($configmaps as $configmap)
                <tr>
                    <td>{{$configmap->getName()}}</td>
                    <td>
                        @foreach($configmap->toArray()['metadata']['labels']??json_decode('{"":""}') as $key => $label)
                            @if($key == "")
                                -
                            @else
                                {{$key}}: {{$label}}<br>
                            @endif
                        @endforeach
                    </td>
                    <td>{{$configmap->toArray()['metadata']['creationTimestamp']}}</td>
                </tr>
            @endforeach

            </tbody>
        </table>
    @endif


    @if(!is_null($secrets) && count($secrets) != 0)
        <table class="table table-secondary" style="padding-left: 30px" >
            <thead>
            <h3 style="padding-left: 30px" id="secrets_table">Secrets</h3>
            </thead>
            <tbody>
            <tr>
                <td>Name</td>
                <td>Labels</td>
                <td>Type</td>
                <td>Create Time</td>
            </tr>
            @foreach($secrets as $secret)
                <tr>
                    <td>{{$secret->getName()}}</td>
                    <td>
                        @foreach($secret->toArray()['metadata']['labels']??json_decode('{"":""}') as $key => $label)
                            @if($key == "")
                                -
                            @else
                                {{$key}}: {{$label}}<br>
                            @endif
                        @endforeach
                    </td>
                    <td>{{$secret->toArray()['type']}}</td>
                    <td>{{$secret->toArray()['metadata']['creationTimestamp']}}</td>
                </tr>
            @endforeach

            </tbody>
        </table>
    @endif


    @if(!is_null($pvcs) && count($pvcs) != 0)
        <table class="table table-secondary" style="padding-left: 30px" >
            <thead>
            <h3 style="padding-left: 30px" id="pvcs_table">Persistent Volume Claims</h3>
            </thead>
            <tbody>
            <tr>
                <td>Name</td>
                <td>Labels</td>
                <td>Status</td>
                <td>Volume</td>
                <td>Capacity</td>
                <td>Access Modes</td>
                <td>Storage Class</td>
                <td>Create Time</td>
            </tr>
            @foreach($pvcs as $pvc)
                <tr>
                    <td>{{$pvc->getName()}}</td>
                    <td>
                        @foreach($pvc->toArray()['metadata']['labels']??json_decode('{"":""}') as $key => $label)
                            @if($key == "")
                                -
                            @else
                                {{$key}}: {{$label}}<br>
                            @endif
                        @endforeach
                    </td>
                    <td>{{$pvc->toArray()['status']['phase']}}</td>
                    <td>{{$pvc->toArray()['spec']['volumeName']??"-"}}</td>
                    <td>{{$pvc->toArray()['spec']['resources']['requests']['storage']??"-"}}</td>
                    <td>
                        @foreach($pvc->toArray()['spec']['accessModes'] as $accessmode)
                            {{$accessmode}}<br>
                        @endforeach
                    </td>
                    <td>{{$pvc->toArray()['spec']['storageClassName']}}</td>
                    <td>{{$pvc->toArray()['metadata']['creationTimestamp']}}</td>
                </tr>
            @endforeach

            </tbody>
        </table>
    @endif

    @if(!is_null($storageclasses) && count($storageclasses) != 0)
        <table class="table table-secondary" style="padding-left: 30px" >
            <thead>
            <h3 style="padding-left: 30px" id="storageclasses_table">Storage Classes</h3>
            </thead>
            <tbody>
            <tr>
                <td>Name</td>
                <td>Provisioner</td>
                <td>Parameters</td>
                <td>Create Time</td>
            </tr>
            @foreach($storageclasses as $storageclass)
                <tr>
                    <td>{{$storageclass->getName()}}</td>
                    <td>{{$storageclass->toArray()['provisioner']}}</td>
                    <td>
                        @foreach($storageclass->toArray()['parameters'] as $key => $value)
                            {{$key}}: {{$value}}<br>
                        @endforeach
                    </td>
                    <td>{{$storageclass->toArray()['metadata']['creationTimestamp']}}</td>
                </tr>
            @endforeach

            </tbody>
        </table>
    @endif


@endsection
