#!make
########################## Variables #####################
HERE := $(dir $(realpath $(firstword $(MAKEFILE_LIST))))
SHELL := /bin/bash
COMPOSER_ACTION ?= update
##########################################################

############################### Colors ################################
# Call these using the construct @$(call {VAR},"text to display")
MK_RED = echo -e "\e[31m"$(1)"\e[0m"
MK_GREEN = echo -e "\e[32m"$(1)"\e[0m"
MK_YELLOW = echo -e "\e[33m"$(1)"\e[0m"
MK_BLUE = echo -e "\e[34m"$(1)"\e[0m"
MK_MAGENTA = echo -e "\e[35m"$(1)"\e[0m"
MK_CYAN = echo -e "\e[36m"$(1)"\e[0m"
MK_BOLD = echo -e "\e[1m"$(1)"\e[0m"
MK_UNDERLINE = echo -e "\e[4m"$(1)"\e[0m"
MK_RED_BOLD = echo -e "\e[1;31m"$(1)"\e[0m"
MK_GREEN_BOLD = echo -e "\e[1;32m"$(1)"\e[0m"
MK_YELLOW_BOLD = echo -e "\e[1;33m"$(1)"\e[0m"
MK_BLUE_BOLD = echo -e "\e[1;34m"$(1)"\e[0m"
MK_MAGENTA_BOLD = echo -e "\e[1;35m"$(1)"\e[0m"
MK_CYAN_BOLD = echo -e "\e[1;36m"$(1)"\e[0m"
MK_RED_UNDERLINE = echo -e "\e[4;31m"$(1)"\e[0m"
MK_GREEN_UNDERLINE = echo -e "\e[4;32m"$(1)"\e[0m"
MK_YELLOW_UNDERLINE = echo -e "\e[4;33m"$(1)"\e[0m"
MK_BLUE_UNDERLINE = echo -e "\e[4;34m"$(1)"\e[0m"
MK_MAGENTA_UNDERLINE = echo -e "\e[4;35m"$(1)"\e[0m"
MK_CYAN_UNDERLINE = echo -e "\e[4;36m"$(1)"\e[0m"

# Semantic names
MK_ERROR = $(call MK_RED,$1)
MK_ERROR_BOLD = $(call MK_RED_BOLD,$1)
MK_ERROR_UNDERLINE = $(call MK_RED_UNDERLINE,$1)
MK_INFO = $(call MK_BLUE,$1)
MK_INFO_BOLD = $(call MK_BLUE_BOLD,$1)
MK_INFO_UNDERLINE = $(call MK_BLUE_UNDERLINE,$1)
MK_SUCCESS = $(call MK_GREEN,$1)
MK_SUCCESS_BOLD = $(call MK_GREEN_BOLD,$1)
MK_SUCCESS_UNDERLINE = $(call MK_GREEN_UNDERLINE,$1)
######################################################################

.PHONY: help clean deps prod-dockerfiles pull-images prod-volumes prod-build prod-run prod-status prod-build dev-build dev-run dev-status dev-stop zendhq-connect

date := $(shell date +%Y-%m-%d)

default: help

##@ Help

help:  ## Display this help
	@awk 'BEGIN {FS = ":.*##"; printf "\nUsage:\n  make \033[36m<target>\033[0m\n"} /^[0-9a-zA-Z_-]+:.*?##/ { printf "  \033[36m%-40s\033[0m %s\n", $$1, $$2 } /^##@/ { printf "\n\033[1m%s\033[0m\n", substr($$0, 5) } ' $(MAKEFILE_LIST)

##@ Build tasks

clean:  ## Cleanup and remove any generated files
	@$(call MK_INFO,"Removing any previously generated files...")
	rm -f $(HERE)/.docker/*.prod.Dockerfile

deps: ## Install/update dependencies - use COMPOSER_ACTION to switch between "update" (default) and "install"
	composer $(COMPOSER_ACTION) --ignore-platform-req=ext-zendhq

.docker/nginx.prod.Dockerfile:  ## Build the production nginx Dockerfile
	@$(call MK_INFO,"Creating .docker/nginx.prod.Dockerfile...")
	awk -v "template=$$(cat "./.docker/nginx.prod-template.Dockerfile")" "{sub(/## TEMPLATED ##/,template)}1" "./.docker/nginx.Dockerfile" > "./.docker/nginx.prod.Dockerfile"
	@$(call MK_SUCCESS,"[DONE] Created .docker/nginx.prod.Dockerfile")

.docker/php.prod.Dockerfile:  ## Build the production php Dockerfile
	@$(call MK_INFO,"Creating .docker/php.prod.Dockerfile...")
	awk -v "template=$$(cat "./.docker/php.prod-template.Dockerfile")" "{sub(/## TEMPLATED ##/,template)}1" "./.docker/php.Dockerfile" > "./.docker/php.prod.Dockerfile"
	@$(call MK_SUCCESS,"[DONE] Created .docker/php.prod.Dockerfile")

prod-dockerfiles:  clean .docker/nginx.prod.Dockerfile .docker/php.prod.Dockerfile ## Build the production dockerfiles

pull-images:  ## Pull new versions of all base images
	@$(call MK_INFO,"Pulling updates for base images")
	bin/pull-images.sh
	@$(call MK_SUCCESS,"[DONE] Pulled updates for base images")

##@ Production containers

prod-volumes:  ## Create the production shared volumes
	@$(call MK_INFO,"Creating the production shared volumes")
	if ! docker volume ls | grep -q "mwop_net_redis";then docker volume create mwop_net_redis; fi
	if ! docker volume ls | grep -q "mwop_net_shared_data";then docker volume create mwop_net_shared_data; fi
	if ! docker volume ls | grep -q "mwop_net_zendhq_db";then docker volume create mwop_net_zendhq_db; fi
	@$(call MK_SUCCESS,"[DONE] Created production shared volumes")

prod-build: prod-dockerfiles pull-images ## Build production compose containers
	@$(call MK_INFO,"Building production compose containers")
	cd $(HERE)
	docker compose -f ./docker-compose.yml build
	@$(call MK_SUCCESS,"[DONE] Built production compose containers")

prod-run: prod-volumes  ## Run production compose containers
	@$(call MK_INFO,"Starting production compose containers")
	cd $(HERE)
	docker compose -f ./docker-compose.yml up -d
	@$(call MK_SUCCESS,"[DONE] Started production compose containers")

prod-status:  ## Get production containers status
	@$(call MK_INFO,"Getting production compose container status")
	cd $(HERE)
	docker compose -f ./docker-compose.yml ps

prod-stop:  ## Stop production compose containers
	@$(call MK_INFO,"Stopping production compose containers")
	cd $(HERE)
	docker compose -f ./docker-compose.yml down
	@$(call MK_SUCCESS,"[DONE] Stopped production compose containers")

##@ Dev containers

dev-build:  ## Build dev compose containers
	@$(call MK_INFO,"Building dev compose containers")
	cd $(HERE)
	docker compose -f ./docker-compose.dev.yml build
	@$(call MK_SUCCESS,"[DONE] Built dev compose containers")

dev-run:  ## Run dev compose containers
	@$(call MK_INFO,"Starting dev compose containers")
	cd $(HERE)
	docker compose -f ./docker-compose.dev.yml up

dev-status:  ## Get dev containers status
	@$(call MK_INFO,"Getting dev compose container status")
	cd $(HERE)
	docker compose -f ./docker-compose.dev.yml ps

dev-stop:  ## Stop dev compose containers
	@$(call MK_INFO,"Stopping dev compose containers")
	cd $(HERE)
	docker compose -f ./docker-compose.dev.yml down
	@$(call MK_SUCCESS,"[DONE] Stopped dev compose containers")

##@ Monitoring

zendhq-connect:  ## Setup SSH tunnel for ZendHQ
	@$(call MK_INFO,"Starting port forwarding session to allow using ZendHQ")
	@$(call MK_CYAN,"Type Ctrl-C to disconnect")
	ssh -N -L 10091:mwop.net:10091 mwop.net
