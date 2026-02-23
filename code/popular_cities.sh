   #!/bin/bash

   # Проверка на наличие файла
   if [ ! -f cities.txt ]; then
       echo "Ошибка: файл cities.txt не найден."
       exit 1
   fi

   # Обработка файла и вывод 3 наиболее популярных городов
   awk 'NR > 1 {print $3}' cities.txt | sort | uniq -c | sort -nr | head -n 3
   