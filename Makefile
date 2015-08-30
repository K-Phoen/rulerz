tests:
	php ./bin/phpspec run -vvv

database:
	sqlite3 ./examples/rulerz.db < ./examples/database.sql
