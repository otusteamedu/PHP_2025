В папке находиться резервная копия базы данных <b>dump.sql</b>

#### Разворачиваем базу на сервере: 

для Docker 
*  копируем файл **dump.sql** в контейнер, 
*  выполняем команду psql -d your-database -f dump.sql -U pg-user


## Структура таблиц построена по принципу EAV 

### В базе данных таблицы: 

*  films(id, title), 
*  attributes(id,title), 
*  attributes_type(id,title), 
*  values(film_id, type_id, attribute_id, value ) 


### Добавлены view:
*  service_dates - выборка служебных данных
*  about_films - выборка маркетинговых данных в виде таблицы
*  about_films_json - выборка маркетинговых данных в виде JSON
*  workd - выборка служебных данных (c подготовкой запроса - тест)