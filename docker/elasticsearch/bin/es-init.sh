#!/bin/bash
set -euo pipefail

CONFIG_DIR="${PWD}/config"
CERT_DIR="${CONFIG_DIR}/certs"
CA_CERT_DIR="${CERT_DIR}/ca"

CA_CERT_CRT="${CA_CERT_DIR}/ca.crt"
CA_CERT_KEY="${CA_CERT_DIR}/ca.key"
HTTP_CERT="${CERT_DIR}/http.p12"
TRANSPORT_CERT="${CERT_DIR}/transport.p12"

ELASTIC_KEYSTORE="${CONFIG_DIR}/elasticsearch.keystore"
ELASTIC_KEYSTORE_MOUNT_DIR="${CONFIG_DIR}/keystore"

if [ ! -d "${CA_CERT_DIR}" ]; then
  bin/elasticsearch-certutil ca --pem --out "${CERT_DIR}/ca.zip" --pass ""
  unzip -q "${CERT_DIR}/ca.zip" -d "${CERT_DIR}" && rm "${CERT_DIR}/ca.zip"
fi

if [ ! -f "${HTTP_CERT}" ]; then
  bin/elasticsearch-certutil cert \
    --ca-cert "${CA_CERT_CRT}" \
    --ca-key "${CA_CERT_KEY}" \
    --name "${ELASTIC_NODE_NAME}" \
    --dns localhost,"${ELASTIC_HOST}" \
    --out "${HTTP_CERT}" \
    --pass "${ELASTIC_HTTP_CERT_PASSWORD}"
fi

if [ ! -f "${TRANSPORT_CERT}" ]; then
  bin/elasticsearch-certutil cert \
    --ca-cert "${CA_CERT_CRT}" \
    --ca-key "${CA_CERT_KEY}" \
    --name "${ELASTIC_NODE_NAME}" \
    --dns localhost,"${ELASTIC_HOST}" \
    --out "${TRANSPORT_CERT}" \
    --pass "${ELASTIC_TRANSPORT_CERT_PASSWORD}"
fi

if [ ! -f "${ELASTIC_KEYSTORE}" ]; then
  bin/elasticsearch-keystore create
  echo "${ELASTIC_PASSWORD}" | bin/elasticsearch-keystore add --stdin bootstrap.password
  echo "${ELASTIC_HTTP_CERT_PASSWORD}" | \
    bin/elasticsearch-keystore add --stdin xpack.security.http.ssl.keystore.secure_password
  echo "${ELASTIC_TRANSPORT_CERT_PASSWORD}" | \
    bin/elasticsearch-keystore add --stdin xpack.security.transport.ssl.keystore.secure_password
  echo "${ELASTIC_TRANSPORT_CERT_PASSWORD}" | \
    bin/elasticsearch-keystore add --stdin xpack.security.transport.ssl.truststore.secure_password
  bin/elasticsearch-keystore list
  cp "${ELASTIC_KEYSTORE}" "${ELASTIC_KEYSTORE_MOUNT_DIR}"
fi

# Флаг того, что сертификаты и хранилище ключей существуют/создались
touch /tmp/elasticsearch_init_complete

exec tail -f /dev/null
