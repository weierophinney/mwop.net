#!make
########################## Variables #####################
HERE := $(dir $(realpath $(firstword $(MAKEFILE_LIST))))
##########################################################

BUILD_ARGS ?=

##### Makefile related #####
.PHONY: 

default: help

##@ Help

help:  ## Display this help
	@awk 'BEGIN {FS = ":.*##"; printf "\nUsage:\n  make \033[36m<target>\033[0m\n"} /^[0-9a-zA-Z_-]+:.*?##/ { printf "  \033[36m%-40s\033[0m %s\n", $$1, $$2 } /^##@/ { printf "\n\033[1m%s\033[0m\n", substr($$0, 5) } ' $(MAKEFILE_LIST)

clean:  ## Cleanup (remove) build assets
	@cd "$(HERE)"
	@printf "\n\033[92mCleaning up...\033[0m\n"
	rm -rf dist

dist:  ## Create distribution directory
	@cd "$(HERE)"
	@printf "\n\033[92mCreating dist directory tree...\033[0m\n"
	mkdir -p dist/css dist/js

styles: dist ## Build CSS
	@cd "$(HERE)"
	@printf "\n\033[92mBuilding CSS...\033[0m\n"
	( IS_PROD=1 postcss css/site.css -o dist/css/site.css )
	cp -a node_modules/@fortawesome/fontawesome-free/webfonts dist/css/

scripts: dist ## Build JS
	@cd "$(HERE)"
	@printf "\n\033[92mBuilding JS...\033[0m\n"
	cp js/*.js dist/js/
	cp node_modules/masonry-layout/dist/masonry.pkgd.min.js dist/js/
	cp node_modules/imagesloaded/imagesloaded.pkgd.min.js dist/js/

assets: clean styles scripts  ## Build all assets
