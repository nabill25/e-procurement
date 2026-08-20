<?php
/**
 * @package 	eProcurement Application
 * @author 		eproc2025
 * @since 		25. Version 3.1
 *
 */

  class Entity extends CI_Model{

	var $currentRow;
	var $errorMsg;
  var $rowCount;
  var $rowResult=array();
	var $currentRowIndex;
    /**
    * Class constructor.
    **/
    function Entity()
	{
      $this->load->database();
    }

	public function __construct()
	{
		parent::__construct();
	}

	public function setField($fieldName,$fieldValue){
		$fieldName = strtolower($fieldName);

		$this->currentRow[$fieldName]=$fieldValue;
	}

	public function setFieldValidasi($fieldName,$fieldValue){
		$fieldName = strtolower($fieldName);

		$this->currentRow[$fieldName]= str_replace("'", "''", trim($fieldValue));
	}

	public function getField($fieldName){
		$fieldName = strtolower($fieldName);

		return isset($this->currentRow[$fieldName]) ? $this->currentRow[$fieldName] : null;
	}

	public function getNextId($idName,$tableName,$filter=""){
		$str = "SELECT MAX($idName) AS maxvalue FROM $tableName ";
			$query = $this->db->query($str);
			if ($query) {
        $row = $query->first_row();
        $temp = $row->maxvalue + 1; //next key*/
      } else {
        $temp = 1;
      }

		return $temp;
	}

	function selectLimit($sql,$numrow=-1,$offset=-1){

		$_rowResult = array();
		$this->rowResult = array();
		$this->rowCount = 0;

		$sql = $this->db->_limit($sql, $numrow, $offset);
		$rs = $this->db->query($sql);
		if(!$rs){
		}
		else
		{
			$i=0;
			foreach ($rs->result() as $row)
			{
				foreach($row as $key=>$val){
					$arr[strtolower($key)] = $val;
				}
				$_rowResult[$i]= $arr;
				$i = $i+1;
			}
			$this->rowResult = $_rowResult;
			$this->rowCount = $i;

			$this->currentRowIndex = -1;
			$this->currentRow = array();

		}
		return $rs;
    }

	function selectClobLimit($sql,$numrow=-1,$offset=-1){

		$_rowResult = array();
		$_clobField = array();
		$this->rowResult = array();
		$this->rowCount = 0;


		$sql = $this->db->_limit($sql, $numrow, $offset);
		$rs = $this->db->query($sql);
		$fields = $rs->field_data();
		foreach ($fields as $field)
		{
			if($field->type == "CLOB")
			   $_clobField[] = $field->name;
		}
		if(!$rs){
		}
		else
		{
			$i=0;
			foreach ($rs->result() as $row)
			{
				foreach($row as $key=>$val){
					if(in_array($key, $_clobField))
					{
						if($val)
							$value = $val->read($val->size());
						else
							$value = "";
					}
					else
						$value = $val;
					$arr[strtolower($key)] = $value;
				}
				$_rowResult[$i]= $arr;
				$i = $i+1;
			}
			$this->rowResult = $_rowResult;
			$this->rowCount = $i;

			$this->currentRowIndex = -1;
			$this->currentRow = array();

		}
		return $rs;
    }

	function selectClob($sql){

		$_rowResult = array();
		$this->rowResult = array();
		$this->rowCount = 0;

		$rs = $this->db->query($sql);
		if(!$rs){
		}
		else
		{
			$i=0;
			foreach ($rs->result() as $row)
			{
				foreach($row as $key=>$val){
					if($val)
						$value = $val->read($val->size());
					else
						$value = "";
					$arr[strtolower($key)] = $value;
				}
				$_rowResult[$i]= $arr;
				$i = $i+1;
			}
			$this->rowResult = $_rowResult;
			$this->rowCount = $i;

			$this->currentRowIndex = -1;
			$this->currentRow = array();

		}
		return $rs;
    }

	function select($sql){

		$_rowResult = array();
		$this->rowResult = array();
		$this->rowCount = 0;

		$rs = $this->db->query($sql);
		if(!$rs){
		}
		else
		{
			$i=0;
			foreach ($rs->result() as $row)
			{
				foreach($row as $key=>$val){
					$arr[strtolower($key)] = $val;
				}
				$_rowResult[$i]= $arr;
				$i = $i+1;
			}
			$this->rowResult = $_rowResult;
			$this->rowCount = $i;

			$this->currentRowIndex = -1;
			$this->currentRow = array();

		}
		return $rs;
    }

	 function nextRow(){
		if($this->currentRowIndex < $this->rowCount-1){
			$this->currentRowIndex++;
			$this->setRowValue();
			return true;
		} else {
			return false;
    	}
	}

	function firstRow(){
      	if($this->rowCount>0){
			$this->currentRowIndex=0;
			$this->setRowValue();
			return true;
      	} else {
        	return false;
    	}
	}

	function countRow(){
      	return $this->rowCount;
	}

	function setRowValue(){
		$this->currentRow = isset($this->rowResult[$this->currentRowIndex]) ? $this->rowResult[$this->currentRowIndex] : null;
	}

	function execQuery($sql){

		return $this->db->query($sql);
    }

	function updateClob($table, $primary_key, $primary_value, $field, $field_value){

	   $jumlah_char = strlen($field_value);
	   $jumlah_char = round($jumlah_char / 4000);
	   $this->db->query("UPDATE $table SET $field='' WHERE $primary_key = $primary_value");

	   if(trim($field_value) == "")
	   {}
	   else
	   {
		   for($i=0;$i<=$jumlah_char;$i++)
		   {
			   $a = $i * 4000;
			   $b = 4000;
			   $value = substr($field_value, $a, $b);
			   $this->db->query("UPDATE $table SET $field = $field || ? WHERE $primary_key = $primary_value",array($value));
		   }
	   }

    }



	function selectLimitDB($koneksi, $sql,$numrow=-1,$offset=-1){

		$sql = $koneksi->_limit($sql, $numrow, $offset);
		$sql = $koneksi->query($sql);
		if(!$rs){
		}
		return $rs;
    }

  }
?>
