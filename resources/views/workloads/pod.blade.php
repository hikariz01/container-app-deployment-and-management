@extends('layouts.app2', ['namespaces'=>$namespaces])

@section('content')

    <style>
        .table td, .table th {
            padding: 5px 0.4rem;
        }

        .codebox {
            border:1px solid black;
            background-color:#EEEEFF;
            white-space: pre-line;
            padding:10px;
            font-size:0.9em;
            display: inline-block;
        }

        code {
            margin-left: -12.5%;
            width: 90%;
        }
    </style>

    <table class="table table-secondary table-borderless" style="padding-left: 30px">
        <thead>
        <h3 style="padding-left: 30px"id="deployment_table">Metadata</h3>
        </thead>
        <tbody>
        <tr>
            <th>Name</th>
            <th>Namespace</th>
            <th>Created</th>
            <th>Age</th>
            <th>UID</th>
        </tr>
        <tr>
            <td>{{$pod->getName()}}</td>
            <td>{{$pod->getNamespace()}}</td>
            <td>{{$pod->toArray()['metadata']['creationTimestamp']}}</td>
            <td>{{$age}}</td>
            <td>{{$pod->getResourceUid()}}</td>
        </tr>
        <tr>
            <th colspan="5">Labels</th>
        </tr>
        <tr>
            <td colspan="5">
                @foreach($pod->toArray()['metadata']['labels']??json_decode('{"":""}') as $key => $label)
                    @if($key == "")
                        -
                    @else
                        {{$key}}: {{$label}}<br>
                    @endif
                @endforeach
            </td>
        </tr>
        <tr>
            <th colspan="5">Annotations</th>
        </tr>
        <tr>
            <td colspan="5">
                @foreach($pod->toArray()['metadata']['annotations']??json_decode('{"":""}') as $key => $value)
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
            <th>Node</th>
            <th>Status</th>
            <th>IP</th>
            <th>Qos Class</th>
            <th>Restarts</th>
            <th>Service Account</th>
        </tr>
        <tr>
            <td><a href="#">{{$pod->toArray()['spec']['nodeName']}}</a></td>
            <td>{{$pod->getStatus('phase')}}</td>
            <td>
                @foreach($pod->getPodIps() as $podIP)
                    {{$podIP['ip']}}<br>
                @endforeach
            </td>
            <td>{{$pod->getQos()}}</td>
            <td>
                @foreach($pod->getStatus('containerStatuses') as $containerStatus)
                    {{$containerStatus['restartCount']}}<br>
                @endforeach
            </td>
            <td><a href="#">{{$pod->toArray()['spec']['serviceAccountName']??''}}</a></td>
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

        @foreach($pod->getConditions() as $condition)
            <tr>
                <td>{{$condition['type']}}</td>
                <td>{{$condition['status']}}</td>
                <td>{{$condition['lastProbeTime']??'-'}}</td>
                <td>{{$condition['lastTransitionTime']}}</td>
                <td>{{$condition['reason']??'-'}}</td>
                <td>{{$condition['message']??'-'}}</td>
            </tr>
        @endforeach

        </tbody>
    </table>


    <table class="table table-secondary table-borderless" style="padding-left: 30px">
        <thead>
        <h3 style="padding-left: 30px"id="deployment_table">Controlled by</h3>
        </thead>
        <tbody>
        @if($owners != [])
            <tr>
                <th>Name</th>
                <th>Kind</th>
                <th>Pods</th>
                <th>Age</th>
            </tr>
            <tr>
                @foreach($owners as $owner)
    {{--                TODO ทำ Route ที่สามารถเลือก kind ของ workloads ได้--}}
                    <td><a href="#">{{$owner['metadata']['name']}}</a></td>
                    <td>{{$owner['kind']}}</td>
                    <td>{{$owner['status']['readyReplicas']}}/{{$owner['status']['replicas']}}</td>
                    <td>{{$owner['metadata']['creationTimestamp']}}</td>
                @endforeach

            </tr>
            <tr>
                <th colspan="5">Labels</th>
            </tr>
            <tr>
                <td colspan="5">
                    @foreach($owner['metadata']['labels']??json_decode('{"":""}') as $key => $label)
                        @if($key == "")
                            -
                        @else
                            {{$key}}: {{$label}}<br>
                        @endif
                    @endforeach
                </td>
            </tr>
            <tr>
                <th colspan="5">Images</th>
            </tr>
            <tr>
                <td colspan="5">
                    @foreach($owner['spec']['template']['spec']['containers'] as $container)
                        {{$container['image']}}<br>
                    @endforeach
                </td>
            </tr>
        @else
            <tr class="text-center">
                <th colspan="5">No Resource Found...</th>
            </tr>
        @endif
        </tbody>
    </table>


    <table class="table table-secondary table-borderless" style="padding-left: 30px" >
        <thead>
        <h3 style="padding-left: 30px" id="events_table">Persistent Volume Claims</h3>
        </thead>
        <tbody>

        @if(count($pvcs) != 0)
            <tr>
                <th>Name</th>
                <th>Labels</th>
                <th>Status</th>
                <th>Volume</th>
                <th>Capacity</th>
                <th>Access Modes</th>
                <th>Storage Class</th>
                <th>Create Time</th>
            </tr>
            @foreach($pvcs as $pvc)
                <tr>
                    <td><a href="{{ route('pvc-details', ['name'=>$pvc->getName(), 'namespace'=>$pvc->getNamespace()??'default']) }}">{{$pvc->getName()}}</a></td>
                    <td>
                        @foreach($pvc->getLabels() as $key => $label)
                            {{$key}}: {{$label}}<br>
                        @endforeach
                    </td>
                    <td>{{$pvc->getStatus('phase')??'?'}}</td>
                    <td><a href="#">{{$pvc->getSpec('volumeName')}}</a></td>
                    <td>{{$pvc->getStatus('capacity')['storage']}}</td>
                    <td>
                        @foreach($pvc->getStatus('accessModes') as $accessMode)
                            {{$accessMode}}<br>
                        @endforeach
                    </td>
                    <td>{{$pvc->getSpec('storageClassName')}}</td>
                    <td>{{$pvc->getMetadata()['creationTimestamp']}}</td>
                </tr>
            @endforeach
        @else
            <tr class="text-center">
                <th>Resource Not Found...</th>
            </tr>
        @endif

        </tbody>
    </table>

    <table class="table table-secondary" style="padding-left: 30px" >
        <thead>
        <h3 style="padding-left: 30px" id="events_table">Events</h3>
        </thead>
        <tbody>

        @if(count($events) != 0 && !is_null($events))
            <tr>
                <th>Name</th>
                <th>Reason</th>
                <th>Message</th>
                <th>Source</th>
                <th>Sub-Object</th>
                <th>Count</th>
                <th>First Seen</th>
                <th>Last Seen</th>
            </tr>
            @foreach($events as $event)
                @if(str_contains($event->toArray()['involvedObject']['name']??"", $pod->getName()))
                    <tr>
                        <td>{{$event->getName()}}</td>
                        <td>{{$event->toArray()['reason']}}</td>
                        <td>{{$event->toArray()['message']}}</td>
                        <td>{{$event->toArray()['source']['component']??"-"}}/{{$event->toArray()['source']['host']??"-"}}</td>
                        <td>{{$event->toArray()['involvedObject']['kind']}}/{{$event->toArray()['involvedObject']['name']??""}}</td>
                        <td>{{$event->toArray()['count']??"0"}}</td>
                        <td>{{$event->toArray()['firstTimestamp']}}</td>
                        <td>{{$event->toArray()['lastTimestamp']}}</td>
                    </tr>
                @else
                    <tr class="text-center">
                        <th colspan="8">No Event Found...</th>
                    </tr>
                @endif
            @endforeach
        @else
            <tr class="text-center">
                <th>Resource Not Found...</th>
            </tr>
        @endif

        </tbody>
    </table>


    <table class="table table-secondary table-borderless mb-0" style="padding-left: 30px">
        <thead>
        <h3 style="padding-left: 30px"id="deployment_table">Containers</h3>
        </thead>
        @for($i=0;$i < count($containers);$i++)
            <tbody>
                <tr>
                    <th colspan="6"><h4><b>{{$containers[$i]['name']}}</b></h4></th>
                </tr>
                <tr>
                    <th colspan="6">Image</th>
                </tr>
                <tr>
                    <td colspan="6">{{$containers[$i]['image']}}</td>
                </tr>
                <tr>
                    <th colspan="6">Status</th>
                </tr>
                <tr>
                    <th>Ready</th>
                    <th>Started</th>
                    @foreach($containerStatuses[$i]['state']??[] as $key => $value)
                        @if(!strcmp($key, 'running'))
                            <th>Started At</th>
                        @elseif(!strcmp($key, 'terminated') || !strcmp($key, 'waiting'))
                            <th>Reason</th>
                        @else
                            <th>{{ucwords($key)}}</th>
                        @endif
                    @endforeach
                </tr>
                <tr>
                    <td>{{$containerStatuses[$i]['ready'] ? 'true' : 'false'}}</td>
                    <td>{{$containerStatuses[$i]['started'] ? 'true' : 'false'}}</td>
{{--                    <td>{{$containerStatuses[$i]['state']['running']['startedAt']??'-'}}</td>--}}
                    @foreach($containerStatuses[$i]['state']??[] as $key => $value)
                        @if(!strcmp($key, 'running'))
                            <td>{{$value['startedAt']??'-'}}</td>
                        @elseif(!strcmp($key, 'terminated') || !strcmp($key, 'waiting'))
                            <td>{{$value['reason']??'-'}}</td>
                        @else
                            <td>{{$value['reason']??'-'}}</td>
                        @endif
                    @endforeach
                </tr>
                @if(count($containers[$i]['args']??[]) != 0 && !is_null($containers[$i]['args']??null))
                    <tr>
                        <th colspan="6">Arguments</th>
                    </tr>
                    <tr>
                        <td colspan="6"><p>
                                @foreach($containers[$i]['args'] as $arg)
                                    {{$arg}}<br>
                                @endforeach
                            </p>
                        </td>
                    </tr>
                @endif
                @if(count($containers[$i]['env']??[]) != 0 && !is_null($containers[$i]['env']??null))
                    <tr>
                        <th colspan="6">Environment Variables</th>
                    </tr>
                    <tr>
                        @foreach($containers[$i]['env'] as $env)
                            <td >{{$env['name']}}</td>
                        @endforeach
                    </tr>
                    <tr>
                        @foreach($containers[$i]['env'] as $env)
                            <td >{{$env['value']??'-'}}</td>
                        @endforeach
                    </tr>
                @endif
                @if(count($containers[$i]['command']??[]) != 0 && !is_null($containers[$i]['command']??null))
                    <tr>
                        <th colspan="6">Commands</th>
                    </tr>
                    <tr>
                        <td colspan="6">
                            <pre>
                                <code class="codebox">
                                    @foreach($containers[$i]['command'] as $cmd)
                                        {{$cmd}}<br>
                                    @endforeach
                                </code>
                            </pre>
                        </td>
                    </tr>
                @endif
                <tr>
                    <th colspan="6">Mounts</th>
                </tr>
                <tr>
                    <table class="table table-secondary table-bordered mb-0">
                        <tbody>
                            <tr>
                                <th>Name</th>
                                <th>Read Only</th>
                                <th>Mount Path</th>
                                <th>Sub Path</th>
                                <th>Source Type</th>
                                <th>Source Name</th>
                            </tr>
                            @foreach($containers[$i]['volumeMounts'] as $volumeMount)
                                <tr>
                                    <td>{{$volumeMount['name']}}</td>
                                    <td>{{$volumeMount['readOnly']??false ? 'true' : 'false'}}</td>
                                    <td>{{$volumeMount['mountPath']??'-'}}</td>
                                    <td>{{$volumeMount['subPath']??'-'}}</td>
{{--                                    TODO FIND SOURCE TYPE--}}
                                    <td>I Dunno</td>
                                    <td>I Dunno Either...</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </tr>
                @if(count($containers[$i]['securityContext']??[]) != 0 && !is_null($containers[$i]['securityContext']??null))
                    <table class="table table-secondary table-borderless mb-0">
                        <tr>
                            <th colspan="6">Security Context</th>
                        </tr>
                        <tr>
                            @foreach($containers[$i]['securityContext'] as $key => $value)
                                @if(!strcmp($key, 'runAsUser'))
                                    <th>Run as User</th>
                                @elseif(!strcmp($key, 'runAsGroup'))
                                    <th>Run as Group</th>
                                @elseif(!strcmp($key, 'readOnlyRootFilesystem'))
                                    <th>Read Only Filesystem</th>
                                @elseif(!strcmp($key, 'allowPrivilegeEscalation'))
                                    <th>Allow Privilege Escalation</th>
                                @elseif(!strcmp($key, 'capabilities'))
                                    <th>Added Capabilities</th>
                                    <th>Dropped Capabilities</th>
                                @else
                                    <th>{{ucwords($key)}}</th>
                                @endif
                            @endforeach
                        </tr>
                        <tr>
                            @foreach($containers[$i]['securityContext'] as $key => $value)
                                @if(!strcmp($key, 'capabilities'))
                                    <td>
                                    @foreach($containers[$i]['securityContext']['capabilities']['add']??'-' as $addValue)
                                        {{$addValue}}<br>
                                    @endforeach
                                    </td>
                                    <td>
                                        @foreach($containers[$i]['securityContext']['capabilities']['drop']??[] as $addValue)
                                            {{$addValue}}<br>
                                        @endforeach
                                    </td>
                                @elseif($value === true || $value === false)
                                    <td>{{$value ? 'true' : 'false'}}</td>
                                @else
                                    <td>{{$value}}</td>
                                @endif
                            @endforeach
                        </tr>
                    </table>
                @endif

                @if(count($containers[$i]['livenessProbe']??[]) != 0 && !is_null($containers[$i]['livenessProbe']??null))
                    <table class="table table-secondary table-borderless mb-0">
                        <tr>
                            <th colspan="6">Liveness Probe</th>
                        </tr>
                        <tr>
                            <th>Initial Delay (Seconds)</th>
                            <th>Timeout (Seconds)</th>
                            <th>Probe Period (Seconds)</th>
                            <th>Success Threshold</th>
                            <th>Failure Threshold</th>
                            <th>HTTP Healthcheck URI</th>
                        </tr>
                        <tr>
                            <td>{{$containers[$i]['livenessProbe']['initialDelaySeconds']}}</td>
                            <td>{{$containers[$i]['livenessProbe']['timeoutSeconds']}}</td>
                            <td>{{$containers[$i]['livenessProbe']['periodSeconds']}}</td>
                            <td>{{$containers[$i]['livenessProbe']['successThreshold']}}</td>
                            <td>{{$containers[$i]['livenessProbe']['failureThreshold']}}</td>

                            @if(count($containers[$i]['livenessProbe']['httpGet']??[]) != 0 && !is_null($containers[$i]['livenessProbe']['httpGet']??null))
                                <td>{{strtolower($containers[$i]['livenessProbe']['httpGet']['scheme']??'')}}://{{$containers[$i]['livenessProbe']['httpGet']['host']??'[host]'}}:{{$containers[$i]['livenessProbe']['httpGet']['port']??''}}{{$containers[$i]['livenessProbe']['httpGet']['path']??''}}</td>
                            @else
                                <td>-</td>
                            @endif

                        </tr>
                    </table>
                @endif

                @if(count($containers[$i]['readinessProbe']??[]) != 0 && !is_null($containers[$i]['readinessProbe']??null))
                    <table class="table table-secondary table-borderless mb-0">
                        <tr>
                            <th colspan="6">Readiness Probe</th>
                        </tr>
                        <tr>
                            <th>Timeout (Seconds)</th>
                            <th>Probe Period (Seconds)</th>
                            <th>Success Threshold</th>
                            <th>Failure Threshold</th>
                            <th>HTTP Healthcheck URI</th>
                        </tr>
                        <tr>
                            <td>{{$containers[$i]['readinessProbe']['timeoutSeconds']}}</td>
                            <td>{{$containers[$i]['readinessProbe']['periodSeconds']}}</td>
                            <td>{{$containers[$i]['readinessProbe']['successThreshold']}}</td>
                            <td>{{$containers[$i]['readinessProbe']['failureThreshold']}}</td>

                            @if(count($containers[$i]['readinessProbe']['httpGet']??[]) != 0 && !is_null($containers[$i]['readinessProbe']['httpGet']??null))
                                <td>{{strtolower($containers[$i]['readinessProbe']['httpGet']['scheme']??'')}}://{{$containers[$i]['readinessProbe']['httpGet']['host']??'[host]'}}:{{$containers[$i]['readinessProbe']['httpGet']['port']??''}}{{$containers[$i]['readinessProbe']['httpGet']['path']??''}}</td>
                            @else
                                <td>-</td>
                            @endif

                        </tr>
                    </table>
                @endif


                @if(count($containers[$i]['startupProbe']??[]) != 0 && !is_null($containers[$i]['startupProbe']??null))
                    <table class="table table-secondary table-borderless mb-0">
                        <tr>
                            <th colspan="6">Startup Probe</th>
                        </tr>
                        <tr>
                            <th>Initial Delay (Seconds)</th>
                            <th>Timeout (Seconds)</th>
                            <th>Probe Period (Seconds)</th>
                            <th>Success Threshold</th>
                            <th>Failure Threshold</th>
                            <th>HTTP Healthcheck URI</th>
                        </tr>
                        <tr>
                            <td>{{$containers[$i]['startupProbe']['initialDelaySeconds']}}</td>
                            <td>{{$containers[$i]['startupProbe']['timeoutSeconds']}}</td>
                            <td>{{$containers[$i]['startupProbe']['periodSeconds']}}</td>
                            <td>{{$containers[$i]['startupProbe']['successThreshold']}}</td>
                            <td>{{$containers[$i]['startupProbe']['failureThreshold']}}</td>

                            @if(count($containers[$i]['startupProbe']['httpGet']??[]) != 0 && !is_null($containers[$i]['startupProbe']['httpGet']??null))
                                <td>{{strtolower($containers[$i]['startupProbe']['httpGet']['scheme']??'')}}://{{$containers[$i]['startupProbe']['httpGet']['host']??'[host]'}}:{{$containers[$i]['startupProbe']['httpGet']['port']??''}}{{$containers[$i]['startupProbe']['httpGet']['path']??''}}</td>
                            @else
                                <td>-</td>
                            @endif

                        </tr>
                    </table>
                @endif


            </tbody>
        @endfor
    </table>


@endsection
