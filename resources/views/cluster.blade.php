@extends('layouts.app2', ["namespaces"=>$namespaces])

@section('content')

<div class="container">
    <div class="row">
        <div class="col-12">
            @if(!is_null($namespaces) && count($namespaces) != 0)
                <table class="table table-secondary dashboard">
                    <h3 style="padding-left: 30px" id="namespaces_table">Namespaces</h3>
                    <thead>
                    <tr>
                        <th>Name</th>
                        <th>Labels</th>
                        <th>Phase</th>
                        <th>Create Time</th>
                        <th><i class="fa fa-cog" aria-hidden="true"></i></th>
                    </tr>
                    </thead>
                    <tbody>
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
                            <td>
                                <div class="dropdown">
                                    <button class="btn btn-outline-info dropdown-toggle" role="button" id="dropdownEditButton" type="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <i class="fa fa-ellipsis-v" aria-hidden="true"></i>
                                    </button>
                                    <div class="dropdown-menu" aria-labelledby="dropdownEditButton">
                                        <a class="dropdown-item editForm {{$namespace->getNamespace()}} {{$namespace->getName()}}" onclick="edit(this)" role="button" data-bs-toggle="modal" data-bs-target="#editForm" href="#">Edit</a>
                                        <a class="dropdown-item {{$namespace->getKind()}} {{$namespace->getNamespace()}} {{$namespace->getName()}}" role="button" data-bs-toggle="modal" data-bs-target="#deleteForm" href="#" onclick="deleteData(this)">Delete</a>
                                    </div>
                                </div>
                                <div class="namespace" id="{{$namespace->getNamespace().$namespace->getName()}}" style="display: none">{{$namespaceDataArr[$namespace->getNamespace().$namespace->getName()]}}</div>
                            </td>
                        </tr>
                    @endforeach

                    </tbody>
                </table>
            @endif
        </div>
    </div>


    <div class="row">
        <div class="col-12">
            @if(!is_null($nodes) && count($nodes) != 0)
                <table class="table table-secondary dashboard">
                    <h3 style="padding-left: 30px" id="nodes_table">Nodes</h3>
                    <thead>
                    <tr>
                        <th>Name</th>
                        <th>Labels</th>
                        <th>Ready</th>
                        <th>CPU Requests(cores)</th>
                        <th>CPU Limits(cores)</th>
                        <th>Memory Requests(bytes)</th>
                        <th>Memory Limits(bytes)</th>
                        <th>Pods</th>
                        <th>Create Time</th>
                        <th><i class="fa fa-cog" aria-hidden="true"></i></th>
                    </tr>
                    </thead>
                    <tbody>
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
                            <td>
                                <div class="dropdown">
                                    <button class="btn btn-outline-info dropdown-toggle" role="button" id="dropdownEditButton" type="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <i class="fa fa-ellipsis-v" aria-hidden="true"></i>
                                    </button>
                                    <div class="dropdown-menu" aria-labelledby="dropdownEditButton">
                                        <a class="dropdown-item editForm {{$node->getNamespace()}} {{$node->getName()}}" onclick="edit(this)" role="button" data-bs-toggle="modal" data-bs-target="#editForm" href="#">Edit</a>
                                        <a class="dropdown-item {{$node->getKind()}} {{$node->getNamespace()}} {{$node->getName()}}" role="button" data-bs-toggle="modal" data-bs-target="#deleteForm" href="#" onclick="deleteData(this)">Delete</a>
                                    </div>
                                </div>
                                <div class="node" id="{{$node->getNamespace().$node->getName()}}" style="display: none">{{$nodeDataArr[$node->getNamespace().$node->getName()]}}</div>
                            </td>
                        </tr>
                    @endforeach

                    </tbody>
                </table>
            @endif
        </div>
    </div>



    <div class="row">
        <div class="col-12">
            @if(!is_null($persistentvolumes) && count($persistentvolumes) != 0)
                <table class="table table-secondary dashboard">
                    <h3 style="padding-left: 30px" id=persistentvolumes_table">Persistent Volumes</h3>
                    <thead>
                    <tr>
                        <th>Name</th>
                        <th>Capacity</th>
                        <th>Access Modes</th>
                        <th>Reclaim Policy</th>
                        <th>Status</th>
                        <th>Claim</th>
                        <th>Storage Class</th>
                        <th>Reason</th>
                        <th>Create Time</th>
                        <th><i class="fa fa-cog" aria-hidden="true"></i></th>
                    </tr>
                    </thead>
                    <tbody>
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
                            <td>
                                <div class="dropdown">
                                    <button class="btn btn-outline-info dropdown-toggle" role="button" id="dropdownEditButton" type="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <i class="fa fa-ellipsis-v" aria-hidden="true"></i>
                                    </button>
                                    <div class="dropdown-menu" aria-labelledby="dropdownEditButton">
                                        <a class="dropdown-item editForm {{$persistentvolume->getNamespace()}} {{$persistentvolume->getName()}}" onclick="edit(this)" role="button" data-bs-toggle="modal" data-bs-target="#editForm" href="#">Edit</a>
                                        <a class="dropdown-item {{$persistentvolume->getKind()}} {{$persistentvolume->getNamespace()}} {{$persistentvolume->getName()}}" role="button" data-bs-toggle="modal" data-bs-target="#deleteForm" href="#" onclick="deleteData(this)">Delete</a>
                                    </div>
                                </div>
                                <div class="persistentvolume" id="{{$persistentvolume->getNamespace().$persistentvolume->getName()}}" style="display: none">{{$persistentvolumeDataArr[$persistentvolume->getNamespace().$persistentvolume->getName()]}}</div>
                            </td>
                        </tr>
                    @endforeach

                    </tbody>
                </table>
            @endif
        </div>
    </div>


    <div class="row">
        <div class="col-12">
            @if(!is_null($clusterRoles) && count($clusterRoles) != 0)
                <table class="table table-secondary dashboard">
                    <h3 style="padding-left: 30px" id="clusterRoles_table">Cluster Roles</h3>
                    <thead>
                    <tr>
                        <th>Name</th>
                        <th>Create Time</th>
                        <th><i class="fa fa-cog" aria-hidden="true"></i></th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($clusterRoles as $clusterRole)
                        <tr>
                            <td><a href="{{ route('clusterrole-details', ['name'=>$clusterRole->getName()]) }}">{{$clusterRole->getName()}}</a></td>
                            <td>{{\Carbon\Carbon::createFromTimeString($clusterRole->toArray()['metadata']['creationTimestamp'], 'UTC')->addHours(7)->toDayDateTimeString()}}</td>
                            <td>
                                <div class="dropdown">
                                    <button class="btn btn-outline-info dropdown-toggle" role="button" id="dropdownEditButton" type="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <i class="fa fa-ellipsis-v" aria-hidden="true"></i>
                                    </button>
                                    <div class="dropdown-menu" aria-labelledby="dropdownEditButton">
                                        <a class="dropdown-item editForm {{$clusterRole->getNamespace()}} {{$clusterRole->getName()}}" onclick="edit(this)" role="button" data-bs-toggle="modal" data-bs-target="#editForm" href="#">Edit</a>
                                        <a class="dropdown-item {{$clusterRole->getKind()}} {{$clusterRole->getNamespace()}} {{$clusterRole->getName()}}" role="button" data-bs-toggle="modal" data-bs-target="#deleteForm" href="#" onclick="deleteData(this)">Delete</a>
                                    </div>
                                </div>
                                <div class="clusterRole" id="{{$clusterRole->getNamespace().$clusterRole->getName()}}" style="display: none">{{$clusterRoleDataArr[$clusterRole->getNamespace().$clusterRole->getName()]}}</div>
                            </td>
                        </tr>
                    @endforeach

                    </tbody>
                </table>
            @endif
        </div>
    </div>


    <div class="row">
        <div class="col-12">
            @if(!is_null($clusterRoleBindings) && count($clusterRoleBindings) != 0)
                <table class="table table-secondary dashboard">
                    <h3 style="padding-left: 30px" id="clusterRoleBindings_table">Cluster Role Bindings</h3>
                    <thead>
                    <tr>
                        <th>Name</th>
                        <th>Create Time</th>
                        <th><i class="fa fa-cog" aria-hidden="true"></i></th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($clusterRoleBindings as $clusterRoleBinding)
                        <tr>
                            <td><a href="{{ route('clusterrolebinding-details', ['name'=>$clusterRoleBinding->getName()]) }}">{{$clusterRoleBinding->getName()}}</a></td>
                            <td>{{\Carbon\Carbon::createFromTimeString($clusterRoleBinding->toArray()['metadata']['creationTimestamp'], 'UTC')->addHours(7)->toDayDateTimeString()}}</td>
                            <td>
                                <div class="dropdown">
                                    <button class="btn btn-outline-info dropdown-toggle" role="button" id="dropdownEditButton" type="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <i class="fa fa-ellipsis-v" aria-hidden="true"></i>
                                    </button>
                                    <div class="dropdown-menu" aria-labelledby="dropdownEditButton">
                                        <a class="dropdown-item editForm {{$clusterRoleBinding->getNamespace()}} {{$clusterRoleBinding->getName()}}" onclick="edit(this)" role="button" data-bs-toggle="modal" data-bs-target="#editForm" href="#">Edit</a>
                                        <a class="dropdown-item {{$clusterRoleBinding->getKind()}} {{$clusterRoleBinding->getNamespace()}} {{$clusterRoleBinding->getName()}}" role="button" data-bs-toggle="modal" data-bs-target="#deleteForm" href="#" onclick="deleteData(this)">Delete</a>
                                    </div>
                                </div>
                                <div class="clusterRoleBinding" id="{{$clusterRoleBinding->getNamespace().$clusterRoleBinding->getName()}}" style="display: none">{{$clusterRoleBindingDataArr[$clusterRoleBinding->getNamespace().$clusterRoleBinding->getName()]}}</div>
                            </td>
                        </tr>
                    @endforeach

                    </tbody>
                </table>
            @endif
        </div>
    </div>


    <div class="row">
        <div class="col-12">
            @if(!is_null($events) && count($events) != 0)
                <table class="table table-secondary dashboard">
                    <h3 style="padding-left: 30px" id="events_table">Events</h3>
                    <thead>
                    <tr>
                        <th>Name</th>
                        <th>Reason</th>
                        <th>Message</th>
                        <th>Source</th>
                        <th>Object</th>
                        <th>Count</th>
                        <th>First Seen</th>
                        <th>Last Seen</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($events as $event)
                        <tr>
                            <td>{{$event->getName()}}</td>
                            <td>{{$event->toArray()['reason']??'-'}}</td>
                            <td>{{$event->toArray()['message']??'-'}}</td>
                            <td>{{$event->toArray()['source']['component']??"-"}}/{{$event->toArray()['source']['host']??"-"}}</td>
                            {{--                    TODO Add link to object--}}
                            <td>{{$event->toArray()['involvedObject']['kind']}}/{{$event->toArray()['involvedObject']['name']??""}}</td>
                            <td>{{$event->toArray()['count']??"0"}}</td>
                            <td>{{\Carbon\Carbon::createFromTimeString($event->toArray()['firstTimestamp']??'0', 'UTC')->addHours(7)->toDayDateTimeString()}}</td>
                            <td>{{\Carbon\Carbon::createFromTimeString($event->toArray()['lastTimestamp']??'0', 'UTC')->addHours(7)->toDayDateTimeString()}}</td>
                        </tr>
                    @endforeach

                    </tbody>
                </table>
            @endif
        </div>
    </div>


    <div class="row">
        <div class="col-12">
            @if(!is_null($serviceAccounts) && count($serviceAccounts) != 0)
                <table class="table table-secondary dashboard">
                    <h3 style="padding-left: 30px" id="serviceAccounts_table">Service Accounts</h3>
                    <thead>
                    <tr>
                        <th>Name</th>
                        @if($_GET['namespace']??'default' === 'all')
                            <th>Namespace</th>
                        @endif
                        <th>Labels</th>
                        <th>Create Time</th>
                        <th><i class="fa fa-cog" aria-hidden="true"></i></th>
                    </tr>
                    </thead>
                    <tbody>
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
                            <td>
                                <div class="dropdown">
                                    <button class="btn btn-outline-info dropdown-toggle" role="button" id="dropdownEditButton" type="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <i class="fa fa-ellipsis-v" aria-hidden="true"></i>
                                    </button>
                                    <div class="dropdown-menu" aria-labelledby="dropdownEditButton">
                                        <a class="dropdown-item editForm {{$serviceAccount->getNamespace()}} {{$serviceAccount->getName()}}" onclick="edit(this)" role="button" data-bs-toggle="modal" data-bs-target="#editForm" href="#">Edit</a>
                                        <a class="dropdown-item {{$serviceAccount->getKind()}} {{$serviceAccount->getNamespace()}} {{$serviceAccount->getName()}}" role="button" data-bs-toggle="modal" data-bs-target="#deleteForm" href="#" onclick="deleteData(this)">Delete</a>
                                    </div>
                                </div>
                                <div class="serviceAccount" id="{{$serviceAccount->getNamespace().$serviceAccount->getName()}}" style="display: none">{{$serviceAccountDataArr[$serviceAccount->getNamespace().$serviceAccount->getName()]}}</div>
                            </td>
                        </tr>
                    @endforeach

                    </tbody>
                </table>
            @endif
        </div>
    </div>


    <div class="row">
        <div class="col-12">
            @if(!is_null($roles) && count($roles) != 0)
                <table class="table table-secondary dashboard">
                    <h3 style="padding-left: 30px" id="roles_table">Roles</h3>
                    <thead>
                    <tr>
                        <th>Name</th>
                        @if($_GET['namespace']??'default' === 'all')
                            <th>Namespace</th>
                        @endif
                        <th>Create Time</th>
                        <th><i class="fa fa-cog" aria-hidden="true"></i></th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($roles as $role)
                        <tr>
                            <td><a href="{{ route('role-details', ['name'=>$role->getName(), 'namespace'=>$role->getMetadata()['namespace']??'default']) }}">{{$role->getName()}}</a></td>
                            @if($_GET['namespace']??'default' === 'all')
                                <td>{{$role->getMetadata()['namespace']??'-'}}</td>
                            @endif
                            <td>{{\Carbon\Carbon::createFromTimeString($role->toArray()['metadata']['creationTimestamp'], 'UTC')->addHours(7)->toDayDateTimeString()}}</td>
                            <td>
                                <div class="dropdown">
                                    <button class="btn btn-outline-info dropdown-toggle" role="button" id="dropdownEditButton" type="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <i class="fa fa-ellipsis-v" aria-hidden="true"></i>
                                    </button>
                                    <div class="dropdown-menu" aria-labelledby="dropdownEditButton">
                                        <a class="dropdown-item editForm {{$role->getNamespace()}} {{$role->getName()}}" onclick="edit(this)" role="button" data-bs-toggle="modal" data-bs-target="#editForm" href="#">Edit</a>
                                        <a class="dropdown-item {{$role->getKind()}} {{$role->getNamespace()}} {{$role->getName()}}" role="button" data-bs-toggle="modal" data-bs-target="#deleteForm" href="#" onclick="deleteData(this)">Delete</a>
                                    </div>
                                </div>
                                <div class="role" id="{{$role->getNamespace().$role->getName()}}" style="display: none">{{$roleDataArr[$role->getNamespace().$role->getName()]}}</div>
                            </td>
                        </tr>
                    @endforeach

                    </tbody>
                </table>
            @endif
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            @if(!is_null($roleBindings) && count($roleBindings) != 0)
                <table class="table table-secondary dashboard">
                    <h3 style="padding-left: 30px" id="roleBindings_table">Role Bindings</h3>
                    <thead>
                    <tr>
                        <th>Name</th>
                        @if($_GET['namespace']??'default' === 'all')
                            <th>Namespace</th>
                        @endif
                        <th>Create Time</th>
                        <th><i class="fa fa-cog" aria-hidden="true"></i></th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($roleBindings as $roleBinding)
                        <tr>
                            <td><a href="{{ route('rolebinding-details', ['name'=>$roleBinding->getName(), 'namespace'=>$roleBinding->getMetadata()['namespace']??'default']) }}">{{$roleBinding->getName()}}</a></td>
                            @if($_GET['namespace']??'default' === 'all')
                                <td>{{$roleBinding->getMetadata()['namespace']??'-'}}</td>
                            @endif
                            <td>{{\Carbon\Carbon::createFromTimeString($roleBinding->toArray()['metadata']['creationTimestamp'], 'UTC')->addHours(7)->toDayDateTimeString()}}</td>
                            <td>
                                <div class="dropdown">
                                    <button class="btn btn-outline-info dropdown-toggle" role="button" id="dropdownEditButton" type="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <i class="fa fa-ellipsis-v" aria-hidden="true"></i>
                                    </button>
                                    <div class="dropdown-menu" aria-labelledby="dropdownEditButton">
                                        <a class="dropdown-item editForm {{$roleBinding->getNamespace()}} {{$roleBinding->getName()}}" onclick="edit(this)" role="button" data-bs-toggle="modal" data-bs-target="#editForm" href="#">Edit</a>
                                        <a class="dropdown-item {{$roleBinding->getKind()}} {{$roleBinding->getNamespace()}} {{$roleBinding->getName()}}" role="button" data-bs-toggle="modal" data-bs-target="#deleteForm" href="#" onclick="deleteData(this)">Delete</a>
                                    </div>
                                </div>
                                <div class="roleBinding" id="{{$roleBinding->getNamespace().$roleBinding->getName()}}" style="display: none">{{$roleBindingDataArr[$roleBinding->getNamespace().$roleBinding->getName()]}}</div>
                            </td>
                        </tr>
                    @endforeach

                    </tbody>
                </table>
            @endif
        </div>
    </div>



</div>


    <div class="modal fade" id="editForm" tabindex="-1" aria-labelledby="editFormLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editFormLabel">Edit Resource</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('edit') }}" method="POST" onsubmit="updateData()">
                    @csrf
                    <div class="modal-body" id="editorContainer">
                        <div id="editor">//test</div>
                    </div>

                    <input type="hidden" name="value" style="display: none" id="editorValue" value="">
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>


    <div class="modal fade" id="deleteForm" tabindex="-1" aria-labelledby="deleteFormLabel" aria-hidden="true">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title text-center" id="deleteFormLabel">Confirmation</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('delete') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <p>Your resource will be gone forever!, Are you sure about that?</p>
                    </div>
                    <input type="hidden" id="deleteValue" name="resource" value="" style="display: none">
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-danger">Delete</button>
                    </div>
                </form>
            </div>
        </div>
    </div>


@endsection


@section('js')

    <script>
        let editor = document.querySelector('#editor')
        let aceEditor = ace.edit("editor");

        aceEditor.setTheme('ace/theme/monokai')
        aceEditor.session.setMode("ace/mode/yaml");


        function edit(e) {
            let classname = e.className.split(' ')
            let data = document.getElementById(classname[2]+classname[3]).innerHTML
            aceEditor.session.setValue(data)
        }

        function deleteData(e) {
            let classname = e.className.split(' ')
            document.getElementById('deleteValue').value = classname[1] + ' ' + classname[2] + ' ' + classname[3]
        }

        function scaleResource(e) {
            let classname = e.className.split(' ')
            if (classname[1] === 'ReplicaSet' || classname[1] === 'IngressClass') {
                let data = document.getElementById(classname[2]+classname[3]).innerHTML
                document.getElementById('scaleValue').value = data;
            }
            else {
                document.getElementById('scaleValue').value = classname[1] + ' ' + classname[2] + ' ' + classname[3]
            }
            document.getElementById('scaleNumber').value = classname[4]
        }

        function updateData() {
            document.getElementById('editorValue').value = aceEditor.session.getValue()
        }
    </script>


@endsection
