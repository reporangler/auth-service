# Repo Rangler: Auth Service

This docker service will accept headers, authenticate, and return the user data

# Installation

You should be able to resolve `auth.reporangler.develop` locally on your computer. 
Maybe you need to edit `/etc/hosts` file or add to dns?

Run the following commands:
- You can change the network, but change it in all the commands
- You can change the port, but then obviously you need to change the curl command too
```
# Create two variables to make it easier to run
port=80
network=reporangler

# Create the docker network (this might already exist, it's safe to ignore errors if it does)
docker network create ${network}

# Create the containers
docker run --rm --network=${network} --name auth_service_phpfpm reporangler/auth_service_phpfpm
docker run --rm --network=${network} --name auth_service_nginx -p ${port}:80 reporangler/auth_service_nginx

# See whether they are running
docker ps

# Query the container to see what it replies
curl -vvv http://auth.reporangler.develop:${port}/healthz
```

It should output
```
> GET /healthz HTTP/1.1
> Host: auth.reporangler.develop
> User-Agent: curl/7.54.0
> Accept: */*
> 
< HTTP/1.1 200 OK
< Server: nginx/1.16.0
< Content-Type: application/json
< Transfer-Encoding: chunked
< Connection: keep-alive
< X-Powered-By: PHP/7.3.4
< Cache-Control: no-cache, private
< Date: Sat, 31 Aug 2019 17:32:06 GMT
< Access-Control-Allow-Origin: *.reporangler.develop
< Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS
< Access-Control-Allow-Credentials: true
< Access-Control-Allow-Headers: *
< 
* Connection #0 to host auth.reporangler.develop left intact
{"statusCode":200,"service":"http:\/\/auth.reporangler.develop"} 
```

# Usage

Right now all the users are faked, but you can test the auth to return users by using the following curl commands.

```
curl -H 'Content-Type: application/json' -X POST -d '{"type":"http-basic", "username": "chris", "password": "thomas"}' http://auth.reporangler.develop/auth
```

should return (as long as the faked data is still unchanged):
```
{"id":1,"username":"chris","groups":["chris","companyA","companyB"]}
```

a non existence user will do this
```
curl -H 'Content-Type: application/json' -X POST -d '{"type":"http-basic", "username": "hello", "password": "thomas"}' http://auth.reporangler.develop/auth
```

and return:
```
{"message":"This user does not exist","code":404,"stack":[...stack dump if debug is enabled...]}
```

The php-service already uses this faked data to retrieve the logged in user and will send the data to the package 
metadata-service to retrieve the list of packages

# Future Ideas 

- Support LDAP login

# Notes

There are no notes
