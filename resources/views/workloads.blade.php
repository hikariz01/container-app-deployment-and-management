@extends('layouts.app2', ["namespaces"=>$namespaces])


@section('content')
    @if(!is_null($deployments) && count($deployments) != 0)
    <table class="table table-secondary" style="padding-left: 30px">
        <thead>
            <tr>
                <td colspan="10"><h3 style="padding-left: 30px"id="deployment_table">Deployments</h3></td>
            </tr>
        </thead>
        <tbody>
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
    @foreach($deployments as $deployment)
{{--        <h1>{{$deployment->getName()}}</h1>--}}
        <tr>
            <td><a href="{{ route('deployment-details', ['name'=>$deployment->getName(), 'namespace'=>$deployment->getMetadata()['namespace']??'default']) }}">{{$deployment->getName()}}</a></td>
            @if(!strcmp($_GET['namespace']??"no", 'all'))
                <td>{{$deployment->toArray()['metadata']['namespace']}}</td>
            @endif
            <td>
                @foreach(json_decode($deployment->toJson())->spec->template->spec->containers as $container)
                    {{$container->image}}<br>
                @endforeach
            </td>
            <td>
                @foreach($deployment->toArray()['metadata']['labels']??json_decode('{"":""}') as $key => $label)
                    @if($key == "")
                        -
                    @else
                        {{$key}}: {{$label}}<br>
                    @endif
                @endforeach
            </td>
            <td>{{json_decode($deployment->toJson())->status->readyReplicas??"0"}}/{{json_decode($deployment->toJson())->status->replicas}}</td>
            <td>{{\Carbon\Carbon::createFromTimeString($deployment->toArray()['metadata']['creationTimestamp'], 'UTC')->addHours(7)->toDayDateTimeString()}}</td>
            <td>
                <div class="dropdown">
                    <button class="btn btn-outline-info dropdown-toggle" role="button" id="dropdownEditButton" type="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="fa fa-ellipsis-v" aria-hidden="true"></i>
                    </button>
                    <div class="dropdown-menu" aria-labelledby="dropdownEditButton">
                        <a class="dropdown-item" href="#">Edit</a>
                        <a class="dropdown-item" href="#">Delete</a>
                    </div>
                </div>
            </td>
        </tr>
    @endforeach
        </tbody>
    </table>
    @endif

    @if(!is_null($daemonsets) && count($daemonsets) != 0)
    <table class="table table-secondary" style="padding-left: 30px">
        <thead>
            <tr>
                <td colspan="10"><h3 style="padding-left: 30px" id="daemonsets_table">Daemonsets</h3></td>
            </tr>
        </thead>
        <tbody>
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
    @foreach($daemonsets as $daemonset)
        <tr>
            <td><a href="{{ route('daemonset-details', ['name'=>$daemonset->getName(), 'namespace'=>$daemonset->getMetadata()['namespace']??'default']) }}">{{$daemonset->getName()}}</a></td>
            @if(!strcmp($_GET['namespace']??"no", 'all'))
                <td>{{$daemonset->toArray()['metadata']['namespace']}}</td>
            @endif
            <td>
            @foreach(json_decode($daemonset->toJson())->spec->template->spec->containers as $container)
                {{$container->image}}<br>
            @endforeach
            </td>
            <td>
                @foreach($daemonset->toArray()['metadata']['labels']??json_decode('{"":""}') as $key => $label)
                    @if($key == "")
                        -
                    @else
                        {{$key}}: {{$label}}<br>
                    @endif
                @endforeach
            </td>
            <td>{{json_decode($daemonset->toJson())->status->currentNumberScheduled}}/{{json_decode($daemonset->toJson())->status->desiredNumberScheduled}}</td>
            <td>{{\Carbon\Carbon::createFromTimeString($daemonset->toArray()['metadata']['creationTimestamp'], 'UTC')->addHours(7)->toDayDateTimeString()}}</td>
            <td>
                <div class="dropdown">
                    <button class="btn btn-outline-info dropdown-toggle" role="button" id="dropdownEditButton" type="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="fa fa-ellipsis-v" aria-hidden="true"></i>
                    </button>
                    <div class="dropdown-menu" aria-labelledby="dropdownEditButton">
                        <a class="dropdown-item" type="button" data-bs-toggle="modal" data-bs-target="#editForm">Edit</a>
                        <a class="dropdown-item" href="#">Delete</a>
                    </div>
                </div>
            </td>
        </tr>

    @endforeach
        </tbody>
    </table>

    <div class="modal fade" id="editForm" tabindex="-1" aria-labelledby="editFormLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editFormLabel">Modal title</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    ...
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary">Save changes</button>
                </div>
            </div>
        </div>
    </div>

    @endif


    @if(!is_null($jobs) && count($jobs) != 0)
    <table class="table table-secondary" style="padding-left: 30px" >
        <thead>
            <tr>
                <td colspan="10"><h3 style="padding-left: 30px" id="jobs_table">Jobs</h3></td>
            </tr>
        </thead>
        <tbody>
        <tr>
            <th>Name</th>
            @if(!strcmp($_GET['namespace']??"no", 'all'))
                <th>Namespace</th>
            @endif
            <th>Images</th>
            <th>Labels</th>
            <th>Pods</th>
            <th>Create Time</th>
        </tr>
        @foreach($jobs as $job)
            <tr>
                <td><a href="{{route('job-details', ['name'=>$job->getName(), 'namespace'=>$job->getMetadata()['namespace']??'default'])}}">{{$job->getName()}}</a></td>
                @if(!strcmp($_GET['namespace']??"no", 'all'))
                    <td>{{$job->toArray()['metadata']['namespace']}}</td>
                @endif
                <td>
                    @foreach(json_decode($job->toJson())->spec->template->spec->containers as $container)
                        {{$container->image}}<br>
                    @endforeach
                </td>
                <td>
                    @foreach($job->toArray()['metadata']['labels']??json_decode('{"":""}') as $key => $label)
                        @if($key == "")
                            -
                        @else
                            {{$key}}: {{$label}}<br>
                        @endif
                    @endforeach
                </td>
                <td>{{json_decode($job->toJson())->status->ready}}/{{json_decode($job->toJson())->status->succeeded??'1'}}</td>
                <td>{{\Carbon\Carbon::createFromTimeString($job->toArray()['metadata']['creationTimestamp'], 'UTC')->addHours(7)->toDayDateTimeString()}}</td>
            </tr>
        @endforeach

        </tbody>
    </table>
    @endif

    @if(!is_null($cronjobs) && count($cronjobs) != 0)
    <table class="table table-secondary" style="padding-left: 30px" >
        <thead>
            <tr>
                <td colspan="10"><h3 style="padding-left: 30px" id="cronjobs_table">Cron Jobs</h3></td>
            </tr>
        </thead>
        <tbody>
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
        </tr>
        @foreach($cronjobs as $cronjob)
            <tr>
                <td><a href="{{ route('cronjob-details', ['name'=>$cronjob->getName(), 'namespace'=>$cronjob->getMetadata()['namespace']??'default']) }}">{{$cronjob->getName()}}</a></td>
                @if(!strcmp($_GET['namespace']??"no", 'all'))
                    <td>{{$cronjob->getMetadata()['namespace']}}</td>
                @endif
                <td>
                    @foreach($cronjob->getJobTemplate()->getTemplate()->getSpec('containers') as $container)
                        {{$container['image']}}<br>
                    @endforeach
                </td>
                <td>
                    @foreach($cronjob->getMetadata()['labels']??[''=>''] as $key => $label)
                        @if(strcmp($key, ''))
                            {{$key}}: {{$label}}<br>
                        @else
                            -
                        @endif
                    @endforeach
                </td>
                <td>{{$cronjob->getSpec('schedule')}}</td>
                <td>{{$cronjob->getSpec('suspend') ? 'true' : 'false'}}</td>
                <td>{{count($cronjob->getActiveJobs()->toArray())}}</td>
                <td>{{date('d-m-Y H:i:s',$cronjob->getLastSchedule()->getTimestamp())}}</td>
                <td>{{\Carbon\Carbon::createFromTimeString($cronjob->toArray()['metadata']['creationTimestamp'], 'UTC')->addHours(7)->toDayDateTimeString()}}</td>
            </tr>
        @endforeach

        </tbody>
    </table>
    @endif


    @if(!is_null($pods) && count($pods) != 0)
    <table class="table table-secondary" style="padding-left: 30px" >
        <thead>
            <tr>
                <td colspan="10"><h3 style="padding-left: 30px" id="pods_table">Pods</h3></td>
            </tr>
        </thead>
        <tbody>
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
        </tr>
        @foreach($pods as $pod)
            <tr>
                <td><a href="{{ route('pod-details', ['name'=>$pod->getName(), 'namespace'=>$pod->getMetadata()['namespace']??'default']) }}">{{$pod->getName()}}</a></td>
                @if(!strcmp($_GET['namespace']??"no", 'all'))
                    <td>{{$pod->toArray()['metadata']['namespace']}}</td>
                @endif
                <td>
                    @foreach(json_decode($pod->toJson())->spec->containers as $container)
                        {{$container->image}}<br>
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
                <td>{{json_decode($pod->toJson())->status->phase}}</td>
                <td>
                    @foreach(json_decode($pod->toJson())->status->containerStatuses??[json_decode('{"restartCount":"-"}')] as $status)
                        {{$status->restartCount}}<br>
                    @endforeach
                </td>
                <td>{{ $pod->getSpec('nodeName')??'-'}}</td>
                <td>{{\Carbon\Carbon::createFromTimeString($pod->toArray()['metadata']['creationTimestamp'], 'UTC')->addHours(7)->toDayDateTimeString()}}</td>
            </tr>
        @endforeach

        </tbody>
    </table>
    @endif



    @if(!is_null($replicasets) && count($replicasets) != 0)
        <table class="table table-secondary" style="padding-left: 30px" >
            <thead>
                <tr>
                    <td colspan="10"><h3 style="padding-left: 30px" id="replicasets_table">Replicasets</h3></td>
                </tr>
            </thead>
            <tbody>
            <tr>
                <th>Name</th>
                @if(!strcmp($_GET['namespace']??"no", 'all'))
                    <th>Namespace</th>
                @endif
                <th>Images</th>
                <th>Labels</th>
                <th>Pods</th>
                <th>Create Time</th>
            </tr>
            @foreach($replicasets as $replicaset)
                <tr>
                    <td><a href="{{ route('replicaset-details', ['name'=>$replicaset['metadata']['name'], 'namespace'=>$replicaset['metadata']['namespace']??'default']) }}">{{$replicaset['metadata']['name']}}</a></td>
                    @if(!strcmp($_GET['namespace']??"no", 'all'))
                        <td>{{$replicaset['metadata']['namespace']}}</td>
                    @endif
                    <td>
                        @foreach($replicaset['spec']['template']['spec']['containers'] as $container)
                            {{$container['image']}}<br>
                        @endforeach
                    </td>
                    <td>
                        @foreach($replicaset['metadata']['labels']??json_decode('{"":""}') as $key => $label)
                            @if($key == "")
                                -
                            @else
                                {{$key}}: {{$label}}<br>
                            @endif
                        @endforeach
                    </td>
                    <td>{{$replicaset['status']['readyReplicas']??'0'}}/{{$replicaset['status']['replicas']??'-'}}</td>
                    <td>{{\Carbon\Carbon::createFromTimeString($replicaset['metadata']['creationTimestamp'], 'UTC')->addHours(7)->toDayDateTimeString()}}</td>
                </tr>
            @endforeach

            </tbody>
        </table>
    @endif



    @if(!is_null($statefulsets) && count($statefulsets) != 0)
    <table class="table table-secondary" style="padding-left: 30px" >
        <thead>
            <tr>
                <td colspan="10"><h3 style="padding-left: 30px" id="statefulsets_table">Stateful Sets</h3></td>
            </tr>
        </thead>
        <tbody>
        <tr>
            <th>Name</th>
            @if(!strcmp($_GET['namespace']??"no", 'all'))
                <th>Namespace</th>
            @endif
            <th>Images</th>
            <th>Labels</th>
            <th>Pods</th>
            <th>Create Time</th>
        </tr>
        @foreach($statefulsets as $statefulset)
            <tr>
                <td><a href="{{ route('statefulset-details', ['name'=>$statefulset->getName(), 'namespace'=>$statefulset->getMetadata()['namespace']??'default']) }}">{{$statefulset->getName()}}</a></td>
                @if(!strcmp($_GET['namespace']??"no", 'all'))
                    <td>{{$statefulset->toArray()['metadata']['namespace']}}</td>
                @endif
                <td>
                    @foreach($statefulset->toArray()['spec']['template']['spec']['containers'] as $container)
                        {{$container['image']}}<br>
                    @endforeach
                </td>
                <td>
                    @foreach($statefulset->toArray()['metadata']['labels']??json_decode('{"":""}') as $key => $label)
                        @if($key == "")
                            -
                        @else
                            {{$key}}: {{$label}}<br>
                        @endif
                    @endforeach
                </td>
                <td>{{$statefulset->getReadyReplicasCount()}}/{{$statefulset->getDesiredReplicasCount()}}</td>
                <td>{{\Carbon\Carbon::createFromTimeString($statefulset->toArray()['metadata']['creationTimestamp'], 'UTC')->addHours(7)->toDayDateTimeString()}}</td>
            </tr>
        @endforeach

        </tbody>
    </table>
    @endif

@endsection
