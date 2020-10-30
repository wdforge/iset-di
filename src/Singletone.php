<?php

namespace Iset\Di;

/**
 * Class Singletone
 * @package Iset\Di
 */
class Singletone
{

  /**
   * @var object
   */
  protected static $_instance;

  /**
   * Singletone constructor.
   */
  public function __construct()
  {
    $this->createInstance();
  }

  /**
   *
   */
  private function createInstance()
  {
    if (!static::getInstance()) {
      static::$_instance = new static;
    }
  }

  /**
   * @return mixed
   */
  public static function getInstance()
  {
    return static::$_instance;
  }

}
