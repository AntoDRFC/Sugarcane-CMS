<?php
class DB {
	
	var $count = 0;
	var $mysql_id;
	var $db_name;
	var $last_id;
	//var $last_num_rows;
	var $sql_result;
	
	
	// 
	function connect ($db, $host = 'localhost', $user = 'root', $password = ''){
		//echo "Connect";
		if (isset($db)) {

			$this -> db_name = $db;
			$this -> mysql_id = mysql_connect($host, $user, $password);
			if (!$this -> mysql_id){
				$this -> _halt("Could not connect to $host.<br />".mysql_error());
			} else {
				//echo "Connected! with $host $user $password";
				return true;
			}

		} else {
			$this -> _halt("Need db name for DBConnection class.<br />");
		}

		return false;
	} // End connect

	function query ($sql){
		if (mysql_select_db($this -> db_name)) {
			
			if($this -> sql_result = mysql_query($sql)) {
				$this -> last_id = mysql_insert_id();
				//$this -> last_num_rows = mysql_num_rows($this -> sql_result);
				return true;
			} else {
			    var_dump($sql);
				$this -> _halt("Could not query ".$this -> db_name.".<br />".mysql_error());
			}
		} else {
			$this -> _halt("Could not select database<br />".mysql_error());
		}

		return false;
	}

	// Get the last inserted id
	function get_last_id() {
		return $this -> last_id;
	}

	// Creates a big array of values based on the sql statement given to it.
	function get_rows ($sql) {
		$this -> query($sql);
		$result = array();
		$count = 0;
		while ($rows = mysql_fetch_assoc($this -> sql_result)) {
			foreach ($rows as $key => $value ) {
				$result[$count][$key] = $value;
			}
			$count++;
		}
		return $result;
	}

	function get_row ($sql) {
		$result=$this -> get_rows($sql);
		if (count($result)>0)
			return $result[0];
		return array();
	}

	function get_field($table, $field, $condition){
		$sql = "SELECT $field FROM $table WHERE $condition LIMIT 1";
		if($this -> query($sql)){
			$row = mysql_fetch_array($this -> sql_result);
			return $row[0];
		}
	}
	
	function get_count($sql) {
		$rows = $this -> get_rows ($sql);
		return count($rows);
	}
	
	/*
	function get_count($sql) {
		if($this -> query($sql)){
			return $this -> last_num_rows;
		}
		return false;
	}
	*/
	
	/**
	 * Escapes a string for insert into the database.
	 *
	 * Escapes a string for use in the database.
	 *
	 *
	 * @since	1.0
	 * @param	string	$string	The string to escape.
	 * @return	string	The escaped string.
	 * @access	public
	 */
	function escape_string ($value) {
		$value = trim($value);
		// Stripslashes
		if (get_magic_quotes_gpc()) {
			$value = stripslashes($value);
		}
		// Quote if not integer
		if (!is_numeric($value) && !$this->is_db_function_name($value)) {
			$value = "'" . mysql_real_escape_string($value) . "'";
		}
		//echo "escape ($value) ";
		return $value;

	}
	function is_db_function_name ($word) {
		$is_function = false;
		switch (strtolower($word)) {
			case "now()":
			case "curdate()":
				$is_function = true;
				break;
		}
		return $is_function;
	}

	/**
	 * Like sprintf but sql escapes all of the parameters before using them.
	 *
	 * It's a safe way to build up a pre-formatted query from tainted data as
	 * it prevents the tainted data from escaping any quote marks.
	 * NOTE: It's up to the caller to use quote marks around the placeholders in the pattern
	 * NOTE: This is not a class method but an object method. Why is this important?
	 *       Because a subclass might override the escape_string method
	 *
	 * @param	string $pattern	An sprintf style format string.
	 * @param	mixed $rest,...	rest of the string.
	 * @access	public
	 * @return	string	Formatted result string
	 */
	
	function sqlprintf ($pattern) {
		 
		// Get the function arguments.
		$args = func_get_args();

		// Get rid of the firest - i.e. the patterm.
		array_shift($args);

		// Loop through the rest esacping them for use with MySQL.
		foreach (array_keys($args) as $arg) $args[$arg] = $this -> escape_string($args[$arg]);

		// Put the function call back.
		array_unshift($args, $pattern);

		// And return the result using the standard sprintf.
		return call_user_func_array("sprintf", $args);
	}

	function _halt($error_message){
		//echo "Halt";
		trigger_error('DBConnection Class Error : ' . $error_message, E_USER_ERROR);
		exit;
	}

	function debug_var($val){
		echo "<PRE>";
		print_r($val);
		echo "</PRE>";
	}
	
	/* Test stuff */
	
	function addCount () {
		//echo "<pre>Add 1 to $this->count</pre>";
		$this->count++;
		//echo "<pre>Added 1 to $this->count</pre>";
	}
	
	function showCount () {
		echo "<pre>DB Instance Count is: $this->count</pre>";
	}
	

	
}
?>