test:
	php vendor/bin/phpunit

test-coverage:
	XDEBUG_MODE=coverage php vendor/bin/phpunit --coverage-text

test-coverage-html:
	XDEBUG_MODE=coverage php vendor/bin/phpunit --coverage-html coverage
