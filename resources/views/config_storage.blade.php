@extends('layouts.app2', ["namespaces"=>$namespaces])

@section('content')



    @if(!is_null($configmaps) && count($configmaps) != 0)
        <table class="table table-secondary" style="padding-left: 30px" >
            <thead>
                <tr>
                    <td colspan="10"><h3 style="padding-left: 30px" id="configmaps_table">Config Maps</h3></td>
                </tr>
            </thead>
            <tbody>
            <tr>
                <th>Name</th>
                @if(!strcmp($_GET['namespace']??"no", 'all'))
                    <th>Namespace</th>
                @endif
                <th>Labels</th>
                <th>Create Time</th>
                <th><i class="fa fa-cog" aria-hidden="true"></i></th>
            </tr>
            @foreach($configmaps as $configmap)
                <tr>
                    <td><a href="{{ route('configmap-details', ['name'=>$configmap->getName(), 'namespace'=>$configmap->getMetadata()['namespace']??'default']) }}">{{$configmap->getName()}}</a></td>
                    @if(!strcmp($_GET['namespace']??"no", 'all'))
                        <td>{{$configmap->getNamespace()}}</td>
                    @endif
                    <td>
                        @foreach($configmap->toArray()['metadata']['labels']??json_decode('{"":""}') as $key => $label)
                            @if($key == "")
                                -
                            @else
                                {{$key}}: {{$label}}<br>
                            @endif
                        @endforeach
                    </td>
                    <td>{{\Carbon\Carbon::createFromTimeString($configmap->toArray()['metadata']['creationTimestamp'], 'UTC')->addHours(7)->toDayDateTimeString()}}</td>
                    <td>
                        <div class="dropdown">
                            <button class="btn btn-outline-info dropdown-toggle" role="button" id="dropdownEditButton" type="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="fa fa-ellipsis-v" aria-hidden="true"></i>
                            </button>
                            <div class="dropdown-menu" aria-labelledby="dropdownEditButton">
                                <a class="dropdown-item editForm {{$configmap->getNamespace()}} {{$configmap->getName()}}" onclick="edit(this)" role="button" data-bs-toggle="modal" data-bs-target="#editForm" href="#">Edit</a>
                                <a class="dropdown-item {{$configmap->getKind()}} {{$configmap->getNamespace()}} {{$configmap->getName()}}" role="button" data-bs-toggle="modal" data-bs-target="#deleteForm" href="#" onclick="deleteData(this)">Delete</a>
                            </div>
                        </div>
                        <div class="configmap" id="{{$configmap->getNamespace().$configmap->getName()}}" style="display: none">{{$configmapDataArr[$configmap->getNamespace().$configmap->getName()]}}</div>
                    </td>
                </tr>
            @endforeach

            </tbody>
        </table>
    @endif


    @if(!is_null($secrets) && count($secrets) != 0)
        <table class="table table-secondary" style="padding-left: 30px" >
            <thead>
                <tr>
                    <td colspan="10"><h3 style="padding-left: 30px" id="secrets_table">Secrets</h3></td>
                </tr>
            </thead>
            <tbody>
            <tr>
                <th>Name</th>
                @if(!strcmp($_GET['namespace']??"no", 'all'))
                    <th>Namespace</th>
                @endif
                <th>Labels</th>
                <th>Type</th>
                <th>Create Time</th>
                <th><i class="fa fa-cog" aria-hidden="true"></i></th>
            </tr>
            @foreach($secrets as $secret)
                <tr>
                    <td><a href="{{ route('secret-details', ['name'=>$secret->getName(), 'namespace'=>$secret->getMetadata()['namespace']??'default']) }}">{{$secret->getName()}}</a></td>
                    @if(!strcmp($_GET['namespace']??"no", 'all'))
                        <td>{{$secret->getNamespace()}}</td>
                    @endif
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
                    <td>{{\Carbon\Carbon::createFromTimeString($secret->toArray()['metadata']['creationTimestamp'], 'UTC')->addHours(7)->toDayDateTimeString()}}</td>
                    <td>
                        <div class="dropdown">
                            <button class="btn btn-outline-info dropdown-toggle" role="button" id="dropdownEditButton" type="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="fa fa-ellipsis-v" aria-hidden="true"></i>
                            </button>
                            <div class="dropdown-menu" aria-labelledby="dropdownEditButton">
                                <a class="dropdown-item editForm {{$secret->getNamespace()}} {{$secret->getName()}}" onclick="edit(this)" role="button" data-bs-toggle="modal" data-bs-target="#editForm" href="#">Edit</a>
                                <a class="dropdown-item {{$secret->getKind()}} {{$secret->getNamespace()}} {{$secret->getName()}}" role="button" data-bs-toggle="modal" data-bs-target="#deleteForm" href="#" onclick="deleteData(this)">Delete</a>
                            </div>
                        </div>
                        <div class="secret" id="{{$secret->getNamespace().$secret->getName()}}" style="display: none">{{$secretDataArr[$secret->getNamespace().$secret->getName()]}}</div>
                    </td>
                </tr>
            @endforeach

            </tbody>
        </table>
    @endif


    @if(!is_null($pvcs) && count($pvcs) != 0)
        <table class="table table-secondary" style="padding-left: 30px" >
            <thead>
                <tr>
                    <td colspan="10"><h3 style="padding-left: 30px" id="pvcs_table">Persistent Volume Claims</h3></td>
                </tr>
            </thead>
            <tbody>
            <tr>
                <th>Name</th>
                @if(!strcmp($_GET['namespace']??"no", 'all'))
                    <th>Namespace</th>
                @endif
                <th>Labels</th>
                <th>Status</th>
                <th>Volume</th>
                <th>Capacity</th>
                <th>Access Modes</th>
                <th>Storage Class</th>
                <th>Create Time</th>
                <th><i class="fa fa-cog" aria-hidden="true"></i></th>
            </tr>
            @foreach($pvcs as $pvc)
                <tr>
                    <td><a href="{{ route('pvc-details', ['name'=>$pvc->getName(), 'namespace'=>$pvc->getMetadata()['namespace']??'default']) }}">{{$pvc->getName()}}</a></td>
                    @if(!strcmp($_GET['namespace']??"no", 'all'))
                        <td>{{$pvc->getNamespace()}}</td>
                    @endif
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
                    <td>{{\Carbon\Carbon::createFromTimeString($pvc->toArray()['metadata']['creationTimestamp'], 'UTC')->addHours(7)->toDayDateTimeString()}}</td>
                    <td>
                        <div class="dropdown">
                            <button class="btn btn-outline-info dropdown-toggle" role="button" id="dropdownEditButton" type="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="fa fa-ellipsis-v" aria-hidden="true"></i>
                            </button>
                            <div class="dropdown-menu" aria-labelledby="dropdownEditButton">
                                <a class="dropdown-item editForm {{$pvc->getNamespace()}} {{$pvc->getName()}}" onclick="edit(this)" role="button" data-bs-toggle="modal" data-bs-target="#editForm" href="#">Edit</a>
                                <a class="dropdown-item {{$pvc->getKind()}} {{$pvc->getNamespace()}} {{$pvc->getName()}}" role="button" data-bs-toggle="modal" data-bs-target="#deleteForm" href="#" onclick="deleteData(this)">Delete</a>
                            </div>
                        </div>
                        <div class="pvc" id="{{$pvc->getNamespace().$pvc->getName()}}" style="display: none">{{$pvcDataArr[$pvc->getNamespace().$pvc->getName()]}}</div>
                    </td>
                </tr>
            @endforeach

            </tbody>
        </table>
    @endif

    @if(!is_null($storageclasses) && count($storageclasses) != 0)
        <table class="table table-secondary" style="padding-left: 30px" >
            <thead>
                <tr>
                    <td colspan="10"><h3 style="padding-left: 30px" id="storageclasses_table">Storage Classes</h3></td>
                </tr>
            </thead>
            <tbody>
            <tr>
                <th>Name</th>
                <th>Provisioner</th>
                <th>Parameters</th>
                <th>Create Time</th>
                <th><i class="fa fa-cog" aria-hidden="true"></i></th>
            </tr>
            @foreach($storageclasses as $storageclass)
                <tr>
                    <td><a href="{{ route('storageclass-details', ['name'=>$storageclass->getName()]) }}">{{$storageclass->getName()}}</a></td>
                    <td>{{$storageclass->toArray()['provisioner']}}</td>
                    <td>
                        @foreach($storageclass->toArray()['parameters'] as $key => $value)
                            {{$key}}: {{$value}}<br>
                        @endforeach
                    </td>
                    <td>{{\Carbon\Carbon::createFromTimeString($storageclass->toArray()['metadata']['creationTimestamp'], 'UTC')->addHours(7)->toDayDateTimeString()}}</td>
                    <td>
                        <div class="dropdown">
                            <button class="btn btn-outline-info dropdown-toggle" role="button" id="dropdownEditButton" type="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="fa fa-ellipsis-v" aria-hidden="true"></i>
                            </button>
                            <div class="dropdown-menu" aria-labelledby="dropdownEditButton">
                                <a class="dropdown-item editForm {{$storageclass->getNamespace()}} {{$storageclass->getName()}}" onclick="edit(this)" role="button" data-bs-toggle="modal" data-bs-target="#editForm" href="#">Edit</a>
                                <a class="dropdown-item {{$storageclass->getKind()}} {{$storageclass->getNamespace()}} {{$storageclass->getName()}}" role="button" data-bs-toggle="modal" data-bs-target="#deleteForm" href="#" onclick="deleteData(this)">Delete</a>
                            </div>
                        </div>
                        <div class="storageclass" id="{{$storageclass->getNamespace().$storageclass->getName()}}" style="display: none">{{$storageclassDataArr[$storageclass->getNamespace().$storageclass->getName()]}}</div>
                    </td>
                </tr>
            @endforeach

            </tbody>
        </table>
    @endif


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
