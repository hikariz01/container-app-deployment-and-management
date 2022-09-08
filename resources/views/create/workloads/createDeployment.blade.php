@extends('layouts.app2', ['namespaces'=>$namespaces])

@section('content')

    <style>
        sup {
            color: red;
        }

    </style>


    <div style="width: 80vw; height: calc(100vh - 90px); margin: 30px auto;">
        <form action="{{ route('result') }}" method="POST" onsubmit="podNameCheck()">
            <div class="form-group row">
                @csrf
                <div class="col-12 mb-2">
                    <label for="resourceType" class="col-form-label-lg">You selected resource</label>
                    <input type="text" name="resourceType" class="form-control" value="{{$resourceType}}" readonly>
                </div>
                <div class="col-12">
                    <h3>Metadata</h3>
                </div>
                <div class="col-6">
                    <label for="name" class="col-form-label">Deployment Name<sup>*</sup></label>
                    <input type="text" name="name" class="form-control" placeholder="Deployment Name" onchange="changeFunction(this)">
                </div>
                <div class="col-6">
                    <label for="namespace" class="col-form-label">Namespace<sup>*</sup></label>
                    <select name="namespace" id="namespace" class="form-control">
                        @foreach($namespaces as $namespace)
                            <option value="{{$namespace->getName()}}">{{$namespace->getName()}}</option>
                        @endforeach
{{--                        TODO ดูว่าทำให้สร้างใหม่ได้ป่าว--}}
{{--                            <option value="createNew">Create New</option>--}}
                    </select>
                </div>



                <div class="col-12 my-1" id="deploymentLabel">
                    <div class="row">
                        <div class="col-10">
                            <label for="selector" class="col-form-label">Labels</label>
                        </div>
                        <div class="col-2">
                            <button type="button" class="btn btn-success" onclick="add('deploymentLabel')"><i class="fa fa-plus" aria-hidden="true"></i></button>
                        </div>
                    </div>
                    <div class="row my-1" id="deploymentLabelRow-0">
                        <div class="col-5">
                            <input type="text" name="deploymentLabel[0][key]" class="form-control" value="deployment-name" readonly>
                        </div>
                        <div class="col-5">
                            <input type="text" name="deploymentLabel[0][value]" class="form-control" placeholder="Deployment Name" value="" readonly>
                        </div>
                        <div class="col-2">
                            <button type="button" class="btn btn-danger" onclick="remove('deploymentLabel', this)"><i class="fa fa-minus" aria-hidden="true"></i></button>
                        </div>
                    </div>
                </div>




                <div class="col-12 my-2 advancedOption" style="display: none">
                    <label for="annotation">Description</label>
                    <textarea name="annotation" id="annotation" class="form-control" rows="2"></textarea>
                </div>



            </div>
            <div class="form-group row">
                <div class="col-12">
                    <h3>Spec</h3>
                </div>
                <div class="col-6" id="labelSelector">
                    <div class="row">
                        <div class="col-10">
                            <label for="selector" class="col-form-label">Selector (Match Labels)</label>
                        </div>
                        <div class="col-2">
                            <button type="button" class="btn btn-success" onclick="add('labelSelector')"><i class="fa fa-plus" aria-hidden="true"></i></button>
                        </div>
                    </div>
                    <div class="row my-1" id="labelSelectorRow-0">
                        <div class="col-5">
                            <input type="text" name="labelSelector[0][key]" class="form-control" value="deployment-name" readonly>
                        </div>
                        <div class="col-5">
                            <input type="text" name="labelSelector[0][value]" class="form-control" placeholder="Deployment Name" value="" readonly>
                        </div>
                        <div class="col-2">
                            <button type="button" class="btn btn-danger" onclick="remove('labelSelector', this)"><i class="fa fa-minus" aria-hidden="true"></i></button>
                        </div>
                    </div>
                </div>
                <div class="col-6">
                    <label for="replicas">Replicas<sup>*</sup></label>
                    <input type="number" name="replicas" class="form-control" value="1">
                </div>
                <div class="col-6">
                    <label for="containerImage">Container Image<sup>*</sup></label>
                    <input type="text" name="containerImage" class="form-control" placeholder="Container Image">
                </div>
                <div class="col-6">
                    <label for="containerImageVersion">Container Image Version<sup>*</sup></label>
                    <input type="text" name="containerImageVersion" class="form-control" placeholder="Container Image Version">
                </div>
            </div>
            <div class="form-group row advancedOption" style="display: none">
                <div class="col-12">
                    <h4>Pod</h4>
                </div>
                <div class="col-6">
                    <label for="podName">Pod Name<sup>*</sup></label>
                    <input type="text" name="podName" class="form-control" placeholder="Pod Name">
                </div>


                <div class="col-6" id="podLabel">
                    <div class="row">
                        <div class="col-10">
                            <label for="selector" class="col-form-label">Pod Labels</label>
                        </div>
                        <div class="col-2">
                            <button type="button" class="btn btn-success" onclick="add('podLabel')"><i class="fa fa-plus" aria-hidden="true"></i></button>
                        </div>
                    </div>
                    <div class="row my-1" id="podLabelRow-0">
                        <div class="col-5">
                            <input type="text" name="podLabel[0][key]" class="form-control" value="deployment-name" readonly>
                        </div>
                        <div class="col-5">
                            <input type="text" name="podLabel[0][value]" class="form-control" placeholder="Deployment Name" value="" readonly>
                        </div>
                        <div class="col-2">
                            <button type="button" class="btn btn-danger" onclick="remove('podLabel', this)"><i class="fa fa-minus" aria-hidden="true"></i></button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="form-group row advancedOption" id="container" style="display: none">
                <div class="col-12">
                    <h4>Container</h4>
                </div>

{{--                Container Name--}}
                <div class="col-6">
                    <label for="containerName" class="col-form-label">Container Name<sup>*</sup></label>
                    <input type="text" class="form-control" name="containerName" placeholder="Container Name">
                </div>


{{--                Container Label--}}
                <div class="col-6 my-1" id="containerLabel">
                    <div class="row">
                        <div class="col-10">
                            <label for="selector" class="col-form-label">Container Labels</label>
                        </div>
                        <div class="col-2">
                            <button type="button" class="btn btn-success" onclick="add('containerLabel')"><i class="fa fa-plus" aria-hidden="true"></i></button>
                        </div>
                    </div>
                    <div class="row my-1" id="containerLabelRow-0">
                        <div class="col-5">
                            <input type="text" name="containerLabel[0][key]" class="form-control" value="deployment-name" readonly>
                        </div>
                        <div class="col-5">
                            <input type="text" name="containerLabel[0][value]" class="form-control" placeholder="Deployment Name" value="" readonly>
                        </div>
                        <div class="col-2">
                            <button type="button" class="btn btn-danger" onclick="remove('containerLabel', this)"><i class="fa fa-minus" aria-hidden="true"></i></button>
                        </div>
                    </div>
                </div>

{{--                Requirement--}}
                <div class="col-6">
                    <label for="cpuRequest" class="col-form-label">CPU Requirement (cores)</label>
                    <input type="number" name="cpuRequest" class="form-control" placeholder="1">
                </div>
                <div class="col-6">
                    <label for="memRequest" class="col-form-label">Memory Requirement (MiB)</label>
                    <input type="text" name="memRequest" class="form-control" placeholder="512">
                </div>

{{--                Container Port--}}
                <div class="col-12 my-1" id="containerPort">
                    <div class="row">
                        <div class="col-11">
                            <label for="selector" class="col-form-label">Port</label>
                        </div>
                        <div class="col-1 mt-auto">
                            <button type="button" class="btn btn-success" onclick="addPort()"><i class="fa fa-plus" aria-hidden="true"></i></button>
                        </div>
                    </div>
                    <div class="row my-1" id="containerPortRow-0">
                        <div class="col-4">
                            <label for="containerPortName">Port Name</label>
                            <input type="text" name="containerPort[0][name]" class="form-control" placeholder="Port Name">
                        </div>
                        <div class="col-3">
                            <label for="containerPortPort">Port</label>
                            <input type="number" name="containerPort[0][containerPort]" class="form-control" placeholder="Port">
                        </div>
                        <div class="col-4">
                            <label for="containerPortProtocol">Protocol</label>
                            <input type="text" name="containerPort[0][protocol]" class="form-control" placeholder="Port Protocol">
                        </div>
{{--                        <div class="col-3">--}}
{{--                            <label for="containerPortHostIP">Host IP</label>--}}
{{--                            <input type="text" name="containerPortHostIP1" class="form-control" placeholder="Port HostIP">--}}
{{--                        </div>--}}
{{--                        <div class="col-2">--}}
{{--                            <label for="containerPortHostPort">Host Port</label>--}}
{{--                            <input type="text" name="containerPortHostPort1" class="form-control" placeholder="Port Host Port">--}}
{{--                        </div>--}}
                        <div class="col-1 mt-auto">
                            <button type="button" class="btn btn-danger" onclick="remove('containerPort', this)"><i class="fa fa-minus" aria-hidden="true"></i></button>
                        </div>
                    </div>
                </div>

{{--                Run Command--}}
                <div class="col-6">
                    <label for="runCommand" class="col-form-label">Run Command</label>
                    <input type="text" name="runCommand" class="form-control" placeholder="<cmd>,<cmd>,... Ex. printenv">
                </div>
                <div class="col-6">
                    <label for="runCommandArgument" class="col-form-label">Run Command Arguments</label>
                    <input type="text" name="runCommandArgument" class="form-control" placeholder="<args>,<args>,... Ex. HOSTNAME,KUBERNETES_PORT">
                </div>

{{--                Environment Variables--}}
                <div class="col-12 my-1" id="envVariable">
                    <div class="row">
                        <div class="col-10">
                            <label for="envVariable" class="col-form-label">Environment Variables</label>
                        </div>
                        <div class="col-2">
                            <button type="button" class="btn btn-success" onclick="add('envVariable')"><i class="fa fa-plus" aria-hidden="true"></i></button>
                        </div>
                    </div>
                    <div class="row my-1" id="envVariableRow-0">
                        <div class="col-5">
                            <input type="text" name="envVariable[0][key]" class="form-control" placeholder="Name">
                        </div>
                        <div class="col-5">
                            <input type="text" name="envVariable[0][value]" class="form-control" placeholder="Value">
                        </div>
                        <div class="col-2">
                            <button type="button" class="btn btn-danger" onclick="remove('envVariable', this)"><i class="fa fa-minus" aria-hidden="true"></i></button>
                        </div>
                    </div>
                </div>

{{--                <div class="col-12 my-1" id="mountVolume">--}}
{{--                    <div class="row">--}}
{{--                        <div class="col-11">--}}
{{--                            <label for="mountVolume" class="col-form-label">Mount</label>--}}
{{--                        </div>--}}
{{--                        <div class="col-1 mt-auto">--}}
{{--                            <button type="button" class="btn btn-success" onclick="addMount()"><i class="fa fa-plus" aria-hidden="true"></i></button>--}}
{{--                        </div>--}}
{{--                    </div>--}}
{{--                    <div class="row my-1" id="mountVolumeRow-1">--}}
{{--                        <div class="col-3">--}}
{{--                            <label for="mountName1" class="col-form-label">Name</label>--}}
{{--                            <input type="text" name="mountName1" class="form-control" placeholder="Name">--}}
{{--                        </div>--}}
{{--                        <div class="col-3">--}}
{{--                            <label for="mountPath1" class="col-form-label">Mount Path</label>--}}
{{--                            <input type="text" name="mountPath1" class="form-control" placeholder="Mount Path">--}}
{{--                        </div>--}}
{{--                        <div class="col-2">--}}
{{--                            <label for="mountReadOnly1" class="col-form-label">Read Only</label>--}}
{{--                            <select name="mountReadOnly1" id="mountReadOnly1" class="form-control">--}}
{{--                                <option value="1">true</option>--}}
{{--                                <option value="0" selected>false</option>--}}
{{--                            </select>--}}
{{--                        </div>--}}
{{--                        <div class="col-3">--}}
{{--                            <label for="mountSubPath" class="col-form-label">Sub Path</label>--}}
{{--                            <input type="text" name="mountSubPath1" class="form-control" placeholder="Sub Path">--}}
{{--                        </div>--}}
{{--                        <div class="col-1 mt-auto">--}}
{{--                            <button type="button" class="btn btn-danger" onclick="remove('mountVolume', this)"><i class="fa fa-minus" aria-hidden="true"></i></button>--}}
{{--                        </div>--}}
{{--                    </div>--}}
{{--                </div>--}}
                <div class="col-12">
                    <h4>Probe</h4>
                </div>
                <div class="col-12" id="startupProbe">
                    <div class="row">
                        <div class="col-12">
                            <b><label for="startupProbe" class="col-form-label">Startup Probe</label></b>
                        </div>
                    </div>
                    <div class="row" id="startupProbeRow">
                        <div class="col-2">
                            <label for="startupProbeInitialDelaySeconds" class="col-form-label">Initial Delay Seconds</label>
                            <input type="number" name="startupProbe['initialDelaySeconds']" class="form-control" placeholder="Initial Delay Seconds">
                        </div>
                        <div class="col-2">
                            <label for="startupProbePeriodSeconds" class="col-form-label">Period Seconds</label>
                            <input type="number" name="startupProbe['periodSeconds']" class="form-control" placeholder="Period Seconds">
                        </div>
                        <div class="col-2">
                            <label for="startupProbeTimeoutSeconds" class="col-form-label">Timeout Seconds</label>
                            <input type="number" name="startupProbe['timeoutSeconds']" class="form-control" placeholder="Timeout Seconds">
                        </div>
                        <div class="col-3">
                            <label for="startupProbeFailureThreshold" class="col-form-label">Failure Threshold</label>
                            <input type="number" name="startupProbe['failureThreshold']" class="form-control" placeholder="Failure Threshold">
                        </div>
                        <div class="col-3">
                            <label for="startupProbeSuccessThreshold" class="col-form-label">Success Threshold</label>
                            <input type="number" name="startupProbe['successThreshold']" class="form-control" placeholder="Success Threshold">
                        </div>


                        <div class="col-12">
                            <label for="command" class="col-form-label">Command</label>
                            <input type="text" name="startupProbe['command']" class="form-control" placeholder="<cmd>,<cmd>">
                        </div>


                        <div class="col-6">
                            <label for="startupProbeTCPSocketPort" class="col-form-label">TCP Socket Port</label>
                            <input type="text" name="startupProbe['tcp']['port']" class="form-control" placeholder="TCP Port">
                        </div>
                        <div class="col-6">
                            <label for="startupProbeTCPSocketHostIP" class="col-form-label">TCP Socket Host IP</label>
                            <input type="text" name="startupProbe['tcp']['host']" class="form-control" placeholder="Host IP">
                        </div>


                        <div class="col-2">
                            <label for="startupProbeHTTPScheme" class="col-form-label">HTTP Scheme</label>
                            <select name="startupProbe['http']['scheme']" id="startupProbeScheme" class="form-control">
                                <option value="HTTP">HTTP</option>
                                <option value="HTTPS">HTTPS</option>
                            </select>
                        </div>
                        <div class="col-2">
                            <label for="startupProbeHTTPPath" class="col-form-label">HTTP Path</label>
                            <input type="number" name="startupProbe['http']['path']" class="form-control" placeholder="Path">
                        </div>
                        <div class="col-2">
                            <label for="startupProbeHTTPPort" class="col-form-label">HTTP Port</label>
                            <input type="number" name="startupProbe['http']['port']" class="form-control" placeholder="port">
                        </div>


                    </div>
                </div>

            </div>

            <a id="more" href="#" class="btn btn-outline-primary" onclick="$('.advancedOption').slideToggle(function(){$('#more').html($('.details').is(':visible')?'Hide Advanced Options':'Advanced Options');});">Advanced Options</a>
            <button type="submit" class="btn btn-outline-success" style="width: 10vw"><i class="fa fa-arrow-right" aria-hidden="true"></i> Continue</button>
        </form>
    </div>

    <script>
        let deploymentLabelCount = 0
        let labelSelectorCount = 0
        let podLabelCount = 0
        let portCount = 0
        let envVarCount = 0
        let labelSelectorArr = [1]
        let mountCount = 0

        // function arrayRemove(arr, value) {
        //
        //     return arr.filter(function(ele){
        //         return ele != value;
        //     });
        // }

        function add(name) {
            let tempCount
            if (name === 'labelSelector') {
                tempCount = ++labelSelectorCount
            }
            else if (name === 'deploymentLabel') {
                tempCount = ++deploymentLabelCount
            }
            else if (name === 'podLabel') {
                tempCount = ++podLabelCount
            }
            else {
                tempCount = ++envVarCount
            }
            let labelSelector = document.getElementById(name)
            labelSelector.innerHTML +=
                '<div class="row my-1" id="'+name+'Row-'+tempCount+'" class="form-control"> \
                    <div class="col-5"> \
                        <input type="text" name="'+name+'['+tempCount+'][key]" class="form-control" placeholder="key"> \
                    </div> \
                    <div class="col-5"> \
                        <input type="text" name="'+name+'['+tempCount+'][value]" class="form-control" placeholder="value"> \
                    </div> \
                    <div class="col-2"> \
                        <button type="button" class="btn btn-danger '+tempCount+'" onclick="remove(\''+name+'\', this)"><i class="fa fa-minus" aria-hidden="true"></i></button> \
                    </div> \
                </div>'
            changeFunction(document.getElementsByName('name')[0])
            // console.log(labelSelectorArr)
        }

        function remove(name, e) {
            let number = e.className.split(' ')[2]
            if (number !== 0) {
                let removedLabelSelector = document.getElementById(name+'Row-'+number)
                removedLabelSelector.remove()
            }
            // labelSelectorArr = arrayRemove(labelSelectorArr, number)
            // console.log(labelSelectorArr)
        }

        function addPort() {
            let port = document.getElementById('containerPort');
            portCount++
            port.innerHTML += '\
                <div class="row my-1" id="containerPortRow-'+portCount+'"> \
                    <div class="col-4"> \
                        <input type="text" name="containerPort['+portCount+'][name]" class="form-control" placeholder="Port Name"> \
                    </div> \
                    <div class="col-3"> \
                        <input type="text" name="containerPort['+portCount+'][containerPort]" class="form-control" placeholder="Port"> \
                    </div> \
                    <div class="col-4"> \
                        <input type="text" name="containerPort['+portCount+'][protocol]" class="form-control" placeholder="Port Protocol"> \
                    </div> \
                    <div class="col-1 mt-auto"> \
                        <button type="button" class="btn btn-danger '+portCount+'" onclick="remove(\'containerPort\', this)"><i class="fa fa-minus" aria-hidden="true"></i></button> \
                    </div> \
                </div>'
            changeFunction(document.getElementsByName('name')[0])
        }

        // function addMount() {
        //     let mount = document.getElementById('mountVolume');
        //     ++mountCount
        //     mount.innerHTML += '\
        //     <div class="row my-1" id="mountVolumeRow-'+mountCount+'"> \
        //         <div class="col-3"> \
        //             <input type="text" name="mountName'+mountCount+'" class="form-control" placeholder="Name"> \
        //         </div> \
        //         <div class="col-3"> \
        //             <input type="text" name="mountPath'+mountCount+'" class="form-control" placeholder="Mount Path"> \
        //         </div> \
        //         <div class="col-2"> \
        //             <select name="mountReadOnly'+mountCount+'" id="mountReadOnly'+mountCount+'" class="form-control"> \
        //                 <option value="1">true</option> \
        //                 <option value="0" selected>false</option> \
        //             </select> \
        //         </div> \
        //         <div class="col-3"> \
        //             <input type="text" name="mountSubPath'+mountCount+'" class="form-control" placeholder="Sub Path"> \
        //         </div> \
        //         <div class="col-1"> \
        //             <button type="button" class="btn btn-danger '+mountCount+'" onclick="remove(\'mountVolume\', this)"><i class="fa fa-minus" aria-hidden="true"></i></button> \
        //         </div> \
        //     </div>'
        // }

        function changeFunction(e) {
            let appName = document.getElementsByName('labelSelector[0][value]')[0];
            let appLabel = document.getElementsByName('deploymentLabel[0][value]')[0];
            let podName = document.getElementsByName('podName')[0];
            let podLabel = document.getElementsByName('podLabel[0][value]')[0];
            let containerName = document.getElementsByName('containerName')[0];
            let containerLabel = document.getElementsByName('containerLabel[0][value]')[0];
            appLabel.value = appName.value = podLabel.value = podName.placeholder = containerName.placeholder = containerLabel.value = e.value;
        }

        function podNameCheck() {
            let podName = document.getElementsByName('podName')[0]
            let containerName = document.getElementsByName('containerName')[0];
            if (podName.value === '') {
                podName.value = document.getElementsByName('name')[0].value
            }
            if (containerName.value === '') {
                containerName.value = document.getElementsByName('name')[0].value
            }
        }


    </script>

@endsection
