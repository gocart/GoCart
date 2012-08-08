<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

/*
This File licensed under the terms of the BSD License:

Copyright (c) 2007, Tux IT Services
All rights reserved.

Redistribution and use in source and binary forms, with or without modification,
are permitted provided that the following conditions are met:

    * Redistributions of source code must retain the above copyright notice, this
      list of conditions and the following disclaimer.
    * Redistributions in binary form must reproduce the above copyright notice,
      this list of conditions and the following disclaimer in the documentation
      and/or other materials provided with the distribution.
    * Neither the name of the Tux IT Services nor the names of its contributors
      may be used to endorse or promote products derived from this software
      without specific prior written permission.

THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
"AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR
A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT OWNER OR
CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL,
EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO,
PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR
PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF
LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING
NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS
SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.

*/


/**
* Credit Card Functions
*
* This helper module contains functions which can be used to manipulate credit
* card numbers and related information.
*
* @package    CodeIgniter
* @subpackage    Helpers
* @category    Helpers
* @author    Jim O'Halloran
*/


/**
* Truncates a card number retaining only the first 4 and the last 4 digits.  It then returns the truncated form.
*
* @param string The card number to truncate.
* @return string The truncated card number.
*/
function truncate_card($card_num)
{
	$padsize = (strlen($card_num) < 7 ? 0 : strlen($card_num) - 7);
	return substr($card_num, 0, 4) . str_repeat('X', $padsize). substr($card_num, -3);
}


/**
* Validates a card expiry date.  Finds the midnight on first day of the following
* month and ensures that is greater than the current time (cards expire at the
* end of the printed month).  Assumes basic sanity checks have already been performed
* on month/year (i.e. length, numeric, etc).
*
* @param integer The expiry month shown on the card.
* @param integer The expiry year printed on the card.
* @return boolean Returns true if the card is still valid, false if it has expired.
*/
function card_expiry_valid($month, $year)
{
	$expiry_date = mktime(0, 0, 0, ($month + 1), 1, (int) $year);
	return ($expiry_date > time());
}


/**
* Strips all non-numerics from the card number.
*
* @param string The card number to clean up.
* @return string The stripped down card number.
*/
function card_number_clean($number)
{
	//updated 11-28-2011 to remove the depreciated ereg function
	return preg_replace('#[^\d]#', "", $number);
}


/**
* Uses the Luhn algorithm (aka Mod10) <http://en.wikipedia.org/wiki/Luhn_algorithm>
* to perform basic validation of a credit card number.
*
* @param string The card number to validate.
* @return boolean True if valid according to the Luhn algorith, false otherwise.
*/
function card_number_valid ($card_number) {
	$card_number = strrev(card_number_clean($card_number));
	$sum = 0;
    
	for ($i = 0; $i < strlen($card_number); $i++)
	{
		$digit = substr($card_number, $i, 1);

		// Double every second digit
		if ($i % 2 == 1) {
			$digit *= 2;
		}

		// Add digits of 2-digit numbers together
		if ($digit > 9)
		{
			$digit = ($digit % 10) + floor($digit / 10);
		}

		$sum += $digit;
	}

	// If the total has no remainder it's OK
	return ($sum % 10 == 0);
}