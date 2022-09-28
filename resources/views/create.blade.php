@extends('layouts.app2', ['namespaces'=>$namespaces])

@section('content')

    <nav>
        <div class="nav nav-tabs nav-justified" id="nav-tab" role="tablist">
            <button class="nav-link active" id="nav-form-tab" data-bs-toggle="tab" data-bs-target="#nav-form" type="button" role="tab" aria-controls="nav-form" aria-selected="true">Form</button>
            <button class="nav-link" id="nav-yaml-tab" data-bs-toggle="tab" data-bs-target="#nav-yaml" type="button" role="tab" aria-controls="nav-yaml" aria-selected="false">From Yaml</button>
            <button class="nav-link" id="nav-yaml-file-tab" data-bs-toggle="tab" data-bs-target="#nav-yaml-file" type="button" role="tab" aria-controls="nav-yaml-file" aria-selected="false">From Yaml File</button>
        </div>
    </nav>

    @include('result.alert')

    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible" role="alert">
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="tab-content" id="nav-tabContent">
        <div class="tab-pane fade show active" id="nav-form" role="tabpanel" aria-labelledby="nav-form-tab" tabindex="0">
            <div style="width: 80vw; height: calc(100vh - 90px); margin-left:auto; margin-right: auto; margin-top: 10px">
                <form action="{{ route('create-resource') }}" method="POST">
                    <div class="form-group">
                        @csrf
                        <label for="selectResourceType" style="font-size: 1.1rem">Please select resource type</label>
                        <select name="selectResourceType" id="selectResourceType" class="form-control form-control-lg mb-3">
                            @foreach($resourceTypes as $key => $value)
                                <optgroup label="{{$key}}">
                                    @foreach($value as $resource)
                                        <option value="{{$resource}}">{{$resource}}</option>
                                    @endforeach
                                </optgroup>
                            @endforeach
                        </select>
                    </div>
                    <button type="submit" class="btn btn-outline-success" style="width: 10vw"><i class="fa fa-arrow-right" aria-hidden="true"></i> Continue</button>
                </form>
            </div>
        </div>
        <div class="tab-pane fade" id="nav-yaml" role="tabpanel" aria-labelledby="nav-yaml-tab" tabindex="0">
                <form action="{{ route('create-yaml') }}" method="POST" onsubmit="updateData()">
                    @csrf
                    <div style="height: calc(100vh - 170px); width: 100%">
                        <div id="editor" style="position: relative; height: 100%; width: 100%"># Write your Yaml here.</div>
                    </div>
                    <input type="hidden" name="value" style="display: none" id="editorValue" value="">
                    <button class="btn btn-outline-success mt-3" type="submit" style="width: 10vw; margin-left: 10px"><i class="fa fa-arrow-right" aria-hidden="true"></i> Create</button>
                </form>
        </div>
        <div class="tab-pane fade" id="nav-yaml-file" role="tabpanel" aria-labelledby="nav-yaml-file-tab" tabindex="0">
            <div style="width: 80vw; height: calc(100vh - 90px); margin-left:auto; margin-right: auto; margin-top: 10px">
                <form action="{{ route('create-yaml-files') }}" method="POST" enctype="multipart/form-data">
                    <div class="form-group">
                        @csrf
                        <label for="formFileMultiple" class="form-label" style="font-size: 1.1rem">Select your Yaml files</label>
                        <input class="form-control form-control-lg" type="file" id="formFileMultiple" name="file[]" multiple>
                    </div>
                    <button type="submit" class="btn btn-outline-success" style="width: 10vw" value="Upload"><i class="fa fa-arrow-right" aria-hidden="true"></i> Continue</button>
                </form>
            </div>
        </div>
    </div>




@endsection


@section('js')

    <script>
        let editor = document.querySelector('#editor')
        let aceEditor = ace.edit("editor");

        aceEditor.setOptions({
            mode: 'ace/mode/yaml',
            theme: 'ace/theme/monokai',
        })
        // aceEditor.setTheme('ace/theme/monokai')
        // aceEditor.session.setMode("ace/mode/yaml");
        aceEditor.session.setOptions({
            placeholder: 'Write Yaml here.',
            tabSize: 2,
            useSoftTabs: true
        })

        function updateData() {
            document.getElementById('editorValue').value = aceEditor.session.getValue()
        }
    </script>

@endsection
