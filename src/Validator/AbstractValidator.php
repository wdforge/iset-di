<?php


namespace Iset\Di\Validator;

use Iset\Di\CachedInstance;
use Iset\Di\IDepended;
use Iset\Utils\IParams;

class AbstractValidator extends CachedInstance implements IDepended
{
  protected $_params;

  const E_VALIDATOR_INIT = '';
  const E_VALIDATOR_VALIDATE = '';

  /**
   * @var \Iset\Di\Manager
   */
  protected $_diManager;

  public function init($params = [])
  {
    parent::init($params);
    $this->_diManager->get('application/event/manager')->trigger(self::E_VALIDATOR_INIT);
  }

  /**
   * Установка схемы параметров для проверки данных
   *
   * @param array $params
   */
  public function setSchema(array $params) {
    $this->_params = $params;
  }

  /**
   * Проверка передаваемых данных на соответствие схеме
   *
   * @param IParams $params
   */
  public function validate(IParams $params) : boolean {
    $this->_diManager->get('application/event/manager')->trigger(self::E_VALIDATOR_VALIDATE);
    return true;
  }

  /**
   * @param \Iset\Di\Manager $diManager
   */
  public function setServiceManager(\Iset\Di\Manager $diManager)
  {
    $this->_diManager = $diManager;
  }

}