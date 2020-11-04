<?php
/**
 * Функционал для срздания объекта через фабрики
 */

namespace Iset\Di\Traits;


use Iset\Di\Factory\AbstractFactory;
use Iset\Di\IDepended;
use Iset\Di\IFactory;
use Iset\Utils\IParams;

trait CreateByFactory
{
  /**
   * @var array
   */
  private $_factories = [];


  /**
   * Назначение классу фабрики
   *
   * @param string $class
   * @param AbstractFactory $factory
   */
  public function setFactory(string $class, AbstractFactory $factory)
  {
    $this->_factories[$class] = $factory;
  }

  /**
   * Получение фабрики по классу
   *
   * @param string $class
   * @return AbstractFactory
   */
  public function getFactory(string $class): AbstractFactory
  {
    if (!isset($this->_factories[$class])) {
      return false;
    }
    if (is_string($this->_factories[$class])) {
      $factoryClass = $this->_factories[$class];
      $this->_factories[$class] = new $factoryClass;

      if ($this->_factories[$class] && in_array(IDepended::class, class_implements($factoryClass))) {
        $this->_factories[$class]->_diManager = $this;
        // далее временно неработающий код
        $getServiceManager = function () {
          return $this->_diManager;
        };
        $getServiceManager->bindTo($this->_factories[$class]);
        $this->_factories[$class]->getServiceManager = $getServiceManager;
      }
    }

    return $this->_factories[$class];
  }

  /**
   * Создание объекта через фабрику
   *
   * @param string $class
   * @param IParams $params
   * @return mixed
   */
  protected function createFromFactory(string $class, IParams $params)
  {
    if (isset($this->_factories[$class]) && is_string($this->_factories[$class])) {
      $this->_factories[$class] = $this->createObject($this->_factories[$class]);
    }
    if ($this->_factories[$class] instanceof IFactory) {
      $this->_factories[$class]->setServiceManager($this);
      return $this->_factories[$class]->createInstance($params, $class);
    } else {
      throw new \Exception(sprintf('Class factory: "%s" must be implement "%s" interface', get_class($this->_factories[$class]), IFactory::class));
    }

    return false;
  }

}