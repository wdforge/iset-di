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
  public function createInstance(string $class, IParams $params, $name = null): ?object
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
   * @param string $class
   * @param array $params
   * @return mixed
   */
  protected function createObject(string $class, $params = []): ?object
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
      if ($params instanceof IParams) {
        $params = $params->toArray();
      }
      $instance = $this->createSimple($class, []);
    }

    if (is_subclass_of($instance, Decorator::class)) {
      $instance->_diManager = $this;

      $getServiceManager = function () {
        return $this;
      };

      $instance->setMethod('getServiceManager', $getServiceManager);
    }

    /**
     * Помещение объекта в контейнеры
     */
    if ($instance) {
      $instance = $this->applyContainers($instance);
    }

    if (!$this->hasCloned($class) && is_subclass_of($instance, Decorator::class)) {
      $this->setCloned($instance);
    }

    return $instance;
  }

  /**
   * Получение значения по ключу
   *
   * @param string $name
   * @param mixed $default
   *
   */
  public function get($name, $default = null)
  {
    if (class_exists($name)) {
      return $this->createObject($name, $this->_params);
    }

    $result = $this->getElementFromPatch($name, $this->_instances);
    return isset($result) ? $result : $default;
  }

  /**
   * Установка значения по ключу
   *
   * @param string $name
   * @param mixed $value
   * @return \Iset\Di\Manager
   */
  public function set(string $name, $value)
  {
    $this->setElementToPatch($name, $this->_instances, $value);

    return $this;
  }

  /**
   * Проверка наличия значения
   *
   * @inheritDoc
   */
  public function has($id): bool
  {
    return $this->get($id) ? true : false;
  }
}
