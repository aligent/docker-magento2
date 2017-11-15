#!/usr/bin/env bash

for s in `docker-compose ps -q`
do
    echo `docker inspect -f "{{.Name}}" $s` `docker inspect -f '{{range .NetworkSettings.Networks}}{{.IPAddress}}{{end}}' $s`
done