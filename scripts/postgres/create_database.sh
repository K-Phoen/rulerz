docker exec -it pg-rulerz psql -U postgres -c "DROP DATABASE IF EXISTS test_rulerz"
docker exec -it pg-rulerz psql -U postgres -c "CREATE DATABASE test_rulerz"
docker exec -it pg-rulerz psql -U postgres -d test_rulerz -f /tmp/rulerz/examples/database.sql
