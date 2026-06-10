#!/bin/bash
# Деплой инфраструктуры RAG-search через Terraform
# Terraform управляет ресурсами: SA, Cloud Function, IAM-роли, Lockbox
# ZIP-архив функции собирается автоматически через data.archive_file.rag_search_zip
# Публичный доступ настраивается через yc CLI после terraform apply
#
# Использование: ./scripts/deploy.sh [аргументы-terraform-apply]
#
# Предварительные требования:
# - terraform.tfvars заполнен реальными значениями
# - yc CLI настроен и доступен в $PATH

set -euo pipefail

SCRIPT_DIR="$(cd "$(dirname "$0")" && pwd)"
TF_DIR="$SCRIPT_DIR/.."

cd "$TF_DIR"

echo "=== Деплой RAG-search через Terraform ==="
echo ""

echo "1/3: Инициализация Terraform..."
terraform init

echo ""
echo "2/3: Планирование изменений..."
terraform plan -out=tfplan "$@"

echo ""
echo "3/3: Применение изменений..."
terraform apply tfplan
rm -f tfplan

echo ""
echo "4/4: Настройка публичного доступа к функции..."
FUNCTION_NAME=$(terraform output -raw function_id 2>/dev/null || echo "")
FOLDER_ID=$(grep -oP 'folder_id\s*=\s*"\K[^"]+' terraform.tfvars 2>/dev/null || echo "")

if [ -n "$FUNCTION_NAME" ] && [ -n "$FOLDER_ID" ]; then
  yc serverless function allow-unauthenticated-invoke mkd-rag-search --folder-id "$FOLDER_ID" && \
    echo "+++ Публичный доступ настроен" || \
    echo "--- Не удалось настроить публичный доступ. Выполните вручную:"
    echo "   yc serverless function allow-unauthenticated-invoke mkd-rag-search --folder-id $FOLDER_ID"
else
  echo "---️Не удалось определить параметры для yc CLI. Настройте публичный доступ вручную:"
  echo "   yc serverless function allow-unauthenticated-invoke mkd-rag-search --folder-id <folder_id>"
fi

echo ""
echo "=== Деплой завершён! ==="
echo ""
echo "Результаты:"
terraform output

echo ""
echo "Следующие шаги:"
echo " 1. Загрузка документов в Search Index: python3 scripts/upload-to-search-index.py"
echo "  (ID индекса автоматически запишется в terraform.tfvars)"
echo " 2. Повторите деплой: ./scripts/deploy.sh"
echo ""
echo "Тестирование:"
echo " FUNCTION_URL=\$(terraform output -raw function_url)"
echo " curl -s -X POST \"\$FUNCTION_URL\" -H 'Content-Type: application/json' -H 'X-API-Key: <webhook_key>' -d '{\"question\": \"test\"}'"
echo ""
