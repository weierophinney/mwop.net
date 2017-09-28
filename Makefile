# mwop.net Makefile
#
# Create a docker-stack.yml based on latest tags of required containers, and
# deploy to swarm.
#
# Allowed/expected variables:
#
# - NGINX_VERSION: specific nginx container version to use
# - PHP_FPM_VERSION: specific php-fpm container version to use
#
# If not specified, each defaults to "latest", which forces a lookup of the
# latest tagged version.

VERSION := $(shell date +%Y%m%d%H%M)

NGINX_VERSION?=latest
PHP_FPM_VERSION?=latest

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
	@echo "- nginx container version: $(NGINX_VERSION)"
	@echo "- php-fpm container version: $(PHP_FPM_VERSION)"
	- $(CURDIR)/bin/create-docker-stack.php -n $(NGINX_VERSION) -p $(PHP_FPM_VERSION)

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
