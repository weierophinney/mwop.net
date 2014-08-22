# mwop.net Makefile
#
# Primary purpose is for deployment
#
# Configurable variables:
# - PHP      - PHP executable to use, if not in path
# - SITE     - Site deploying to (defaults to mwop.net)
# - VERSION  - Version name to use; defaults to a timestamp
# - CONFIGS  - Path to directory containing deployment-specific configs
# - ZSCLIENT - Path to zs-client.phar (defaults to zs-client.phar)
# - ZSTARGET - Target for zs-client.phar (defaults to mwop)
#
# Available targets:
# - composer - update the composer executable
# - zpk      - build a zpk
# - deploy   - deploy the site
# - all      - synonym for deploy target

PHP ?= $(shell which php)
SITE ?= mwop.net
VERSION ?= $(shell date -u +"%Y.%m.%d.%H.%M")
CONFIGS ?= $(CURDIR)/../site-settings
ZSCLIENT ?= zs-client.phar
ZSTARGET ?= mwop

COMPOSER = $(CURDIR)/composer.phar

.PHONY : all composer sitesub zpk deploy clean

all : deploy

composer :
	@echo "Ensuring composer is up-to-date..."
	-$(COMPOSER) self-update
	@echo "[DONE] Ensuring composer is up-to-date..."

sitesub :
	@echo "Injecting site name into deploy scripts..."
	-sed --in-place -r -e "s/server \= '[^']+'/server = 'http:\/\/$(SITE)'/" $(CURDIR)/zpk/scripts/post_activate.php
	@echo "[DONE] Injecting site name into deploy scripts..."

zpk : composer sitesub
	@echo "Creating zpk..."
	-$(CURDIR)/vendor/bin/zfdeploy.php build mwop-$(VERSION).zpk --configs=$(CONFIGS) --zpkdata=$(CURDIR)/zpk --version=$(VERSION)
	@echo "[DONE] Creating zpk."

deploy : zpk
	@echo "Deploying ZPK..."
	-$(ZSCLIENT) applicationUpdate --appId=20 --appPackage=mwop-$(VERSION).zpk --target=$(ZSTARGET)
	@echo "[DONE] Deploying ZPK."

clean :
	@echo "Cleaning up..."
	-rm -Rf $(CURDIR)/*.zpk
	@echo "[DONE] Cleaning up."
