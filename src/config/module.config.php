<?php

return [
  'class' => [
    'factories' => \Iset\Di\Factory\ClassFactory::class,
    'containers' => \Iset\Di\Factory\ClassContainer::class
  ],

  'interface' => [
    'factories' => \Iset\Di\Factory\InterfaceFactory::class,
    'containers' => \Iset\Di\Factory\InterfaceContainer::class,
  ],
];