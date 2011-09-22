<?php
abstract class Weblynx_DataMappers_Abstract
{
	/*
	 * Zend_Db
	 *
	 * @var Zend_Db_Adapter_Abstract
	 */
	protected $db;

	public function __construct(Zend_Db_Adapter_Abstract $db)
	{
		$this->db = $db;
	}

	public function foundRows()
	{
		$sql = 'SELECT FOUND_ROWS() as rows';
		return $this->db->fetchOne($sql);
	}

	public function lastInsertId()
	{
		return $this->db->lastInsertId();
	}
}
