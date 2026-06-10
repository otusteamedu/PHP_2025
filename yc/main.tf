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

resource "yandex_iam_service_account" "sa" {
  name        = "mkd-chatbot-sa"
  description = "Сервисный аккаунт для чат-бота МКД"
}

resource "yandex_resourcemanager_folder_iam_member" "sa-invoker" {
  folder_id = var.folder_id
  role      = "functions.functionInvoker"
  member    = "serviceAccount:${yandex_iam_service_account.sa.id}"
}

resource "yandex_resourcemanager_folder_iam_member" "sa-ai" {
  folder_id = var.folder_id
  role     = "ai.languageModels.user"
  member   = "serviceAccount:${yandex_iam_service_account.sa.id}"
}

resource "yandex_resourcemanager_folder_iam_member" "sa-assistant-editor" {
  folder_id = var.folder_id
  role = "ai.assistants.editor"
  member = "serviceAccount:${yandex_iam_service_account.sa.id}"
}

resource "yandex_lockbox_secret" "rag_secrets" {
  name        = "mkd-rag-search-secrets"
  description = "Секреты для Cloud Function mkd-rag-search"
  folder_id   = var.folder_id
}

resource "yandex_lockbox_secret_version" "rag_secrets_version" {
  secret_id = yandex_lockbox_secret.rag_secrets.id

  entries {
    key        = "YANDEX_API_KEY"
    text_value = var.yandex_api_key
  }

  entries {
    key        = "WEBHOOK_API_KEY"
    text_value = var.webhook_api_key
  }
}

resource "yandex_resourcemanager_folder_iam_member" "sa-lockbox-viewer" {
  folder_id = var.folder_id
  role     = "lockbox.payloadViewer"
  member   = "serviceAccount:${yandex_iam_service_account.sa.id}"
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
  memory             = 128
  execution_timeout  = "30"
  user_hash          = data.archive_file.rag_search_zip.output_md5
  service_account_id = yandex_iam_service_account.sa.id
  folder_id          = var.folder_id

  content {
    zip_filename = data.archive_file.rag_search_zip.output_path
  }

  environment = {
    YANDEX_FOLDER_ID  = var.folder_id
    VECTOR_STORE_IDS  = var.vector_store_ids
    YANDEX_INSTRUCTIONS = var.yandex_instructions
  }

  secrets {
    id                   = yandex_lockbox_secret.rag_secrets.id
    version_id           = yandex_lockbox_secret_version.rag_secrets_version.id
    key                  = "YANDEX_API_KEY"
    environment_variable = "YANDEX_API_KEY"
  }

  secrets {
    id                   = yandex_lockbox_secret.rag_secrets.id
    version_id           = yandex_lockbox_secret_version.rag_secrets_version.id
    key                  = "WEBHOOK_API_KEY"
    environment_variable = "WEBHOOK_API_KEY"
  }

  labels = {
    project = "mkd-chatbot"
    environment = "production"
  }

}
