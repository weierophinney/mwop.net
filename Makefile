# mwop.net Makefile
#
# Create a docker-stack.yml based on latest tags of required containers, and
# deploy to swarm.

MACHINE=mwopnet

.PHONY : all machineenv deploy

all: deploy

machineenv:
	@echo "Setting docker-machine environment"
	- eval $(docker-machine env $(MACHINE))

docker-stack.yml:
	@echo "Creating docker-stack.yml"
	- $(CURDIR)/bin/create-docker-stack.php

deploy: docker-stack.yml machineenv
	@echo "Deploying to swarm"
	- docker stack deploy --with-registry-auth -c docker-stack.yml $(MACHINE)
	- rm docker-stack.yml
