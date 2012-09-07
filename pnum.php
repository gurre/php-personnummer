<?php
/*
 * Pnum 1.3
 *
 * Levonline made this class so you can verify Swedish
 * personal and company numbers.
 * Levonline is a web hosting company with office and datacenter
 * in Stockholm, Sweden. We offer shared web hosting,
 * dedicated servers, co-location and virtual servers. Our
 * stability is the best on the Swedish market, with guaranteed
 * uptime. Check out http://www.levonline.com/
 *
 * LICENSE
 * =======
 *
 * The class is made available under the MIT license:
 *
 * Copyright (c) 2009 Le-vonline AB, http://www.levonline.com/
 *
 * Permission is hereby granted, free of charge, to any person
 * obtaining a copy of this software and associated documentation
 * files (the "Software"), to deal in the Software without
 * restriction, including without limitation the rights to use,
 * copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the
 * Software is furnished to do so, subject to the following
 * conditions:
 *
 * The above copyright notice and this permission notice shall be
 * included in all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND,
 * EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES
 * OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND
 * NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT
 * HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY,
 * WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING
 * FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR
 * OTHER DEALINGS IN THE SOFTWARE.
 *
 *
 * DESCRIPTION OF LICENSE
 * ======================
 *
 * Use this code however you want, but don't remove the license
 * or copyright notice above. You can use the software in
 * your own code without asking us for permission.
 * We do not guarantee that this code will work, use at your own risk.
 *
 *
 * DOCUMENTATION
 * =============
 *
 * All functions return (bool) false if there are some sort
 * of error. Note that some of the functions can return 0,
 * so you need to check the type of the return value.
 * All functions are public static, so you don't need to
 * instantiate this class.
 *
 * Example:
 * include_once('pnum.class.php');
 * if(Pnum::check($_POST['personal_number'])) {
 *    echo "The personal number is correct";
 * }
 *
 *
 * function checksum($pnum)
 *    Return: (int) the checksum number
 *    Will calculate the checksum for a personal number.
 *    The checksum is the last value of the personal number
 *    and can be used to verify that the number is entered
 *    correctly.
 *    This function does not do much format checking. Make
 *
 * function check($pnum, $personal_only = true)
 *    Return: (bool) false on incorrect number
 *    This function will check if an entered personal or
 *    company number is correct. By default it will only
 *    check for personal numbers (it will do a date check,
 *    which will fail for company numbers). Change the
 *    second parameter to false to disable the date check.
 *
 * function datecheck($pnum)
 *    Return: (bool) true if it's a correct date part
 *    Check that the date part of a personal number
 *    is in fact a date. Note that this will not work for
 *    company numbers.
 *
 * function gender($pnum)
 *    Return: (int) 1 = male, 2 = female, false = error
 *    Which gender is this personal number belonging to?
 *    Will do a check and datecheck to see that it's
 *    correctly formated.
 *
 * function ispersonal($pnum)
 *    Return (bool) true if it is a personal number
 *    This is actually an alias for check($pnum, true)
 *
 * function iscompany($pnum)
 *    Return: (bool) true if it is a company number
 *    Note that this function does not detect private
 *    companies (enskilda firmor) since their company number
 *    is the same as the owners personal number.
 *
 * function company($pnum)
 *    Return: (string) see below
 *    false = incorrect format or not a company number
 *    'OF' = Government, county or municipality ("offentlig")
 *    'AB' = stock company (aktiebolag)
 *    'EK' = economic association (ekonomisk förening)
 *    'IF' = non-profit association (ideell förening)
 *    'HB' = General or limied partnership (handels-/kommandit-bolag)
 *    true = other, undefined company type
 *    Note that this functions does not detect private
 *    companies (enskilda firmor). See iscompany() above.
 *
 * function filter($pnum)
 *    Return: (string) filtered personal number
 *    Filters the input to the other functions so it is in
 *    a general form. It removes everything except numbers
 *    (which, of course, is wrong if you want to save the
 *    personal number, because personal numbers can have
 *    a - or a + as a delimiter, with different meanings).
 *    It also checks to see if you have provided century
 *    numbers and removes them.
 *    We discourage outside use of this function.
 *
 *
 * AUTHORS
 * =======
 *
 * Emil Vikstrom
 *
 */


class Pnum {
   public static function checksum($pnum) {
      $pnum = self::filter($pnum);
      $len = strlen($pnum);
      if($len == 10) {
         $pnum = substr($pnum, 0, 9);
      }elseif($len > 10) {
         return false;
      }

      $checksum = 0;
      $onetwo = 1;
      for($i = 0; $i < 9; $i++) {
         $onetwo = $onetwo==1?2:1;
         $tmp = $pnum[$i] * $onetwo;
         if($tmp > 9) {
            $tmp = $tmp - 10 + 1;
         }
         $checksum += $tmp;
      }
      $checksum %= 10;
      if($checksum != 0) {
         $checksum = 10 - $checksum;
      }
      return $checksum;
   }

   public static function check($pnum, $personal_only = true) {
      $pnum = self::filter($pnum);
      $len = strlen($pnum);
      if($len != 10) {
         return false;
      }

      if($personal_only && ($pnum[2] > 1 || !self::datecheck($pnum)) ) {
         return false;
      }

      return self::checksum($pnum) == $pnum[$len-1];
   }

   public static function datecheck($pnum) {
      $pnum = self::filter($pnum);
      $date = substr($pnum, 0, 6);
      $y = substr($date, 0, 2);
      $m = substr($date, 2, 2);
      $d = substr($date, 4, 2);
      $date = "19$y-$m-$d";
      return $date == date('Y-m-d', strtotime($date));
   }

   public static function gender($pnum) {
      $pnum = self::filter($pnum);
      if(!self::check($pnum, true)) {
         return false;
      }
      return ($pnum[8] % 2) ? 1 : 2;
   }

   public static function ispersonal($pnum) {
      $pnum = self::filter($pnum);
      return self::check($pnum, true);
   }

   public static function iscompany($pnum) {
      $pnum = self::filter($pnum);
      return !($pnum[2] < 2 || !self::check($pnum, false));
   }

   public static function company($pnum) {
      $pnum = self::filter($pnum);
      if(!self::iscompany($pnum)) {
         return false;
      }
      switch($pnum[0]) {
      case 2:
         return 'OF';
      case 5:
         return 'AB';
      case 7:
         return 'EK';
      case 8:
         return 'IF';
      case 9:
         return 'HB';
      default:
         return true;
      }
   }

   public static function filter($pnum) {
      $pnum = preg_replace('/[^0-9]/', '', $pnum);
      if(strlen($pnum) > 10 && $pnum[0] <= 2) {
         $pnum = substr($pnum, 2);
      }
      return $pnum;
   }
}