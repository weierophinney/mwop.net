# mwop.net Makefile
#
# Create and tag the various containers that make up the stack.

VERSION := $(shell date +%Y%m%d%H%M)

CADDY_VERSION?=latest
REDIS_VERSION?=latest
SWOOLE_VERSION?=latest

.PHONY : all assets caddy redis swoole

all: caddy redis swoole

redis:
	@echo "Creating redis container"
	@echo "- Building container"
	- docker build -t mwopredis -f ./etc/docker/redis.Dockerfile .
	@echo "- Tagging image"
	- docker tag mwopredis:latest mwop/mwopredis:$(VERSION)
	@echo "- Pushing image to hub"
	- docker push mwop/mwopredis:$(VERSION)

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
	@echo "- Building container"
	- docker build -t mwopswoole -f ./etc/docker/php.Dockerfile .
	@echo "- Tagging image"
	- docker tag mwopswoole:latest mwop/mwopswoole:$(VERSION)
	@echo "- Pushing image to hub"
	- docker push mwop/mwopswoole:$(VERSION)

assets:
	@echo "Building assets"
	@echo "- Installing dependencies"
	- (cd assets && npm install)
	@echo "- Building assets"
	- (cd assets && grunt)
