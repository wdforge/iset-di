<?php

namespace Iset\Di;

use Iset\Di\Middleware\Decorator;
use Iset\Di\Traits\CreateByFactory;
use Iset\Di\Traits\CreateClonedTrait;
use Iset\Di\Traits\CreateContainedTrait;
use Iset\Di\Traits\CreateSimpleTrait;
use Iset\Di\Traits\MiddlewareTrait;
use Iset\Utils\TreeContainer;

use Iset\Utils\IInitial;
use Iset\Utils\IParams;
use Psr\Container\ContainerInterface as PsrContainerInterface;

/**
 * Class Manager
 * @package Iset\Di
 */
class Manager implements IInitial, PsrContainerInterface
{
  use TreeContainer, CreateClonedTrait, CreateSimpleTrait,
    CreateContainedTrait, MiddlewareTrait, CreateByFactory;

  /**
   * @var IParams
   */
  private $_params;

  /**
   * @var array
   */
  private $_instances = [];


  /**
   * @param IParams $params
   */
  public function init(IParams $params): self
  {
    $this->_params = $params;
    $this->_factories = $params->get('application/di/factories', []);
    $this->_containers = $params->get('application/di/containers', []);
    $this->_middlewares = $params->get('application/di/middlewares', []);

    return $this;
  }

  /**
   * Создание объекта и помещение в хранилище
   *
   * @param $class
   * @param IParams $params
   * @param null $name
   * @return mixed
   */
  public function createInstance($class, IParams $params, $name = null): ?mixed
  {
    if (!$class) {
      return null;
    }

    $instance = null;

    if (!$instance) {
      $instance = $this->createObject($class, $params);
    }

    if ($name) {
      $this->set($name, $instance);
    }

    return $instance;
  }

  /**
   * Универсальное создание объекта
   *
   * @param type $class
   * @param type $params
   * @return type
   */
  protected function createObject($class, $params = [])
  {
    /**
     * Для Декоратора генерация объекта происходит через клонирование
     */
    if ($this->hasCloned($class) && is_subclass_of($class, Decorator::class)) {
      $instance = $this->createCloned($class, $params);
    }

    if (!isset($instance) && isset($this->_factories[$class])) {
      $instance = $this->createFromFactory($class, $params);
    }

    if (!isset($instance)) {
      $instance = $this->createSimple($class, $params);
    }

    /**
     * Помещение объекта в контейнеры
     */
    if ($instance) {
      $instance = $this->applyContainers($instance);
    }

    if (!$this->hasCloned($class) && is_subclass_of($class, Decorator::class)) {
      $this->setCloned($class, $instance);
    }

    return $instance;
  }

  /**
   * Получение значения по ключу
   *
   * @param type $name
   * @param type $default
   *
   * @return type
   */
  public function get($name, $default = null)
  {
    if(class_exists($name)) {
      return $this->createObject($name, $this->_params);
    }

    $result = $this->getElementFromPatch($name, $this->_instances);
    return isset($result) ? $result : $default;
  }

  /**
   * Установка значения по ключу
   *
   * @param type $name
   * @param type $value
   * @return \Iset\Di\Manager
   */
  public function set($name, $value)
  {
    $this->setElementToPatch($name, $this->_instances, $value);

    return $this;
  }

  /**
   * Проверка наличия значения
   *
   * @inheritDoc
   */
  public function has($id)
  {
    return $this->get($id) ? true : false;
  }
}
