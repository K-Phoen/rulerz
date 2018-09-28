tests: phpspec behat

release:
	./bin/RMT release

rusty:
	php ./bin/rusty check --bootstrap-file=./vendor/autoload.php src
	php ./bin/rusty check --no-execute doc

behat:
	php ./bin/behat --colors -vvv

phpspec:
	php ./bin/phpspec run --ansi  -vvv
