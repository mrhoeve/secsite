#!/bin/bash
docker run -d --name maildev --rm -p 1080:80 -p 1025:25 maildev/maildev --web 80 --smtp 25 --verbose