#!/bin/bash

echo "Installing dependencies..."
composer install

echo "Running unit tests..."
./vendor/bin/phpunit --testsuite Unit

echo "Running integration tests..."
./vendor/bin/phpunit --testsuite Integration

echo "Running with coverage..."
./vendor/bin/phpunit --coverage-text

echo "All tests completed!"