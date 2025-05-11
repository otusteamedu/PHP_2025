<?php
/**
 * Класс для отображения данных
 */
class View 
{
    /**
     * Формирует успешный ответ
     * 
     * @param string $string Проверенная строка
     * @return string HTML для отображения
     */
    public function renderSuccess($string) 
    {
        return '<code>' . htmlspecialchars($string) . '</code> - right syntax';
    }
    
    /**
     * Формирует сообщение об ошибке
     * 
     * @param string $message Текст ошибки
     * @return string HTML для отображения
     */
    public function renderError($message) 
    {
        return htmlspecialchars($message);
    }
}
