terraform {
  required_providers {
    yandex = {
      source  = "yandex-cloud/yandex"
      version = "~>0.146.0"
    }
  }
}

provider "yandex" {
  token     = var.yc_token
  cloud_id  = var.cloud_id
  folder_id = var.folder_id
  zone      = var.zone
}

resource "random_string" "suffix" {
  length  = 4
  special = false
  upper   = false
}

resource "yandex_storage_bucket" "documents" {
  access_key = var.storage_access_key
  secret_key = var.storage_secret_key
  bucket     = "mkd-chatbot-docs-${random_string.suffix.result}"
  acl        = "private"

  anonymous_access_flags {
    read = true
  }

  tags = {
    project     = "mkd-chatbot"
    environment = "production"
  }
}

resource "yandex_iam_service_account" "sa" {
  name        = "mkd-chatbot-sa"
  description = "Сервисный аккаунт для чат-бота МКД"
}

resource "yandex_iam_service_account_key" "sa_key" {
  service_account_id = yandex_iam_service_account.sa.id
  description        = "Ключ авторизации для чат-бота МКД"
  key_algorithm      = "RSA_2048"
  format             = "PEM_FILE"
}

# Вызов Cloud Function
resource "yandex_resourcemanager_folder_iam_member" "sa-invoker" {
  folder_id = var.folder_id
  role      = "functions.functionInvoker"
  member    = "serviceAccount:${yandex_iam_service_account.sa.id}"
}

# Доступ к языковым моделям YandexGPT
resource "yandex_resourcemanager_folder_iam_member" "sa-ai" {
  folder_id = var.folder_id
  role      = "ai.languageModels.user"
  member    = "serviceAccount:${yandex_iam_service_account.sa.id}"
}

# Загрузка документов в S3-бакет
resource "yandex_resourcemanager_folder_iam_member" "sa-storage-uploader" {
  folder_id = var.folder_id
  role      = "storage.uploader"
  member    = "serviceAccount:${yandex_iam_service_account.sa.id}"
}

# Чтение документов из S3-бакета (для Cloud Function)
resource "yandex_resourcemanager_folder_iam_member" "sa-storage-viewer" {
  folder_id = var.folder_id
  role      = "storage.viewer"
  member    = "serviceAccount:${yandex_iam_service_account.sa.id}"
}

data "archive_file" "rag_search_zip" {
  type        = "zip"
  output_path = "${path.module}/rag-search-function.zip"
  source_dir  = "${path.module}/functions/rag-search"
}

resource "yandex_function" "rag_search" {
  name               = "mkd-rag-search"
  description        = "RAG-функция поиска по документам ЖКХ через YandexGPT + File Search Tool"
  runtime            = "php82"
  entrypoint         = "index.main"
  memory             = 256
  execution_timeout  = "30"
  user_hash          = data.archive_file.rag_search_zip.output_md5
  service_account_id = yandex_iam_service_account.sa.id
  folder_id          = var.folder_id

  content {
    zip_filename = data.archive_file.rag_search_zip.output_path
  }

  environment = {
    YANDEX_FOLDER_ID = var.folder_id
    VECTOR_STORE_IDS = var.vector_store_ids
  }

  labels = {
    project     = "mkd-chatbot"
    environment = "production"
  }
}
