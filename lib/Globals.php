<?php
/**
 * Utilities class for Global framework
 *
 */
class Globals
{
	const SALT_LENGTH = 9;
	const REGEX_WORD = '/^[\w -\']+$/iD';

	static public $months = array(
	1 => 'Jan',
	2 => 'Feb',
	3 => 'Mar',
	4 => 'Apr',
	5 => 'May',
	6 => 'Jun',
	7 => 'Jul',
	8 => 'Aug',
	9 => 'Sep',
	10 => 'Oct',
	11 => 'Nov',
	12 => 'Dec'
	);

	static public $monthsFull = array(
	1 => 'January',
	2 => 'February',
	3 => 'March',
	4 => 'April',
	5 => 'May',
	6 => 'June',
	7 => 'July',
	8 => 'August',
	9 => 'September',
	10 => 'October',
	11 => 'November',
	12 => 'December'
	);

	/**
	 * Basic ifsetor implementation - if the var is set, return it, else set
	 * it to the default passed
	 */
	static public function ifsetor(&$var, $default = null)
	{
		if (!empty($var)){
			return $var;
		} else {
			return $default;
		}
	}
	
	static public function ifobjectvarsetor($object, $key, $default = null)
	{
		if(isset($object)) {
            if($object[$key]) {
                return $object[$key];
            } else {
                return $default;
            }
		} else {
			return $default;
		}
	}

	/**
	 * Is variable set AND has value ?
	 */
	static public function isThere(&$var)
	{
		if (isset($var)) {
            if (is_string($var) && strlen($var) > 0) {
    			return true;
            } elseif (is_int($var) && $var > 0) {
    			return true;
            } elseif (is_array($var) && count($var) > 0) {
    			return true;
            } elseif (is_bool($var) && $var == true) {
    			return true;
            } else {
    			return false;
            }
		} else {
			return false;
		}
	}

	/**
	 * Hashing function to convert plaintext into a hash, which we store in the db
	 *
	 * When comparing $hash to $plaintext (eg in a password comparison) use as follows:-
	 *
	 *      if($hash === this::hash($plainText, $hash)){ echo "$plainText is valid" }
	 *
	 * @see http://phpsec.org/articles/2005/password-hashing.html
	 * @param string $plainText
	 * @param string $salt
	 * @return string
	 */
	static public function hash($plainText, $salt = null)
	{
		if ($salt === null){
			$salt = substr(md5(uniqid(rand(), true)), 0, self::SALT_LENGTH);
		}else{
			$salt = substr($salt, 0, self::SALT_LENGTH);
		}

		return $salt . sha1($salt . $plainText);
	}

	static public function in_array_recursive($needle, $haystack) {

		$it = new RecursiveIteratorIterator(new RecursiveArrayIterator($haystack));

		foreach($it as $k=>$element) {
			if($element === $needle) {
				return true;
			}
		}

		return false;
	}

	static public function array_key_exists_recursive($needle, $haystack)
	{
		$result = array_key_exists($needle, $haystack);
		if ($result) return $result;
		foreach ($haystack as $v) {
			if (is_array($v)) {
				$result = self::array_key_exists_recursive($needle, $v);
			}
			if ($result) return $result;
		}
		return $result;
	}


	static public function makeOptions($array, $selected = null, $escape = true, $prependValue = '')
	{
		$format  = "<option value=\"%s%s\"%s>%s</option>\n";
		$selAttr = ' selected="selected"';
		$html = '';
		foreach ($array as $val => $label){
			$val   = $escape ? htmlentities($val, ENT_QUOTES) : $val;
			$label = $escape ? htmlentities($label, ENT_QUOTES) : $label;

			if(is_array($selected)){
				$isSelected = in_array($val, $selected);
			}elseif ($selected !== null){
				$isSelected = ($val==$selected);
			}else{
				$isSelected = false;
			}


			$html .= sprintf(
			$format,
			$prependValue,
			$val,
			$isSelected ? $selAttr : '',
			$label
			);
		}
		return $html;
	}

        /**
         * Gimme a range of integer <option>s
         * @author Daniel Dammann
         */
	static public function makeIntegerOptions($min, $max, $selected = null)
	{
            for ($i = $min; $i <= $max; $i++) {
                $integerArray[$i] = $i;
            }

            return self::makeOptions($integerArray, $selected, false, '');
        }

        /**
         * Gimme a range of percentage <option>s
         * @author Daniel Dammann
         */
	static public function makePercentageOptions($min, $max, $selected = null)
	{
            for ($i = $min; $i <= $max; $i++) {
                $percentageArray[$i] = $i . '%';
            }

            return self::makeOptions($percentageArray, $selected, false, '');
        }

	static public function htmlentities_recursive($var, $quotes = ENT_QUOTES, $charset = 'UTF-8')
	{
		switch (gettype($var)){
			case 'object':
				foreach ($var as $prop => $v){
					$var->$prop = self::htmlentities_recursive($v);
				}
				break;

			case 'array' :
				foreach ($var as $k => &$v){
					$var[$k] = self::htmlentities_recursive($v);
				}
				break;

			case 'resource':
				break;

			default:
				$var = htmlentities($var, $quotes, $charset);
		}

		return $var;
	}

	static public function time_since($original)
	{
		// array of time period chunks
		$chunks = array(
		array(60 * 60 * 24 * 365 , 'year'),
		array(60 * 60 * 24 * 30 , 'month'),
		array(60 * 60 * 24 * 7, 'week'),
		array(60 * 60 * 24 , 'day'),
		array(60 * 60 , 'hour'),
		array(60 , 'minute'),
		);

		$today = time(); /* Current unix time  */
		$since = $today - $original;

		// $j saves performing the count function each time around the loop
		for ($i = 0, $j = count($chunks); $i < $j; $i++) {

			$seconds = $chunks[$i][0];
			$name = $chunks[$i][1];

			// finding the biggest chunk (if the chunk fits, break)
			if (($count = floor($since / $seconds)) != 0) {
				// DEBUG print "<!-- It's $name -->\n";
				break;
			}
		}

		$print = ($count == 1) ? '1 '.$name : "$count {$name}s";

		if ($i + 1 < $j) {
			// now getting the second item
			$seconds2 = $chunks[$i + 1][0];
			$name2 = $chunks[$i + 1][1];

			// add second item if it's greater than 0
			if (($count2 = floor(($since - ($seconds * $count)) / $seconds2)) != 0) {
				$print .= ($count2 == 1) ? ', 1 '.$name2 : ", $count2 {$name2}s";
			}
		}
		return $print;
	}
	static public function value_key_transform($array, $separator1 = '=', $separator2 = '&') {
		/**
		 * returns keys, values as string
		 * useful to display filters in url or text display arrays
		 */
		$data = array();
		foreach ($array as $k => $v) {
			$data[] = $k . $separator1 . $v;
		}
		return implode($separator2, $data);
	}    /**
	* Converts a Mysql date into UK format
	*
	* @param string $date yyyy-mm-dd
	* @return string dd/mm/yy
	*/
	static public function dateMysqlToUk($date)
	{
		if($date) {
			return date('d/m/Y', strtotime($date));
		} else {
			return '-';
		}
	}

	/**
	 * Converts a Mysql datetime into UK format
	 *
	 * @param string $date yyyy-mm-dd hh:mm:ss
	 * @return string dd/mm/yy hh:mm:ss
	 */
	static public function datetimeMysqlToUk($datetime,$showseconds = false)
	{
		$format = $showseconds ? 'd/m/Y H:i:s' : 'd/m/Y H:i';
		return date($format, strtotime($datetime));
	}

	/**
	 * Converts a UK formatted date to Mysql format
	 *
	 * @param string $date dd/mm/yy
	 * @return string yyyy-mm-dd
	 */
	static public function dateUkToMysql($date)
	{
		list($d, $m, $y) = explode('/', $date, 3);
		$ts = strtotime("{$y}-{$m}-{$d}");
		$date = date('Y-m-d', $ts);

		if(!$ts || !checkdate($m, $d, $y)){
			return false;
		}else {
			return $date;
		}
	}

}
