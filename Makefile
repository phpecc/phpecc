test: phpunit phpcs

.PHONY: test phpunit phpcs

pretest:
		composer install --dev

phpunit: pretest
		mkdir -p tests/output
		vendor/bin/phpunit --coverage-text --coverage-clover=tests/output/coverage.clover

ifndef STRICT
STRICT = 0
endif

ifeq "$(STRICT)" "1"
phpcs: pretest
		vendor/bin/phpcs --standard=phpcs.xml src
else
phpcs: pretest
		vendor/bin/phpcs --standard=phpcs.xml -n src
endif

ifdef OCULAR_TOKEN
scrutinizer: ocular
		@php ocular.phar code-coverage:upload --format=php-clover tests/output/coverage.clover --access-token=$(OCULAR_TOKEN);
else
scrutinizer: ocular
		php ocular.phar code-coverage:upload --format=php-clover tests/output/coverage.clover;
endif

clean: clean-env clean-deps

clean-env:
		rm -rf coverage.clover
		rm -rf ocular.phar
		rm -rf tests/output/

clean-deps:
		rm -rf vendor/
