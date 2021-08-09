<?php

use PrestaShop\PrestaShop\Core\Util\InternationalizedDomainNameConverter;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * StarterTheme TODO: B2B fields, Genders, CSRF.
 */
class CustomerForm extends CustomerFormCore
{
  public function validate()
  {
      $emailField = $this->getField('email');
      $customer_exists_asp = Customer::customerExistsAsp($emailField->getValue());
      $customer = $this->getCustomer();
      if (!$customer->id && $customer_exists_asp) {
          $emailField->addError($this->translator->trans(
              'The email is already used, please choose another one or sign in',
              [],
              'Shop.Notifications.Error'
          ));
      }



      return parent::validate();
  }

}
