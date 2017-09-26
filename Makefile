# mwop.net Makefile
#
# Create a docker-stack.yml based on latest tags of required containers, and
# deploy to swarm.

MACHINE=mwopnet

VERSION := $(shell date +%Y%m%d%H%M)

.PHONY : all deploy nginx php-fpm

all: nginx php-fpm deploy

docker-stack.yml:
	@echo "Creating docker-stack.yml"
	- $(CURDIR)/bin/create-docker-stack.php

deploy: docker-stack.yml
	@echo "Setting docker-machine environment"
	- $(shell eval $(docker-machine env $(MACHINE)))
	@echo "Deploying to swarm"
	- docker stack deploy --with-registry-auth -c docker-stack.yml $(MACHINE)
	- rm docker-stack.yml

nginx:
	@echo "Creating nginx container"
	@echo "- Building assets"
	- composer build-nginx
	@echo "- Building container"
	- docker build -t mwopnginx -f ./etc/docker/nginx.Dockerfile .
	@echo "- Tagging image"
	- docker tag mwopnginx:latest mwop/mwopnginx:$(VERSION)
	@echo "- Pushing image to hub"
	- docker push mwop/mwopnginx:$(VERSION)

php-fpm:
	@echo "Creating php-fpm container"
	@echo "- Building container"
	- docker build -t mwopphp -f ./etc/docker/php-fpm.Dockerfile .
	@echo "- Tagging image"
	- docker tag mwopphp:latest mwop/mwopphp:$(VERSION)
	@echo "- Pushing image to hub"
	- docker push mwop/mwopphp:$(VERSION)
