#!/bin/bash

set -e

scp docker-compose.staging.yml root@${STAGING_SERVER}:~/staging
ssh root@${STAGING_SERVER} "docker-compose -f ./staging/docker-compose.staging.yml pull web && \
docker-compose -f ./staging/docker-compose.staging.yml up -d && \
docker restart nginxproxy_nginx-proxy_1"