@extends('layouts.app2', ["namespaces"=>$namespaces])

@section('content')


    @if(!is_null($services) && count($services) != 0)
        <table class="table table-secondary" style="padding-left: 30px" >
            <thead>
                <tr>
                    <td colspan="10"><h3 style="padding-left: 30px" id="services_table">Services</h3></td>
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
                <th>Cluster IP</th>
                <th>Ports</th>
                <th>External IP</th>
                <th>Create Time</th>
                <th><i class="fa fa-cog" aria-hidden="true"></i></th>
            </tr>
            @foreach($services as $service)
                <tr>
                    <td><a href="{{ route('service-details', ['name'=>$service->getName(), 'namespace'=>$service->getMetadata()['namespace']??'default']) }}">{{$service->getName()}}</a></td>
                    @if(!strcmp($_GET['namespace']??"no", 'all'))
                        <td>{{$service->toArray()['metadata']['namespace']}}</td>
                    @endif
                    <td>
                        @foreach($service->toArray()['metadata']['labels']??json_decode('{"":""}') as $key => $label)
                            @if($key == "")
                                -
                            @else
                                {{$key}}: {{$label}}<br>
                            @endif
                        @endforeach
                    </td>
                    <td>{{$service->toArray()['spec']['type']}}</td>
                    <td>
                        @foreach($service->toArray()['spec']['clusterIPs'] as $clusterIP)
                            {{$clusterIP}}<br>
                        @endforeach
                    </td>
                    <td>
                        @foreach($service->toArray()['spec']['ports'] as $port)
                            Name: {{$port['name']??"-"}}; Protocol: {{$port['protocol']}}<br>Port: {{$port['port']}}; Target Port: {{$port['targetPort']}}; Node Port: {{$port['nodePort']??"-"}}<hr>
                        @endforeach
                    </td>
                    <td>
                        @foreach($service->toArray()['status']['loadBalancer']['ingress']??["-"] as $externalIP)
                            {{$externalIP['ip']??"-"}}<br>
                        @endforeach
                    </td>
                    <td>{{\Carbon\Carbon::createFromTimeString($service->toArray()['metadata']['creationTimestamp'], 'UTC')->addHours(7)->toDayDateTimeString()}}</td>
                    <td>
                        <div class="dropdown">
                            <button class="btn btn-outline-info dropdown-toggle" role="button" id="dropdownEditButton" type="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="fa fa-ellipsis-v" aria-hidden="true"></i>
                            </button>
                            <div class="dropdown-menu" aria-labelledby="dropdownEditButton">
                                <a class="dropdown-item editForm {{$service->getNamespace()}} {{$service->getName()}}" onclick="edit(this)" role="button" data-bs-toggle="modal" data-bs-target="#editForm" href="#">Edit</a>
                                <a class="dropdown-item {{$service->getKind()}} {{$service->getNamespace()}} {{$service->getName()}}" role="button" data-bs-toggle="modal" data-bs-target="#deleteForm" href="#" onclick="deleteData(this)">Delete</a>
                            </div>
                        </div>
                        <div class="service" id="{{$service->getNamespace().$service->getName()}}" style="display: none">{{$serviceDataArr[$service->getNamespace().$service->getName()]}}</div>
                    </td>
                </tr>
            @endforeach

            </tbody>
        </table>
    @endif

    @if(!is_null($ingresses) && count($ingresses) != 0)
        <table class="table table-secondary" style="padding-left: 30px" >
            <thead>
                <tr>
                    <td colspan="10"><h3 style="padding-left: 30px" id="ingresses_table">Ingresses</h3></td>
                </tr>
            </thead>
            <tbody>
            <tr>
                <th>Name</th>
                @if(!strcmp($_GET['namespace']??"no", 'all'))
                    <th>Namespace</th>
                @endif
                <th>Labels</th>
                <th>Host</th>
                <th>Paths</th>
                <th>Service</th>
                <th>Create Time</th>
                <th><i class="fa fa-cog" aria-hidden="true"></i></th>
            </tr>
            @foreach($ingresses as $ingress)
                <tr>
                    <td><a href="{{ route('ingress-details', ['name'=>$ingress->getName(), 'namespace'=>$ingress->getMetadata()['namespace']??'default']) }}">{{$ingress->getName()}}</a></td>
                    @if(!strcmp($_GET['namespace']??"no", 'all'))
                        <td>{{$ingress->toArray()['metadata']['namespace']}}</td>
                    @endif
                    <td>
                        @foreach($ingress->toArray()['metadata']['labels']??json_decode('{"":""}') as $key => $label)
                            @if($key == "")
                                -
                            @else
                                {{$key}}: {{$label}}<br>
                            @endif
                        @endforeach
                    </td>
                    <td>
                        @foreach($ingress->toArray()['spec']['rules'] as $rule)
                            <a href="{{'https://'.$rule['host']}}" target="_blank">{{$rule['host']}}</a><br>
                        @endforeach
                    </td>
                    <td>
                        @foreach($ingress->toArray()['spec']['rules'] as $rule)
                            @foreach($rule['http']['paths'] as $path)
                                {{$path['path']}}<br>
                            @endforeach
                        @endforeach
                    </td>
                    <td>
                        @foreach($ingress->toArray()['spec']['rules'] as $rule)
                            @foreach($rule['http']['paths'] as $path)
                                {{$path['backend']['service']['name']}} (Port: {{$path['backend']['service']['port']['number']}})<br>
                            @endforeach
                        @endforeach
                    </td>
                    <td>{{\Carbon\Carbon::createFromTimeString($ingress->toArray()['metadata']['creationTimestamp'], 'UTC')->addHours(7)->toDayDateTimeString()}}</td>
                    <td>
                        <div class="dropdown">
                            <button class="btn btn-outline-info dropdown-toggle" role="button" id="dropdownEditButton" type="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="fa fa-ellipsis-v" aria-hidden="true"></i>
                            </button>
                            <div class="dropdown-menu" aria-labelledby="dropdownEditButton">
                                <a class="dropdown-item editForm {{$ingress->getNamespace()}} {{$ingress->getName()}}" onclick="edit(this)" role="button" data-bs-toggle="modal" data-bs-target="#editForm" href="#">Edit</a>
                                <a class="dropdown-item {{$ingress->getKind()}} {{$ingress->getNamespace()}} {{$ingress->getName()}}" role="button" data-bs-toggle="modal" data-bs-target="#deleteForm" href="#" onclick="deleteData(this)">Delete</a>
                            </div>
                        </div>
                        <div class="ingress" id="{{$ingress->getNamespace().$ingress->getName()}}" style="display: none">{{$ingressDataArr[$ingress->getNamespace().$ingress->getName()]}}</div>
                    </td>
                </tr>
            @endforeach

            </tbody>
        </table>
    @endif

{{--    TODO INGRESS CLASS PAGE--}}

    @if(!is_null($ingressclasses) && count($ingressclasses) != 0)
        <table class="table table-secondary" style="padding-left: 30px" >
            <thead>
                <tr>
                    <td colspan="10"><h3 style="padding-left: 30px" id="ingressclasses_table">Ingress Classes</h3></td>
                </tr>
            </thead>
            <tbody>
            <tr>
                <th>Name</th>
                <th>Controller</th>
                <th>Create Time</th>
                <th><i class="fa fa-cog" aria-hidden="true"></i></th>
            </tr>
            @foreach($ingressclasses as $ingressclass)
                <tr>
                    <td><a href="{{ route('ingressclass-details', ['name'=>$ingressclass['metadata']['name']]) }}">{{$ingressclass['metadata']['name']}}</a></td>
                    <td>{{$ingressclass['spec']['controller']}}</td>
                    <td>{{\Carbon\Carbon::createFromTimeString($ingressclass['metadata']['creationTimestamp'], 'UTC')->addHours(7)->toDayDateTimeString()}}</td>
                    <td>
                        <div class="dropdown">
                            <button class="btn btn-outline-info dropdown-toggle" role="button" id="dropdownEditButton" type="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="fa fa-ellipsis-v" aria-hidden="true"></i>
                            </button>
                            <div class="dropdown-menu" aria-labelledby="dropdownEditButton">
                                <a class="dropdown-item editForm IngressClass {{$ingressclass['metadata']['name']}}" onclick="edit(this)" role="button" data-bs-toggle="modal" data-bs-target="#editForm" href="#">Edit</a>
                                <a class="dropdown-item IngressClass IngressClass {{$ingressclass['metadata']['name']}}" role="button" data-bs-toggle="modal" data-bs-target="#deleteForm" href="#" onclick="deleteData(this)">Delete</a>
                            </div>
                        </div>
                        <div class="ingressclass" id="{{'IngressClass'.$ingressclass['metadata']['name']}}" style="display: none">{{$ingressclassDataArr[$ingressclass['metadata']['name']]}}</div>
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
