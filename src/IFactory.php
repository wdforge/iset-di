<?php

namespace Iset\Di;

interface IFactory
{
  public function createInstance(\Iset\Utils\IParams $params);

  public function setServiceManager(\Iset\Di\Manager $diManager);
}