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

variable "storage_access_key" {
  description = "Ключ доступа к Object Storage"
  type        = string
  sensitive   = true
}

variable "storage_secret_key" {
  description = "Секретный ключ Object Storage"
  type        = string
  sensitive   = true
}

variable "vector_store_ids" {
  description = "ID поисковых индексов из Yandex AI Studio (через запятую)"
  type        = string
}
