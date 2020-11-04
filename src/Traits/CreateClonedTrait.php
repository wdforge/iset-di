<?php

namespace Iset\Di\Traits;

use Iset\Di\Middleware\Decorator;

trait CreateClonedTrait
{
  /**
   * unique valued storage for based (empted) instances
   */
  private $_clonedInstances = [];

  /**
   * Проверка на образец
   *
   * @param $class
   * @return bool
   */
  protected function hasCloned($class)
  {
    return isset($this->_clonedInstances[$class]);
  }

  /**
   * Метод копирования объекта и присвоения свойств
   *
   * @param string $class
   * @param array $params
   * @return object
   */
  protected function createCloned(string $class, $params = []): ?object
  {
    if ($this->hasCloned($class)) {
      $instance = clone $this->_clonedInstances[$class];
      $this->setProperties($instance, $params);
      return $instance;
    }

    return null;
  }

  /**
   * Запись образца
   *
   * @param $instance
   */
  protected function setCloned(object $instance)
  {
    if (isset($instance)) {
      $this->_clonedInstances[get_class($instance)] = $instance;
    }
  }

  /**
   * Присвоение свойств объекта
   *
   * @param Decorator $instance
   * @param array $params
   */
  protected function setProperties(Decorator $instance, array $params)
  {
    $properties = $this->getProperties($instance);
    foreach ($params as $key => $value) {

      if (isset($properties['public'][$key])) {
        $instance->$key = $value;
      }

      if (isset($properties['protected'][$key])) {
        $instance->setProtectedProperty($key, $value);
      }

      if (isset($properties['private'][$key])) {
        $instance->setPrivateProperty($key, $value);
      }
    }

    if (isset($properties['static'][$key])) {
      $instance->setStaticProperty($key, $value);
    }
  }

  /**
   * Получение свойств объекта
   *
   * @param Decorator $instance
   * @return array
   */
  protected function getProperties(Decorator $instance)
  {
    $reflect = new \ReflectionClass($instance);
    return [
      'private' => $reflect->getProperties(\ReflectionProperty::IS_PRIVATE),
      'public' => $reflect->getProperties(\ReflectionProperty::IS_PUBLIC),
      'protected' => $reflect->getProperties(\ReflectionProperty::IS_PROTECTED),
      'static' => $reflect->getStaticProperties()
    ];
  }

}