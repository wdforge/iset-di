<?php

namespace Iset\Di;

/**
 * Class CachedInstance
 * @package Iset\Di
 */
class CachedInstance
{
  /**
   * @var array
   */
  private static $_useInstances = [];

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
  }

  /**
   * @param null $class
   * @param bool $isShared
   * @return bool
   */
  protected function isCreated($class = null, $isShared = true)
  {
    if ($isShared) {
      if (!$class) {
        if (!empty(self::$_useInstances) && in_array(static::class, array_keys(self::$_useInstances))) {
          return true;
        }
      } else {
        if (!empty(self::$_useInstances) && in_array($class, array_keys(self::$_useInstances))) {
          return true;
        }
      }
    } else {
      if (!$class) {
        if (!empty(static::$_useInstances) && in_array(static::class, array_keys(static::$_useInstances))) {
          return true;
        }
      } else {
        if (!empty(static::$_useInstances) && in_array($class, array_keys(static::$_useInstances))) {
          return true;
        }
      }
    }

    return false;
  }

  /**
   * @param bool $isShared
   * @param null $alterClass
   */
  protected function initInstance($isShared = true, $alterClass = null)
  {
    if ($isShared) {
      if (!$this->isCreated()) {
        if (!$alterClass) {
          self::$_useInstances[static::class] = $this;
        } else {
          self::$_useInstances[$alterClass] = $this;
        }
      }
    } else {
      if (!$this->isCreated()) {
        if (!$alterClass) {
          static::$_useInstances[static::class] = $this;
        } else {
          static::$_useInstances[$alterClass] = $this;
        }
      }
    }
  }

  /**
   * @param null $class
   * @param bool $isShared
   * @return mixed|null
   */
  protected function getInstance($class = null, $isShared = true)
  {
    if ($isShared) {
      if ($class && $this->isCreated($class)) {
        return self::$_useInstances[$class];
      } elseif ($this->isCreated()) {
        return self::$_useInstances[static::class];
      }
    } else {
      if ($class && $this->isCreated($class)) {
        return static::$_useInstances[$class];
      } elseif ($this->isCreated()) {
        return static::$_useInstances[static::class];
      }
    }

    return null;
  }

  /**
   * @param $key
   * @param bool $isShared
   * @return bool
   */
  public function has($key, $isShared = true)
  {
    return isset(self::$_useInstances[$key]);
  }

  /**
   * @param $key
   * @param null $value
   * @param bool $isShared
   */
  public function set($key, $value = null, $isShared = true)
  {
    self::$_useInstances[$key] = $value;
  }

}