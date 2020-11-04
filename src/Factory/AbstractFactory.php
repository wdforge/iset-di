<?php

namespace Iset\Di\Factory;

use Generic\Exception\LoggedException;
use Iset\Di\CachedInstance;
use Iset\Di\IDepended;
use Iset\Di\Middleware\Decorator;
use Iset\Di\Traits\AddServiceManagerTrait;
use Iset\Di\Traits\CreateClonedTrait;

/**
 * Class AbstractFactory
 * @package Iset\Create\Factory
 */
abstract class AbstractFactory extends CachedInstance implements \Iset\Di\IFactory
{
  use CreateClonedTrait, AddServiceManagerTrait;

  /**
   * @var \Iset\Di\Manager
   */
  public $_diManager;

  /**
   *
   * @param $class
   * @param array $params
   * @return object
   * @throws \ReflectionException
   */
  public function create($class, array $params = [])
  {
    $reflection = null;

    if (!$this->has($class)) {
      $reflection = new \ReflectionClass($class);
    } else {
      $reflection = $this->get($class);
    }

    $resultObject = $reflection->newInstanceArgs($params);

    return $resultObject;
  }

  abstract public function createInstance(\Iset\Utils\IParams $params, $class = null);

  /**
   * @param \Iset\Di\Manager $diManager
   */
  public function setServiceManager(\Iset\Di\Manager $diManager)
  {
    $this->_diManager = $diManager;
  }
}