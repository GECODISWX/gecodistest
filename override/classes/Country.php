<?php

class Country extends CountryCore
{
  static $_code2to3 = array(
    'FR' => 'FRA',
    // 'ES' => 'ESP'
  );
  static function getCountryCode3By2($code2){
    if (isset(self::$_code2to3[$code2])) {
      return self::$_code2to3[$code2];
    }
    else {
      return false;
    }
  }

  static function getCountryCode2By3($code3){
    foreach (self::$_code2to3 as $key => $value) {
      if ($value == $code3) {
        return $key;
      }
    }
    return false;
  }

}
