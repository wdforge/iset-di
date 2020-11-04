<?php

namespace Iset\Di\Traits;

use Iset\Di\IDepended;
use Iset\Di\Manager;
use Iset\Di\Middleware\Decorator;

trait AddServiceManagerTrait
{
  public function setServiceManager(Decorator $object, Manager $serviceManager)
  {
    if ($object) {
      $object->setPrivateProperty('_diManager', $serviceManager);
      $object->setMethod('getServiceManager', function () {
        return $this->_diManager;
      });
    }
  }
}