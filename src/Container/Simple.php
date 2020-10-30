<?php

namespace Iset\Di\Container;
/**
 * Класс реализующий базовый функционал контейнера объекта
 */

/**
 * Class Simple
 * @package Iset\Di\Container
 */
class Simple
{
  /**
   * @var object
   */
  protected $_instance;

  /**
   * Simple constructor.
   * @param $object
   */
  public function __construct($object)
  {
    // конструирование объекта по пабору параметров
    $this->_instance = $object;
  }

  /**
   * @param $name
   * @return bool
   */
  public function __isset($name)
  {
    return isset($this->getInstance()->{$name});
  }

  /**
   * @return mixed
   */
  public function getInstance()
  {
    return $this->_instance;
  }

  /**
   * @param $name
   * @return mixed
   */
  public function __get($name)
  {
    return $this->getInstance()->{$name};
  }

  /**
   * @param $name
   * @param $value
   */
  public function __set($name, $value)
  {
    $this->getInstance()->{$name} = $value;
  }

  /**
   * @param $name
   */
  public function __unset($name)
  {
    unset($this->getInstance()->{$name});
  }
}
