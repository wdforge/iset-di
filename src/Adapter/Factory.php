<?php

namespace Iset\Di\Adapter;

use Iset\Di\Factory\AbstractFactory;

/**
 * Class Factory
 * создание объекта по классу
 * @package Iset\Create\Adapter
 */
class Factory extends AbstractFactory
{
  /**
   * @var array
   */
  protected $_settings = [];

  /**
   * @var array
   */
  protected static $_useInstances = [];

  /**
   * @param array $params
   */
  public function init($params = [])
  {
    $this->_settings = $params;
    $this->initInstance();
    return $this;
  }

  /**
   * @param $class
   * @param array $params
   * @return null|object
   * @throws \ReflectionException
   */
  public function create($class, $params = [])
  {
    $resultObject = null;

    if (isset($this->_settings) && is_array($this->_settings)) {

      foreach ($this->_settings as $classObject => $classFactory) {

        if ($classObject == $class) {

          $objectFactory = null;

          if ($this->isCreated($classFactory)) {
            $objectFactory = $this->getInstance($classFactory);
          } else {
            $objectFactory = parent::create($classFactory);

            if (!$objectFactory->isSubclassOf(AbstractFactory::class)) {
              throw new Exception(
                sprintf('Factory "%s" is not sub class of "%s"', $classFactory, AbstractFactory::class)
              );
            }

            self::$_useInstances[$classFactory] = $objectFactory;
          }

          $resultObject = $objectFactory->create($class, $params);
        }
      }
    }

    return $resultObject;
  }

}