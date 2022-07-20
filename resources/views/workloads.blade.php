@extends('layouts.app2')


@section('content')
    <table class="table table-secondary" style="padding-left: 30px">
        <thead>
            <h3 style="padding-left: 30px">Deployments</h3>
        </thead>
        <tbody>
        <tr>
            <td>Name</td>
            <td>Images</td>
            <td>Labels</td>
            <td>Pods</td>
            <td>Create Time</td>
        </tr>
    @foreach($deployments as $deployment)
{{--        <h1>{{$deployment->getName()}}</h1>--}}
        <tr>
            <td>{{$deployment->getName()}}</td>
            <td>
                @foreach(json_decode($deployment->toJson())->spec->template->spec->containers as $container)
                    {{$container->image}}<br>
                @endforeach
            </td>
            <td>
            @foreach($deployment->getLabels() as $key=>$data)
                {{$key}} : {{$data}}<br>
            @endforeach
            </td>
            <td>{{json_decode($deployment->toJson())->status->readyReplicas}}/{{json_decode($deployment->toJson())->status->replicas}}</td>
            <td>{{json_decode($deployment->toJson())->metadata->creationTimestamp}}</td>
        </tr>
    @endforeach
        </tbody>
    </table>

    <table class="table table-secondary" style="padding-left: 30px">
        <thead>
        <h3 style="padding-left: 30px">Daemonsets</h3>
        </thead>
        <tbody>
        <tr>
            <td>Name</td>
            <td>Images</td>
            <td>Labels</td>
            <td>Pods</td>
            <td>Create Time</td>
        </tr>
    @foreach($daemonsets as $daemonset)
        <tr>
            <td>{{$daemonset->getName()}}</td>
            <td>
            @foreach(json_decode($daemonset->toJson())->spec->template->spec->containers as $container)
                {{$container->image}}<br>
            @endforeach
            </td>
            <td>
                @foreach($daemonset->getLabels() as $key => $label)
                    {{$key}} : {{$label}}<br>
                @endforeach
            </td>
            <td>{{json_decode($daemonset->toJson())->status->currentNumberScheduled}}/{{json_decode($daemonset->toJson())->status->desiredNumberScheduled}}</td>
            <td>{{json_decode($daemonset->toJson())->metadata->creationTimestamp}}</td>
        </tr>

    @endforeach
        </tbody>
    </table>


    <table class="table table-secondary" style="padding-left: 30px">
        <thead>
        <h3 style="padding-left: 30px">Jobs</h3>
        </thead>
        <tbody>
        <tr>
            <td>Name</td>
            <td>Images</td>
            <td>Labels</td>
            <td>Pods</td>
            <td>Create Time</td>
        </tr>
        @foreach($jobs as $job)
            <tr>
                <td>{{$job->getName()}}</td>
                <td>
                    @foreach(json_decode($job->toJson())->spec->template->spec->containers as $container)
                        {{$container->image}}<br>
                    @endforeach
                </td>
                <td>
                    @foreach($job->getLabels() as $key => $label)
                        {{$key}} : {{$label}}<br>
                    @endforeach
                </td>
                <td>{{json_decode($job->toJson())->status->ready}}/{{json_decode($job->toJson())->status->succeeded}}</td>
                <td>{{json_decode($job->toJson())->metadata->creationTimestamp}}</td>
            </tr>
        @endforeach

        </tbody>
    </table>

@if(\PHPUnit\Framework\isNull($cronjobs) == false)
    <table class="table table-secondary" style="padding-left: 30px">
        <thead>
        <h3 style="padding-left: 30px">Cron Jobs</h3>
        </thead>
        <tbody>
        <tr>
            <td>Name</td>
            <td>Images</td>
            <td>Labels</td>
            <td>Pods</td>
            <td>Create Time</td>
        </tr>
        @foreach($cronjobs as $cronjob)
            <tr>
                <td>{{$cronjob->getName()}}</td>
                <td>
                    @foreach(json_decode($cronjob->toJson())->spec->template->spec->containers as $container)
                        {{$container->image}}<br>
                    @endforeach
                </td>
                <td>
                    @foreach($cronjob->getLabels() as $key => $label)
                        {{$key}} : {{$label}}<br>
                    @endforeach
                </td>
                <td>{{json_decode($cronjob->toJson())->status->ready}}/{{json_decode($cronjob->toJson())->status->succeeded}}</td>
                <td>{{json_decode($cronjob->toJson())->metadata->creationTimestamp}}</td>
            </tr>
        @endforeach

        </tbody>
    </table>
@endif


{{--@if(\PHPUnit\Framework\isNull($pods) == false)--}}
    <table class="table table-secondary" style="padding-left: 30px">
        <thead>
        <h3 style="padding-left: 30px">Pods</h3>
        </thead>
        <tbody>
        <tr>
            <td>Name</td>
            <td>Images</td>
            <td>Labels</td>
            <td>Status</td>
            <td>Restarts</td>
            <td>Running on Host</td>
            <td>Create Time</td>
        </tr>
        @foreach($pods as $pod)
            <tr>
                <td>{{$pod->getName()}}</td>
                <td>
                    @foreach(json_decode($pod->toJson())->spec->containers as $container)
                        {{$container->image}}<br>
                    @endforeach
                </td>
                <td>
                    @foreach($pod->getLabels() as $key => $label)
                        {{$key}} : {{$label}}<br>
                    @endforeach
                </td>
                <td>{{json_decode($pod->toJson())->status->phase}}</td>
                <td>
                    @foreach(json_decode($pod->toJson())->status->containerStatuses??[json_decode('{"restartCount":"-"}')] as $status)
                        {{$status->restartCount}}<br>
                    @endforeach
                </td>
                <td>{{ $pod->toArray()['status']['hostIP']??'-' }}</td>
                <td>{{json_decode($pod->toJson())->metadata->creationTimestamp}}</td>
            </tr>
        @endforeach

        </tbody>
    </table>
{{--@endif--}}

    <table class="table table-secondary" style="padding-left: 30px">
        <thead>
        <h3 style="padding-left: 30px">Stateful Sets</h3>
        </thead>
        <tbody>
        <tr>
            <td>Name</td>
            <td>Images</td>
            <td>Labels</td>
            <td>Pods</td>
            <td>Create Time</td>
        </tr>
        @foreach($statefulsets as $statefulset)
            <tr>
                <td>{{$statefulset->getName()}}</td>
                <td>
                    @foreach($statefulset->toArray()['spec']['template']['spec']['containers'] as $container)
                        {{$container['image']}}<br>
                    @endforeach
                </td>
                <td>
                    @foreach($statefulset->getLabels()??[json_decode('{"key":"-"}')] as $key => $label)
                        {{$key}} : {{$label}}<br>
                    @endforeach
                </td>
                <td>{{$statefulset->getReadyReplicasCount()}}/{{$statefulset->getDesiredReplicasCount()}}</td>
                <td>{{$statefulset->toArray()['metadata']['creationTimestamp']}}</td>
            </tr>
        @endforeach

        </tbody>
    </table>

@endsection
