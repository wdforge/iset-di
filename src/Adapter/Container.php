<?php

namespace Iset\Di\Factory;

use Iset\Di\Container\AbstractContainer;

/**
 * Class Container
 * упаковка объекта в контейнер с созданием контейнера
 *
 * @package Iset\Create\Factory
 */
class Container extends AbstractContainer
{
  /**
   * @var array
   */
  protected $_settings = [];

  /**
   * @param array $params
   */
  public function init($params = [])
  {
    $this->_settings = $params;
    $this->initInstance();
    return $this;
  }


  public function push($object, $params = [])
  {
    $this->_instanceObject = $object;
    return $this;
  }

  /**
   * @param $class
   * @param array $params
   * @return mixed
   */
  public function create($class, $params = [])
  {
    $resultObject = null;

    if (isset($this->_settings) && is_array($this->_settings)) {

      foreach ($this->_settings as $classObject => $classContainer) {

        if ($classObject == $class) {
          $object = $this->create($classObject);
          $container = $this->create($classContainer, [$object]);
        }
      }
    }

    return $container;
  }

}