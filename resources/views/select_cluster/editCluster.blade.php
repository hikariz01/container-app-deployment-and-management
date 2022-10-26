@extends('layouts.app2')


@section('content')

    <style>

        .codebox {
            background-color: #6c757d;
            width: 100%;
            padding: 15px;
            color: white;
        }

    </style>


    <div style="width: 80vw; margin-left:auto; margin-right: auto; margin-top: 10px">
        <h4>Cluster List</h4>
        <table class="table table-secondary table-borderless dashboard">
            <thead>
                <th>Cluster Name</th>
                <th>Cluster URL</th>
                <th>Select Cluster</th>
                <th><i class="fa fa-cog" aria-hidden="true"></i></th>
            </thead>
            <tbody>
                @if($clusters !== null)
                    @foreach($clusters as $cluster)
                        <tr>
                            <td>{{$cluster->name}}{{($selected_cluster_name === $cluster->name) ? ' (Current)' : ''}}</td>
                            <td>{{$cluster->url}}</td>
                            <td>
                                @if (session('cluster_id') == $cluster->id)
                                    <button class="btn btn-secondary" disabled>Use this Cluster</button>
                                @else
                                    <form action="{{ route('submit-cluster') }}" method="POST">
                                        @csrf
                                        <input name="selectedCluster" type="hidden" style="display: none" value="{{$cluster->id}}">
                                        <button class="btn btn-success" type="submit">Use this Cluster</button>
                                    </form>
                                @endif
                            </td>
                            <td style="overflow: visible">
                                <div class="dropdown">
                                    <button class="btn btn-outline-info dropdown-toggle" role="button" id="dropdownEditButton" type="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <i class="fa fa-ellipsis-v" aria-hidden="true"></i>
                                    </button>
                                    <div class="dropdown-menu" aria-labelledby="dropdownEditButton">
                                        <a class="dropdown-item {{$cluster->id}}" onclick="editCluster(this)" role="button" data-bs-toggle="modal" data-bs-target="#editForm" href="#">Update</a>
                                        <a class="dropdown-item {{$cluster->id}}" role="button" data-bs-toggle="modal" data-bs-target="#deleteForm" href="#" onclick="deleteCluster(this)">Delete</a>
                                    </div>
                                </div>
                                <div style="display: none" id="name{{$cluster->id}}">{{$cluster->name}}</div>
                                <div style="display: none" id="url{{$cluster->id}}">{{$cluster->url}}</div>
                                <div style="display: none" id="token{{$cluster->id}}">{{$cluster->token}}</div>
                                <div style="display: none" id="cacert{{$cluster->id}}">{{$cluster->cacert}}</div>
                            </td>
                        </tr>
                    @endforeach
                @else
                    <tr>
                        <td colspan="4">Still no cluster yet.</td>
                    </tr>
                @endif
            </tbody>
        </table>
    </div>

    <div style="width: 80vw; margin-left:auto; margin-right: auto; margin-top: 10px">
        <form action="{{ route('add-cluster') }}" method="POST">
            @csrf
            <div class="form-group row">
                <div class="col-12">
                    <h3>Register Cluster</h3>
                </div>
                <div class="col-4">
                    <div class="form-floating">
                        <input type="text" class="form-control" name="clusterName" id="clusterName" placeholder="Cluster Name">
                        <label for="clusterName">Cluster Name</label>
                    </div>
                </div>
                <div class="col-4">
                    <div class="form-floating">
                        <input type="url" class="form-control" name="kubeURL" id="kubeURL" placeholder="URL">
                        <label for="kubeURL">Kubernetes API URL</label>
                    </div>
                </div>
                <div class="col-4">
                    <div class="form-floating">
                        <input type="text" class="form-control" name="kubeToken" id="kubeToken" placeholder="Cluster Name">
                        <label for="kubeToken">Token</label>
                    </div>
                </div>
            </div>
            <div class="form-group row">
                <div class="col-12">
                    <div class="form-floating">
                        <textarea class="form-control" placeholder="Copy your CA Cert here" name="cacert" id="cacert" style="height: 120px; resize: none"></textarea>
                        <label for="cacert">CA Cert</label>
                    </div>
                </div>
            </div>
            <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#getToken"><i class="fa fa-info-circle" aria-hidden="true"></i> How to get Token&CACert</button>
            <button type="submit" class="btn btn-outline-success" style="width: 10vw"><i class="fa fa-arrow-right" aria-hidden="true"></i> Continue</button>
        </form>
    </div>


    <div class="modal fade" id="editForm" tabindex="-1" aria-labelledby="editFormLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editFormLabel">Edit Resource</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('submit-edit-cluster') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group row">
                            <div class="col-4">
                                <div class="form-floating">
                                    <input type="text" class="form-control" name="editClusterName" id="editClusterName" placeholder="Cluster Name">
                                    <label for="editClusterName">Cluster Name</label>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="form-floating">
                                    <input type="url" class="form-control" name="editKubeURL" id="editKubeURL" placeholder="URL">
                                    <label for="editKubeURL">Kubernetes API URL</label>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="form-floating">
                                    <input type="text" class="form-control" name="editKubeToken" id="editKubeToken" placeholder="Cluster Name">
                                    <label for="editKubeToken">Token</label>
                                </div>
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-12">
                                <div class="form-floating">
                                    <textarea class="form-control" placeholder="Copy your CA Cert here" name="editCacert" id="editCacert" style="height: 120px; resize: none"></textarea>
                                    <label for="editCacert">CA Cert</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <input type="hidden" name="id" style="display: none" id="id" value="">
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
                <form action="{{ route('delete-cluster') }}" method="POST">
                    @csrf
                    <div class="modal-body">
                        <p>Your resource will be gone forever!, Are you sure about that?</p>
                    </div>
                    <input type="hidden" id="deleteValue" name="deleteValue" value="" style="display: none">
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-danger">Delete</button>
                    </div>
                </form>
            </div>
        </div>
    </div>


    <div class="modal fade" id="getToken" tabindex="-1" aria-labelledby="getTokenLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title text-center" id="getTokenLabel">How to get API Token and CA Cert</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body overflow-auto" style="height: 75vh">
                    <div class="row">
                        <div class="col-12">
                            <h2>TL;DR</h2>
                            <div class="codebox">
<pre style="margin: auto">KUBE_API_ENDPOINT=`kubectl config view -o jsonpath='{.clusters[0].cluster.server}'`
kubectl create sa web-app-sa
kubectl create clusterrolebinding web-app-cluster-role-binding --clusterrole cluster-admin --serviceaccount default:web-app-sa
cat <&lt;EOF | kubectl apply -f -
apiVersion: v1
kind: Secret
metadata:
  name: web-app-secret
  annotations:
    kubernetes.io/service-account.name: web-app-sa
type: kubernetes.io/service-account-token
EOF
kubectl get secret web-app-secret -o jsonpath='{.data.token}'|base64 --decode > token.txt
kubectl get secret web-app-secret -o jsonpath='{.data.ca\.crt}'|base64 --decode > ca.crt
echo $KUBE_API_ENDPOINT
cat token.txt
cat ca.crt
</pre>
                            </div>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-12">
                            <h3>1. Get Kubernetes API Endpoint</h3>
                            <div class="codebox mt-2">
                                <pre style="margin: auto">kubectl config view -o jsonpath='{.clusters[0].cluster.server}'</pre>
                            </div>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-12">
                            <h3>2. Create a new Service Account</h3>
                            <p>Create Service Account with Cluster-admin permission<br></p>
                        </div>
                        <div class="col-12">
                            <p>Create with kubectl command</p>
                            <div class="codebox">
                                <pre style="margin: auto">kubectl create sa web-app-sa</pre>
                            </div>
                        </div>
                        <div class="col-12 mt-3">
                            <p>or Create with Yaml file</p>
                            <div class="codebox">
                                <pre style="margin: auto">
#Yaml File
apiVersion: v1
kind: ServiceAccount
metadata:
  name: web-app-sa</pre>
                            </div>
                            <div class="codebox mt-2">
                                <pre style="margin: auto">kubectl apply -f &lt;your-yaml-file-path&gt;</pre>
                            </div>
                        </div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-12">
                            <h3>3. Create a new Cluster Role Binding</h3>
                        </div>
                        <div class="col-12">
                            <p>Create with kubectl command</p>
                            <div class="codebox">
                                <pre style="margin: auto">kubectl create clusterrolebinding web-app-cluster-role-binding --clusterrole cluster-admin --serviceaccount default:web-app-sa</pre>
                            </div>
                        </div>
                        <div class="col-12 mt-3">
                            <p>or Create with Yaml file</p>
                            <div class="codebox">
                                <pre style="margin: auto">
#Yaml File
apiVersion: rbac.authorization.k8s.io/v1
kind: ClusterRoleBinding
metadata:
  name: web-app-cluster-role-binding
subjects:
- kind: ServiceAccount
  name: web-app-sa
  namespace: default
roleRef:
  kind: ClusterRole
  name: cluster-admin
  apiGroup: rbac.authorization.k8s.io</pre>
                            </div>
                            <div class="codebox mt-2">
                                <pre style="margin: auto">kubectl apply -f &lt;your-yaml-file-path&gt;</pre>
                            </div>
                        </div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-12">
                            <h3>4. Create a Service Account Secret</h3>
                        </div>
                        <div class="col-12">
                            <p>Create with Yaml File</p>
                            <div class="codebox">
                                <pre style="margin: auto">
#Yaml File
apiVersion: v1
kind: Secret
metadata:
  name: web-app-secret
  annotations:
    kubernetes.io/service-account.name: web-app-sa
type: kubernetes.io/service-account-token</pre>
                            </div>
                            <div class="codebox mt-2">
                                <pre style="margin: auto">kubectl apply -f &lt;your-yaml-file-path&gt;</pre>
                            </div>
                        </div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-12">
                            <h3>5. Get Service Account API credentials</h3>
                        </div>
                        <div class="col-12">
                            <p>Get API Token</p>
                            <div class="codebox mt-2">
                                <pre style="margin: auto">kubectl get secret web-app-secret -o jsonpath='{.data.token}'|base64 --decode > token.txt</pre>
                            </div>
                        </div>
                        <div class="col-12 mt-2">
                            <p>Get API CA Cert</p>
                            <div class="codebox mt-2">
                                <pre style="margin: auto">kubectl get secret web-app-secret -o jsonpath='{.data.ca\.crt}'|base64 --decode > ca.crt</pre>
                            </div>
                        </div>
                        <div class="col-12 mt-2">
                            <p>You can view the Token and CA Cert by these commands</p>
                            <div class="codebox">
                                <pre style="margin: auto">cat token.txt</pre>
                            </div>
                            <div class="codebox mt-2">
                                <pre style="margin: auto">cat ca.crt</pre>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-success" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>


@endsection


@section('js')

    <script>

        function editCluster(e) {
            let id = e.className.split(' ')[1]
            let name = document.getElementById('name'+id).innerHTML
            let url = document.getElementById('url'+id).innerHTML
            let token = document.getElementById('token'+id).innerHTML
            let cacert = document.getElementById('cacert'+id).innerHTML

            document.getElementById('editClusterName').value = name
            document.getElementById('editKubeURL').value = url
            document.getElementById('editKubeToken').value = token
            document.getElementById('editCacert').value = cacert
            document.getElementById('id').value = id

        }

        function deleteCluster(e) {
            let deleteID = e.className.split(' ')
            document.querySelector('input[name=deleteValue]').value = deleteID[1]
        }

    </script>

@endsection
