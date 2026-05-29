#!/bin/bash
# Загрузка документов в бакет Yandex Object Storage
# Использование: BUCKET=mkd-chatbot-docs-XXXX ./scripts/upload-docs.sh

set -euo pipefail

# Убедитесь, что yc CLI доступен в PATH
# Раскомментируйте и укажите правильный путь при необходимости:
# export PATH="$PATH:/home/alex/yandex-cloud/bin"

SCRIPT_DIR="$(cd "$(dirname "$0")" && pwd)"
DOCS_DIR="$SCRIPT_DIR/../docs"

if [ -z "${BUCKET:-}" ]; then
    echo "Ошибка: переменная окружения BUCKET не задана"
    echo "Использование: BUCKET=mkd-chatbot-docs-XXXX $0"
    exit 1
fi

echo "Загрузка документов в бакет $BUCKET..."

# Загрузка документов из раздела законодательства
for file in "$DOCS_DIR"/legislation/*; do
    if [ -f "$file" ]; then
        filename=$(basename "$file")
        key="legislation/$filename"
        echo " Загрузка $key..."
        yc storage s3api put-object --bucket "$BUCKET" --key "$key" --body "$file"
    fi
done

# Загрузка документов из раздела FAQ
for file in "$DOCS_DIR"/faq/*; do
    if [ -f "$file" ]; then
        filename=$(basename "$file")
        key="faq/$filename"
        echo " Загрузка $key..."
        yc storage s3api put-object --bucket "$BUCKET" --key "$key" --body "$file"
    fi
done

# Загрузка контактов
if [ -f "$DOCS_DIR/contacts.json" ]; then
    echo " Загрузка contacts.json..."
    yc storage s3api put-object --bucket "$BUCKET" --key "contacts.json" --body "$DOCS_DIR/contacts.json"
fi

echo ""
echo "Документы загружены в s3://$BUCKET/"
echo ""
echo "Теперь вручную создайте векторное хранилище в Yandex AI Studio:"
echo " 1. Откройте https://aistudio.yandex.ru/"
echo " 2. Создайте Vector Store, указав бакет $BUCKET"
echo " 3. Скопируйте полученный vector_store_id"
echo " 4. Обновите переменную VECTOR_STORE_IDS в Terraform или environment функции"
