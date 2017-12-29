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

databases: sqlite elasticsearch postgres solr

sqlite:
	rm -f ./examples/rulerz.db && sqlite3 ./examples/rulerz.db < ./examples/database.sql

elasticsearch:
	./scripts/elasticsearch/create_mapping.sh && php ./scripts/elasticsearch/load_fixtures.php

solr:
	./scripts/solr/create_core.sh && php ./scripts/solr/load_fixtures.php

postgres:
	./scripts/postgres/create_database.sh

docker_start: elasticsearch_start postgres_start solr_start

docker_stop: elasticsearch_stop postgres_stop solr_stop

elasticsearch_start:
	docker run -d -p 9200:9200 --name es-rulerz elasticsearch:2.4

elasticsearch_stop:
	docker rm -f es-rulerz

postgres_start:
	docker run -d -p 5432:5432 -v $(shell pwd):/tmp/rulerz --name pg-rulerz postgres:9.4

postgres_stop:
	docker rm -f pg-rulerz

solr_start:
	docker run -d -p 8983:8983 -v $(shell pwd)/scripts/solr/config/conf:/opt/solr/server/solr/rulerz_tests/conf --name solr-rulerz solr:5.3.1
	docker exec -it --user=root solr-rulerz chown -R solr:solr /opt/solr/server/solr/rulerz_tests

solr_stop:
	docker rm -f solr-rulerz
