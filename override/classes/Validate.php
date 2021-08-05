<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */
use Egulias\EmailValidator\EmailValidator;
use Egulias\EmailValidator\Validation\MultipleValidationWithAnd;
use Egulias\EmailValidator\Validation\RFCValidation;
use PrestaShop\PrestaShop\Core\ConstraintValidator\Constraints\CustomerName;
use PrestaShop\PrestaShop\Core\ConstraintValidator\Factory\CustomerNameValidatorFactory;
use PrestaShop\PrestaShop\Core\Domain\Currency\ValueObject\NumericIsoCode;
use PrestaShop\PrestaShop\Core\Email\SwiftMailerValidation;
use PrestaShop\PrestaShop\Core\String\CharacterCleaner;
use Symfony\Component\Validator\Validation;

class Validate extends ValidateCore
{
  /**
   * @param string $dni to validate
   *
   * @return bool
   */
  public static function isDniLite($dni)
  {

      if (Tools::getValue('id_country')) {
        if (Tools::getValue('id_country')==6){
          return self::checkDNI($dni);
        }
      }
      return empty($dni) || (bool) preg_match('/^[0-9A-Za-z-.]{1,16}$/U', $dni);
  }

  /**
 * Function which checks if a string is a valid spanish DNI
 *
 * @param string $dniUnchecked
 * @return string $dni
 */
public static function checkDNI($dniUnchecked) {
    //Error-Output
    $errors = '';
    //Possible values for the final letter
    $letterValues = array(
        'T' => 0, 'R' => 1, 'W' => 2, 'A' => 3, 'G' => 4, 'M' => 5,
        'Y' => 6, 'F' => 7, 'P' => 8, 'D' => 9, 'X' => 10, 'B' => 11,
        'N' => 12, 'J' => 13, 'Z' => 14, 'S' => 15, 'Q' => 16, 'V' => 17,
        'H' => 18, 'L' => 19, 'C' => 20, 'K' => 21, 'E' => 22
    );
    //Check if entered
    if($dniUnchecked == ''){
        // $errors .= 'Please enter a DNI.<br/>';
        // echo $errors;
        return false;
    }
    //Check length
    elseif(strlen($dniUnchecked) != 9){
        // $errors .= 'Please enter a DNI that has 8 digits and a check-letter.<br/>';
        // echo $errors;
        return false;
    }
    //Check validity
    elseif (preg_match('/^[0-9]{8}[A-Z]$/i', $dniUnchecked)) {
        // take numbers as big integer
        $checkNumber = (int)substr($dniUnchecked, 0, 8);
        // var_dump($checkNumber);
        // modulo 23 and check if modulo equals corresponding checkletter

        if($checkNumber % 23 == $letterValues[strtoupper(substr($dniUnchecked, 8, 1))]){
        //All was ok
        // echo 'all ok';
            $dni = trim($dniUnchecked);
            $dni = stripslashes($dni);
            $dni = htmlspecialchars($dni);
            return true;
        } else {
        return false;
        }
    }
    return false;
  }

  /**
   * Check for postal code validity.
   *
   * @param string $postcode Postal code to validate
   *
   * @return bool Validity is ok or not
   */
  public static function isPostCode($postcode)
  {
    $r = empty($postcode) || preg_match('/^[a-zA-Z 0-9-]+$/', $postcode);
    if (Tools::getValue('id_country')) {
      if (Tools::getValue('id_country')==6){
        $r2 = self::checkPostCodeES($postcode);
        return $r && $r2;
      }
    }
    return $r;

  }

  public static function checkPostCodeES($postcode){
    $t = substr($postcode,0,2);
    $l = ['07','35'];
    if (in_array($t,$l)) {
      return false;
    }
    return true;
  }


}
