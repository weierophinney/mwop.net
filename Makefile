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
# - APPID    - Application ID on Zend Server (defaults to 25)
# - GIT      - Path to git executable
# - NPM      - Path to npm executable
# - PORT     - Port to use with built-in web server (for serve target)
#
# Available targets:
# - composer - update the composer executable
# - serve    - run the website with the built-in web server
# - grunt    - run grunt to minimize CSS
# - zpk      - build a zpk
# - deploy   - deploy the site
# - all      - synonym for deploy target

PHP ?= $(shell which php)
SITE ?= https://mwop.net
VERSION ?= $(shell date -u +"%Y.%m.%d.%H.%M")
CONFIGS ?= $(CURDIR)/../settings.mwop.net
ZSCLIENT ?= zs-client.phar
ZSTARGET ?= mwop
APPID ?= 25
GIT ?= $(shell which git)
NPM ?= $(shell which npm)
PORT ?= 8080

COMPOSER = $(CURDIR)/composer.phar

.PHONY : all composer sitesub pagerules node_modules grunt node_cleanup zpk deploy clean

all : deploy

composer :
	@echo "Ensuring composer is up-to-date..."
	-$(COMPOSER) self-update
	@echo "[DONE] Ensuring composer is up-to-date..."

sitesub :
	@echo "Injecting site name into deploy scripts..."
	-sed --in-place -r -e "s#server \= '[^']+'#server = '$(SITE)'#" $(CURDIR)/zpk/scripts/post_activate.php
	@echo "[DONE] Injecting site name into deploy scripts..."

pagerules :
	@echo "Configuring page cache rules..."
	-$(GIT) checkout -- zpk/scripts/pagecache_rules.xml
	-$(PHP) $(CURDIR)/bin/mwop.net.php prep-page-cache-rules --appId=$(APPID) --site=$(SITE)
	@echo "[DONE] Configuring page cache rules..."

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

zpk : composer sitesub pagerules node_cleanup
	@echo "Creating zpk..."
	-$(CURDIR)/vendor/zfcampus/zf-deploy/bin/zfdeploy.php build mwop-$(VERSION).zpk --configs=$(CONFIGS) --zpkdata=$(CURDIR)/zpk --version=$(VERSION)
	@echo "[DONE] Creating zpk."

deploy : zpk
	@echo "Deploying ZPK..."
	-$(ZSCLIENT) applicationUpdate --appId=$(APPID) --appPackage=mwop-$(VERSION).zpk --target=$(ZSTARGET)
	@echo "[DONE] Deploying ZPK."

serve:
	@echo "Starting built-in web server"
	$(PHP) -S 0:$(PORT) -t public/ public/index.php
	@echo "[DONE] Running built-in web server"

clean :
	@echo "Cleaning up..."
	-rm -Rf $(CURDIR)/*.zpk
	@echo "[DONE] Cleaning up."
