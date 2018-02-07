.PHONY: cs-check cs-fix help lint lint-composer lint-yaml test

default: help

help:
	@grep -E '^[a-zA-Z_-]+:.*?##.*$$' $(MAKEFILE_LIST) | sort | awk '{split($$0, a, ":"); printf "\033[36m%-30s\033[0m %-30s %s\n", a[1], a[2], a[3]}'

cs-check: ## to show files that need to be fixed
	php-cs-fixer fix --ansi --verbose --diff --dry-run

cs-fix: ## to fix files that need to be fixed
	php-cs-fixer fix --verbose

lint: ## to lint php, yaml & composer
	make lint-composer lint-composer cs-check lint-yaml

lint-composer: ## to validate composer.lock
	composer validate

lint-yaml: ## to lint yaml
	find . -name '*.yml' -not -path './vendor/*' -not -path './src/Resources/public/vendor/*' | xargs yaml-lint

test: ## to run unit tests
	phpunit -c phpunit.xml.dist --coverage-clover build/logs/clover.xml
