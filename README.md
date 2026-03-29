# FastFood API

Интернет-ресторан фаст-фуда с REST API.

## Функционал

- Создание заказов с несколькими товарами
- Добавки к товарам дополнительных ингридиентов (сыр, салат, лук, перец, помидор)
- Автоматическая обработка заказов через воркер
- История изменения статусов заказа
- Хранение данных в PostgreSQL

## Запуск

docker compose up -d

Сервисы:

- **API**: http://localhost
- **pgAdmin**: http://localhost:5050 (admin@admin.com / admin)

## API

### Информация об API

get http://localhost/

### Меню ресторана

get http://localhost/menu

### Создать заказ

post http://localhost/order \

**Один товар:**

```json
{ "product_type": "burger", "additions": ["cheese"] }
```

**Несколько товаров:**

```json
{
  "items": [
    { "product_type": "burger", "additions": ["cheese", "lettuce"] },
    { "product_type": "hotdog", "additions": ["onion"] }
  ]
}
```

### Получить информацию о заказе

get http://localhost/order/ORD-12345678

### Получить историю изменения статусов заказа

get http://localhost/order/ORD-12345678/history

### Отменить заказ

post http://localhost/order/ORD-12345678/cancel

Отмена доступна только для статусов: created, preparing, cooking

### Логика проверки качества

При переходе из `quality_check` с вероятностью 5% заказ не проходит проверку:

- В историю добавляется запись о провале проверки
- Заказ возвращается на статус - подготовка ингредиентов
- Цикл приготовления начинается заново

Воркер автоматически меняет статусы с интервалом 10 секунд.
