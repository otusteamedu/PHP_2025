#!bin/bash

curl -X POST "http://application.local/check-emails" \
     -H "Content-Type: application/json; charset=UTF-8" \
     -H "Accept: application/json" \
     -d '{"emails": ["user@example.com", "invalid-email", "test@fake-domain.xyz"]}'
