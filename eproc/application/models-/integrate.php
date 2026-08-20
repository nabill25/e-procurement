<?php

include_once('entity.php');

class Integrate extends Entity{ 

	var $query;

    function __construct(){
	  parent::__construct();
	}
	
	function updateStatus()
	{
		$str = "UPDATE INTEGRATION_ORACLE_LOGS SET
				  send_status = '".$this->getField("SEND_STATUS")."',
				  send_note = '".$this->getField("SEND_NOTE")."',
				  send_date = CURRENT_TIMESTAMP
				WHERE SEND_STATUS IN ('0','2')
				"; 
				$this->query = $str;
		return $this->execQuery($str);
    }   

    function insert()
	{
		$this->setField("INTEGRATION_LOGS_ID", $this->getNextId("INTEGRATION_LOGS_ID","INTEGRATION_ORACLE_LOGS")); 
		$str = "
			INSERT INTO INTEGRATION_ORACLE_LOGS (
			   INTEGRATION_LOGS_ID, TYPE, FILE_NAME, FILE_PATH, NOTE, CREATED_DATE)
 
  			 	VALUES (
				  '".$this->getField("INTEGRATION_LOGS_ID")."',
  				  '".$this->getField("TYPE")."',
   				  '".$this->getField("FILE_NAME")."',
				  '".$this->getField("FILE_PATH")."', 
				  '".$this->getField("NOTE")."', 
				  CURRENT_TIMESTAMP
				)"; 
				
		$this->query = $str;
		$this->id = $this->getField("INTEGRATION_LOGS_ID");
		return $this->execQuery($str);
    }  

    function insertLog()
	{
		$this->setField("INTEGRATION_ID", $this->getNextId("INTEGRATION_ID","INTEGRATION_ORACLE")); 
		$str = "
			INSERT INTO INTEGRATION_ORACLE (
			   INTEGRATION_ID, REKANAN_ID, STATUS_GENERATE_EXCEL, FILE_NAME, CREATED_DATE)
 
  			 	VALUES (
				  '".$this->getField("INTEGRATION_ID")."',
  				  '".$this->getField("REKANAN_ID")."',
   				  '".$this->getField("STATUS_GENERATE_EXCEL")."',
				  '".$this->getField("FILE_NAME")."', 
				  CURRENT_TIMESTAMP
				)"; 
				
		$this->query = $str;
		$this->id = $this->getField("INTEGRATION_ID");
		return $this->execQuery($str);
    }  

    function insertLogPO()
	{
		$this->setField("INTEGRATION_PO_ID", $this->getNextId("INTEGRATION_PO_ID","INTEGRATION_ORACLE_PO")); 
		$str = "
			INSERT INTO INTEGRATION_ORACLE_PO (
			   INTEGRATION_PO_ID, PAKET_ID, STATUS_GENERATE_EXCEL, FILE_NAME, CREATED_DATE)
 
  			 	VALUES (
				  '".$this->getField("INTEGRATION_PO_ID")."',
  				  '".$this->getField("PAKET_ID")."',
   				  '".$this->getField("STATUS_GENERATE_EXCEL")."',
				  '".$this->getField("FILE_NAME")."', 
				  CURRENT_TIMESTAMP
				)"; 
		$this->query = $str;
		$this->id = $this->getField("INTEGRATION_PO_ID");
		return $this->execQuery($str);
    }   

    function selectByParams($paramsArray=array(),$limit,$from, $statement='')
	{
		$str = "SELECT * 
				FROM INTEGRATION_ORACLE A
				WHERE 1 = 1".$statement; 
		while(list($key,$val)=each($paramsArray))
		{
			$str .= " AND $key = '$val' ";
		}
		
		$this->query = $str;
		$str .= $statement;
				
		return $this->selectLimit($str,$limit,$from); 
    }

    function selectByParamsPRHeader($paramsArray=array(),$limit,$from, $statement='')
	{
		$str = "SELECT A.* 
				FROM INTEGRATION_IMPORT_PR_HEADER A
				WHERE 1 = 1".$statement; 
		while(list($key,$val)=each($paramsArray))
		{
			$str .= " AND $key = '$val' ";
		}
		// echo $str;
		$this->query = $str;
		$str .= $statement;
				
		return $this->selectLimit($str,$limit,$from); 
    }

    function selectByParamsPRHeaderA($paramsArray=array(),$limit,$from, $statement='')
	{
		$str = "SELECT A.* 
				FROM INTEGRATION_IMPORT_PR_HEADER_ATTACHMENT A
				WHERE 1 = 1".$statement; 
		while(list($key,$val)=each($paramsArray))
		{
			$str .= " AND $key = '$val' ";
		}
		// echo $str;
		$this->query = $str;
		$str .= $statement;
				
		return $this->selectLimit($str,$limit,$from); 
    }

    function selectByParamsPRHeaderLine($paramsArray=array(),$limit,$from, $statement='')
	{
		$str = "SELECT A.* 
				FROM INTEGRATION_IMPORT_PR_LINE A
				WHERE 1 = 1".$statement; 
		while(list($key,$val)=each($paramsArray))
		{
			$str .= " AND $key = '$val' ";
		}
		// echo $str;
		$this->query = $str;
		$str .= $statement;
				
		return $this->selectLimit($str,$limit,$from); 
    }

    function selectByParamsPRDistribution($paramsArray=array(),$limit,$from, $statement='')
	{
		$str = "SELECT A.* 
				FROM INTEGRATION_IMPORT_PR_DISTRIBUTION A
				WHERE 1 = 1".$statement; 
		while(list($key,$val)=each($paramsArray))
		{
			$str .= " AND $key = '$val' ";
		}
		// echo $str;
		$this->query = $str;
		$str .= $statement;
				
		return $this->selectLimit($str,$limit,$from); 
    }

    function selectByParamsRKA($paramsArray=array(),$limit,$from, $statement='')
	{
		$str = "SELECT * 
				FROM INTEGRATION_IMPORT_RKA_BUDGET A
				WHERE 1 = 1".$statement; 
		while(list($key,$val)=each($paramsArray))
		{
			$str .= " AND $key = '$val' ";
		}
		
		$this->query = $str;
		$str .= $statement;
				
		return $this->selectLimit($str,$limit,$from); 
    }


    function selectByParamsViewIntegrasi($paramsArray=array(),$limit,$from, $statement='')
	{
		$str = "SELECT * 
				FROM VIEW_INTEGRASI_ORACLE A
				WHERE 1 = 1".$statement; 
		while(list($key,$val)=each($paramsArray))
		{
			$str .= " AND $key = '$val' ";
		}
		
		$this->query = $str;
		$str .= $statement;
				
		return $this->selectLimit($str,$limit,$from); 
    }

    function selectByParamsViewIntegrasiIjinUsaha($paramsArray=array(),$limit,$from, $statement='')
	{
		$str = "SELECT * 
				FROM VIEW_INTEGRASI_ORACLE_IJIN_USAHA A
				WHERE 1 = 1".$statement; 
		while(list($key,$val)=each($paramsArray))
		{
			$str .= " AND $key = '$val' ";
		}
		
		$this->query = $str;
		$str .= $statement;
				
		return $this->selectLimit($str,$limit,$from); 
    }

    function selectByParamsViewIntegrasiPO($paramsArray=array(),$limit,$from, $statement='')
	{
		$str = "SELECT * 
				FROM VIEW_INTEGRASI_ORACLE_PAKET_SELESAI A
				WHERE 1 = 1".$statement; 
		while(list($key,$val)=each($paramsArray))
		{
			$str .= " AND $key = '$val' ";
		}
		
		$this->query = $str;
		$str .= $statement;
				
		return $this->selectLimit($str,$limit,$from); 
    }

    function selectByParamsLogs($paramsArray=array(),$limit,$from, $statement='')
	{
		$str = "SELECT * 
				FROM INTEGRATION_ORACLE_LOGS A
				WHERE 1 = 1"; 
		while(list($key,$val)=each($paramsArray))
		{
			$str .= " AND $key = '$val' ";
		}
		
		$str .= $statement;
		$this->query = $str;

		return $this->selectLimit($str,$limit,$from); 
    }

   
    function getCountByParamsLogs($paramsArray=array(), $statement='')
	{
		$str = "SELECT COUNT(A.INTEGRATION_LOGS_ID) AS ROWCOUNT 
				FROM INTEGRATION_ORACLE_LOGS A
				WHERE 1 = 1".$statement; 
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

    // IMPORT PR
    function insertPRHeader()
	{

		$this->setField("INTEGRATION_IMPORT_PR_HEADER_ID", $this->getNextId("INTEGRATION_IMPORT_PR_HEADER_ID","INTEGRATION_IMPORT_PR_HEADER")); 
		$str = "
			INSERT INTO INTEGRATION_IMPORT_PR_HEADER (
			   INTEGRATION_IMPORT_PR_HEADER_ID, REQUISITION_HEADER_ID, IMPORT_OPERATION, CREATION_DATE, LAST_UPDATE_DATE, REQUISITION_NUMBER, DESCRIPTION, REQ_BU_ID, BU_NAME, DOCUMENT_STATUS, PR_TYPE, METODE_PENGADAAN, JENIS_ANGGARAN, IMPORT_FILE, NOMOR_RUP, SUBDIVISI, IMPORT_DATE)
 
  			 	VALUES (
				  '".$this->getField("INTEGRATION_IMPORT_PR_HEADER_ID")."',
  				  ".$this->getField("REQUISITION_HEADER_ID").",
   				  '".$this->getField("IMPORT_OPERATION")."',
				  ".$this->getField("CREATION_DATE").", 
				  ".$this->getField("LAST_UPDATE_DATE").", 
				  '".$this->getField("REQUISITION_NUMBER")."', 
				  '".$this->getField("DESCRIPTION")."', 
				  ".$this->getField("REQ_BU_ID").", 
				  '".$this->getField("BU_NAME")."', 
				  '".$this->getField("DOCUMENT_STATUS")."', 
				  '".$this->getField("PR_TYPE")."', 
				  '".$this->getField("METODE_PENGADAAN")."', 
				  '".$this->getField("JENIS_ANGGARAN")."', 
				  '".$this->getField("IMPORT_FILE")."', 
				  '".$this->getField("NOMOR_RUP")."', 
				  '".$this->getField("SUBDIVISI")."', 
				  CURRENT_TIMESTAMP
				)"; 
				// echo $str; 
		$this->query = $str;
		$this->id = $this->getField("INTEGRATION_IMPORT_PR_HEADER_ID");
		return $this->execQuery($str);
    }  

    function updatePRHeader()
	{
		$str = "UPDATE INTEGRATION_IMPORT_PR_HEADER SET
				  IMPORT_OPERATION = '".$this->getField("IMPORT_OPERATION")."',
				  CREATION_DATE = ".$this->getField("CREATION_DATE").",
				  LAST_UPDATE_DATE = ".$this->getField("LAST_UPDATE_DATE").",
				  REQUISITION_NUMBER = '".$this->getField("REQUISITION_NUMBER")."',
				  DESCRIPTION = '".$this->getField("DESCRIPTION")."',
				  REQ_BU_ID = ".$this->getField("REQ_BU_ID").",
				  BU_NAME = '".$this->getField("BU_NAME")."',
				  DOCUMENT_STATUS = '".$this->getField("DOCUMENT_STATUS")."',
				  PR_TYPE = '".$this->getField("PR_TYPE")."',
				  METODE_PENGADAAN = '".$this->getField("METODE_PENGADAAN")."',
				  JENIS_ANGGARAN = '".$this->getField("JENIS_ANGGARAN")."',
				  NOMOR_RUP = '".$this->getField("NOMOR_RUP")."',
				  SUBDIVISI = '".$this->getField("SUBDIVISI")."',
				  IMPORT_FILE = '".$this->getField("IMPORT_FILE")."'
				WHERE REQUISITION_HEADER_ID = ".$this->getField("REQUISITION_HEADER_ID")."
				"; 
				$this->query = $str;
		return $this->execQuery($str);
    }  

    function insertPRHeaderA()
	{

		$this->setField("INTEGRATION_IMPORT_PR_HEADER_ATTACHMENT_ID", $this->getNextId("INTEGRATION_IMPORT_PR_HEADER_ATTACHMENT_ID","INTEGRATION_IMPORT_PR_HEADER_ATTACHMENT")); 
		$str = "
			INSERT INTO INTEGRATION_IMPORT_PR_HEADER_ATTACHMENT (
			   INTEGRATION_IMPORT_PR_HEADER_ATTACHMENT_ID, REQUISITION_HEADER_ID, ATTACHED_DOCUMENT_ID, IMPORT_OPERATION, CREATION_DATE, LAST_UPDATE_DATE, FILE_NAME, IMPORT_FILE, IMPORT_DATE)
 
  			 	VALUES (
				  '".$this->getField("INTEGRATION_IMPORT_PR_HEADER_ATTACHMENT_ID")."',
  				  ".$this->getField("REQUISITION_HEADER_ID").",
   				  ".$this->getField("ATTACHED_DOCUMENT_ID").",
				  '".$this->getField("IMPORT_OPERATION")."', 
				  ".$this->getField("CREATION_DATE").", 
				  ".$this->getField("LAST_UPDATE_DATE").", 
				  '".$this->getField("FILE_NAME")."', 
				  '".$this->getField("IMPORT_FILE")."', 
				  CURRENT_TIMESTAMP
				)"; 
				// echo $str; 
		$this->query = $str;
		$this->id = $this->getField("INTEGRATION_IMPORT_PR_HEADER_ATTACHMENT_ID");
		return $this->execQuery($str);
    }  

    function updatePRHeaderA()
	{
		$str = "UPDATE INTEGRATION_IMPORT_PR_HEADER_ATTACHMENT SET
				  ATTACHED_DOCUMENT_ID = ".$this->getField("ATTACHED_DOCUMENT_ID").",
				  IMPORT_OPERATION = '".$this->getField("IMPORT_OPERATION")."',
				  CREATION_DATE = ".$this->getField("CREATION_DATE").",
				  LAST_UPDATE_DATE = ".$this->getField("LAST_UPDATE_DATE").",
				  FILE_NAME = '".$this->getField("FILE_NAME")."',
				  IMPORT_FILE = '".$this->getField("IMPORT_FILE")."'
				WHERE REQUISITION_HEADER_ID = ".$this->getField("REQUISITION_HEADER_ID")."
				AND ATTACHED_DOCUMENT_ID = ".$this->getField("ATTACHED_DOCUMENT_ID")."
				"; 
				// echo $str;
				$this->query = $str;
		return $this->execQuery($str);
    }  

    function insertPRLine()
	{
		$this->setField("INTEGRATION_IMPORT_PR_LINE_ID", $this->getNextId("INTEGRATION_IMPORT_PR_LINE_ID","INTEGRATION_IMPORT_PR_LINE")); 
		$str = "
			INSERT INTO INTEGRATION_IMPORT_PR_LINE (
			   INTEGRATION_IMPORT_PR_LINE_ID, REQUISITION_HEADER_ID, REQUISITION_LINE_ID, IMPORT_OPERATION, 
			   CREATION_DATE, LAST_UPDATE_DATE, LINE_TYPE_ID, LINE_TYPE, ITEM_ID, ITEM_NUMBER, ITEM_DESCRIPTION, 
			   QUANTITY, UOM_CODE, UNIT_PRICE, AMOUNT, REQUESTER_ID, REQUESTER_NAME, DELIVER_TO_LOCATION_ID, 
			   DELIVER_TO_LOCATION, DESTINATION_TYPE_CODE, DESTINATION_ORGANIZATION_ID, DESTINATION_ORGANIZATION, 
			   URGENT_FLAG, LINE_STATUS, IMPORT_FILE, IMPORT_DATE)
 
  			 	VALUES (
				  '".$this->getField("INTEGRATION_IMPORT_PR_LINE_ID")."',
  				  ".$this->getField("REQUISITION_HEADER_ID").",
   				  ".$this->getField("REQUISITION_LINE_ID").",
				  '".$this->getField("IMPORT_OPERATION")."', 
				  ".$this->getField("CREATION_DATE").", 
				  ".$this->getField("LAST_UPDATE_DATE").", 
				  ".$this->getField("LINE_TYPE_ID").", 
				  '".$this->getField("LINE_TYPE")."', 
				  ".$this->getField("ITEM_ID").", 
				  '".$this->getField("ITEM_NUMBER")."', 
				  '".$this->getField("ITEM_DESCRIPTION")."', 
				  ".$this->getField("QUANTITY").", 
				  '".$this->getField("UOM_CODE")."', 
				  ".$this->getField("UNIT_PRICE").",
				  ".$this->getField("AMOUNT").",
				  ".$this->getField("REQUESTER_ID").",
				  '".$this->getField("REQUESTER_NAME")."', 
				  ".$this->getField("DELIVER_TO_LOCATION_ID").", 
				  '".$this->getField("DELIVER_TO_LOCATION")."', 
				  '".$this->getField("DESTINATION_TYPE_CODE")."', 
				  ".$this->getField("DESTINATION_ORGANIZATION_ID").", 
				  '".$this->getField("DESTINATION_ORGANIZATION")."', 
				  '".$this->getField("URGENT_FLAG")."', 
				  '".$this->getField("LINE_STATUS")."', 
				  '".$this->getField("IMPORT_FILE")."', 
				  CURRENT_TIMESTAMP
				)"; 
				// echo $str; 
		$this->query = $str;
		$this->id = $this->getField("INTEGRATION_IMPORT_PR_LINE_ID");
		return $this->execQuery($str);
    }  

    function updatePRLine()
	{
		$str = "UPDATE INTEGRATION_IMPORT_PR_LINE SET
				  IMPORT_OPERATION = '".$this->getField("IMPORT_OPERATION")."',
				  CREATION_DATE = ".$this->getField("CREATION_DATE").",
				  LAST_UPDATE_DATE = ".$this->getField("LAST_UPDATE_DATE").",
				  LINE_TYPE_ID = ".$this->getField("LINE_TYPE_ID").",
				  LINE_TYPE = '".$this->getField("LINE_TYPE")."',
				  ITEM_ID = ".$this->getField("ITEM_ID").",
				  ITEM_NUMBER = '".$this->getField("ITEM_NUMBER")."',
				  ITEM_DESCRIPTION = '".$this->getField("ITEM_DESCRIPTION")."',
				  QUANTITY = ".$this->getField("QUANTITY").",
				  UOM_CODE = '".$this->getField("UOM_CODE")."',
				  UNIT_PRICE = ".$this->getField("UNIT_PRICE").",
				  AMOUNT = ".$this->getField("AMOUNT").",
				  REQUESTER_ID = ".$this->getField("REQUESTER_ID").",
				  REQUESTER_NAME = '".$this->getField("REQUESTER_NAME")."',
				  DELIVER_TO_LOCATION_ID = ".$this->getField("DELIVER_TO_LOCATION_ID").",
				  DELIVER_TO_LOCATION = '".$this->getField("DELIVER_TO_LOCATION")."',
				  DESTINATION_TYPE_CODE = '".$this->getField("DESTINATION_TYPE_CODE")."',
				  DESTINATION_ORGANIZATION_ID = ".$this->getField("DESTINATION_ORGANIZATION_ID").",
				  DESTINATION_ORGANIZATION = '".$this->getField("DESTINATION_ORGANIZATION")."',
				  URGENT_FLAG = '".$this->getField("URGENT_FLAG")."',
				  LINE_STATUS = '".$this->getField("LINE_STATUS")."', 
				  IMPORT_FILE = '".$this->getField("IMPORT_FILE")."'
				WHERE REQUISITION_HEADER_ID = ".$this->getField("REQUISITION_HEADER_ID")."
				AND REQUISITION_LINE_ID = ".$this->getField("REQUISITION_LINE_ID")."
				"; 
				// echo $str;
				$this->query = $str;
		return $this->execQuery($str);
    }  

    function insertPRDistribution()
	{
		$this->setField("INTEGRATION_IMPORT_PR_DISTRIBUTION_ID", $this->getNextId("INTEGRATION_IMPORT_PR_DISTRIBUTION_ID","INTEGRATION_IMPORT_PR_DISTRIBUTION")); 
		$str = "
			INSERT INTO INTEGRATION_IMPORT_PR_DISTRIBUTION (
			INTEGRATION_IMPORT_PR_DISTRIBUTION_ID, REQUISITION_LINE_ID, REQUISITION_DISTRIBUTION_ID,
			IMPORT_OPERATION, CREATION_DATE, LAST_UPDATE_DATE, CODE_COMBINATION_ID, COA_SEGMENT1, COA_SEGMENT2, 
			COA_SEGMENT3, COA_SEGMENT4, COA_SEGMENT5, COA_SEGMENT6, COA_SEGMENT7, DISTRIBUTION_QUANTITY, 
			DISTRIBUTION_AMOUNT, PJC_PROJECT_ID, PROJECT_NUMBER, PJC_TASK_ID, TASK_NUMBER, 
			PJC_EXPENDITURE_TYPE_ID, EXPENDITURE_TYPE_NAME, EXPENDITURE_ITEM_DATE, 
			IMPORT_FILE, IMPORT_DATE)
  			 	VALUES (
				  '".$this->getField("INTEGRATION_IMPORT_PR_DISTRIBUTION_ID")."',
  				  ".$this->getField("REQUISITION_LINE_ID").",
   				  ".$this->getField("REQUISITION_DISTRIBUTION_ID").",
				  '".$this->getField("IMPORT_OPERATION")."', 
				  ".$this->getField("CREATION_DATE").", 
				  ".$this->getField("LAST_UPDATE_DATE").", 
				  ".$this->getField("CODE_COMBINATION_ID").", 
				  '".$this->getField("COA_SEGMENT1")."', 
				  '".$this->getField("COA_SEGMENT2")."', 
				  '".$this->getField("COA_SEGMENT3")."', 
				  '".$this->getField("COA_SEGMENT4")."', 
				  '".$this->getField("COA_SEGMENT5")."', 
				  '".$this->getField("COA_SEGMENT6")."', 
				  '".$this->getField("COA_SEGMENT7")."',
				  ".$this->getField("DISTRIBUTION_QUANTITY").",
				  ".$this->getField("DISTRIBUTION_AMOUNT").",
				  ".$this->getField("PJC_PROJECT_ID").", 
				  '".$this->getField("PROJECT_NUMBER")."', 
				  ".$this->getField("PJC_TASK_ID").", 
				  '".$this->getField("TASK_NUMBER")."', 
				  ".$this->getField("PJC_EXPENDITURE_TYPE_ID").", 
				  '".$this->getField("EXPENDITURE_TYPE_NAME")."', 
				  '".$this->getField("EXPENDITURE_ITEM_DATE")."', 
				  '".$this->getField("IMPORT_FILE")."', 
				  CURRENT_TIMESTAMP
				)"; 
				// echo $str; 
		$this->query = $str;
		$this->id = $this->getField("INTEGRATION_IMPORT_PR_LINE_ID");
		return $this->execQuery($str);
    }  

    function updatePRDistribution()
	{
		$str = "UPDATE INTEGRATION_IMPORT_PR_DISTRIBUTION SET
				  IMPORT_OPERATION = '".$this->getField("IMPORT_OPERATION")."',
				  CREATION_DATE = ".$this->getField("CREATION_DATE").",
				  LAST_UPDATE_DATE = ".$this->getField("LAST_UPDATE_DATE").",
				  CODE_COMBINATION_ID = ".$this->getField("CODE_COMBINATION_ID").",
				  COA_SEGMENT1 = '".$this->getField("COA_SEGMENT1")."',
				  COA_SEGMENT2 = '".$this->getField("COA_SEGMENT2")."',
				  COA_SEGMENT3 = '".$this->getField("COA_SEGMENT3")."',
				  COA_SEGMENT4 = '".$this->getField("COA_SEGMENT4")."',
				  COA_SEGMENT5 = '".$this->getField("COA_SEGMENT5")."',
				  COA_SEGMENT6 = '".$this->getField("COA_SEGMENT6")."',
				  COA_SEGMENT7 = '".$this->getField("COA_SEGMENT7")."',
				  DISTRIBUTION_QUANTITY = ".$this->getField("DISTRIBUTION_QUANTITY").",
				  DISTRIBUTION_AMOUNT = ".$this->getField("DISTRIBUTION_AMOUNT").",
				  PJC_PROJECT_ID = ".$this->getField("PJC_PROJECT_ID").",
				  PROJECT_NUMBER = '".$this->getField("PROJECT_NUMBER")."',
				  PJC_TASK_ID = ".$this->getField("PJC_TASK_ID").",
				  TASK_NUMBER = '".$this->getField("TASK_NUMBER")."',
				  PJC_EXPENDITURE_TYPE_ID = ".$this->getField("PJC_EXPENDITURE_TYPE_ID").",
				  EXPENDITURE_TYPE_NAME = '".$this->getField("EXPENDITURE_TYPE_NAME")."',
				  EXPENDITURE_ITEM_DATE = '".$this->getField("EXPENDITURE_ITEM_DATE")."',
				  IMPORT_FILE = '".$this->getField("IMPORT_FILE")."'
				WHERE REQUISITION_LINE_ID = ".$this->getField("REQUISITION_LINE_ID")."
				AND REQUISITION_DISTRIBUTION_ID = ".$this->getField("REQUISITION_DISTRIBUTION_ID")."
				"; 
				// echo $str;
				$this->query = $str;
		return $this->execQuery($str);
    }  

    function insertRKA()
	{
		$this->setField("INTEGRATION_IMPORT_RKA_BUDGET_ID", $this->getNextId("INTEGRATION_IMPORT_RKA_BUDGET_ID","INTEGRATION_IMPORT_RKA_BUDGET")); 
		$str = "
			INSERT INTO INTEGRATION_IMPORT_RKA_BUDGET (
			INTEGRATION_IMPORT_RKA_BUDGET_ID, RKA_KEY, BUDGET_CODE_COMBINATION_ID,
			START_DATE_YEAR, IMPORT_OPERATION, LAST_UPDATE_DATE, SEGMENT1, SEGMENT1_DESC, SEGMENT2, 
			SEGMENT2_DESC, SEGMENT3, SEGMENT3_DESC, SEGMENT4, SEGMENT4_DESC, SEGMENT5, 
			SEGMENT5_DESC, SEGMENT6, SEGMENT6_DESC, SEGMENT7, SEGMENT7_DESC, 
			BUDGET_AMT, REMAIN_AMT, IMPORT_FILE, IMPORT_DATE)
  			 	VALUES (
				  '".$this->getField("INTEGRATION_IMPORT_RKA_BUDGET_ID")."',
  				  '".$this->getField("RKA_KEY")."',
   				  ".$this->getField("BUDGET_CODE_COMBINATION_ID").",
				  ".$this->getField("START_DATE_YEAR").",
				  '".$this->getField("IMPORT_OPERATION")."', 
				  ".$this->getField("LAST_UPDATE_DATE").", 
				  '".$this->getField("SEGMENT1")."', 
				  '".$this->getField("SEGMENT1_DESC")."', 
				  '".$this->getField("SEGMENT2")."', 
				  '".$this->getField("SEGMENT2_DESC")."', 
				  '".$this->getField("SEGMENT3")."', 
				  '".$this->getField("SEGMENT3_DESC")."', 
				  '".$this->getField("SEGMENT4")."', 
				  '".$this->getField("SEGMENT4_DESC")."',
				  '".$this->getField("SEGMENT5")."',
				  '".$this->getField("SEGMENT5_DESC")."',
				  '".$this->getField("SEGMENT6")."', 
				  '".$this->getField("SEGMENT6_DESC")."', 
				  '".$this->getField("SEGMENT7")."', 
				  '".$this->getField("SEGMENT7_DESC")."', 
				  ".$this->getField("BUDGET_AMT").", 
				  ".$this->getField("REMAIN_AMT").", 
				  '".$this->getField("IMPORT_FILE")."', 
				  CURRENT_TIMESTAMP
				)"; 
				// echo $str; 
		$this->query = $str;
		$this->id = $this->getField("INTEGRATION_IMPORT_RKA_BUDGET_ID");
		return $this->execQuery($str);
    }  

    function updateRKA()
	{ 
		$str = "UPDATE INTEGRATION_IMPORT_RKA_BUDGET SET
				  RKA_KEY = '".$this->getField("RKA_KEY")."',
				  BUDGET_CODE_COMBINATION_ID = ".$this->getField("BUDGET_CODE_COMBINATION_ID").",
				  START_DATE_YEAR = ".$this->getField("START_DATE_YEAR").",
				  IMPORT_OPERATION = '".$this->getField("IMPORT_OPERATION")."',
				  LAST_UPDATE_DATE = ".$this->getField("LAST_UPDATE_DATE").",
				  SEGMENT1 = '".$this->getField("SEGMENT1")."',
				  SEGMENT1_DESC = '".$this->getField("SEGMENT1_DESC")."',
				  SEGMENT2 = '".$this->getField("SEGMENT2")."',
				  SEGMENT2_DESC = '".$this->getField("SEGMENT2_DESC")."',
				  SEGMENT3 = '".$this->getField("SEGMENT3")."',
				  SEGMENT3_DESC = '".$this->getField("SEGMENT3_DESC")."',
				  SEGMENT4 = '".$this->getField("SEGMENT4")."',
				  SEGMENT4_DESC = '".$this->getField("SEGMENT4_DESC")."',
				  SEGMENT5 = '".$this->getField("SEGMENT5")."',
				  SEGMENT5_DESC = '".$this->getField("SEGMENT5_DESC")."',
				  SEGMENT6 = '".$this->getField("SEGMENT6")."',
				  SEGMENT6_DESC = '".$this->getField("SEGMENT6_DESC")."',
				  SEGMENT7 = '".$this->getField("SEGMENT7")."',
				  SEGMENT7_DESC = '".$this->getField("SEGMENT7_DESC")."',
				  BUDGET_AMT = ".$this->getField("BUDGET_AMT").",
				  REMAIN_AMT = ".$this->getField("REMAIN_AMT").",
				  IMPORT_FILE = '".$this->getField("IMPORT_FILE")."'
				WHERE BUDGET_CODE_COMBINATION_ID = ".$this->getField("BUDGET_CODE_COMBINATION_ID")."
				"; 
				// echo $str;
				$this->query = $str;
		return $this->execQuery($str);
    }  

  } 
?>