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
curl -H 'Content-Type: application/json' -X POST -d '{"type":"http-basic", "username": "chris", "password": "thomas"}' http://auth.reporangler.develop/user/login
```

should return (as long as the faked data is still unchanged):
```
{"id":1,"username":"chris","groups":["chris","companyA","companyB"]}
```

a non existence user will do this
```
curl -H 'Content-Type: application/json' -X POST -d '{"type":"http-basic", "username": "hello", "password": "thomas"}' http://auth.reporangler.develop/user/login
```

and return:
```
{"message":"This user does not exist","code":404,"stack":[...stack dump if debug is enabled...]}
```

The php-service already uses this faked data to retrieve the logged in user and will send the data to the package 
metadata-service to retrieve the list of packages

# REST Api

There is a (TODO: not secured) REST Api which can be used to configure the authenticated users and what packages they can access.
This REST Api is the same api that the command line client (TODO: not created yet) uses in order to do what it does.

## /package-group

These are the allowed groups that users can be assigned to. They define what groups of packages can be made. A typical set
of package groups are:
- public
    - All users when created are given this group by default
- custom-group
    - This might be the name of a company department or some segmenting property. E.g. 'data-eng' or 'platform-eng'
- username
    - All users are given a group named after themselves, allowing them to publish private packages nobody else can see

#### Endpoints:
- get `/package-group`: list all registered package groups
- get `/package-group/{name}`: get package group by name
- get `/package-group/{id}`: get package group by id
- post `/package-group`: create a new package group
    - fields: [`name:string`]
- put `/package-group`: update a package group
    - fields: [`id:integer`, `name:string`]    
- delete `/package-group/{id}`: delete a package by id

### /user

Users are required to know who can login, to what package repository they have access and defined what 
package groups, when authorized they are allowed to read

#### Endpoints:
- post `/user/login`: attempt a login
    - fields: [
        `type:one_of(database,http-basic,ldap)`, 
        `username:string`, 
        `password:string`, 
        `repository_type:string`
    ]
- get `/user/check`: check a token
    - headers: [
        `Authorization: Bearer {token}`
    ]
- get `/user`: get a list of all users
- get `/user/{name}`: get a user by username
- get `/user/{id}`: get a user by id
- post `/user`: create a user
    - fields: [
        `username:string`,
        `password:string`,
        `repository_type:string`
    ]
- put `/user/{id}`: update a user by id
    - fields: [
        (optional)`username:string`,
        (optional)`password:string`,
        (optional)`repository_type:string`
    ]
- delete `/user/{id}`: delete a user by id
- post `/user/{id}/token`: create a service access token
    - fields: [
        `type:one_of(github)`,
        `token:string`
    ]
- delete `/user/{userId}/token/{tokenId}`: delete a service token by id from a specific user

### /user-package-group

A user must be assigned package groups otherwise they can not read any package lists

#### Endpoints:
- get `/user-package-group/user/{id}`: list all mappings by user id
- get `/user-package-group/group/{id}`: list all mappings by package group id
- get `/user-package-group`: list all mappings
- post `/user-package-group`: create a mapping between user and package group
    - fields: [`user_id:integer`, `package_group_id:integer`]
- delete `/user-package-group/user/{user_id}/group/{group_id}`: delete a specific mapping between a user and package_group
- delete `/user-package-group/user/{id}`: delete all mappings for user id (removes a user out of all groups)
- delete `/user-package-group/group/{id}`: delete all mappings from group id (removes a group from all users)

# Future Ideas 

- Support LDAP login

# Notes

There are no notes
