<?php

namespace Iset\Di\Factory;

use Generic\Exception\LoggedException;
use Iset\Di\CachedInstance;
use Iset\Di\IDepended;
use Iset\Di\Middleware\Decorator;

/**
 * Class AbstractFactory
 * @package Iset\Create\Factory
 */
abstract class AbstractFactory extends CachedInstance implements \Iset\Di\IFactory
{
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

    if ($resultObject && in_array(IDepended::class, class_implements($class))) {
      $resultObject->_diManager = $this->_diManager;
      if (in_array(Decorator::class, class_parents($class))) {
        $resultObject->setMethod('getServiceManager', function () {
          return $this->_diManager;
        });
      } else {
        throw new \Exception('Object must be parent by Decorator class');
      }
    }
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