#!/bin/bash
docker build -f Dockerfile-www . -t somesite:latest
docker build -f Dockerfile-mysql . -t somesitesql:latest