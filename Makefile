# mwop.net Makefile

.PHONY : all assets

all: assets

assets:
	@echo "Building assets"
	@echo "- Installing dependencies"
	- (cd assets && npm install)
	@echo "- Building assets"
	- (cd assets && grunt)
