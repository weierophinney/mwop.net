# mwop.net Makefile
#
# Create a docker-stack.yml based on latest tags of required containers, and
# deploy to swarm.
#
# Allowed/expected variables:
#
# - CADDY_VERSION: specific Caddy container version to use
# - SWOOLE_VERSION: specific php+swoole container version to use
#
# If not specified, each defaults to "latest", which forces a lookup of the
# latest tagged version.

VERSION := $(shell date +%Y%m%d%H%M)

CADDY_VERSION?=latest
SWOOLE_VERSION?=latest

.PHONY : all deploy swoole caddy

all: check-env deploy swoole caddy

check-env:
ifndef DOCKER_MACHINE_NAME
	$(error DOCKER_MACHINE_NAME is undefined; run "eval $$(docker-machine env mwopnet)" first)
endif
ifneq ($(DOCKER_MACHINE_NAME),mwopnet)
	$(error DOCKER_MACHINE_NAME is incorrect; run "eval $$(docker-machine env mwopnet)" first)
endif

docker-stack.yml:
	@echo "Creating docker-stack.yml"
	@echo "- swoole container version: $(SWOOLE_VERSION)"
	@echo "- caddy container version: $(CADDY_VERSION)"
	- $(CURDIR)/bin/create-docker-stack.php -p $(SWOOLE_VERSION) -c ${CADDY_VERSION}

deploy: check-env docker-stack.yml
	@echo "Deploying to swarm"
	- docker stack deploy --with-registry-auth -c docker-stack.yml mwopnet
	- rm docker-stack.yml

caddy:
	@echo "Creating caddy container"
	@echo "- Building container"
	- docker build -t mwopswoolecaddy -f ./etc/docker/caddy.Dockerfile .
	@echo "- Tagging image"
	- docker tag mwopswoolecaddy:latest mwop/mwopswoolecaddy:$(VERSION)
	@echo "- Pushing image to hub"
	- docker push mwop/mwopswoolecaddy:$(VERSION)

swoole:
	@echo "Creating swoole container"
	@echo "- Building assets"
	- composer docker:assets
	@echo "- Building container"
	- docker build -t mwopswoole -f ./etc/docker/php.Dockerfile .
	@echo "- Tagging image"
	- docker tag mwopswoole:latest mwop/mwopswoole:$(VERSION)
	@echo "- Pushing image to hub"
	- docker push mwop/mwopswoole:$(VERSION)
