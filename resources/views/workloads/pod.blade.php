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

    </style>

    <nav class="navbar navbar-light bg-info">
        <div class="container-fluid">
            <a class="btn btn-primary" type="button" style="margin-left: auto; margin-right: 8px" href="{{ route('pod-logs', ['namespace'=>$pod->getNamespace(), 'name'=>$pod->getName()]) }}"><i class="fa fa-file" aria-hidden="true"></i> View Logs</a>
            <button class="btn btn-primary" style="margin-right: 8px" data-bs-toggle="modal" data-bs-target="#editForm"><i class="fa fa-cog" aria-hidden="true"></i> Edit</button>
            <button class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteForm"><i class="fa fa-trash" aria-hidden="true"></i> Delete</button>
        </div>
    </nav>

    <table class="table table-secondary table-borderless" style="padding-left: 30px">
        <thead>
        <tr>
            <td colspan="10"><h3 style="padding-left: 30px"id="deployment_table">Metadata</h3></td>
        </tr>
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
            <td>{{\Carbon\Carbon::createFromTimeString($pod->toArray()['metadata']['creationTimestamp'], 'UTC')->addHours(7)->toDayDateTimeString()}}</td>
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
                        <div class="badge badge-pill bg-primary">
                            {{$key}}: {{$label}}
                        </div>
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
                        @if(is_array(json_decode($value, true)))
                            {{$key}}: <button class="btn btn-outline-primary rounded-pill" data-bs-toggle="modal" data-bs-target="#annoJSON" type="button" onclick="updateJSON(this)" value="{{$key}}">JSON</button><br>
                            <div class="{{$key}}" style="display: none">{{$value}}</div>
                        @else
                            {{$key}}: {{$value}}<br>
                        @endif
                    @endif
                @endforeach
            </td>
        </tr>
        </tbody>
    </table>


    @include('layouts.jsonViewModal')


    <table class="table table-secondary table-borderless" style="padding-left: 30px">
        <thead>
        <tr>
            <td colspan="10"><h3 style="padding-left: 30px"id="deployment_table">Resource Information</h3></td>
        </tr>
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
            <td><a href="{{ route('node-details', ['name'=>$pod->getSpec('nodeName')])??'#' }}">{{$pod->toArray()['spec']['nodeName']}}</a></td>
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
            <td><a href="{{ route('serviceaccount-details', ['name'=>$pod->getSpec('serviceAccountName')??'#', 'namespace'=>$pod->getNamespace()??'#'])??'#' }}">{{$pod->toArray()['spec']['serviceAccountName']??'-'}}</a></td>
        </tr>
        </tbody>
    </table>



    <table class="table table-secondary" style="padding-left: 30px">
        <thead>
        <tr>
            <td colspan="10">
                <h3 style="padding-left: 30px"id="deployment_table">Conditions</h3>
            </td>
        </tr>
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
                <td>{{\Carbon\Carbon::createFromTimeString($condition['lastProbeTime']??'0', 'UTC')->isToday() ? '-' : \Carbon\Carbon::createFromTimeString($condition['lastProbeTime']??'0', 'UTC')->addHours(7)->toDayDateTimeString()}}</td>
                <td>{{\Carbon\Carbon::createFromTimeString($condition['lastTransitionTime']??'0', 'UTC')->isToday() ? '-' : \Carbon\Carbon::createFromTimeString($condition['lastTransitionTime']??'0', 'UTC')->addHours(7)->toDayDateTimeString()}}</td>
                <td>{{$condition['reason']??'-'}}</td>
                <td>{{$condition['message']??'-'}}</td>
            </tr>
        @endforeach

        </tbody>
    </table>


    <table class="table table-secondary table-borderless" style="padding-left: 30px">
        <thead>
        <tr>
            <td colspan="10">
                <h3 style="padding-left: 30px"id="deployment_table">Controlled by</h3>
            </td>
        </tr>
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
                @foreach($owners as $key => $owner)
                    @if($owner['kind'] === 'ReplicaSet')
                        <td><a href="{{ route('replicaset-details', ['name'=>$owner['metadata']['name'], 'namespace'=>$owner['metadata']['namespace']??'default']) }}">{{$owner['metadata']['name']}}</a></td>
                    @elseif($owner['kind'] === 'StatefulSet')
                        <td><a href="{{ route('statefulset-details', ['name'=>$owner['metadata']['name'], 'namespace'=>$owner['metadata']['namespace']??'default']) }}">{{$owner['metadata']['name']}}</a></td>
                    @endif
                    <td>{{$owner['kind']}}</td>
                    <td>{{$owner['status']['readyReplicas']??'0'}}/{{$owner['status']['replicas']??'-'}}</td>
                    <td>{{$ownersAge[$key]}}</td>
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
                            <div class="badge badge-pill bg-primary">
                            {{$key}}: {{$label}}
                        </div>
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
                        <div class="badge badge-pill bg-primary">
                            {{$container['image']}}
                        </div><br>
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
        <tr>
            <td colspan="10">
                <h3 style="padding-left: 30px" id="events_table">Persistent Volume Claims</h3>
            </td>
        </tr>
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
                            <div class="badge badge-pill bg-primary">
                            {{$key}}: {{$label}}
                        </div>
                        @endforeach
                    </td>
                    <td>{{$pvc->getStatus('phase')??'?'}}</td>
                    <td><a href="{{ route('pv-details', ['name'=>$pvc->getSpec('volumeName')]) }}">{{$pvc->getSpec('volumeName')}}</a></td>
                    <td>{{$pvc->getStatus('capacity')['storage']}}</td>
                    <td>
                        @foreach($pvc->getStatus('accessModes') as $accessMode)
                            {{$accessMode}}<br>
                        @endforeach
                    </td>
                    <td>{{$pvc->getSpec('storageClassName')}}</td>
                    <td>{{\Carbon\Carbon::createFromTimeString($pvc->getMetadata()['creationTimestamp'], 'UTC')->addHours(7)->toDayDateTimeString()}}</td>
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
        <tr>
            <td colspan="10">
                <h3 style="padding-left: 30px" id="events_table">Events</h3>
            </td>
        </tr>
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
                        <td>{{\Carbon\Carbon::createFromTimeString($event->toArray()['firstTimestamp']??'0', 'UTC')->addHours(7)->toDayDateTimeString()}}</td>
                        <td>{{\Carbon\Carbon::createFromTimeString($event->toArray()['lastTimestamp']??'0', 'UTC')->addHours(7)->toDayDateTimeString()}}</td>
                    </tr>
{{--                @else--}}
{{--                    <tr class="text-center">--}}
{{--                        <th colspan="8">No Event Found...</th>--}}
{{--                    </tr>--}}
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
        <tr>
            <td colspan="10">
                <h3 style="padding-left: 30px"id="deployment_table">Containers</h3>
            </td>
        </tr>
        </thead>
        @for($i=0;$i < count($containers);$i++)
            <tbody>
                <tr>
                    <th colspan="6">
                        <h4>
                            @if(array_key_first($containerStatuses[$i]['state']) === 'running')
                                <span class="badge rounded-pill bg-success" style="font-size: 1rem">Running</span>
                            @elseif(array_key_first($containerStatuses[$i]['state']) === 'waiting')
                                <span class="badge rounded-pill bg-warning" style="font-size: 1rem">Waiting</span>
                            @else
                                <span class="badge rounded-pill bg-danger" style="font-size: 1rem">{{ucwords(array_key_first($containerStatuses[$i]['state']))}}</span>
                            @endif
                                <b>{{$containers[$i]['name']}}</b>
                        </h4>
                    </th>
                </tr>
                <tr>
                    <th colspan="6">Image</th>
                </tr>
                <tr>
                    <td colspan="6">
                        <div class="badge badge-pill bg-primary">
                            {{$containers[$i]['image']}}
                        </div><br>
                    </td>
                </tr>
                <tr>
                    <th colspan="6"><span class="badge badge-pill bg-primary" style="font-size: 1rem">Status</span></th>
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
                            <td>{{\Carbon\Carbon::createFromTimeString($value['startedAt']??'0', 'UTC')->addHours(7)->isToday() ? '-' : \Carbon\Carbon::createFromTimeString($value['startedAt']??'0', 'UTC')->addHours(7)->toDayDateTimeString()}}</td>
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
                        <th colspan="2">Name</th>
                        <th colspan="2">Value</th>
                    </tr>
                        @foreach($containers[$i]['env'] as $env)
                            <tr>
                                <td colspan="2">{{$env['name']}}</td>
                                <td colspan="2">{{$env['value']??'-'}}</td>
                            </tr>
                        @endforeach
{{--                    <tr>--}}
{{--                        @foreach($containers[$i]['env'] as $env)--}}
{{--                            --}}
{{--                        @endforeach--}}
{{--                    </tr>--}}
                @endif
                @if(count($containers[$i]['command']??[]) != 0 && !is_null($containers[$i]['command']??null))
                    <tr>
                        <th colspan="6">Commands</th>
                    </tr>
                    <tr>
                        <td colspan="6">

{{--                            EDITOR--}}

                            <div class="codecontainer" style="width: 80vw; height: calc(30vh); margin-left: auto; margin-right: auto">
                                <div id="viewer" style="position: relative; height: 100%; width: 100%">@foreach($containers[$i]['command'] as $cmd){{$cmd}}
@endforeach<br></div>
                            </div>
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
{{--                                <th>Source Type</th>--}}
{{--                                <th>Source Name</th>--}}
                            </tr>
                            @foreach($containers[$i]['volumeMounts'] as $volumeMount)
                                <tr>
                                    <td>{{$volumeMount['name']}}</td>
                                    <td>{{$volumeMount['readOnly']??false ? 'true' : 'false'}}</td>
                                    <td>{{$volumeMount['mountPath']??'-'}}</td>
                                    <td>{{$volumeMount['subPath']??'-'}}</td>
{{--                                    TODO FIND SOURCE TYPE--}}
{{--                                    <td>I Dunno</td>--}}
{{--                                    <td>I Dunno Either...</td>--}}
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
                                    @foreach($containers[$i]['securityContext']['capabilities']['add']??[] as $addValue)
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

    <div id="data" style="display: none">{{\Symfony\Component\Yaml\Yaml::dump($pod->toArray(), 512, 2)}}</div>

    @include('layouts.editFormModal')

    @include('layouts.deleteFormModal')

@endsection


@section('js')

    <script>

        try {
            let viewer = document.querySelector('#viewer')
            let viewerEditor = ace.edit("viewer");
            viewerEditor.setOptions({
                mode: 'ace/mode/scrypt',
                theme: 'ace/theme/monokai',
            })

            viewerEditor.setReadOnly(true)
        }catch (e) {

        }

        let jsonEditor = document.querySelector('#jsonEditor')
        let aceJSONEditor = ace.edit("jsonEditor");

        aceJSONEditor.setTheme('ace/theme/monokai')
        aceJSONEditor.session.setMode("ace/mode/json");

        aceJSONEditor.setReadOnly(true)

        function updateJSON(e) {
            let data = document.getElementsByClassName(e.value)[0].innerHTML
            let jsonData = JSON.stringify(JSON.parse(data), null, '\t')
            aceJSONEditor.session.setValue(jsonData)
        }


        let aceData = document.querySelector('#data').innerHTML

        let editor = document.querySelector('#editor')
        let aceEditor = ace.edit("editor");

        aceEditor.setTheme('ace/theme/monokai')
        aceEditor.session.setMode("ace/mode/yaml");

        aceEditor.session.setValue(aceData)

        function updateData() {
            document.querySelector('input[name="value"]').value = aceEditor.session.getValue()
        }

        let kind = '{{$pod->getKind()}}';
        let namespace = '{{$pod->getNamespace()}}';
        let name = '{{$pod->getName()}}';

        document.getElementById('deleteValue').value = kind + ' ' + namespace + ' ' + name


    </script>


@endsection
