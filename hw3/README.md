# PHP_2025

### 🚀 Быстрый старт

## Установка

composer require 

## Класс для работы со URL строкой 

```
class UrlHelper
{
    /**
     * Метод для проверки http протокола 
     */
    public function isSecure(string $url): bool
    {
        $parsedUrl = parse_url($url);
        return isset($parsedUrl['scheme']) && $parsedUrl['scheme'] === 'https';
    }
}
```
