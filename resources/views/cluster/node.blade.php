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
            <td>{{$node->getName()}}</td>
            <td>{{\Carbon\Carbon::createFromTimeString($node->toArray()['metadata']['creationTimestamp'], 'UTC')->addHours(7)->toDayDateTimeString()}}</td>
            <td>{{$age}}</td>
            <td>{{$node->getResourceUid()}}</td>
        </tr>
        <tr>
            <th colspan="4">Labels</th>
        </tr>
        <tr>
            <td colspan="4">
                @foreach($node->toArray()['metadata']['labels']??json_decode('{"":""}') as $key => $label)
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
                @foreach($node->toArray()['metadata']['annotations']??json_decode('{"":""}') as $key => $value)
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
            <th>PodCIDR</th>
        </tr>
        <tr>
            <td>{{$node->getSpec()['podCIDR']??'-'}}</td>
        </tr>
        <tr>
            <th>Addresses</th>
        </tr>
        <tr>
            <td>
                @foreach($node->getStatus('addresses') as $address)
                    {{$address['type'].': '.$address['address']}}<br>
                @endforeach
            </td>
        </tr>
        </tbody>
    </table>


    <table class="table table-secondary table-borderless" style="padding-left: 30px">
        <thead>
        <h3 style="padding-left: 30px"id="deployment_table">System Information</h3>
        </thead>
        <tbody>
        <tr>
            <th colspan="2">Machine ID</th>
            <th colspan="2">System UUID</th>
        </tr>
        <tr>
            <td colspan="2">{{$node->getStatus('nodeInfo')['machineID']}}</td>
            <td colspan="2">{{$node->getStatus('nodeInfo')['systemUUID']}}</td>
        </tr>
        <tr>
            <th>Boot ID</th>
            <th>Kernel version</th>
            <th>OS Image</th>
            <th>Container runtime version</th>
        </tr>
        <tr>
            <td>{{$node->getStatus('nodeInfo')['bootID']}}</td>
            <td>{{$node->getStatus('nodeInfo')['kernelVersion']}}</td>
            <td>{{$node->getStatus('nodeInfo')['osImage']}}</td>
            <td>{{$node->getStatus('nodeInfo')['containerRuntimeVersion']}}</td>
        </tr>
        <tr>
            <th>kubelet version</th>
            <th>kube-proxy version</th>
            <th>Operating system</th>
            <th>Architecture</th>
        </tr>
        <tr>
            <td>{{$node->getStatus('nodeInfo')['kubeletVersion']}}</td>
            <td>{{$node->getStatus('nodeInfo')['kubeProxyVersion']}}</td>
            <td>{{$node->getStatus('nodeInfo')['operatingSystem']}}</td>
            <td>{{$node->getStatus('nodeInfo')['architecture']}}</td>
        </tr>
        </tbody>
    </table>


    <table class="table table-secondary" style="padding-left: 30px">
        <thead>
        <h3 style="padding-left: 30px"id="deployment_table">Conditions</h3>
        </thead>
        <tbody>
        <tr>
            <th>Type</th>
            <th>Status</th>
            <th>Last probe time</th>
            <th>Last transition time</th>
            <th>Reason</th>
            <th>Message</th>
        </tr>
        @foreach($node->getStatus('conditions') as $condition)
            <tr>
                <td>{{$condition['type']}}</td>
                <td>{{$condition['status']}}</td>
                <td>{{!\Carbon\Carbon::createFromTimeString($condition['lastHeartbeatTime']??'9999', 'UTC')->isValid() ? '-' : \Carbon\Carbon::createFromTimeString($condition['lastHeartbeatTime']??'9999', 'UTC')->addHours(7)->toDayDateTimeString()}}</td>
                <td>{{!\Carbon\Carbon::createFromTimeString($condition['lastTransitionTime']??'9999', 'UTC')->isValid() ? '-' : \Carbon\Carbon::createFromTimeString($condition['lastTransitionTime']??'9999', 'UTC')->addHours(7)->toDayDateTimeString()}}</td>
                <td>{{$condition['reason']}}</td>
                <td>{{$condition['message']}}</td>
            </tr>
        @endforeach
        </tbody>
    </table>


    <table class="table table-secondary" style="padding-left: 30px" >
        <thead>
        <h3 style="padding-left: 30px" id="pods_table">Pods</h3>
        </thead>
        <tbody>
        <tr>
            <th>Name</th>
            <th>Namespace</th>
            <th>Images</th>
            <th>Labels</th>
            <th>Status</th>
            <th>Restarts</th>
            <th>Running on Host</th>
            <th>Create Time</th>
        </tr>
        @foreach($podCount as $pod)
            <tr>
                <td><a href="{{ route('pod-details', ['name'=>$pod->getName(), 'namespace'=>$pod->getMetadata()['namespace']]) }}">{{$pod->getName()}}</a></td>
                <td>{{$pod->getNamespace()}}</td>
                <td>
                    @foreach($pod->toArray()['spec']['containers'] as $container)
                        {{$container['image']}}<br>
                    @endforeach
                </td>
                <td>
                    @foreach($pod->toArray()['metadata']['labels']??json_decode('{"":""}') as $key => $label)
                        @if($key == "")
                            -
                        @else
                            {{$key}}: {{$label}}<br>
                        @endif
                    @endforeach
                </td>
                <td>{{$pod->toArray()['status']['phase']}}</td>
                <td>
                    @foreach($pod->toArray()['status']['containerStatuses']??["restartCount"=>"-"] as $status)
                        {{$status['restartCount']}}<br>
                    @endforeach
                </td>
                <td>{{$pod->getSpec('nodeName')??'-'}}</td>
                <td>{{\Carbon\Carbon::createFromTimeString($pod->toArray()['metadata']['creationTimestamp'], 'UTC')->addHours(7)->toDayDateTimeString()}}</td>
            </tr>
        @endforeach

        </tbody>
    </table>


@endsection
