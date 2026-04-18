INSERT INTO attributes (attribute_type_id, code, name, data_type, description)
SELECT at.id, x.code, x.name, x.data_type, x.description
FROM attribute_types at
JOIN (
    VALUES
        ('review', 'critics_review', 'Рецензия критиков', 'text', 'Текст рецензии от критиков'),
        ('review', 'academy_review', 'Отзыв киноакадемии', 'text', 'Текст отзыва киноакадемии'),

        ('award', 'oscar', 'Оскар', 'boolean', 'Премия Оскар'),
        ('award', 'nika', 'Ника', 'boolean', 'Премия Ника'),

        ('important_date', 'world_premiere', 'Мировая премьера', 'date', 'Дата мировой премьеры'),
        ('important_date', 'ru_premiere', 'Премьера в РФ', 'date', 'Дата премьеры в РФ'),

        ('service_date', 'ticket_sales_start', 'Дата начала продажи билетов', 'date', 'Дата старта продаж билетов'),
        ('service_date', 'tv_ads_start', 'Запуск рекламы на ТВ', 'date', 'Дата запуска рекламы на ТВ'),

        ('rating', 'imdb_rating', 'Рейтинг IMDb', 'numeric', 'Числовой рейтинг IMDb'),
        ('rating', 'kp_rating', 'Рейтинг Кинопоиска', 'numeric', 'Рейтинг Кинопоиска')
) AS x(type_code, code, name, data_type, description)
ON at.code = x.type_code;
