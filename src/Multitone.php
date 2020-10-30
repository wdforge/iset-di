<?php

namespace Iset\Di;

/**
 * Class Multitone
 * @package Iset\Di
 */
class Multitone
{
  /**
   * @var object
   */
  protected static $_default;
  /**
   * @var array
   */
  protected static $_instances;

  /**
   * @param string $key
   */
  private function createInstance(scalar $key)
  {
    if (!static::getInstance($key)) {
      static::$_instances[$key] = new static;
    }
  }

  /**
   * @param string $key
   * @return mixed
   */
  public static function getInstance(scalar $key)
  {
    return static::$_instances[$key];
  }

}
