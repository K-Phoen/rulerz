tests: phpspec behat

behat:
	php ./bin/behat -vvv

phpspec:
	php ./bin/phpspec run -vvv

databases: sqlite elasticsearch

sqlite:
	rm -f ./example/rulerz.db && sqlite3 ./examples/rulerz.db < ./examples/database.sql

elasticsearch:
	./scripts/elasticsearch/create_mapping.sh && php ./scripts/elasticsearch/load_fixtures.php
