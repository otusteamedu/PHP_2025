# Сценарии проверки

## Request 1 - позитивный сценарий (email)
> fetch 503 → retry → 200 (задержка 15 с), email доставлен → **completed**

```text
[*] Waiting for messages on 'notifications.email'. CTRL+C to exit.
[RECEIVED] (delivery 1) | Task: 1 | Attempt: 0/3 | Channel: email | Recipient: test@example.com | Retry-age: 0.0 s
[REJECT] [OrderDataProvidingException] Сервис заказов временно недоступен. | Task: 1 | Attempt: 1/3 | Transitions: pending → fetching → retrying | Time: 0.0 s | Retry-age: 0.0 s
[RECEIVED] (delivery 2) | Task: 1 | Attempt: 1/3 | Channel: email | Recipient: test@example.com | Retry-age: 30.8 s
[ACK] Task: 1 | Attempt: 2/3 | Transitions: retrying → fetching → sending → completed | Time: 15.1 s | Retry-age: 30.8 s
```

## Request 2 - не удалось получить данные по заказу:
> fetch 503 на каждой попытке → retry ×3 → drop → **failed**

```text
[*] Waiting for messages on 'notifications.sms'. CTRL+C to exit.
[RECEIVED] (delivery 1) | Task: 2 | Attempt: 0/3 | Channel: sms | Recipient: +79269788807 | Retry-age: 0.0 s
[REJECT] [OrderDataProvidingException] Сервис заказов временно недоступен. | Task: 2 | Attempt: 1/3 | Transitions: pending → fetching → retrying | Time: 0.0 s | Retry-age: 0.0 s
[RECEIVED] (delivery 2) | Task: 2 | Attempt: 1/3 | Channel: sms | Recipient: +79269788807 | Retry-age: 30.5 s
[REJECT] [OrderDataProvidingException] Сервис заказов временно недоступен. | Task: 2 | Attempt: 2/3 | Transitions: retrying → fetching → retrying | Time: 0.0 s | Retry-age: 30.5 s
[RECEIVED] (delivery 3) | Task: 2 | Attempt: 2/3 | Channel: sms | Recipient: +79269788807 | Retry-age: 60.5 s
[REJECT] [OrderDataProvidingException] Сервис заказов временно недоступен. | Task: 2 | Attempt: 3/3 | Transitions: retrying → fetching → retrying | Time: 0.0 s | Retry-age: 60.5 s
[RECEIVED] (delivery 4) | Task: 2 | Attempt: 3/3 | Channel: sms | Recipient: +79269788807 | Retry-age: 90.5 s
[DROP] [MaxAttemptsExceededException] Задача № 2 исчерпала лимит попыток (3). | Task: 2 | Attempt: 3/3 | Transitions: retrying → failed | Time: 0.0 s | Retry-age: 90.5 s
```

## Request 3 - SMS с задержкой:
> fetch 200 (задержка 15 с), доставка на +40700000003 (пауза 3 с) → **completed**

```text
[*] Waiting for messages on 'notifications.sms'. CTRL+C to exit.
[RECEIVED] (delivery 1) | Task: 3 | Attempt: 0/3 | Channel: sms | Recipient: +40700000003 | Retry-age: 0.0 s
[ACK] Task: 3 | Attempt: 1/3 | Transitions: pending → fetching → sending → completed | Time: 18.0 s | Retry-age: 0.0 s
```

## Request 4 - SMS с таймаутом:
> fetch 200 (задержка 15 с), доставка на +40700000006 → timeout 30 с, гейтвейт 504 → retry ×3 → drop → **failed**

```text
[*] Waiting for messages on 'notifications.sms'. CTRL+C to exit.
[RECEIVED] (delivery 1) | Task: 4 | Attempt: 0/3 | Channel: sms | Recipient: +40700000006 | Retry-age: 0.0 s
[REJECT] [NotificationSendingException] Не удалось отправить SMS-уведомление: SMS gateway returned status code: 504 | Task: 4 | Attempt: 1/3 | Transitions: pending → fetching → sending → retrying | Time: 45.1 s | Retry-age: 0.0 s
[RECEIVED] (delivery 2) | Task: 4 | Attempt: 1/3 | Channel: sms | Recipient: +40700000006 | Retry-age: 30.1 s
[REJECT] [NotificationSendingException] Не удалось отправить SMS-уведомление: SMS gateway returned status code: 504 | Task: 4 | Attempt: 2/3 | Transitions: retrying → fetching → sending → retrying | Time: 45.0 s | Retry-age: 30.1 s
[RECEIVED] (delivery 3) | Task: 4 | Attempt: 2/3 | Channel: sms | Recipient: +40700000006 | Retry-age: 105.2 s
[REJECT] [NotificationSendingException] Не удалось отправить SMS-уведомление: SMS gateway returned status code: 504 | Task: 4 | Attempt: 3/3 | Transitions: retrying → fetching → sending → retrying | Time: 45.0 s | Retry-age: 105.2 s
[RECEIVED] (delivery 4) | Task: 4 | Attempt: 3/3 | Channel: sms | Recipient: +40700000006 | Retry-age: 180.2 s
[DROP] [MaxAttemptsExceededException] Задача № 4 исчерпала лимит попыток (3). | Task: 4 | Attempt: 3/3 | Transitions: retrying → failed | Time: 0.0 s | Retry-age: 180.2 s
```

## Request 5 - SMS на обычный РФ-номер:
> fetch 503 → retry → 200 (задержка 15 с), SMS доставлен → **completed**

```text
[*] Waiting for messages on 'notifications.sms'. CTRL+C to exit.
[RECEIVED] (delivery 1) | Task: 5 | Attempt: 0/3 | Channel: sms | Recipient: +79269788807 | Retry-age: 0.0 s
[REJECT] [OrderDataProvidingException] Сервис заказов временно недоступен. | Task: 5 | Attempt: 1/3 | Transitions: pending → fetching → retrying | Time: 0.0 s | Retry-age: 0.0 s
[RECEIVED] (delivery 2) | Task: 5 | Attempt: 1/3 | Channel: sms | Recipient: +79269788807 | Retry-age: 30.2 s
[ACK] Task: 5 | Attempt: 2/3 | Transitions: retrying → fetching → sending → completed | Time: 15.0 s | Retry-age: 30.2 s
```

## Request 6  негативный сценарий валидации:
> orderId строкой, orderEvent не поддерживается → **422** с маппингом `fields`

```text
HTTP/1.1 422 
Server: nginx/1.28.3
Date: Sun, 01 Nov 2026 19:05:44 GMT
Content-Type: application/json; charset=utf-8
Transfer-Encoding: chunked
Connection: keep-alive
X-Powered-By: PHP/8.5.9

{
  "success": false,
  "error": "Ошибки валидации.",
  "fields": {
    "orderId": [
      "Неверный тип поля. Ожидается int, получено: string"
    ],
    "orderEvent": [
      "Недопустимое событие заказа: in-progress. Допустимые события: created, paid, shipped"
    ]
  }
}
```

---