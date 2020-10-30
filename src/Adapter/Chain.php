<?php

namespace Iset\Di\Adapter;

use Iset\Di\Chain\AbstractChain;

// обработка объекта через фабричный конвейер (chain)
// Паттерн: Цепочка обязанностей
// injections and multiple create
// f(c)->f(o)->f(o)->f(o)
class Chain extends AbstractChain
{

  public static $_useInstances = [];

  /**
   * @param $object
   * @param array $params
   * @return null
   */
  public function processing($object, $params = [])
  {
    $resultObject = null;
    $objectChain = null;

    /**
     * неизвестный код
     */
    $classFactory = null;
    $objectFactory = null;
    $class = null;
    $interfaces = class_implements($class);

    if (isset($this->_settings) && is_array($this->_settings)) {

      foreach ($this->_settings as $interfaceObject => $chainClass) {

        if (in_array($interfaceObject, $interfaces)) {

          if ($this->isCreated($chainClass)) {
            $objectChain = $this->getInstance($chainClass);
          } else {
            $objectChain = $this->create($chainClass);

            if (!$objectFactory->isSubclassOf(AbstractChain::class)) {
              throw new Exception(
                sprintf('Chain "%s" is not sub class of "%s"', $chainClass, AbstractChain::class)
              );
            }

            self::$_useInstances[$classFactory] = $objectChain;
          }

          $resultObject = $objectChain->processing($object, $params);
        }
      }
    }

    return $resultObject;
  }

}
