#!/bin/bash

echo "Starting Elasticsearch initialization..."

echo "Waiting for Elasticsearch to be available..."
until curl -s "http://localhost:9200" > /dev/null; do
    echo "Elasticsearch is not available yet. Waiting..."
    sleep 2
done
echo "Elasticsearch is now available!"

curl -X GET "http://localhost:9200"

echo "Deleting existing index 'otus-shop'..."
curl -X DELETE "localhost:9200/otus-shop"

echo "Creating index 'otus-shop' with nested mapping for stock..."
curl -X PUT "localhost:9200/otus-shop" -H 'Content-Type: application/json' -d '{
  "mappings": {
    "properties": {
      "title": { "type": "text" },
      "sku": { "type": "keyword" },
      "category": { "type": "keyword" },
      "price": { "type": "integer" },
      "stock": {
        "type": "nested",
        "properties": {
          "shop": { "type": "keyword" },
          "stock": { "type": "integer" }
        }
      }
    }
  }
}'

echo "Uploading test data to Elasticsearch..."
curl -X POST "localhost:9200/_bulk" -H "Content-Type: application/json" --data-binary @books.json

echo "Elasticsearch initialization complete."
