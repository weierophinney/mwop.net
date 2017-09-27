# mwop.net Makefile
#
# Create a docker-stack.yml based on latest tags of required containers, and
# deploy to swarm.

VERSION := $(shell date +%Y%m%d%H%M)

.PHONY : all deploy nginx php-fpm

all: check-env deploy nginx php-fpm

check-env:
ifndef DOCKER_MACHINE_NAME
	$(error DOCKER_MACHINE_NAME is undefined; run "eval $$(docker-machine env mwopnet)" first)
endif
ifneq ($(DOCKER_MACHINE_NAME),mwopnet)
	$(error DOCKER_MACHINE_NAME is incorrect; run "eval $$(docker-machine env mwopnet)" first)
endif

docker-stack.yml:
	@echo "Creating docker-stack.yml"
	- $(CURDIR)/bin/create-docker-stack.php

deploy: check-env docker-stack.yml
	@echo "Deploying to swarm"
	- docker stack deploy --with-registry-auth -c docker-stack.yml mwopnet
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
