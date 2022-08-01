@extends('layouts.app2', ["namespaces"=>$namespaces])


@section('content')
    @if(!is_null($deployments) && count($deployments) != 0)
    <table class="table table-secondary" style="padding-left: 30px">
        <thead>
            <h3 style="padding-left: 30px"id="deployment_table">Deployments</h3>
        </thead>
        <tbody>
        <tr>
            <td>Name</td>
            @if(!strcmp($_GET['namespace']??"no","all"))
            <td>Namespace</td>
            @endif
            <td>Images</td>
            <td>Labels</td>
            <td>Pods</td>
            <td>Create Time</td>
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
            <td>{{json_decode($deployment->toJson())->metadata->creationTimestamp}}</td>
        </tr>
    @endforeach
        </tbody>
    </table>
    @endif

    @if(!is_null($daemonsets) && count($daemonsets) != 0)
    <table class="table table-secondary" style="padding-left: 30px">
        <thead>
        <h3 style="padding-left: 30px" id="daemonsets_table">Daemonsets</h3>
        </thead>
        <tbody>
        <tr>
            <td>Name</td>
            @if(!strcmp($_GET['namespace']??"no", 'all'))
                <td>Namespace</td>
            @endif
            <td>Images</td>
            <td>Labels</td>
            <td>Pods</td>
            <td>Create Time</td>
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
            <td>{{json_decode($daemonset->toJson())->metadata->creationTimestamp}}</td>
        </tr>

    @endforeach
        </tbody>
    </table>
    @endif


    @if(!is_null($jobs) && count($jobs) != 0)
    <table class="table table-secondary" style="padding-left: 30px" >
        <thead>
        <h3 style="padding-left: 30px" id="jobs_table">Jobs</h3>
        </thead>
        <tbody>
        <tr>
            <td>Name</td>
            @if(!strcmp($_GET['namespace']??"no", 'all'))
                <td>Namespace</td>
            @endif
            <td>Images</td>
            <td>Labels</td>
            <td>Pods</td>
            <td>Create Time</td>
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
                <td>{{json_decode($job->toJson())->metadata->creationTimestamp}}</td>
            </tr>
        @endforeach

        </tbody>
    </table>
    @endif

    @if(!is_null($cronjobs) && count($cronjobs) != 0)
    <table class="table table-secondary" style="padding-left: 30px" >
        <thead>
        <h3 style="padding-left: 30px" id="cronjobs_table">Cron Jobs</h3>
        </thead>
        <tbody>
        <tr>
            <td>Name</td>
            @if(!strcmp($_GET['namespace']??"no", 'all'))
                <td>Namespace</td>
            @endif
            <td>Images</td>
            <td>Labels</td>
            <td>Schedule</td>
            <td>Suspend</td>
            <td>Active</td>
            <td>Last Schedule</td>
            <td>Create Time</td>
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
                <td>{{$cronjob->getMetadata()['creationTimestamp']}}</td>
            </tr>
        @endforeach

        </tbody>
    </table>
    @endif


    @if(!is_null($pods) && count($pods) != 0)
    <table class="table table-secondary" style="padding-left: 30px" >
        <thead>
        <h3 style="padding-left: 30px" id="pods_table">Pods</h3>
        </thead>
        <tbody>
        <tr>
            <td>Name</td>
            @if(!strcmp($_GET['namespace']??"no", 'all'))
                <td>Namespace</td>
            @endif
            <td>Images</td>
            <td>Labels</td>
            <td>Status</td>
            <td>Restarts</td>
            <td>Running on Host</td>
            <td>Create Time</td>
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
                <td>{{json_decode($pod->toJson())->metadata->creationTimestamp}}</td>
            </tr>
        @endforeach

        </tbody>
    </table>
   @endif

    @if(!is_null($statefulsets) && count($statefulsets) != 0)
    <table class="table table-secondary" style="padding-left: 30px" >
        <thead>
        <h3 style="padding-left: 30px" id="statefulsets_table">Stateful Sets</h3>
        </thead>
        <tbody>
        <tr>
            <td>Name</td>
            @if(!strcmp($_GET['namespace']??"no", 'all'))
                <td>Namespace</td>
            @endif
            <td>Images</td>
            <td>Labels</td>
            <td>Pods</td>
            <td>Create Time</td>
        </tr>
        @foreach($statefulsets as $statefulset)
            <tr>
                <td>{{$statefulset->getName()}}</td>
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
                <td>{{$statefulset->toArray()['metadata']['creationTimestamp']}}</td>
            </tr>
        @endforeach

        </tbody>
    </table>
    @endif

@endsection
