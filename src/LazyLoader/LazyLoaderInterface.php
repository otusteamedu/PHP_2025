<?php

namespace Igor\Test\LazyLoader;

/**
 * Интерфейс для ленивой загрузки связанных данных
 */
interface LazyLoaderInterface
{
    /**
     * Загрузка данных по идентификатору
     *
     * @param int $id Идентификатор для загрузки
     * @return mixed Загруженный объект или null
     */
    public function load(int $id);
}
