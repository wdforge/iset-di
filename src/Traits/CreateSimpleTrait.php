<?php

namespace Iset\Di\Traits;

use ReflectionClass;

trait CreateSimpleTrait
{
  /**
   * hash key-valued storage for same objects
   */
  private $_hashLinks = [];

  /**
   * Простое создание объекта (без new)
   *
   * @param string $class
   * @param array|IParams $params
   * @return object
   */
  protected function createSimple(string $class, array $params = [])
  {
    $reflection = new ReflectionClass($class);
    $instance = $reflection->newInstance();
    $this->setProperties($instance, $params);

    $hash = spl_object_hash($instance);

    if (!in_array($hash, array_keys($this->_hashLinks))) {
      $this->_hashLinks[$hash] = $instance;
    } else {
      return $this->_hashLinks[$hash];
    }

    return $instance;
  }

}