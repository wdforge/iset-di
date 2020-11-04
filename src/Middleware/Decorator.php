<?php

namespace Iset\Di\Middleware;
use Iset\Utils\NullObject;

/**
 * Class Decorator
 * @package Iset\Di\Middleware
 */
class Decorator
{
  /**
   * @var array
   */
  protected static $_methods;

  /**
   * @var array
   */
  protected $_properties;

    /**
   * @param $name
   * @return bool
   */
  public function __isset($name)
  {
    return isset($this->_properties[$name]);
  }

  /**
   * @param $name
   * @return mixed
   */
  public function __get($name)
  {
    return isset($this->_properties[$name]) ? 
        $this->_properties[$name]: 
        NullObject::create(sprintf('%s::%s', get_class($this), $name));
  }

  /**
   * @param $name
   * @param $value
   */
  public function __set($name, $value)
  {
    $this->_properties[$name] = $value;
  }

  /**
   * @param $name
   */
  public function __unset($name)
  {
    unset($this->_properties[$name]);
  }

  /**
   * @param string $name
   * @param array $arguments
   * @return mixed
   * @throws \Exception
   */
  public function __call($name, array $arguments)
  {
    if (!empty(static::$_methods[$name]) && is_callable(static::$_methods[$name])) {
      return call_user_func_array(static::$_methods[$name], $arguments);
    }

    throw new \Exception(sprintf('Method not found %s::%s', get_class($this), $name));
  }

  /**
   * Добавление нового метода в класс
   *
   * @param $name
   * @param $callable
   */
  public function setMethod($name, $callable)
  {
    static::$_methods[$name] = $callable;
    static::$_methods[$name]->bindTo($this);
  }

  /**
   * Установка свойств
   *
   * @param string $name
   * @param mixed $value
   */
  public function setProtectedProperty(string $name, $value)
  {
    $this->$name = $value;
  }

  /**
   * Установка свойств
   *
   * @param string $name
   * @param mixed $value
   */
  public function setPrivateProperty(string $name, $value)
  {
    $this->$name = $value;
  }

  /**
   * Установка свойств
   *
   * @param string $name
   * @param mixed $value
   */
  public function setStaticProperty(string $name, $value)
  {
    static::$name = $value;
  }

}
