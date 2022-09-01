@extends('layouts.app2', ['namespaces'=>$namespaces])

@section('content')

    <div style="width: 80vw; height: calc(100vh - 90px); margin-left:auto; margin-right: auto; margin-top: 30px">
        <form action="{{ route('result') }}" method="POST">
            <div class="form-group row">
                @csrf
                <div class="col-12">
                    <h3>Metadata</h3>
                </div>
                <div class="col-6">
                    <label for="name" class="col-form-label">Deployment Name</label>
                    <input type="text" name="name" class="form-control" placeholder="Deployment Name">
                </div>
                <div class="col-6">
                    <label for="namespace" class="col-form-label">Namespace</label>
                    <select name="namespace" id="namespace" class="form-control">
                        @foreach($namespaces as $namespace)
                            <option value="{{$namespace->getName()}}">{{$namespace->getName()}}</option>
                        @endforeach
{{--                        TODO ดูว่าทำให้สร้างใหม่ได้ป่าว--}}
{{--                            <option value="createNew">Create New</option>--}}
                    </select>
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
                            <button type="button" class="btn btn-success" onclick="addLabelSelector()"><i class="fa fa-plus" aria-hidden="true"></i></button>
                        </div>
                    </div>
                    <div class="row my-1" id="row-1">
                        <div class="col-5">
                            <input type="text" name="key1" class="form-control" placeholder="key">
                        </div>
                        <div class="col-5">
                            <input type="text" name="value1" class="form-control" placeholder="value">
                        </div>
                        <div class="col-2">
                            <button type="button" class="btn btn-danger" onclick="removeLabelSelector(this)"><i class="fa fa-minus" aria-hidden="true"></i></button>
                        </div>
                    </div>
                </div>
                <div class="col-6">
                    <label for="replicas">Replicas</label>
                    <input type="number" name="replicas" class="form-control">
                </div>
            </div>
            <button type="submit" class="btn btn-outline-success" style="width: 10vw"><i class="fa fa-arrow-right" aria-hidden="true"></i> Continue</button>
        </form>
    </div>

    <script>
        let labelSelectorCount = 1
        let labelSelectorArr = [1];


        function arrayRemove(arr, value) {

            return arr.filter(function(ele){
                return ele != value;
            });
        }

        function addLabelSelector() {
            labelSelectorArr.push(++labelSelectorCount);
            let labelSelector = document.getElementById('labelSelector')
            labelSelector.innerHTML +=
                '<div class="row my-1" id="row-'+labelSelectorCount+'"> \
                    <div class="col-5"> \
                        <input type="text" name="key'+labelSelectorCount+'" class="form-control" placeholder="key"> \
                    </div> \
                    <div class="col-5"> \
                        <input type="text" name="value'+labelSelectorCount+'" class="form-control" placeholder="value"> \
                    </div> \
                    <div class="col-2"> \
                        <button type="button" class="btn btn-danger '+labelSelectorCount+'" onclick="removeLabelSelector(this)"><i class="fa fa-minus" aria-hidden="true"></i></button> \
                    </div> \
                </div>'
            console.log(labelSelectorArr)
        }

        function removeLabelSelector(e) {
            let number = e.className.split(' ')[2]
            let removedLabelSelector = document.getElementById('row-'+number)
            removedLabelSelector.remove()
            labelSelectorArr = arrayRemove(labelSelectorArr, number)
            console.log(labelSelectorArr)
        }



    </script>

@endsection
