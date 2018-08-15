# mwop.net Makefile
#
# Create a docker-stack.yml based on latest tags of required containers, and
# deploy to swarm.
#
# Allowed/expected variables:
#
# - CADDY_VERSION: specific Caddy container version to use
# - PHP_VERSION: specific php container version to use
#
# If not specified, each defaults to "latest", which forces a lookup of the
# latest tagged version.

VERSION := $(shell date +%Y%m%d%H%M)

CADDY_VERSION?=latest
PHP_VERSION?=latest

.PHONY : all deploy php caddy

all: check-env deploy php caddy

check-env:
ifndef DOCKER_MACHINE_NAME
	$(error DOCKER_MACHINE_NAME is undefined; run "eval $$(docker-machine env mwopnet)" first)
endif
ifneq ($(DOCKER_MACHINE_NAME),mwopnet)
	$(error DOCKER_MACHINE_NAME is incorrect; run "eval $$(docker-machine env mwopnet)" first)
endif

docker-stack.yml:
	@echo "Creating docker-stack.yml"
	@echo "- php container version: $(PHP_VERSION)"
	@echo "- caddy container version: $(CADDY_VERSION)"
	- $(CURDIR)/bin/create-docker-stack.php -p $(PHP_VERSION) -c ${CADDY_VERSION}

deploy: check-env docker-stack.yml
	@echo "Deploying to swarm"
	- docker stack deploy --with-registry-auth -c docker-stack.yml mwopnet
	- rm docker-stack.yml

caddy:
	@echo "Creating caddy container"
	@echo "- Building container"
	- docker build -t mwopcaddy -f ./etc/docker/caddy.Dockerfile .
	@echo "- Tagging image"
	- docker tag mwopcaddy:latest mwop/mwopcaddy:$(VERSION)
	@echo "- Pushing image to hub"
	- docker push mwop/mwopcaddy:$(VERSION)

php:
	@echo "Creating php container"
	@echo "- Building container"
	- docker build -t mwopphp -f ./etc/docker/php.Dockerfile .
	@echo "- Tagging image"
	- docker tag mwopphp:latest mwop/mwopphp:$(VERSION)
	@echo "- Pushing image to hub"
	- docker push mwop/mwopphp:$(VERSION)
