<p align="center">Container-App Deploy and Management Web-App</p>

## Function of this Web-Application
- Create/Edit/Delete Kubernetes Object.
- View details of Kubernetes Object.
- View Logs of Pod
- Namespace Switchable.
- Multi-Cluster Manageable.

## How to install
### Clone this project
```
git clone https://github.com/hikariz01/container-app-deployment-and-management.git
cd container-app-deployment-and-management
```
### Setting your `.env` file
```
cp .env.example .env
nano .env
```
### Example of `.env` file
- you must set `DB_HOST` and `DB_DATABASE` to same name
```
APP_NAME=Container-App-Deploy-and-Manage
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_URL=http://localhost

LOG_CHANNEL=stack
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=debug

DB_CONNECTION=mysql
DB_HOST=Laravel-DB
DB_PORT=3306
DB_DATABASE=Laravel-DB
DB_USERNAME=changeme
DB_PASSWORD=changeme
```

### Use `docker-compose` to run Application
```
docker-compose up -d
```

### When you're done with Application you can `STOP` or `REMOVE`
- Stop
```
docker-compose stop
```
- Remove (Everything will be deleted)
```
docker-compose down
```

## How to get Credentials for Authentication
### TL;DR
Copy this code and run on Kubernetes Control Plane Node (Master Node)
```
KUBE_API_ENDPOINT=`kubectl config view -o jsonpath='{.clusters[0].cluster.server}'`
kubectl create sa web-app-sa
kubectl create clusterrolebinding web-app-cluster-role-binding --clusterrole cluster-admin --serviceaccount default:web-app-sa
cat <<EOF | kubectl apply -f -
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
```

## Development Tools
- Laravel
- <a href='https://github.com/renoki-co/php-k8s'>renoki-co/php-k8s</a>
