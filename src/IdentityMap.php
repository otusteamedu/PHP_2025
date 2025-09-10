<?php

namespace App;

class IdentityMap
{
  private static array $objects = [];

  public static function setObject(int $id, string $tablename, object $object): void
  {
      $key = self::generateKey($id, $tablename);
      static::$objects[$key] = $object;
  }

  public static function getObject(int $id, string $tablename)
  {
      $key = self::generateKey($id, $tablename);
      return static::$objects[$key] ?? null;
  }

  public static function deleteObject(int $id, string $tablename): void
  {
      $key = self::generateKey($id, $tablename);
      unset(static::$objects[$key]);
  }

  private static function generateKey(int $id, string $tablename): string
  {
      return $id . '_' . $tablename;
  }

}