output "function_id" {
  description = "ID Cloud Function mkd-rag-search"
  value       = yandex_function.rag_search.id
}

output "function_url" {
  description = "URL для вызова Cloud Function mkd-rag-search"
  value       = "https://functions.yandexcloud.net/${yandex_function.rag_search.id}"
}

output "service_account_id" {
  description = "ID сервисного аккаунта"
  value       = yandex_iam_service_account.sa.id
}

