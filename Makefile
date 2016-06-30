# mwop.net Makefile
#
# Primary purpose is for installing dependencies.
#
# Configurable variables:
# - PHP      - PHP executable to use, if not in path
# - NPM      - Path to npm executable
# - PORT     - Port to use with built-in web server (for serve target)
#
# Available targets:
# - composer - update the composer executable
# - serve    - run the website with the built-in web server
# - grunt    - run grunt to minimize CSS
# - all      - synonym for composer + grunt

PHP ?= $(shell which php)
NPM ?= $(shell which npm)
PORT ?= 8080

COMPOSER = $(shell which composer)

.PHONY : all composer node_modules grunt node_cleanup serve

all : composer grunt

composer :
	@echo "Ensuring composer is up-to-date..."
	-$(COMPOSER) self-update
	@echo "[DONE] Ensuring composer is up-to-date..."

node_modules :
	@echo "Installing node packages for grunt..."
	-$(NPM) install
	@echo "[DONE] Installing node packages for grunt..."

grunt : node_modules
	@echo "Running grunt to minimize CSS..."
	-grunt
	@echo "[DONE] Running grunt to minimize CSS..."

node_cleanup : grunt
	@echo "Removing node modules..."
	-rm -Rf $(CURDIR)/node_modules
	@echo "[DONE] Removing node modules..."

serve:
	@echo "Starting built-in web server"
	$(PHP) -S 0:$(PORT) -t public/ public/index.php
	@echo "[DONE] Running built-in web server"
