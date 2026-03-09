#!/bin/bash

TIMEOUT=60 # Таймаут ожидания автогенерации сертификатов
INTERVAL=5 # Проверяем каждые 5 секунд
CERTS_DIR="/usr/share/elasticsearch/config/certs"
COPIED_CERTS_DIR="/usr/share/elasticsearch/config/copied-certs"

mkdir -p "$COPIED_CERTS_DIR"
if [ -f "$COPIED_CERTS_DIR/http.p12" ] && \
   [ -f "$COPIED_CERTS_DIR/http_ca.crt" ] && \
   [ -f "$COPIED_CERTS_DIR/transport.p12" ]; then
    echo "Сертификаты были созданы ранее."
    exit 0
fi

for ((i=0; i<TIMEOUT; i+=INTERVAL)); do
  if [ -d "$CERTS_DIR" ] && \
     [ -f "$CERTS_DIR/http.p12" ] && \
     [ -f "$CERTS_DIR/http_ca.crt" ] && \
     [ -f "$CERTS_DIR/transport.p12" ]; then
      echo "Сертификаты сгенерировались, копируем их в подключенный volume."
      cp -r "$CERTS_DIR"/* "$COPIED_CERTS_DIR"/
      exit 0
  fi
  sleep $INTERVAL
done

echo "Превышен таймаут ожидания автогенерации сертификатов ($TIMEOUT сек.)"
exit 1
