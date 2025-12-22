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

echo "Creating index 'otus-shop'..."
curl -X PUT "localhost:9200/otus-shop"

echo "Uploading test data to Elasticsearch..."
curl -X POST "localhost:9200/_bulk" -H "Content-Type: application/json" --data-binary @books.json

echo "Elasticsearch initialization complete."
