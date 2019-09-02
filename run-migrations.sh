#!/usr/bin/env bash

docker-compose exec auth_service_phpfpm php /www/artisan migrate $@
