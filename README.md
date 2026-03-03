Для запуска программы:
1. `docker compose up -d`
2. `docker compose run --rm composer install`
3. `docker compose exec app php entry_point/app.php data/products.json import` - создать индекс
  
Команда `docker compose exec app php entry_point/app.php reset-index` позволит пересоздать индекс.

Для проверки поиска:
1. `docker compose exec app php entry_point/app.php --category="Исторический роман" --price-to="2000" --stock-from="1" --query="рыцОри" search` - запрос из задания
2. `docker compose exec app php entry_point/app.php --query="Москва" search`
3. `docker compose exec app php entry_point/app.php --category="Исторический роман" search`
4. `docker compose exec app php entry_point/app.php --query="рыцОри" search`
5. `docker compose exec app php entry_point/app.php --query="Москва" --category="Сад и огород" search`
6. `docker compose exec app php entry_point/app.php --category="Сад и огород" --price="1754" search`
7. `docker compose exec app php entry_point/app.php --price-from="1753" --price-to="1755" search`
8. `docker compose exec app php entry_point/app.php --price-from="1753" --price-to="1755" search`
9. `docker compose exec app php entry_point/app.php --price-to="1754" search`
10. `docker compose exec app php entry_point/app.php --price-from="1754" search`
11. `docker compose exec app php entry_point/app.php --shop="Мира" search`