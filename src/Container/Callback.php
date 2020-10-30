<?php

namespace Iset\Di\Container;
/**
 * Class Callback
 * @package Iset\Di\Container
 */
class Callback extends Simple
{
  /**
   * перегружаемый метод обработки вызова
   *
   * @return \Closure
   */
  protected function getCallable()
  {
    return (function ($object, $name, $arguments) {
      return call_user_func_array([$object, $name], $arguments);
    });
  }

  /**
   * передача вызова хранимому объекту
   *
   * @param $name
   * @param $arguments
   * @return mixed
   */
  public function __call($name, $arguments)
  {
    $callable = $this->getCallable();
    return $callable($this->getInstance(), $name, $arguments);
  }

}