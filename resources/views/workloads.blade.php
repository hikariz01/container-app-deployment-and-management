@extends('layouts.app2', ["namespaces"=>$namespaces])


@section('content')

    <div class="container">
        <div class="row">
            <div class="col-12">
                @if(!is_null($deployments) && count($deployments) != 0)
                    <table class="table table-secondary dashboard" id="deployment-table">
                        <h3 style="padding-left: 30px"id="deployment_table">Deployments</h3>
                        <thead>
                        <tr>
                            <th>Name</th>
                            @if(!strcmp($_GET['namespace']??"no","all"))
                                <th>Namespace</th>
                            @endif
                            <th>Images</th>
                            <th>Labels</th>
                            <th>Pods</th>
                            <th>Create Time</th>
                            <th><i class="fa fa-cog" aria-hidden="true"></i></th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($deployments as $deployment)
                            {{--        <h1>{{$deployment->getName()}}</h1>--}}
                            <tr>
                                <td><a href="{{ route('deployment-details', ['name'=>$deployment->getName(), 'namespace'=>$deployment->getMetadata()['namespace']??'default']) }}">{{$deployment->getName()}}</a></td>
                                @if(!strcmp($_GET['namespace']??"no", 'all'))
                                    <td>{{$deployment->toArray()['metadata']['namespace']}}</td>
                                @endif
                                <td>
                                    @foreach(json_decode($deployment->toJson())->spec->template->spec->containers as $container)
                                        <div class="badge badge-pill bg-primary">
                                            {{$container->image}}
                                        </div><br>
                                    @endforeach
                                </td>
                                <td>
                                    @foreach($deployment->toArray()['metadata']['labels']??json_decode('{"":""}') as $key => $label)
                                        @if($key == "")
                                            -
                                        @else
                                            <div class="badge badge-pill bg-primary">
                                                {{$key}}: {{$label}}
                                            </div><br>
                                        @endif
                                    @endforeach
                                </td>
                                <td>{{$deployment->getReadyReplicasCount()??"0"}}/{{$deployment->getDesiredReplicasCount()}}</td>
                                <td>{{\Carbon\Carbon::createFromTimeString($deployment->toArray()['metadata']['creationTimestamp'], 'UTC')->addHours(7)->toDayDateTimeString()}}</td>
                                <td style="overflow: visible;">
                                    <div class="dropdown">
                                        <button class="btn btn-outline-info dropdown-toggle" role="button" id="dropdownEditButton" type="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            <i class="fa fa-ellipsis-v" aria-hidden="true"></i>
                                        </button>
                                        <div class="dropdown-menu" aria-labelledby="dropdownEditButton">
                                            <a class="dropdown-item {{$deployment->getKind()}} {{$deployment->getNamespace()}} {{$deployment->getName()}} {{$deployment->getSpec('replicas')}}" role="button" data-bs-toggle="modal" data-bs-target="#scaleForm" href="#" onclick="scaleResource(this)">Scale</a>
                                            <a class="dropdown-item editForm {{$deployment->getNamespace()}} {{$deployment->getName()}}" onclick="edit(this)" role="button" data-bs-toggle="modal" data-bs-target="#editForm" href="#">Edit</a>
                                            <a class="dropdown-item {{$deployment->getKind()}} {{$deployment->getNamespace()}} {{$deployment->getName()}}" role="button" data-bs-toggle="modal" data-bs-target="#deleteForm" href="#" onclick="deleteData(this)">Delete</a>
                                        </div>
                                    </div>
                                    <div class="deployment" id="{{$deployment->getNamespace().$deployment->getName()}}" style="display: none">{{$deploymentDataArr[$deployment->getNamespace().$deployment->getName()]}}</div>
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
            @if(!is_null($daemonsets) && count($daemonsets) != 0)
                <table class="table table-secondary dashboard">
                    <h3 style="padding-left: 30px" id="daemonsets_table">Daemonsets</h3>
                    <thead>
                    <tr>
                        <th>Name</th>
                        @if(!strcmp($_GET['namespace']??"no", 'all'))
                            <th>Namespace</th>
                        @endif
                        <th>Images</th>
                        <th>Labels</th>
                        <th>Pods</th>
                        <th>Create Time</th>
                        <th><i class="fa fa-cog" aria-hidden="true"></i></th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($daemonsets as $daemonset)
                        <tr>
                            <td><a href="{{ route('daemonset-details', ['name'=>$daemonset->getName(), 'namespace'=>$daemonset->getMetadata()['namespace']??'default']) }}">{{$daemonset->getName()}}</a></td>
                            @if(!strcmp($_GET['namespace']??"no", 'all'))
                                <td>{{$daemonset->toArray()['metadata']['namespace']}}</td>
                            @endif
                            <td>
                                @foreach(json_decode($daemonset->toJson())->spec->template->spec->containers as $container)
                                    <div class="badge badge-pill bg-primary">
                                            {{$container->image}}
                                        </div><br>
                                @endforeach
                            </td>
                            <td>
                                @foreach($daemonset->toArray()['metadata']['labels']??json_decode('{"":""}') as $key => $label)
                                    @if($key == "")
                                        -
                                    @else
                                        <div class="badge badge-pill bg-primary">
                                            {{$key}}: {{$label}}
                                        </div><br>
                                    @endif
                                @endforeach
                            </td>
                            <td>{{json_decode($daemonset->toJson())->status->currentNumberScheduled}}/{{json_decode($daemonset->toJson())->status->desiredNumberScheduled}}</td>
                            <td>{{\Carbon\Carbon::createFromTimeString($daemonset->toArray()['metadata']['creationTimestamp'], 'UTC')->addHours(7)->toDayDateTimeString()}}</td>
                            <td style="overflow: visible">
                                <div class="dropdown">
                                    <button class="btn btn-outline-info dropdown-toggle" role="button" id="dropdownEditButton" type="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <i class="fa fa-ellipsis-v" aria-hidden="true"></i>
                                    </button>
                                    <div class="dropdown-menu" aria-labelledby="dropdownEditButton">
                                        <a class="dropdown-item editForm {{$daemonset->getNamespace()}} {{$daemonset->getName()}}" onclick="edit(this)" role="button" data-bs-toggle="modal" data-bs-target="#editForm" href="#">Edit</a>
                                        <a class="dropdown-item {{$daemonset->getKind()}} {{$daemonset->getNamespace()}} {{$daemonset->getName()}}" role="button" data-bs-toggle="modal" data-bs-target="#deleteForm" href="#" onclick="deleteData(this)">Delete</a>
                                    </div>
                                </div>
                                <div class="daemonset" id="{{$daemonset->getNamespace().$daemonset->getName()}}" style="display: none">{{$daemonsetDataArr[$daemonset->getNamespace().$daemonset->getName()]}}</div>
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
            @if(!is_null($jobs) && count($jobs) != 0)
                <table class="table table-secondary dashboard">
                    <h3 style="padding-left: 30px" id="jobs_table">Jobs</h3>
                    <thead>
                    <tr>
                        <th>Name</th>
                        @if(!strcmp($_GET['namespace']??"no", 'all'))
                            <th>Namespace</th>
                        @endif
                        <th>Images</th>
                        <th>Labels</th>
                        <th>Pods</th>
                        <th>Create Time</th>
                        <th><i class="fa fa-cog" aria-hidden="true"></i></th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($jobs as $job)
                        <tr>
                            <td><a href="{{route('job-details', ['name'=>$job->getName(), 'namespace'=>$job->getMetadata()['namespace']??'default'])}}">{{$job->getName()}}</a></td>
                            @if(!strcmp($_GET['namespace']??"no", 'all'))
                                <td>{{$job->toArray()['metadata']['namespace']}}</td>
                            @endif
                            <td>
                                @foreach(json_decode($job->toJson())->spec->template->spec->containers as $container)
                                    <div class="badge badge-pill bg-primary">
                                            {{$container->image}}
                                        </div><br>
                                @endforeach
                            </td>
                            <td>
                                @foreach($job->toArray()['metadata']['labels']??json_decode('{"":""}') as $key => $label)
                                    @if($key == "")
                                        -
                                    @else
                                        <div class="badge badge-pill bg-primary">
                                            {{$key}}: {{$label}}
                                        </div><br>
                                    @endif
                                @endforeach
                            </td>
                            <td>{{json_decode($job->toJson())->status->ready}}/{{json_decode($job->toJson())->status->succeeded??'1'}}</td>
                            <td>{{\Carbon\Carbon::createFromTimeString($job->toArray()['metadata']['creationTimestamp'], 'UTC')->addHours(7)->toDayDateTimeString()}}</td>
                            <td style="overflow: visible">
                                <div class="dropdown">
                                    <button class="btn btn-outline-info dropdown-toggle" role="button" id="dropdownEditButton" type="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <i class="fa fa-ellipsis-v" aria-hidden="true"></i>
                                    </button>
                                    <div class="dropdown-menu" aria-labelledby="dropdownEditButton">
                                        <a class="dropdown-item editForm {{$job->getNamespace()}} {{$job->getName()}}" onclick="edit(this)" role="button" data-bs-toggle="modal" data-bs-target="#editForm" href="#">Edit</a>
                                        <a class="dropdown-item {{$job->getKind()}} {{$job->getNamespace()}} {{$job->getName()}}" role="button" data-bs-toggle="modal" data-bs-target="#deleteForm" href="#" onclick="deleteData(this)">Delete</a>
                                    </div>
                                </div>
                                <div class="job" id="{{$job->getNamespace().$job->getName()}}" style="display: none">{{$jobDataArr[$job->getNamespace().$job->getName()]}}</div>
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
            @if(!is_null($cronjobs) && count($cronjobs) != 0)
                <table class="table table-secondary dashboard">
                    <h3 style="padding-left: 30px" id="cronjobs_table">Cron Jobs</h3>
                    <thead>
                    <tr>
                        <th>Name</th>
                        @if(!strcmp($_GET['namespace']??"no", 'all'))
                            <th>Namespace</th>
                        @endif
                        <th>Images</th>
                        <th>Labels</th>
                        <th>Schedule</th>
                        <th>Suspend</th>
                        <th>Active</th>
                        <th>Last Schedule</th>
                        <th>Create Time</th>
                        <th><i class="fa fa-cog" aria-hidden="true"></i></th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($cronjobs as $cronjob)
                        <tr>
                            <td><a href="{{ route('cronjob-details', ['name'=>$cronjob->getName(), 'namespace'=>$cronjob->getMetadata()['namespace']??'default']) }}">{{$cronjob->getName()}}</a></td>
                            @if(!strcmp($_GET['namespace']??"no", 'all'))
                                <td>{{$cronjob->getMetadata()['namespace']}}</td>
                            @endif
                            <td>
                                @foreach($cronjob->getJobTemplate()->getTemplate()->getSpec('containers') as $container)
                                    <div class="badge badge-pill bg-primary">
                                        {{$container['image']}}
                                    </div><br>
                                @endforeach
                            </td>
                            <td>
                                @foreach($cronjob->getMetadata()['labels']??[''=>''] as $key => $label)
                                    @if(strcmp($key, ''))
                                        <div class="badge badge-pill bg-primary">
                                                {{$key}}: {{$label}}
                                        </div><br>
                                    @else
                                        -
                                    @endif
                                @endforeach
                            </td>
                            <td>{{$cronjob->getSpec('schedule')}}</td>
                            <td>{{$cronjob->getSpec('suspend') ? 'true' : 'false'}}</td>
                            <td>{{count($cronjob->getActiveJobs()->toArray())}}</td>
                            @if(is_null($cronjob->getLastSchedule()))
                                <td>-</td>
                            @else
                                <td>{{date('d-m-Y H:i:s',$cronjob->getLastSchedule()->getTimestamp())}}</td>
                            @endif
                            <td>{{\Carbon\Carbon::createFromTimeString($cronjob->toArray()['metadata']['creationTimestamp'], 'UTC')->addHours(7)->toDayDateTimeString()}}</td>
                            <td style="overflow: visible">
                                <div class="dropdown">
                                    <button class="btn btn-outline-info dropdown-toggle" role="button" id="dropdownEditButton" type="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <i class="fa fa-ellipsis-v" aria-hidden="true"></i>
                                    </button>
                                    <div class="dropdown-menu" aria-labelledby="dropdownEditButton">
                                        <a class="dropdown-item editForm {{$cronjob->getNamespace()}} {{$cronjob->getName()}}" onclick="edit(this)" role="button" data-bs-toggle="modal" data-bs-target="#editForm" href="#">Edit</a>
                                        <a class="dropdown-item {{$cronjob->getKind()}} {{$cronjob->getNamespace()}} {{$cronjob->getName()}}" role="button" data-bs-toggle="modal" data-bs-target="#deleteForm" href="#" onclick="deleteData(this)">Delete</a>
                                    </div>
                                </div>
                                <div class="cronjob" id="{{$cronjob->getNamespace().$cronjob->getName()}}" style="display: none">{{$cronjobDataArr[$cronjob->getNamespace().$cronjob->getName()]}}</div>
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
            @if(!is_null($pods) && count($pods) != 0)
                <table class="table table-secondary dashboard" id="pods_table_table">
                    <h3 style="padding-left: 30px" id="pods_table">Pods</h3>
                    <thead>
                    <tr>
                        <th>Name</th>
                        @if(!strcmp($_GET['namespace']??"no", 'all'))
                            <th>Namespace</th>
                        @endif
                        <th>Images</th>
                        <th>Labels</th>
                        <th>Status</th>
                        <th>Restarts</th>
                        <th>Running on Host</th>
                        <th>Create Time</th>
                        <th><i class="fa fa-cog" aria-hidden="true"></i></th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($pods as $pod)
{{--                        @if($pod->getStatus('phase') !== 'Failed')--}}
                            <tr>
                                <td><a href="{{ route('pod-details', ['name'=>$pod->getName(), 'namespace'=>$pod->getMetadata()['namespace']??'default']) }}">{{$pod->getName()}}</a></td>
                                @if(!strcmp($_GET['namespace']??"no", 'all'))
                                    <td>{{$pod->toArray()['metadata']['namespace']}}</td>
                                @endif
                                <td>
                                    @foreach(json_decode($pod->toJson())->spec->containers as $container)
                                        <div class="badge badge-pill bg-primary">
                                            {{$container->image}}
                                        </div><br>
                                    @endforeach
                                </td>
                                <td>
                                    @foreach($pod->toArray()['metadata']['labels']??json_decode('{"":""}') as $key => $label)
                                        @if($key == "")
                                            -
                                        @else
                                            <div class="badge badge-pill bg-primary">
                                                {{$key}}: {{$label}}
                                            </div><br>
                                        @endif
                                    @endforeach
                                </td>
                                <td>
                                    @if ($pod->getPhase() === 'Running' || $pod->getPhase() === 'Succeeded')
                                        <span class="badge badge-pill bg-success">{{$pod->getPhase()}}</span>
                                    @elseif($pod->getPhase() === 'Pending')
                                        <span class="badge badge-pill bg-warning">{{$pod->getPhase()}}</span>
                                    @else
                                        <span class="badge badge-pill bg-danger">{{$pod->getPhase()}}</span>
                                    @endif
                                </td>
                                <td>
                                    @foreach(json_decode($pod->toJson())->status->containerStatuses??[json_decode('{"restartCount":"-"}')] as $status)
                                        {{$status->restartCount}}<br>
                                    @endforeach
                                </td>
                                <td>{{ $pod->getSpec('nodeName')??'-'}}</td>
                                <td>{{\Carbon\Carbon::createFromTimeString($pod->toArray()['metadata']['creationTimestamp'], 'UTC')->addHours(7)->toDayDateTimeString()}}</td>
                                <td style="overflow: visible;">
                                    <div class="dropdown">
                                        <button class="btn btn-outline-info dropdown-toggle" role="button" id="dropdownEditButton" type="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            <i class="fa fa-ellipsis-v" aria-hidden="true"></i>
                                        </button>
                                        <div class="dropdown-menu" aria-labelledby="dropdownEditButton">
                                            <a class="dropdown-item editForm {{$pod->getNamespace()}} {{$pod->getName()}}" onclick="edit(this)" role="button" data-bs-toggle="modal" data-bs-target="#editForm" href="#">Edit</a>
                                            <a class="dropdown-item {{$pod->getKind()}} {{$pod->getNamespace()}} {{$pod->getName()}}" role="button" data-bs-toggle="modal" data-bs-target="#deleteForm" href="#" onclick="deleteData(this)">Delete</a>
                                        </div>
                                    </div>
                                    <div class="pod" id="{{$pod->getNamespace().$pod->getName()}}" style="display: none">{{$podDataArr[$pod->getNamespace().$pod->getName()]}}</div>
                                </td>
                            </tr>
{{--                        @endif--}}
                    @endforeach

                    </tbody>
                </table>
            @endif
        </div>
    </div>



    <div class="row">
        <div class="col-12">
            @if(!is_null($replicasets) && count($replicasets) != 0)
                <table class="table table-secondary dashboard">
                    <h3 style="padding-left: 30px" id="replicasets_table">Replicasets</h3>
                    <thead>
                    <tr>
                        <th>Name</th>
                        @if(!strcmp($_GET['namespace']??"no", 'all'))
                            <th>Namespace</th>
                        @endif
                        <th>Images</th>
                        <th>Labels</th>
                        <th>Pods</th>
                        <th>Create Time</th>
                        <th><i class="fa fa-cog" aria-hidden="true"></i></th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($replicasets as $replicaset)
                        <tr>
                            <td><a href="{{ route('replicaset-details', ['name'=>$replicaset['metadata']['name'], 'namespace'=>$replicaset['metadata']['namespace']??'default']) }}">{{$replicaset['metadata']['name']}}</a></td>
                            @if(!strcmp($_GET['namespace']??"no", 'all'))
                                <td>{{$replicaset['metadata']['namespace']}}</td>
                            @endif
                            <td>
                                @foreach($replicaset['spec']['template']['spec']['containers'] as $container)
                                    <div class="badge badge-pill bg-primary">
                                        {{$container['image']}}
                                    </div><br>
                                @endforeach
                            </td>
                            <td>
                                @foreach($replicaset['metadata']['labels']??json_decode('{"":""}') as $key => $label)
                                    @if($key == "")
                                        -
                                    @else
                                        <div class="badge badge-pill bg-primary">
                                                {{$key}}: {{$label}}
                                        </div><br>
                                    @endif
                                @endforeach
                            </td>
                            <td>{{$replicaset['status']['readyReplicas']??'0'}}/{{$replicaset['status']['replicas']??'-'}}</td>
                            <td>{{\Carbon\Carbon::createFromTimeString($replicaset['metadata']['creationTimestamp'], 'UTC')->addHours(7)->toDayDateTimeString()}}</td>
                            <td style="overflow: visible">
                                <div class="dropdown">
                                    <button class="btn btn-outline-info dropdown-toggle" role="button" id="dropdownEditButton" type="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <i class="fa fa-ellipsis-v" aria-hidden="true"></i>
                                    </button>
                                    <div class="dropdown-menu" aria-labelledby="dropdownEditButton">
                                        <a class="dropdown-item ReplicaSet {{$replicaset['metadata']['namespace']}} {{$replicaset['metadata']['name']}} {{$replicaset['spec']['replicas']}}" role="button" data-bs-toggle="modal" data-bs-target="#scaleForm" href="#" onclick="scaleResource(this)">Scale</a>
                                        <a class="dropdown-item editForm {{$replicaset['metadata']['namespace']}} {{$replicaset['metadata']['name']}}" onclick="edit(this)" role="button" data-bs-toggle="modal" data-bs-target="#editForm" href="#">Edit</a>
                                        <a class="dropdown-item ReplicaSet {{$replicaset['metadata']['namespace']}} {{$replicaset['metadata']['name']}}" role="button" data-bs-toggle="modal" data-bs-target="#deleteForm" href="#" onclick="deleteData(this)">Delete</a>
                                    </div>
                                </div>
                                <div class="replicaset" id="{{$replicaset['metadata']['namespace'].$replicaset['metadata']['name']}}" style="display: none">{{$replicasetDataArr[$replicaset['metadata']['namespace'].$replicaset['metadata']['name']]}}</div>
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
            @if(!is_null($statefulsets) && count($statefulsets) != 0)
                <table class="table table-secondary dashboard">
                    <h3 style="padding-left: 30px" id="statefulsets_table">Stateful Sets</h3>
                    <thead>
                    <tr>
                        <th>Name</th>
                        @if(!strcmp($_GET['namespace']??"no", 'all'))
                            <th>Namespace</th>
                        @endif
                        <th>Images</th>
                        <th>Labels</th>
                        <th>Pods</th>
                        <th>Create Time</th>
                        <th><i class="fa fa-cog" aria-hidden="true"></i></th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($statefulsets as $statefulset)
                        <tr>
                            <td><a href="{{ route('statefulset-details', ['name'=>$statefulset->getName(), 'namespace'=>$statefulset->getMetadata()['namespace']??'default']) }}">{{$statefulset->getName()}}</a></td>
                            @if(!strcmp($_GET['namespace']??"no", 'all'))
                                <td>{{$statefulset->toArray()['metadata']['namespace']}}</td>
                            @endif
                            <td>
                                @foreach($statefulset->toArray()['spec']['template']['spec']['containers'] as $container)
                                    <div class="badge badge-pill bg-primary">
                                        {{$container['image']}}
                                    </div><br>
                                @endforeach
                            </td>
                            <td>
                                @foreach($statefulset->toArray()['metadata']['labels']??json_decode('{"":""}') as $key => $label)
                                    @if($key == "")
                                        -
                                    @else
                                        <div class="badge badge-pill bg-primary">
                                            {{$key}}: {{$label}}
                                        </div><br>
                                    @endif
                                @endforeach
                            </td>
                            <td>{{$statefulset->getReadyReplicasCount()}}/{{$statefulset->getDesiredReplicasCount()}}</td>
                            <td>{{\Carbon\Carbon::createFromTimeString($statefulset->toArray()['metadata']['creationTimestamp'], 'UTC')->addHours(7)->toDayDateTimeString()}}</td>
                            <td style="overflow: visible">
                                <div class="dropdown">
                                    <button class="btn btn-outline-info dropdown-toggle" role="button" id="dropdownEditButton" type="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <i class="fa fa-ellipsis-v" aria-hidden="true"></i>
                                    </button>
                                    <div class="dropdown-menu" aria-labelledby="dropdownEditButton">
                                        <a class="dropdown-item {{$statefulset->getKind()}} {{$statefulset->getNamespace()}} {{$statefulset->getName()}} {{$statefulset->getSpec('replicas')}}" role="button" data-bs-toggle="modal" data-bs-target="#scaleForm" href="#" onclick="scaleResource(this)">Scale</a>
                                        <a class="dropdown-item editForm {{$statefulset->getNamespace()}} {{$statefulset->getName()}}" onclick="edit(this)" role="button" data-bs-toggle="modal" data-bs-target="#editForm" href="#">Edit</a>
                                        <a class="dropdown-item {{$statefulset->getKind()}} {{$statefulset->getNamespace()}} {{$statefulset->getName()}}" role="button" data-bs-toggle="modal" data-bs-target="#deleteForm" href="#" onclick="deleteData(this)">Delete</a>
                                    </div>
                                </div>
                                <div class="statefulset" id="{{$statefulset->getNamespace().$statefulset->getName()}}" style="display: none">{{$statefulsetDataArr[$statefulset->getNamespace().$statefulset->getName()]}}</div>
                            </td>
                        </tr>
                    @endforeach

                    </tbody>
                </table>
            @endif
        </div>
    </div>
    </div>


{{--    MODAL--}}
    <div class="modal fade" id="editForm" tabindex="-1" aria-labelledby="editFormLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editFormLabel">Edit Resource</h5>
                    <form action="{{ route('download-file') }}" method="POST" onsubmit="updateData()" style="margin-left: 20px">
                        @csrf
                        <input type="hidden" style="display: none" name="resourceName" id="resourceName">
                        <input type="hidden" style="display: none" name="downloadData" id="downloadData">
                        <button type="submit" class="btn btn-success">Download Code</button>
                    </form>
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


    <div class="modal fade" id="scaleForm" tabindex="-1" aria-labelledby="scaleFormLabel" aria-hidden="true">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title text-center" id="scaleFormLabel">Confirmation</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('scale') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <label for="scaleNumber">How many?</label>
                        <input type="number" class="form-control" id="scaleNumber" name="scaleNumber" value="">
                    </div>
                    <input type="hidden" id="scaleValue" name="resource" value="" style="display: none">
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save Changes</button>
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
            document.getElementById('resourceName').value = classname[3]
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
            document.getElementById('downloadData').value = aceEditor.session.getValue()
        }
    </script>


@endsection
