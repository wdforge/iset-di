<?php
/**
 * @future
 * Предполагается делать для хранимого объекта контейнер, который будет пропускать через себя вызовы методов
 * и вызывая при этом по порядку Middlewares привязанные в настройках к конкретному методу объекта.
 * Пока ничего не работает (:
 */
namespace Iset\Di\Traits;

trait MiddlewareTrait
{
  private $_middlewares = [];

  /**
   *
   * @param string $method
   * @param callable $callback
   * @param bool $isAfter
   * @param $object
   * @throws \Exception
   */
  public function addMiddleware(string $method, callable $callback, bool $isAfter = false, $object = null)
  {
    if (isset($object) && !is_object($object)) {
      throw new \Exception("Object param is not object");
    }

    $hash = spl_object_hash($object);

    /**
     * забыл зачем делалось (:
     */
    if ($object === null || spl_object_hash($this) == $hash) {
      if ($isAfter) {
        $this->_middlewares[$method]['after'][] = $callback;
      } else {
        $this->_middlewares[$method]['before'][] = $callback;
      }
    }

    if (in_array($hash, array_keys($this->_hashLinks))) {
      $class = get_class($this->_hashLinks[$hash]);
			if(!isset($this->_containers[$class])) {
        //...
      }
    }
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
    if (!empty($this->_middlewares[$method][$section]) &&
      is_array($this->_middlewares[$method][$section])) {
      foreach ($this->_middlewares[$method][$section] as $middleware) {
        if (is_callable($middleware)) {
          return $middleware();
        } else {
          throw new \Exception("Set Middleware is not callable");
        }
      }
    }

    return null;
  }

}