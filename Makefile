tests: phpspec behat

behat:
	php ./bin/behat --colors -vvv

phpspec:
	php ./bin/phpspec run --ansi  -vvv

databases: sqlite elasticsearch postgres

sqlite:
	rm -f ./examples/rulerz.db && sqlite3 ./examples/rulerz.db < ./examples/database.sql

elasticsearch:
	./scripts/elasticsearch/create_mapping.sh && php ./scripts/elasticsearch/load_fixtures.php

postgres:
	./scripts/postgres/create_database.sh

elasticsearch_start:
	docker run -d -p 9200:9200 --name es-rulerz elasticsearch:1.7

elasticsearch_stop:
	docker rm -f es-rulerz

postgres_start:
	docker run -d -p 5432:5432 -v $(shell pwd):/tmp/rulerz --name pg-rulerz postgres:9.4

postgres_stop:
	docker rm -f pg-rulerz
