#!/bin/sh
set -euo pipefail

ES_URL="${ES_URL:-http://elasticsearch:9200}"

: ${ELASTIC_PASSWORD:?"Требуется ELASTIC_PASSWORD"}
: ${KIBANA_PASSWORD:?"Требуется KIBANA_PASSWORD"}

echo "--- Установка пароля для kibana_system (ES_URL: ${ES_URL})..."

max_attempts=60
attempt=1

while [ $attempt -le $max_attempts ]; do
  echo "Попытка $attempt/$max_attempts: Проверка доступности Elasticsearch..."

  if nslookup elasticsearch >/dev/null 2>&1; then
    if response=$(curl -s -k -w " HTTPSTATUS:%{http_code}" "${ES_URL}/" 2>/dev/null) && echo "$response" | grep -q "HTTPSTATUS:"; then
      echo "Elasticsearch отвечает, проверяем аутентификацию..."
      auth_status=$(curl -s -k -o /dev/null -w "%{http_code}" -u "elastic:${ELASTIC_PASSWORD}" "${ES_URL}/_security/_authenticate" 2>/dev/null || echo "000")
      if [ "$auth_status" = "200" ]; then
        echo "Аутентификация успешна. Elasticsearch - готов."
        break
      elif [ "$auth_status" = "401" ]; then
        echo "Аутентификация не удалась с HTTP 401 - ожидание..."
      else
        echo "Попытка аутентификации вернула HTTP $auth_status - ожидание..."
      fi
    else
      echo "Elasticsearch пока не отвечает должным образом - ожидание..."
    fi
  else
    echo "Сервис Elasticsearch пока не разрешается - ожидание..."
  fi

  if [ $attempt -ge $max_attempts ]; then
    echo "Истекло время ожидания готовности безопасности Elasticsearch после $max_attempts попыток."
    exit 1
  fi

  sleep 5
  attempt=$((attempt + 1))
done

echo "Установка пароля для kibana_system..."
response=$(curl -s -k -u "elastic:${ELASTIC_PASSWORD}" \
  -X POST "${ES_URL}/_security/user/kibana_system/_password" \
  -H "Content-Type: application/json" \
  -d "{\"password\":\"${KIBANA_PASSWORD}\"}" \
  -w "\nHTTP_CODE:%{http_code}")

echo "$response"

if echo "$response" | grep -q "HTTP_CODE:200"; then
  echo "Пароль для kibana_system успешно установлен."
else
  echo "Не удалось установить пароль для kibana_system. Код HTTP не 200."
  exit 1
fi

TOKEN_NAME="${TOKEN_NAME:-elastic-token-$(date +%s)}"
echo "--- Создание API-ключа: ${TOKEN_NAME}"

TOKEN_FILE="${TOKEN_FILE:-/tmp/elastic_token_$(date +%s).tmp}"
response=$(curl -s -k -u "elastic:${ELASTIC_PASSWORD}" \
  -X POST "${ES_URL}/_security/api_key" \
  -H "Content-Type: application/json" \
  -d "{ \"name\": \"${TOKEN_NAME}\", \"expiration\": \"1d\" }" \
  -w "\nHTTP_CODE:%{http_code}")

http_code=$(echo "$response" | tail -n 1 | cut -d':' -f2)
token=$(echo "$response" | head -n -1 | jq -r '.encoded // .api_key' 2>/dev/null || echo "")

if [ "$http_code" = "200" ] && [ -n "$token" ] && [ "$token" != "null" ]; then
  echo "API-ключ успешно создан."
else
  echo "Не удалось создать API-ключ. Код HTTP: $http_code"
  exit 1
fi

echo "$token" > "$TOKEN_FILE"
echo "Токен сохранен в: $TOKEN_FILE"

chmod 600 "$TOKEN_FILE"

exit 0

echo '--- Импорт данных в индекс otus-shop...'

curl -s -X POST "http://elasticsearch:9200/_bulk" \
  -H 'Content-Type: application/json' \
  -u "elastic:$ELASTIC_PASSWORD" \
  --data-binary @/tmp/books.json

echo 'Импорт данных завершён!'
