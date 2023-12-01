#!make
########################## Variables #####################
HERE := $(dir $(realpath $(firstword $(MAKEFILE_LIST))))
##########################################################

.PHONY:

date := $(shell date +%Y-%m-%d)

default: help

##@ Help

help:  ## Display this help
	@awk 'BEGIN {FS = ":.*##"; printf "\nUsage:\n  make \033[36m<target>\033[0m\n"} /^[0-9a-zA-Z_-]+:.*?##/ { printf "  \033[36m%-40s\033[0m %s\n", $$1, $$2 } /^##@/ { printf "\n\033[1m%s\033[0m\n", substr($$0, 5) } ' $(MAKEFILE_LIST)

##@ Build tasks

clean:  ## Cleanup and remove any generated files
	@printf "\n\033[92mRemoving any previously generated files...\033[0m\n"
	rm -f $(HERE)/.docker/*.prod.Dockerfile

.docker/nginx.prod.Dockerfile:  ## Build the production nginx Dockerfile
	@printf "\n\033[92mCreating .docker/nginx.prod.Dockerfile...\033[0m\n"
	awk -v "template=$(cat "./.docker/nginx.prod-template.Dockerfile")" "{sub(/## TEMPLATED ##/,template)}1" "./.docker/nginx.Dockerfile" > "./.docker/nginx.prod.Dockerfile"
	@printf "\n\033[92m[DONE] Created .docker/nginx.prod.Dockerfile\033[0m\n"

.docker/php.prod.Dockerfile:  ## Build the production php Dockerfile
	@printf "\n\033[92mCreating .docker/php.prod.Dockerfile...\033[0m\n"
	awk -v "template=$(cat "./.docker/php.prod-template.Dockerfile")" "{sub(/## TEMPLATED ##/,template)}1" "./.docker/php.Dockerfile" > "./.docker/php.prod.Dockerfile"
	@printf "\n\033[92m[DONE] Created .docker/php.prod.Dockerfile\033[0m\n"

prod-dockerfiles:  clean .docker/nginx.prod.Dockerfile .docker/php.prod.Dockerfile ## Build the production dockerfiles

pull-images:  ## Pull new versions of all base images
	@printf "\n\033[92mPulling updates for base images\033[0m\n"
	bin/pull-images.sh
	@printf "\n\033[92m[DONE] Pulled updates for base images\033[0m\n"

##@ Production containers

prod-build: prod-dockerfiles pull-images ## Build production compose containers
	@printf "\n\033[92mBuilding production compose containers\033[0m\n"
	cd $(HERE)
	docker compose -f ./docker-compose.yml build --no-cache
	@printf "\n\033[92m[DONE] Built production compose containers\033[0m\n"

prod-run:  ## Run production compose containers
	@printf "\n\033[92mStarting production compose containers\033[0m\n"
	cd $(HERE)
	docker compose -f ./docker-compose.yml up -d
	@printf "\n\033[92m[DONE] Started production compose containers\033[0m\n"

prod-status:  ## Get production containers status
	@printf "\n\033[92mGetting production compose containers status\033[0m\n"
	cd $(HERE)
	docker compose -f ./docker-compose.yml ps

prod-stop:  ## Stop production compose containers
	@printf "\n\033[92mStopping production compose containers\033[0m\n"
	cd $(HERE)
	docker compose -f ./docker-compose.yml down
	@printf "\n\033[92m[DONE] Stopped production compose containers\033[0m\n"

##@ Dev containers

dev-build:  ## Build dev compose containers
	@printf "\n\033[92mBuilding dev compose containers\033[0m\n"
	cd $(HERE)
	docker compose -f ./docker-compose.dev.yml build
	@printf "\n\033[92m[DONE] Built dev compose containers\033[0m\n"

dev-run:  ## Run dev compose containers
	@printf "\n\033[92mStarting dev compose containers\033[0m\n"
	cd $(HERE)
	docker compose -f ./docker-compose.dev.yml up

dev-status:  ## Get dev containers status
	@printf "\n\033[92mGetting dev compose container status\033[0m\n"
	cd $(HERE)
	docker compose -f ./docker-compose.dev.yml ps

dev-stop:  ## Stop dev compose containers
	@printf "\n\033[92mStopping dev compose containers\033[0m\n"
	cd $(HERE)
	docker compose -f ./docker-compose.dev.yml down
	@printf "\n\033[92m[DONE] Stopped dev compose containers\033[0m\n"
