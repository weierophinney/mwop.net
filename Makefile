# mwop.net Makefile

.PHONY : all assets

all: asssets

assets:
	@echo "Building assets"
	@echo "- Installing dependencies"
	- (cd assets && npm install)
	@echo "- Building assets"
	- (cd assets && grunt)
