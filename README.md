Personnummer
============

Swedish personal-id verification

DOCUMENTATION
-------------

All functions return (bool) false if there are some sort
of error. Note that some of the functions can return 0,
so you need to check the type of the return value.
All functions are public static, so you don't need to
instantiate this class.

### Example

      include_once('pnum.class.php');
      if(Pnum::check($_POST['personal_number'])) {
         echo "The personal number is correct";
      }


### function checksum($pnum)
Return: (int) the checksum number
Will calculate the checksum for a personal number.
The checksum is the last value of the personal number
and can be used to verify that the number is entered
correctly.
This function does not do much format checking. Make

### function check($pnum, $personal_only = true)
Return: (bool) false on incorrect number
This function will check if an entered personal or
company number is correct. By default it will only
check for personal numbers (it will do a date check,
which will fail for company numbers). Change the
second parameter to false to disable the date check.

### function datecheck($pnum)
Return: (bool) true if it's a correct date part
Check that the date part of a personal number
is in fact a date. Note that this will not work for
company numbers.

### function gender($pnum)
Return: (int) 1 = male, 2 = female, false = error
Which gender is this personal number belonging to?
Will do a check and datecheck to see that it's
correctly formated.

### function ispersonal($pnum)
Return (bool) true if it is a personal number
This is actually an alias for check($pnum, true)

### function iscompany($pnum)
Return: (bool) true if it is a company number
Note that this function does not detect private
companies (enskilda firmor) since their company number
is the same as the owners personal number.

### function company($pnum)
Return: (string) see below
false = incorrect format or not a company number
'OF' = Government, county or municipality ("offentlig")
'AB' = stock company (aktiebolag)
'EK' = economic association (ekonomisk förening)
'IF' = non-profit association (ideell förening)
'HB' = General or limied partnership (handels-/kommandit-bolag)
true = other, undefined company type
Note that this functions does not detect private
companies (enskilda firmor). See iscompany() above.

### function filter($pnum)
Return: (string) filtered personal number
Filters the input to the other functions so it is in
a general form. It removes everything except numbers
(which, of course, is wrong if you want to save the
personal number, because personal numbers can have
a - or a + as a delimiter, with different meanings).
It also checks to see if you have provided century
numbers and removes them.
We discourage outside use of this function.


AUTHORS
-------

Emil Vikstrom


