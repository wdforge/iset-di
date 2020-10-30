<?php

namespace Iset\Di;

use Iset\Utils\TreeContainer;

use Iset\Utils\IInitial;
use Iset\Utils\IParams;
use Psr\Container\ContainerInterface as PsrContainerInterface;
use Psr\Container\ContainerExceptionInterface;

/**
 * Class Manager
 * @package Iset\Di
 */
class Manager implements IInitial, PsrContainerInterface
{
  use TreeContainer;

  //
  private $_factories = [];


  // named instance storage
  private $_instances = [];

  // set links interface=containers
  private $_containers = [];


  // operation middleware
  private $_middlewares = [];


  // inserted injections for decorators
  private $_injections = [];


  // hash key-valued storage for same objects
  private $_hashLinks = [];


  // unique valued storage for based (empted) instances
  private $_clonedInstances = [];

  // extends instances
  private $_methods = [];

  /**
   * @param IParams $params
   * @prefix Maybe need for all operation without shared flag!
   */

  public function init(IParams $params)
  {
    $this->_factories = $params->get('application/di/factories', []);
    $this->_containers = $params->get('application/di/containers', []);
    $this->_middlewares = $params->get('application/di/middlewares', []);
    $this->_injections = $params->get('application/di/injections', []);

    return $this;
  }

  /**
   *
   * @param $class
   * @param IParams $params
   * @param null $name
   * @param bool $is_shared
   * @return null
   */
  public function createInstance($class, IParams $params, $name = null, $is_shared = false)
  {
    if (!$class) {
      return null;
    }

    $instance = null;

    if (in_array($class, array_keys($this->_clonedInstances))) {
      $instance = $this->_clonedInstances[$class];
      $this->setProperties($instance, $params);
    }

    if (!$instance) {
      $instance = $this->createObject($class, $params);
    }

    if ($name) {
      $this->set($name, $instance);
    }

    return $instance;
  }


  /**
   *
   * @param type $method
   * @param type $callback
   * @param type $isAfter
   * @param type $object
   * @throws \Exception
   */
  public function addMiddleware($method, $callback, $isAfter = false, $object = null)
  {
    if (isset($object) && !is_object($object)) {
      throw new \Exception("Object param is not object");
    }

    $hash = spl_object_hash($object);

    if ($object === null || spl_object_hash($this) == $hash) {
      if ($isAfter) {
        $this->middlewares[$method]['after'][] = $callback;
      } else {
        $this->middlewares[$method]['before'][] = $callback;
      }
    }


    // unworkable need debug set container to contain values
    if (in_array($hash, array_keys($this->_hashLinks))) {
//			$class = get_class($this->_hashLinks[$has]);
//			$this->_containers[$class] 
    }

    // create or get middleware container to exists contained object
  }

  /**
   *
   * @param type $method
   * @param type $isAfter
   * @return type
   * @throws \Exception
   */
  protected function execMiddleware($method, $isAfter = true)
  {
    $section = $isAfter ? 'after' : 'before';
    if (!empty($this->middlewares[$method][$section]) &&
      is_array($this->middlewares[$method][$section])) {
      foreach ($this->middlewares[$method][$section] as $middleware) {
        if (is_callable($middleware)) {
          return $middleware();
        } else {
          throw new \Exception("Set Middleware is not callable");
        }
      }
    }

    return null;
  }

  /**
   *
   * @param type $class
   * @param type $params
   * @return type
   */
  protected function createObject($class, $params = [])
  {
    $isCloned = false;

    // cloned instance
    if (isset($this->_clonedInstances[$class])) {
      $instance = $this->createCloned($class, $params);
      $isCloned = true;
    }

    // create from factory
    if (!isset($instance) && isset($this->_factories[$class])) {
      $instance = $this->createFromFactory($class, $params);
    }

    // create simple option create instance
    if (!isset($instance)) {
      $instance = $this->createSimple($class, $params);
    }

    if (isset($instance) && !$isCloned) {
      $this->_clonedInstances[$class] = $instance;
    }

    if ($instance) {
      $instance = $this->applyContainers($instance);
    }

    return $instance;
  }

  /**
   *
   * @param type $instance
   * @return type
   * @throws \Iset\Di\Exception\ParameterIsEmpty
   */
  protected function applyContainers($instance)
  {
    if (!$instance) {
      throw new \Iset\Di\Exception\ParameterIsEmpty;
    }

    $class = get_class($instance);

    // create containers
    $interfaces = class_implements($class);

    if (isset($instance) && !empty($interfaces)) {
      foreach ($interfaces as $interface) {
        if ($container = $this->getContainer($interface)) {
          $instance = $container->setValue($instance);
        }
      }
    }

    return $instance;
  }

  /**
   *
   * @param type $class
   * @param type $params
   * @return type
   */
  protected function createCloned($class, $params = [])
  {
    // cloning object and fill parameters
    if (in_array($class, array_keys($this->_clonedInstances))) {
      $instance = clone $this->_clonedInstances[$class];
      $this->setProperties($instance, $params);
      return $instance;
    }

    return null;
  }

  /**
   *
   * @param type $class
   * @param type $params
   * @return type
   */
  protected function createSimple($class, $params = [])
  {
    // simple create object
    $reflection = new \ReflectionClass($class);
    $instance = $reflection->newInstance();
    $this->setProperties($instance, $params);

    $hash = spl_object_hash($instance);

    if (!in_array($hash, array_keys($this->_hashLinks))) {
      $this->_hashLinks[$hash] = $instance;
    } else {
      return $this->_hashLinks[$hash];
    }

    return $instance;
  }

  /**
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
   *
   * @param type $class
   * @param type $factory
   */
  public function setFactory($class, $factory)
  {
    $this->_factories[$class] = $factory;
  }

  /**
   *
   * @param type $class
   * @return boolean
   */
  public function getFactory($class)
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
   *
   * @param type $class
   * @param type $params
   * @return boolean
   */
  protected function createFromFactory($class, $params = [])
  {
    if (isset($this->_factories[$class]) && is_string($this->_factories[$class])) {

      $this->_factories[$class] = $this->createObject($this->_factories[$class]);

      if ($this->_factories[$class] instanceof IFactory) {
        $this->_factories[$class]->setServiceManager($this);
        return $this->_factories[$class]->createInstance($params);
      }
    }

    return false;
  }

  /**
   *
   * @param type $instance
   * @param type $params
   */
  protected function setProperties($instance, $params = [])
  {
    $class = get_class($instance);
    $properties = $this->getProperties($instance);

    if (empty(!$properties['public']) && isset($params)) {

      foreach ($params as $key => $value) {

        if (isset($properties['public'][$key])) {
          $instance->$key = $value;
        }

        if (isset($properties['protected'][$key])) {
          if (!empty($this->_methods[$class]['setProtectedProperty']) &&
            is_callable($this->_methods[$class]['setProtectedProperty'])) {
            $this->_methods[$class]['setProtectedProperty']($key, $value);
          }
        }

        if (isset($properties['private'][$key])) {
          if (!empty($this->_methods[$class]['setPrivateProperty']) &&
            is_callable($this->_methods[$class]['setPrivateProperty'])) {
            $this->_methods[$class]['setPrivateProperty']($key, $value);
          }
        }

        if (isset($properties['static'][$key])) {
          if (!empty($this->_methods[$class]['setStaticProperty']) &&
            is_callable($this->_methods[$class]['setStaticProperty'])) {
            $this->_methods[$class]['setStaticProperty']($key, $value);
          }
        }

      }
    }
  }

  /**
   *
   * @param type $instance
   * @return type
   */
  protected function getProperties($instance)
  {

    $reflect = new \ReflectionClass($instance);

    return [
      'private' => $reflect->getProperties(\ReflectionProperty::IS_PRIVATE),
      'public' => $reflect->getProperties(\ReflectionProperty::IS_PUBLIC),
      'protected' => $reflect->getProperties(\ReflectionProperty::IS_PROTECTED),
      'static' => $reflect->getStaticProperties(),
    ];
  }

  /**
   *
   * @param type $name
   * @param type $object
   * @param \Iset\Di\callable $callback
   * @return boolean
   */
  public function addMethod($name, $object, callable $callback)
  {

    $class = get_class($object);

    $this->_methods[$class][$name] = $callback;
    $this->_methods[$class][$name]->bindTo($object);

    return true;
  }

  /**
   *
   * @param type $name
   * @param type $default
   *
   * @return type
   */
  public function get($name, $default = null)
  {
    $result = $this->getElementFromPatch($name, $this->_instances);
    return isset($result) ? $result : $default;
  }

  /**
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
   * @inheritDoc
   */
  public function has($id)
  {
    return $this->get($id) ? true : false;
  }
}
