<?php


namespace Iset\Di;


interface IValided
{
  public function validate(): boolean;

  public function setValidator();

  public function getValidator(): \Iset\Di\Validator\AbstractValidator;
}