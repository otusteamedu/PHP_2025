# PHP_2025

https://otus.ru/lessons/razrabotchik-php/?utm_source=github&utm_medium=free&utm_campaign=otus

## Домашнее задание к уроку 13. Другие SQL-решения

- реализуем поиск по книжному интернет-магазину с помощью Elasticsearch
- у каждого товара есть название, категория, цена и кол-во остатков на складе
- поиск должен корректно работать с опечатками и русской морфологией
- пример: пользователь ищет все исторические романы дешевле 2000 рублей (и в наличии) по поисковому запросу "рыцОри"
- в результате должны вернуться товары, ранжированные по релевантности
- домашку нужно сдать как консольное PHP-приложение, которое принимает один или несколько параметров командной строки и
  выводит результат в виде текстовой таблички, после чего завершает работу
- JSON с товарами будет приложен к занятию в ЛК
- способ создания индекса и его первоначального заполнения — на ваш выбор


    <li><a href="/create/">Создать индекс в Elasticsearch</a></li>
    <li><a href="/download/">Загрузить данные в Elasticsearch</a></li>
    <li><a href="/search/">Выполняет поиск</a></li>
    <li><a href="/remove/">Удалить индекс в Elasticsearch</a></li>
</ul>

## Работа с Elasticsearch через консоль:
```
cd public/console/
```


### Создать индекс в Elasticsearch:

```
php index.php --action="create"
```
**или:**
```
php index.php --action="create" --index="otus-shop-new"
```


### Загрузить данные в Elasticsearch

```
php index.php --action="download"
```
**или:**
```
php index.php --action="download" --file="/upload/books.json"
```


### Выполняет поиск

```
php index.php --action="search" --title="рыцОри" --price="2000"
```
**или:**
```
php index.php --action="search" --title="рыцОри" --price="2000" --index="otus-shop-new"
```


### Удалить индекс в Elasticsearch

```
php index.php --action="remove"
```
**или:**
```
php index.php --action="remove" --index="otus-shop-new"
```
