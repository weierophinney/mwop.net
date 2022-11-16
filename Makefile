#!make
##################### Variables ##########################
HERE := $(dir $(realpath $(firstword $(MAKEFILE_LIST))))
# Use bash as the shell (gives us better conditionals)
SHELL = /bin/bash
CR = registry.digitalocean.com/mwop/php-extensions
OPENSWOOLE_TAG = php-8.1-openswoole-4.11.1
##########################################################

####################### Colors ###########################
# These can be used inside of strings passed to printf
BLUE="\\033[34m"
GREEN="\\033[32m"
RED="\\033[31m"
# Closing sequence
END="\\033[0m"
##########################################################

###################### Conditions ########################
# If a .env file is present, include it and export all
# env variables it contains
ifneq (,$(wildcard ./.env))
	include .env
	export
endif
##########################################################

##### Makefile related #####
.PHONY: 

default: help

cmd-exists-%:
	@hash $(*) > /dev/null 2>&1 || \
		(printf "$(RED)ERROR:$(END) '$(*)' must be installed and available on your PATH.\n"; exit 1)

##@ Help

help:  ## Display this help
	@awk 'BEGIN {FS = ":.*##"; printf "\n\033[1mUsage:\033[0m\n  make \033[32m<target>\033[0m\n"} /^[0-9a-zA-Z_-]+:.*?##/ { printf "  \033[32m%-40s\033[0m %s\n", $$1, $$2 } /^##@/ { printf "\n\033[1m%s\033[0m\n", substr($$0, 5) } ' $(MAKEFILE_LIST)

build-openswoole:  ## Build the openswoole image
	@cd $(HERE)
	@printf "$(GREEN)Building OpenSwoole image$(END)\n"
	docker build -t "$(CR):$(OPENSWOOLE_TAG)" -f etc/docker/openswoole.Dockerfile .
	@printf "$(GREEN)Built $(CR):$(OPENSWOOLE_TAG)$(END)\n"

push-openswoole:  ## Push the openswoole image to the container registry
	@printf "$(GREEN)Pushing OpenSwoole image$(END)\n"
	docker push "$(CR):$(OPENSWOOLE_TAG)"
	@printf "$(GREEN)Pushed OpenSwoole image$(END)\n"
