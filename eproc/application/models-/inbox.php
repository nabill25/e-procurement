<?php
/**
 * @package 	eProcurement Application
 * @author 		eproc2025
 * @since 		25. Version 3.1
 * 
 */

  include_once('entity.php');

  class Inbox extends Entity{

	var $query;
    /**
    * Class constructor.
    **/
    function __construct(){
  		parent::__construct();
  	}

    function Inbox()
	{
      $this->Entity();
    }

	function insert()
	{
		/*Auto-generate primary key(s) by next max value (integer) */
		$this->setField("INBOXID", $this->getNextId("INBOXID","INBOX"));

		$str = "
		INSERT INTO INBOX (INBOXID, INBOXCATEGORYID, INBOX_SUBJECT, INBOX_CONTENT, INBOX_FILE, INBOX_TO, INBOX_FROM, STATUS, PARENT, BROWSER, IP, CREATED_BY, CREATED_DATE, INBOX_FILE_NAMA, INBOX_FILE_TYPE, INBOX_FILE_SIZE)
			 	VALUES (
				  ".$this->getField("INBOXID").",
				  ".$this->getField("INBOXCATEGORYID").",
				  '".$this->getField("INBOX_SUBJECT")."',
				  '".$this->getField("INBOX_CONTENT")."',
				  '".$this->getField("INBOX_FILE")."',
				  '".$this->getField("INBOX_TO")."',
				  '".$this->getField("INBOX_FROM")."',
				  '".$this->getField("STATUS")."',
				  '".$this->getField("PARENT")."',
				  '".$this->getField("BROWSER")."',
				  '".$this->getField("IP")."',
				  ".$this->getField("CREATED_BY").",
				  '".$this->getField("CREATED_DATE")."',
				  '".$this->getField("INBOX_FILE_NAMA")."',
				  '".$this->getField("INBOX_FILE_TYPE")."',
				  '".$this->getField("INBOX_FILE_SIZE")."'
				)";
				// echo $str; die();
		$this->query = $str;
		return $this->execQuery($str);
    }

	function delete()
	{
        $str = "DELETE FROM INBOX
                WHERE
                  INBOXID = ".$this->getField("INBOXID")."";

		$this->query = $str;
        return $this->execQuery($str);
    }

    function selectByParams($paramsArray=array(),$limit=-1,$from=-1, $statement='')
	{
		$str = "SELECT a.inboxid, a.inbox_subject, b.ic_name, a.inbox_from, a.inbox_to, a.inbox_content, a.inboxcategoryid,
				a.status, a.parent, a.inbox_file, a.inbox_file_nama, a.inbox_file_size, a.inbox_file_type,
				a.created_by, c.user_nama created_by_str, a.created_date, a.read_by, a.read_date,
				a.browser, a.ip
				FROM INBOX a
				JOIN INBOX_CATEGORY b ON a.inboxcategoryid = b.inboxcategoryid
				LEFT JOIN USER_LOGIN c ON a.created_by = c.user_login_id
				where 1=1 ";

		while(list($key,$val) = each($paramsArray))
		{
			// ikn 20190218
		    $pecah = explode("||", $key);
		    if (count($pecah) > 1) {
		        $str .= "AND $pecah[0] $pecah[1] $val ";
		    } else {
		        $str .= " AND $key = '$val' ";
		    }
		}


		// $str .= $statement." ORDER BY NAMA ASC";
		$str .= $statement." ORDER BY a.inboxid DESC";
		$this->query = $str;
		return $this->selectLimit($str,$limit,$from);
    }

    function selectByPenerima($paramsArray=array(),$limit=-1,$from=-1, $statement='')
	{
		$str = "SELECT A.*,
				(select c.nama || ' ' || b.nama as aa
				 from rekanan b join rekanan_tipe c on b.rekanan_tipe_id=c.rekanan_tipe_id
				 where b.rekanan_id::varchar=A.penerima
				) as penerima_str
				 FROM (
					SELECT a.inboxid, a.inbox_subject, b.ic_name, a.inbox_from, a.inbox_to, a.inboxcategoryid, a.inbox_content,
					unnest(string_to_array(a.inbox_to, ',')) as penerima,
					a.status, a.parent, a.inbox_file, a.inbox_file_nama, a.inbox_file_size, a.inbox_file_type,
					a.created_by, c.user_nama created_by_str, a.created_date, a.read_by, a.read_date,
					a.browser, a.ip
					FROM INBOX a
					JOIN INBOX_CATEGORY b ON a.inboxcategoryid = b.inboxcategoryid
					LEFT JOIN USER_LOGIN c ON a.created_by = c.user_login_id
				) A
				where 1=1 ";

		while(list($key,$val) = each($paramsArray))
		{
			// ikn 20190218
		    $pecah = explode("||", $key);
		    if (count($pecah) > 1) {
		        $str .= "AND $pecah[0] $pecah[1] $val ";
		    } else {
		        $str .= " AND $key = '$val' ";
		    }
		}


		// $str .= $statement." ORDER BY NAMA ASC";
		$str .= $statement." ORDER BY a.inboxid DESC";
		// echo $str;
		$this->query = $str;
		return $this->selectLimit($str,$limit,$from);
    }

    function selectInboxComplainSet($paramsArray=array(),$limit=-1,$from=-1, $statement='')
	{
		$str = "SELECT a.ics_to
				FROM INBOX_COMPLAIN_SET a
				where 1=1 ";

		while(list($key,$val) = each($paramsArray))
		{
			// ikn 20190218
		    $pecah = explode("||", $key);
		    if (count($pecah) > 1) {
		        $str .= "AND $pecah[0] $pecah[1] $val ";
		    } else {
		        $str .= " AND $key = '$val' ";
		    }
		}
		// echo $str;
		$str .= $statement;
		$this->query = $str;
		return $this->selectLimit($str,$limit,$from);
    }

    function selectInboxComplainType($paramsArray=array(),$limit=-1,$from=-1, $statement='')
	{
		$str = "SELECT a.inboxcomplaintypeid, a.ict_name, a.ict_desc
				FROM INBOX_COMPLAIN_TYPE a
				where 1=1 ";

		while(list($key,$val) = each($paramsArray))
		{
			// ikn 20190218
		    $pecah = explode("||", $key);
		    if (count($pecah) > 1) {
		        $str .= "AND $pecah[0] $pecah[1] $val ";
		    } else {
		        $str .= " AND $key = '$val' ";
		    }
		}
		// echo $str;
		$str .= $statement;
		$this->query = $str;
		return $this->selectLimit($str,$limit,$from);
    }

    function getCountByParams($paramsArray=array())
	{
		$str = "SELECT COUNT(INBOXID) AS ROWCOUNT FROM INBOX WHERE INBOXID IS NOT NULL ";
		while(list($key,$val)=each($paramsArray))
		{
			$str .= " AND $key = '$val' ";
		}

		$this->select($str);
		if($this->firstRow())
			return $this->getField("ROWCOUNT");
		else
			return 0;
    }
  }
?>
