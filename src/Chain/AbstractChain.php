<?php

namespace Iset\Di\Chain;

use Iset\Di\CachedInstance;

abstract class AbstractChain extends CachedInstance
{

  /**
   * @param $object
   * @param array $params
   * @return mixed
   */
  abstract public function processing($object, $params = []);

}