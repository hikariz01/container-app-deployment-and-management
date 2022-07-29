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
                    <td>{{$namespace->getName()}}</td>
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
                    <td>{{$namespace->toArray()['metadata']['creationTimestamp']}}</td>
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
                    <td>{{$node->getName()}}</td>
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
                    <td>{{$node->getCapacity()['pods']}}</td>
                    <td>{{$node->toArray()['metadata']['creationTimestamp']}}</td>
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
                    <td>{{$clusterRole->getName()}}</td>
                    <td>{{$clusterRole->toArray()['metadata']['creationTimestamp']}}</td>
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
                    <td>{{$clusterRoleBinding->getName()}}</td>
                    <td>{{$clusterRoleBinding->toArray()['metadata']['creationTimestamp']}}</td>
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
                    <td>{{$event->toArray()['involvedObject']['kind']}}/{{$event->toArray()['involvedObject']['name']??""}}</td>
                    <td>{{$event->toArray()['count']??"0"}}</td>
                    <td>{{$event->toArray()['firstTimestamp']}}</td>
                    <td>{{$event->toArray()['lastTimestamp']}}</td>
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
                <td>Labels</td>
                <td>Create Time</td>
            </tr>
            @foreach($serviceAccounts as $serviceAccount)
                <tr>
                    <td>{{$serviceAccount->getName()}}</td>
                    <td>
                        @foreach($serviceAccount->toArray()['metadata']['labels']??json_decode('{"":""}') as $key => $label)
                            @if($key == "")
                                -
                            @else
                                {{$key}}: {{$label}}<br>
                            @endif
                        @endforeach
                    </td>
                    <td>{{$serviceAccount->toArray()['metadata']['creationTimestamp']}}</td>
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
                <td>Create Time</td>
            </tr>
            @foreach($roles as $role)
                <tr>
                    <td>{{$role->getName()}}</td>
                    <td>{{$role->toArray()['metadata']['creationTimestamp']}}</td>
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
                <td>Create Time</td>
            </tr>
            @foreach($roleBindings as $roleBinding)
                <tr>
                    <td>{{$roleBinding->getName()}}</td>
                    <td>{{$roleBinding->toArray()['metadata']['creationTimestamp']}}</td>
                </tr>
            @endforeach

            </tbody>
        </table>
    @endif

@endsection
