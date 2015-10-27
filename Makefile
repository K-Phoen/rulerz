tests:
	php ./bin/phpspec run -vvv ; php ./bin/behat -vvv

database:
	sqlite3 ./examples/rulerz.db < ./examples/database.sql
