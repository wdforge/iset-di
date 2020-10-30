<?php

namespace Iset\Di\Container;

use Iset\Di\CachedInstance;

/**
 * Class AbstractContainer
 * @package Iset\Create\Container
 */
abstract class AbstractContainer extends CachedInstance
{
  /**
   * @var array
   */
  protected $_instanceObject;

  /**
   * метод помещающий инстанс объекта в хранилище
   *
   * @param $object
   * @param array $params
   * @return mixed
   */
  abstract public function push($object, $params = []);

  /**
   * убираем контроль текущего инстанса
   * @param array $params
   */
  public function init(IParams $params)
  {
    //...
  }
}