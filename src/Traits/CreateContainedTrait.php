<?php
/**
 * Функционал помещения объекты в контейнеры для проксирования вызовов и обращения
 * После создания объекта, отдаётся инстанс на контейнер.
 * @future
 * На основе контейнеров также будет реализовано перегрузка функционала классов
 */
namespace Iset\Di\Traits;

trait CreateContainedTrait
{
  /**
   * @var array
   */
  private $_containers = [];

  /**
   * Получение контейнера по интерфейсу
   *
   * @param type $interface
   * @return boolean
   */
  public function getContainer($interface)
  {
    if (!isset($this->_containers[$interface])) {
      return false;
    }

    if (is_string($this->_containers[$interface])) {
      $this->_containers[$interface] = new $this->_containers[$interface];
    }

    return $this->_containers[$interface];
  }

  /**
   * Помещение объекта в контейнер
   *
   * @param object $instance
   * @return object
   * @throws \Iset\Di\Exception\ParameterIsEmpty
   */
  protected function applyContainers($instance)
  {
    if (!$instance) {
      throw new \Iset\Di\Exception\ParameterIsEmpty;
    }
    $class = get_class($instance);

    /**
     * Создание контейнерной обёртки где
     * интерфейс => контейнер
     */
    $interfaces = class_implements($class);
    if (!empty($interfaces)) {
      foreach ($interfaces as $interface) {
        /**
         * @var $container \Iset\Di\Container\AbstractContainer
         */
        if ($container = $this->getContainer($interface)) {
          $instance = $container->setValue($instance);
        }
      }
    }

    return $instance;
  }

}