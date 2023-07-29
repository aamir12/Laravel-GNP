# Instructions for deploying on Kubernetes

To start, open a terminal in the kubernetes folder.

## Set up namespaces

Namespaces are a handy way of organising containers and configurations into groups. We'll generally we use them to separate out products and environments. You can deploy the namespaces using the command below.

```bash
kubectl apply -f namespaces.yml
```

If you have more environments to set up, add them in here and commit them. For testing we'll use the `earnie-testing` namespace.

## Load balancer & Ingress Controller

Next we need to set up our Ingress Controller. All Ingress Controllers do is route traffic from an entry point of some kind, into our services. The entry point is actually a Load Balancer that is provisioned by the cloud provider and will have a static IP no matter what happens in your cluster (point your DNS records here). This then routes traffic to the ingress controller which in turn load balances across all your pods. We're using the Nginx Ingress Controller.

Run:

```bash
kubectl apply -f nginx-ingress/deploy/static/mandatory.yaml
kubectl apply -f nginx-ingress/deploy/static/provider/aws/service-l4.yaml
kubectl apply -f nginx-ingress/deploy/static/provider/aws/patch-configmap-l4.yaml
```

## Add Config and Secrets

Configs and Secrets define what environment variables our applications will have access to. Obviously secrets will never be committed into the repository so use the example secrets file to create them. These files should be "`namespaced`" and by that we mean that the namespace should be defined in the config file. If you don't you risk applying development config/secrets to production namespaces.

```bash
kubectl apply -f gip-app/config/test-config.yml
kubectl apply -f gip-app/secrets/test-secrets.yml
```

## MySQL DB

Set up the DB with:

```bash
kubectl apply -f mysql-db/mysql-db-deployment.yml -n earnie-testing
```

Note that we apply this to a namespace. That's because these deployments should be namespace agnostic and can work in any environment.

## The Earnie Web App

Next up we need to run the following yaml to add the actual earnie app.

```bash
kubectl apply -f gip-app/deployments/api -n earnie-testing
```

Lastly, add the ingress route for the namespace you're setting up. The folder subpath should have the same name as the namespace, so in this example the ingress file is located in the `earnie-testing` folder:

```bash
kubectl apply -f gip-app/ingress/earnie-testing/earnie-app-ingress.yml
```

Like Configs and CRON jobs, Ingress files should be `namespaced` so you shouldn't have to pass the ingress flag through on the `apply` command.


## Accessing the Kubernetes Dashboard

- Request a token with the following command:
  ```
  kubectl -n kubernetes-dashboard get secret $(kubectl -n kubernetes-dashboard get sa/admin-user -o jsonpath="{.secrets[0].name}") -o go-template="{{.data.token | base64decode}}"
  ```
  Copy this token to your clipboard.
- Run `kubectl proxy`.
- Go to http://127.0.0.1:8001/api/v1/namespaces/kubernetes-dashboard/services/https:kubernetes-dashboard:/proxy/#/workloads?namespace=default
- Paste your token and hit "Sign-in".