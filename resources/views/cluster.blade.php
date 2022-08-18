@extends('layouts.app2', ["namespaces"=>$namespaces])

@section('content')

    @if(!is_null($namespaces) && count($namespaces) != 0)
        <table class="table table-secondary" style="padding-left: 30px" >
            <thead>
            <h3 style="padding-left: 30px" id="namespaces_table">Namespaces</h3>
            </thead>
            <tbody>
            <tr>
                <td>Name</td>
                <td>Labels</td>
                <td>Phase</td>
                <td>Create Time</td>
            </tr>
            @foreach($namespaces as $namespace)
                <tr>
                    <td><a href="{{ route('namespace-details', ['name'=>$namespace->getName()]) }}">{{$namespace->getName()}}</a></td>
                    <td>
                        @foreach($namespace->toArray()['metadata']['labels']??json_decode('{"":""}') as $key => $label)
                            @if($key == "")
                                -
                            @else
                                {{$key}}: {{$label}}<br>
                            @endif
                        @endforeach
                    </td>
                    <td>{{$namespace->toArray()['status']['phase']}}</td>
                    <td>{{\Carbon\Carbon::createFromTimeString($namespace->toArray()['metadata']['creationTimestamp'], 'UTC')->addHours(7)->toDayDateTimeString()}}</td>
                </tr>
            @endforeach

            </tbody>
        </table>
    @endif

    @if(!is_null($nodes) && count($nodes) != 0)
        <table class="table table-secondary" style="padding-left: 30px" >
            <thead>
            <h3 style="padding-left: 30px" id="nodes_table">Nodes</h3>
            </thead>
            <tbody>
            <tr>
                <td>Name</td>
                <td>Labels</td>
                <td>Ready</td>
                <td>CPU Requests(cores)</td>
                <td>CPU Limits(cores)</td>
                <td>Memory Requests(bytes)</td>
                <td>Memory Limits(bytes)</td>
                <td>Pods</td>
                <td>Create Time</td>
            </tr>
            @foreach($nodes as $node)
                <tr>
                    <td><a href="{{ route('node-details', ['name'=>$node->getName()]) }}">{{$node->getName()}}</a></td>
                    <td>
                        @foreach($node->toArray()['metadata']['labels']??json_decode('{"":""}') as $key => $label)
                            @if($key == "")
                                -
                            @else
                                {{$key}}: {{$label}}<br>
                            @endif
                        @endforeach
                    </td>
                    <td>
                        @foreach($node->toArray()['status']['conditions'] as $type)
                            @if($type['type'] == "Ready")
                                {{$type['status']}}
                            @endif
                        @endforeach
                    </td>
                    <td>{{$node->getCapacity()['cpu']}}</td>
                    <td>{{$node->getAllocatableInfo()['cpu']}}</td>
                    <td>{{$node->getCapacity()['memory']}}</td>
                    <td>{{$node->getAllocatableInfo()['memory']}}</td>
                    <td>{{count($podCount[$node->getName()])}}</td>
                    <td>{{\Carbon\Carbon::createFromTimeString($node->toArray()['metadata']['creationTimestamp'], 'UTC')->addHours(7)->toDayDateTimeString()}}</td>
                </tr>
            @endforeach

            </tbody>
        </table>
    @endif

    @if(!is_null($persistentvolumes) && count($persistentvolumes) != 0)
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
                    <td><a href="{{ route('pv-details', ['name'=>$persistentvolume->getName()]) }}">{{$persistentvolume->getName()}}</a></td>
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
                    <td>{{\Carbon\Carbon::createFromTimeString($persistentvolume->toArray()['metadata']['creationTimestamp'], 'UTC')->addHours(7)->toDayDateTimeString()}}</td>
                </tr>
            @endforeach

            </tbody>
        </table>
    @endif

    @if(!is_null($clusterRoles) && count($clusterRoles) != 0)
        <table class="table table-secondary" style="padding-left: 30px" >
            <thead>
            <h3 style="padding-left: 30px" id="clusterRoles_table">Cluster Roles</h3>
            </thead>
            <tbody>
            <tr>
                <td>Name</td>
                <td>Create Time</td>
            </tr>
            @foreach($clusterRoles as $clusterRole)
                <tr>
                    <td><a href="{{ route('clusterrole-details', ['name'=>$clusterRole->getName()]) }}">{{$clusterRole->getName()}}</a></td>
                    <td>{{\Carbon\Carbon::createFromTimeString($clusterRole->toArray()['metadata']['creationTimestamp'], 'UTC')->addHours(7)->toDayDateTimeString()}}</td>
                </tr>
            @endforeach

            </tbody>
        </table>
    @endif


    @if(!is_null($clusterRoleBindings) && count($clusterRoleBindings) != 0)
        <table class="table table-secondary" style="padding-left: 30px" >
            <thead>
            <h3 style="padding-left: 30px" id="clusterRoleBindings_table">Cluster Role Bindings</h3>
            </thead>
            <tbody>
            <tr>
                <td>Name</td>
                <td>Create Time</td>
            </tr>
            @foreach($clusterRoleBindings as $clusterRoleBinding)
                <tr>
                    <td><a href="{{ route('clusterrolebinding-details', ['name'=>$clusterRoleBinding->getName()]) }}">{{$clusterRoleBinding->getName()}}</a></td>
                    <td>{{\Carbon\Carbon::createFromTimeString($clusterRoleBinding->toArray()['metadata']['creationTimestamp'], 'UTC')->addHours(7)->toDayDateTimeString()}}</td>
                </tr>
            @endforeach

            </tbody>
        </table>
    @endif

    @if(!is_null($events) && count($events) != 0)
        <table class="table table-secondary" style="padding-left: 30px" >
            <thead>
            <h3 style="padding-left: 30px" id="events_table">Events</h3>
            </thead>
            <tbody>
            <tr>
                <td>Name</td>
                <td>Reason</td>
                <td>Message</td>
                <td>Source</td>
                <td>Object</td>
                <td>Count</td>
                <td>First Seen</td>
                <td>Last Seen</td>
            </tr>
            @foreach($events as $event)
                <tr>
                    <td>{{$event->getName()}}</td>
                    <td>{{$event->toArray()['reason']??'-'}}</td>
                    <td>{{$event->toArray()['message']??'-'}}</td>
                    <td>{{$event->toArray()['source']['component']??"-"}}/{{$event->toArray()['source']['host']??"-"}}</td>
{{--                    TODO Add link to object--}}
                    <td>{{$event->toArray()['involvedObject']['kind']}}/{{$event->toArray()['involvedObject']['name']??""}}</td>
                    <td>{{$event->toArray()['count']??"0"}}</td>
                    <td>{{\Carbon\Carbon::createFromTimeString($event->toArray()['firstTimestamp'], 'UTC')->addHours(7)->toDayDateTimeString()}}</td>
                    <td>{{\Carbon\Carbon::createFromTimeString($event->toArray()['lastTimestamp'], 'UTC')->addHours(7)->toDayDateTimeString()}}</td>
                </tr>
            @endforeach

            </tbody>
        </table>
    @endif


    @if(!is_null($serviceAccounts) && count($serviceAccounts) != 0)
        <table class="table table-secondary" style="padding-left: 30px" >
            <thead>
            <h3 style="padding-left: 30px" id="serviceAccounts_table">Service Accounts</h3>
            </thead>
            <tbody>
            <tr>
                <td>Name</td>
                @if($_GET['namespace']??'default' === 'all')
                    <td>Namespace</td>
                @endif
                <td>Labels</td>
                <td>Create Time</td>
            </tr>
            @foreach($serviceAccounts as $serviceAccount)
                <tr>
                    <td><a href="{{ route('serviceaccount-details', ['name'=>$serviceAccount->getName(), 'namespace'=>$serviceAccount->getMetadata()['namespace']??'default']) }}">{{$serviceAccount->getName()}}</a></td>
                    @if($_GET['namespace']??'default' === 'all')
                        <td>{{$serviceAccount->getMetadata()['namespace']??'-'}}</td>
                    @endif
                    <td>
                        @foreach($serviceAccount->toArray()['metadata']['labels']??json_decode('{"":""}') as $key => $label)
                            @if($key == "")
                                -
                            @else
                                {{$key}}: {{$label}}<br>
                            @endif
                        @endforeach
                    </td>
                    <td>{{\Carbon\Carbon::createFromTimeString($serviceAccount->toArray()['metadata']['creationTimestamp'], 'UTC')->addHours(7)->toDayDateTimeString()}}</td>
                </tr>
            @endforeach

            </tbody>
        </table>
    @endif


    @if(!is_null($roles) && count($roles) != 0)
        <table class="table table-secondary" style="padding-left: 30px" >
            <thead>
            <h3 style="padding-left: 30px" id="roles_table">Roles</h3>
            </thead>
            <tbody>
            <tr>
                <td>Name</td>
                @if($_GET['namespace']??'default' === 'all')
                    <td>Namespace</td>
                @endif
                <td>Create Time</td>
            </tr>
            @foreach($roles as $role)
                <tr>
                    <td><a href="{{ route('role-details', ['name'=>$role->getName(), 'namespace'=>$role->getMetadata()['namespace']??'default']) }}">{{$role->getName()}}</a></td>
                    @if($_GET['namespace']??'default' === 'all')
                        <td>{{$role->getMetadata()['namespace']??'-'}}</td>
                    @endif
                    <td>{{\Carbon\Carbon::createFromTimeString($role->toArray()['metadata']['creationTimestamp'], 'UTC')->addHours(7)->toDayDateTimeString()}}</td>
                </tr>
            @endforeach

            </tbody>
        </table>
    @endif

    @if(!is_null($roleBindings) && count($roleBindings) != 0)
        <table class="table table-secondary" style="padding-left: 30px" >
            <thead>
            <h3 style="padding-left: 30px" id="roleBindings_table">Role Bindings</h3>
            </thead>
            <tbody>
            <tr>
                <td>Name</td>
                @if($_GET['namespace']??'default' === 'all')
                    <td>Namespace</td>
                @endif
                <td>Create Time</td>
            </tr>
            @foreach($roleBindings as $roleBinding)
                <tr>
                    <td><a href="{{ route('rolebinding-details', ['name'=>$roleBinding->getName(), 'namespace'=>$roleBinding->getMetadata()['namespace']??'default']) }}">{{$roleBinding->getName()}}</a></td>
                    @if($_GET['namespace']??'default' === 'all')
                        <td>{{$roleBinding->getMetadata()['namespace']??'-'}}</td>
                    @endif
                    <td>{{\Carbon\Carbon::createFromTimeString($roleBinding->toArray()['metadata']['creationTimestamp'], 'UTC')->addHours(7)->toDayDateTimeString()}}</td>
                </tr>
            @endforeach

            </tbody>
        </table>
    @endif

@endsection
