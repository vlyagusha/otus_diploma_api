#!/usr/bin/env bash

docker-compose -f docker/docker-compose.yml down --remove-orphans
docker volume prune -f
