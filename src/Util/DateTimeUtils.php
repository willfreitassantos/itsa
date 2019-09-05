<?php
namespace ITSA\Util;

use DateTime;

class DateTimeUtils
{

	public static function isValid($date, $format = 'd/m/y')
	{
    	$d = DateTime::createFromFormat($format, $date);
    	return $d && $d->format($format) === $date;
	}

	public static function isGreaterThanToday($date, $format = 'd/m/y')
	{
		$date = DateTime::createFromFormat($format, $date);
		$current = DateTime::createFromFormat($format, date($format));
		return $date > $current;
	}

	public static function getDateDB($date) {
		$day = explode("/", $date)[0];
		$month = explode("/", $date)[1];
		$year = explode("/", $date)[2];
		return $year . "-" . $month . "-" . $day;
	}
}
