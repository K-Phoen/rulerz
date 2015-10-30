tests: phpspec behat

behat:
	php ./bin/behat --colors -vvv

phpspec:
	php ./bin/phpspec run --ansi  -vvv

databases: sqlite elasticsearch

sqlite:
	rm -f ./examples/rulerz.db && sqlite3 ./examples/rulerz.db < ./examples/database.sql

elasticsearch:
	./scripts/elasticsearch/create_mapping.sh && php ./scripts/elasticsearch/load_fixtures.php

elasticsearch_start:
	docker run -d -p 9200:9200 --name es-rulerz elasticsearch:1.7

elasticsearch_stop:
	docker rm -f es-rulerz
