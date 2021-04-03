<?php

class Db
{
	private $db;

	private $query = [];
	
	private $_result;

	public function __construct($connection = array())
	{
		if (isset($connection['host']) && isset($connection['username']) && isset($connection['password']) && isset($connection['dbname'])) {
			try {
				$this->db = mysqli_connect($connection['host'], $connection['username'], $connection['password'], $connection['dbname']);
			} catch (Exception $e) {
				throw new Exception("Error connection database : " . $e->message, 1);	
			}
		}		
	}

	public function query($query){
		$this->query[] = $query;

		return $this;
	}

	public function select($column = '*')
	{
		$this->query[] = "SELECT ". $column . " ";

		return $this;
	}

	public function from($table = null)
	{
		if (!$table) {
			return false;
		}

		$this->query[] = "FROM ". $table . " ";

		return $this;
	}

	public function where($where)
	{
		$this->query[] = "WHERE ";
		if (is_array($where)) {
			if(count($where) > 1){
				foreach ($where as $key => $value) {
					$value = stripcslashes($value);
					$this->query[] = $key. " = '" .$value. "' AND ";
				}
			} else {
				foreach ($where as $k => $v) {
					$value = stripcslashes($v);
					$this->query[] = $k. " = '" .$v. "' ";
				}
			}

		} else {
			$this->query[] = $where;
		}

		return $this;
	}

	public function insert($data, $table = null)
	{
		if (!$table) {
			return false;
		}

		$_query 	= "INSERT INTO ". $table ."";
		$_column 	= []; 
		$_value 	= [];

		foreach ($data as $key => $value) {
			$_column[] 	= $key;
			$_value[]	= "'$value'";	
		} 

		$column = implode(",", $_column);
		$value 	= implode(",", $_value);
		$_query .= "({$column}) VALUES ({$value})";
		$this->query[] = $_query;

		return $this->_execute();
	}

	public function get()
	{
		if (count($this->query) < 0) {
			return false;
		}

		var_dump($this->_qeury_builder());

		$this->_result = mysqli_query($this->db, $this->_qeury_builder());

		if (!$this->_result) {
			return mysqli_error($this->db);
		}

		return $this;
	}

	private function _qeury_builder()
	{
		if (count($this->query) < 0) {
			return false;
		}

		$_query = implode("", $this->query);

		return $_query;
	}

	private function _execute()
	{
		if (count($this->query) < 0) {
			return false;
		}

		$this->execute = mysqli_query($this->db, $this->_qeury_builder());

		if (!$this->execute) {
			return mysqli_error($this->db);
		}

		return $this;
	}
}