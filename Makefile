tests: phpspec behat

behat:
	php ./bin/behat -vvv

phpspec:
	php ./bin/phpspec run -vvv

databases: sqlite elasticsearch

sqlite:
	sqlite3 ./examples/rulerz.db < ./examples/database.sql

elasticsearch:
	./scripts/elasticsearch/create_mapping.sh && php ./examples/load_elasticsearch_fixtures.php
