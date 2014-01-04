<?php

namespace Lib;

final class DateFormat
{
	static function formatDateDiff($start, $end = null) {
		if(!($start instanceof \DateTime)) {
			//$timestamp = $start;
			$start = new \DateTime($start);
			//$start->setTimestamp($timestamp);
		}
	   
		if($end === null) {
			$end = new \DateTime();
		}
	   
		if(!($end instanceof \DateTime)) {
			//$timestamp = (is_int($start)) ? $start : false;
			$end = new \DateTime($start);
			//if($timestamp) { $start->setTimestamp($end); }
		}
	   
		$interval = $end->diff($start);
		$doPlural = function($nb,$str){ return $nb > 1 ? $str . 's' : $str; }; // adds plurals
	   
		$format = array();
		if($interval->y !== 0) {
			$format[] = "%y ".$doPlural($interval->y, "year");
		}
		if($interval->m !== 0) {
			$format[] = "%m ".$doPlural($interval->m, "month");
		}
		if($interval->d !== 0) {
			$format[] = "%d ".$doPlural($interval->d, "day");
		}
		if($interval->h !== 0) {
			$format[] = "%h ".$doPlural($interval->h, "hour");
		}
		if($interval->i !== 0) {
			$format[] = "%i ".$doPlural($interval->i, "minute");
		}
		if($interval->s !== 0) {
			if(!count($format)) {
				return 'less than a minute ago';
			} else {
				$format[] = "%s ".$doPlural($interval->s, "second");
			}
		}
		if($interval->s === 0 && !count($format)) {
			return 'this very moment';
		}
	   
		// We use the two biggest parts
		if(count($format) > 1) {
			$format = array_shift($format)." and ".array_shift($format);
		} else {
			$format = array_pop($format);
		}
	   
		return $interval->format($format);
	}	
}
