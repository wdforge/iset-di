<?php

namespace Iset\Di\Middleware;
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
   * @param $name
   * @param $callable
   */
  public function setMethod($name, $callable)
  {
    static::$_methods[$name] = $callable;
    static::$_methods[$name]->bindTo($this);
  }
}
