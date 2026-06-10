variable "yc_token" {
  description = "OAuth-токен Yandex Cloud"
  type        = string
  sensitive   = true
}

variable "cloud_id" {
  description = "ID облака"
  type        = string
}

variable "folder_id" {
  description = "ID папки в Yandex Cloud"
  type        = string
}

variable "zone" {
  description = "Зона доступности"
  type        = string
  default     = "ru-central1-a"
}

variable "vector_store_ids" {
  description = "ID поисковых индексов из Yandex AI Studio (через запятую)"
  type        = string
}

variable "yandex_api_key" {
  description = "API-ключ Yandex AI Studio (scope: yc.ai.foundationModels.execute)"
  type        = string
  sensitive   = true
}

variable "webhook_api_key" {
  description = "API-ключ для авторизации входящих запросов к Cloud Function"
  type        = string
  sensitive   = true
}

variable "yandex_instructions" {
  description = "Системная инструкция для RAG-ассистента"
  type        = string
  default     = "Ты — умный ассистент для жителей многоквартирного дома. Отвечай на вопросы по ЖКХ, используя информацию из подключённых поисковых индексов. Если ответа нет в документах — честно скажи об этом."
}
