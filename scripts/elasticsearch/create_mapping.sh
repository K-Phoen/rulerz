curl -X DELETE http://localhost:9200/rulerz_tests

curl -X POST http://localhost:9200/rulerz_tests -d '{
  "mappings": {
    "player": {
      "properties": {
        "pseudo":   { "type": "string", "index":  "not_analyzed" },
        "gender":   { "type": "string", "index":  "not_analyzed" }
      }
    }
  }
}'
