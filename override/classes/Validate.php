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
          return self::isValidIdNumber($dni);
        }
      }
      return empty($dni) || (bool) preg_match('/^[0-9A-Za-z-.]{1,16}$/U', $dni);
  }

  public static function isValidIdNumber( $docNumber ) {
    $fixedDocNumber = strtoupper( $docNumber );
    return self::isValidNIF( $fixedDocNumber ) || self::isValidNIE( $fixedDocNumber ) || self::isValidCIF( $fixedDocNumber );
  }

  public static function isValidNIF( $docNumber ) {
    $isValid = FALSE;
    $fixedDocNumber = "";

    $correctDigit = "";
    $writtenDigit = "";

    if( !preg_match( "/^[A-Z]+$/i", substr( $fixedDocNumber, 1, 1 ) ) ) {
        $fixedDocNumber = strtoupper( substr( "000000000" . $docNumber, -9 ) );
    } else {
        $fixedDocNumber = strtoupper( $docNumber );
    }

    $writtenDigit = strtoupper(substr( $docNumber, -1, 1 ));

    if( self::isValidNIFFormat( $fixedDocNumber ) ) {
        $correctDigit = self::getNIFCheckDigit( $fixedDocNumber );

        if( $writtenDigit == $correctDigit ) {
            $isValid = TRUE;
        }
    }

    return $isValid;
  }

  public static function isValidNIE( $docNumber ) {
    $isValid = FALSE;
    $fixedDocNumber = "";

    if( !preg_match( "/^[A-Z]+$/i", substr( $fixedDocNumber, 1, 1 ) ) ) {
        $fixedDocNumber = strtoupper( substr( "000000000" . $docNumber, -9 ) );
    } else {
        $fixedDocNumber = strtoupper( $docNumber );
    }

    if( self::isValidNIEFormat( $fixedDocNumber ) ) {
        if( substr( $fixedDocNumber, 1, 1 ) == "T" ) {
            $isValid = TRUE;
        } else {
            /* The algorithm for validating the check digits of a NIE number is
                identical to the altorithm for validating NIF numbers. We only have to
                replace Y, X and Z with 1, 0 and 2 respectively; and then, run
                the NIF altorithm */
            $numberWithoutLast = substr( $fixedDocNumber, 0, strlen($fixedDocNumber)-1 );
            $lastDigit = substr( $fixedDocNumber, strlen($fixedDocNumber)-1, strlen($fixedDocNumber) );
            $numberWithoutLast = str_replace('Y', '1', $numberWithoutLast);
            $numberWithoutLast = str_replace('X', '0', $numberWithoutLast);
            $numberWithoutLast = str_replace('Z', '2', $numberWithoutLast);
            $fixedDocNumber = $numberWithoutLast . $lastDigit;
            $isValid = self::isValidNIF( $fixedDocNumber );
        }
    }

    return $isValid;
  }

  public static function isValidCIF( $docNumber ) {
      $isValid = FALSE;
      $fixedDocNumber = "";

      $correctDigit = "";
      $writtenDigit = "";

      $fixedDocNumber = strtoupper( $docNumber );
      $writtenDigit = substr( $fixedDocNumber, -1, 1 );

      if( self::isValidCIFFormat( $fixedDocNumber ) == 1 ) {
          $correctDigit = self::getCIFCheckDigit( $fixedDocNumber );

          if( $writtenDigit == $correctDigit ) {
              $isValid = TRUE;
          }
      }

      return $isValid;
  }

  public static function isValidNIFFormat( $docNumber ) {
      return self::respectsDocPattern(
          $docNumber,
          '/^[KLM0-9][0-9][0-9][0-9][0-9][0-9][0-9][0-9][a-zA-Z0-9]/' );
  }

  public static function isValidNIEFormat( $docNumber ) {
      return self::respectsDocPattern(
          $docNumber,
          '/^[XYZT][0-9][0-9][0-9][0-9][0-9][0-9][0-9][A-Z0-9]/' );
  }

  public static function isValidCIFFormat( $docNumber ) {
      return
          self::respectsDocPattern(
              $docNumber,
              '/^[PQSNWR][0-9][0-9][0-9][0-9][0-9][0-9][0-9][A-Z0-9]/' )
      or
          self::respectsDocPattern(
              $docNumber,
              '/^[ABCDEFGHJUV][0-9][0-9][0-9][0-9][0-9][0-9][0-9][0-9]/' );
  }

  public static function getNIFCheckDigit( $docNumber ) {
      $keyString = 'TRWAGMYFPDXBNJZSQVHLCKE';

      $fixedDocNumber = "";

      $position = 0;
      $writtenLetter = "";
      $correctLetter = "";

      if( !preg_match( "/^[A-Z]+$/i", substr( $fixedDocNumber, 1, 1 ) ) ) {
          $fixedDocNumber = strtoupper( substr( "000000000" . $docNumber, -9 ) );
      } else {
          $fixedDocNumber = strtoupper( $docNumber );
      }

      if( self::isValidNIFFormat( $fixedDocNumber ) ) {
          $writtenLetter = substr( $fixedDocNumber, -1 );

          if( self::isValidNIFFormat( $fixedDocNumber ) ) {
              $fixedDocNumber = str_replace( 'K', '0', $fixedDocNumber );
              $fixedDocNumber = str_replace( 'L', '0', $fixedDocNumber );
              $fixedDocNumber = str_replace( 'M', '0', $fixedDocNumber );

              $position = substr( $fixedDocNumber, 0, 8 ) % 23;
              $correctLetter = substr( $keyString, $position, 1 );
          }
      }

      return $correctLetter;
  }

  public static function getCIFCheckDigit( $docNumber ) {
        $fixedDocNumber = "";

        $centralChars = "";
        $firstChar = "";

        $evenSum = 0;
        $oddSum = 0;
        $totalSum = 0;
        $lastDigitTotalSum = 0;

        $correctDigit = "";

        $fixedDocNumber = strtoupper( $docNumber );

        if( self::isValidCIFFormat( $fixedDocNumber ) ) {
            $firstChar = substr( $fixedDocNumber, 0, 1 );
            $centralChars = substr( $fixedDocNumber, 1, 7 );

            $evenSum =
                substr( $centralChars, 1, 1 ) +
                substr( $centralChars, 3, 1 ) +
                substr( $centralChars, 5, 1 );

            $oddSum =
                self::sumDigits( substr( $centralChars, 0, 1 ) * 2 ) +
                self::sumDigits( substr( $centralChars, 2, 1 ) * 2 ) +
                self::sumDigits( substr( $centralChars, 4, 1 ) * 2 ) +
                self::sumDigits( substr( $centralChars, 6, 1 ) * 2 );

            $totalSum = $evenSum + $oddSum;

            $lastDigitTotalSum = substr( $totalSum, -1 );

            if( $lastDigitTotalSum > 0 ) {
                $correctDigit = 10 - ( $lastDigitTotalSum % 10 );
            } else {
                $correctDigit = 0;
            }
        }
        if( preg_match( '/[PQSNWR]/', $firstChar ) ) {
            $correctDigit = substr( "JABCDEFGHI", $correctDigit, 1 );
        }

        return $correctDigit;
    }

    public static function respectsDocPattern( $givenString, $pattern ) {
        $isValid = FALSE;

        $fixedString = strtoupper( $givenString );

        if( is_int( substr( $fixedString, 0, 1 ) ) ) {
            $fixedString = substr( "000000000" . $givenString , -9 );
        }

        if( preg_match( $pattern, $fixedString ) ) {
            $isValid = TRUE;
        }

        return $isValid;
    }

    public static function sumDigits( $digits ) {
        $total = 0;
        $i = 1;

        while( $i <= strlen( $digits ) ) {
            $thisNumber = substr( $digits, $i - 1, 1 );
            $total += $thisNumber;

            $i++;
        }

        return $total;
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
