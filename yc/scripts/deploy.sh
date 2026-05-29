#!/bin/bash
# Деплой инфраструктуры RAG-search через Terraform
# Terraform управляет всеми ресурсами: SA, S3-бакет, Cloud Function, IAM-роли
# ZIP-архив функции собирается автоматически через data.archive_file.rag_search_zip
#
# Использование: ./scripts/deploy.sh [аргументы-terraform-apply]
#
# Предварительные требования:
# - terraform.tfvars заполнен реальными значениями
# - yc CLI настроен (опционально, для шагов после деплоя)

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
echo "=== Деплой завершён! ==="
echo ""
echo "Результаты:"
terraform output

echo ""
echo "Следующие шаги:"
echo "  1. Загрузка документов: BUCKET=\$(terraform output -raw bucket_name) ./scripts/upload-docs.sh"
echo "  2. Создание Vector Store в AI Studio: https://aistudio.yandex.ru/"
echo "  3. Обновите VECTOR_STORE_IDS в terraform.tfvars и повторите деплой"
echo "  4. Разрешить публичный вызов (для тестирования):"
echo "     yc serverless function allow-unauthenticated-invoke \$(terraform output -raw function_id)"
