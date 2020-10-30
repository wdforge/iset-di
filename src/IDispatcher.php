<?php

namespace Iset\Di;


interface IDispatcher
{

  /**
   *
   * @param $class
   * @param array $params
   * @param null $name
   * @param bool $is_shared
   * @return null
   */
  public function createInstance($class, $params = [], $name = null, $is_shared = false);

}