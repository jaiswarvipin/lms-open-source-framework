<?php 
/***********************************************************************/
/* Purpose 		: Processing the DML, DDL request.
/* Created By 	: Jaiswar Vipin Kumar R.
/***********************************************************************/
defined('BASEPATH') OR exit('No direct script access allowed');

class Dbrequestprocess_model extends CI_Model{

	/********************************************************************/
	/* Purpose 		: Initiating the Default CI Model properties and methods
	/* Inputs		: None.
	/* Returns 		: None.
	/* Created By 	: Jaiswar Vipin Kumar R.
	/********************************************************************/
	public function __construct(){
		/* calling CI MODEL class constructor */
		parent::__construct();
	}
	
	/********************************************************************/
	/* Purpose 		: Execute the requested query.
	/* Inputs		: $pStrQuery	= Query string.
	/* Returns 		: Updated status.
	/* Created By 	: Jaiswar Vipin Kumar R.
	/********************************************************************/
	public function getDirectQueryResult($pStrQuery = ''){
		/* variable  initialization */
		$strReturnValue	= '';
		
		/* if query is not passed then do needful */
		if($pStrQuery == ''){
			return -1;
		}
		/* execute direct query */
		$strReturnValue	= $this->db->query($pStrQuery);
		
		$this->_getlastQuery();
		/* Return the value */
		return $strReturnValue;
	}

	/********************************************************************/
	/* Purpose 		: get data from requested from single tables.
	/* Inputs		: $pStrDataArr	= data filter.
	/* Returns 		: Requested data set.
	/* Created By 	: Jaiswar Vipin Kumar R.
	/********************************************************************/
	public function getDataFromTable($pStrDataArr = array()){
		/* variable initialization */
		$strReturnArr	= array();
		/* if data is empty then do needful */
		if(empty($pStrDataArr) || (!isset($pStrDataArr['table']))){
			/* Return empty array */
			return $strReturnArr;
		}
		
		/* Creating column string */
		$strColumnName	= isset($pStrDataArr['column'])?implode(',',$pStrDataArr['column']):'*';
		
		/* multiple table join */
		if(is_array($pStrDataArr['table'])){
			/* Creating join table */
			$this->_setMultipleTableJoin($pStrDataArr['table'], $pStrDataArr['join'], $strColumnName);
		}else{
			/* get the data set from singleton table */
			$this->db->select($strColumnName)->from($pStrDataArr['table']);
		}
		
		/* Setting filter */
		$this->_setFilterCaluse($pStrDataArr);
		
		/* Setting limit and offset */
		if(isset($pStrDataArr['limit'])){
			/* Setting limit and offset */
			$this->db->limit($pStrDataArr['limit'], $pStrDataArr['offset']);
		}
		
		/* Setting Order By */
		if(isset($pStrDataArr['order']) && (!empty($pStrDataArr['order']))){
			/* Iterating the loop */
			foreach($pStrDataArr['order'] as $pStrDataArrKey => $pStrDataArrValue){
				/* Setting limit and offset */
				$this->db->order_by($pStrDataArrKey, $pStrDataArrValue);
			}
		}
		
		/* Get records from tables */
		$strReturnArr	= $this->db->get()->result_array();

		/* On demand printing the query */
		$this->_getlastQuery();

		/* Return the Dataset */
		return $strReturnArr;
	}

	/********************************************************************/
	/* Purpose 		: set data from requested tables.
	/* Inputs		: $pStrDataArr	= Data set.
	/* Returns 		: Requested data set.
	/* Created By 	: Jaiswar Vipin Kumar R.
	/********************************************************************/
	public function setDataInTable($pStrDataArr = array()){
		/* variable initialization */
		$intInsertedCode	= 0;

		/* if data set is empty then do needful */
		if((empty($pStrDataArr)) || (!isset($pStrDataArr['table']))){
			/* Return empty code */
			return $intInsertedCode;
		}
		
		/* if time stamp is not set then do needful */
		if(!isset($pStrDataArr['data']['record_date'])){
			/* Setting time stamp */
			$pStrDataArr['data']['record_date']	= date('YmdHis');
		}

		/* Checking updated by clause */
		if(!isset($pStrDataArr['data']['updated_by'])){
			/* Setting time stamp */
			$pStrDataArr['data']['updated_by']	= 0;
			/* If session is created then do needful */
			if(isset($_SESSION['userCode'])){
				/* Setting time stamp */
				$pStrDataArr['data']['updated_by']	= $_SESSION['userCode'];
			}
		}

		/* Adding record in data base */
		$this->db->insert($pStrDataArr['table'], $pStrDataArr['data']);

		$this->_getlastQuery();
		
		/* Getting last inserted code */
		$intInsertedCode	= $this->db->insert_id();
		/* Return last inserted code */
		return $intInsertedCode;
	}	

	/********************************************************************/
	/* Purpose 		: Updated date in the table.
	/* Inputs		: $pStrDataArr	= Data set.
	/* Returns 		: Updated status.
	/* Created By 	: Jaiswar Vipin Kumar R.
	/********************************************************************/
	public function setUpdateData($pStrDataArr = array()){
		/* Variable initialization */
		$intUpdatedRecord = 0;

		/* if data set is empty then do needful */
		if(empty($pStrDataArr)){
			/* Return empty value */
			return $intUpdatedRecord;
		}

		/* if data table is not set then do needful */
		if(empty($pStrDataArr) || (!isset($pStrDataArr['table']))){
			/* Return empty array */
			return $intUpdatedRecord;
		}

		/* if data there need to updated is not set then do needful */
		if(empty($pStrDataArr) || (!isset($pStrDataArr['data']))){
			/* Return empty array */
			return $intUpdatedRecord;
		}

		/* if data updated filter clause is not set then do needful */
		if(empty($pStrDataArr) || (!isset($pStrDataArr['where']))){
			/* Return empty array */
			return $intUpdatedRecord;
		}

		/* Checking updated by clause */
		if(!isset($pStrDataArr['data']['updated_date'])){
			/* Setting time stamp */
			$pStrDataArr['data']['updated_date']	= date('YmdHis');
		}

		/* Setting the filter clause */
		$this->_setFilterCaluse($pStrDataArr);
		/* Setting data needs to updated */
		$this->db->set($pStrDataArr['data']);
		/* Setting the table name */
		$this->db->update($pStrDataArr['table']);

		/* printing query log */
		$this->_getlastQuery();

		/* Checking for number of record updated */
		$intUpdatedRecord	= $this->db->affected_rows();

		/* Return number of record set */
		return $intUpdatedRecord;
	}

	/********************************************************************/
	/* Purpose 		: Setting filter clause to existing statement.
	/* Inputs		: $pStrFilterArr :: Filter value array.
	/* Returns 		: None.
	/* Created By 	: Jaiswar Vipin Kumar R.
	/********************************************************************/
	private function _setFilterCaluse($pStrFilterArr	= array()){
		/* if filter is set then do needful */
		if((isset($pStrFilterArr['where'])) && (!empty($pStrFilterArr['where']))){
			/* iterating the loop */
			foreach($pStrFilterArr['where'] as $pStrFilterArrKey => $pStrFilterArrValue){
				/* Setting filter */
				if(is_array($pStrFilterArrValue)){
					$this->db->where_in($pStrFilterArrKey, $pStrFilterArrValue);
				/*Checking for like clause */
				}else if(strstr($pStrFilterArrKey,'like')!=''){
					/*Checking for like clause */
					$this->db->like(str_replace('like','',$pStrFilterArrKey), $pStrFilterArrValue);
				/* Setting filter */
				}else{
					/* Setting filter */
					$this->db->where($pStrFilterArrKey, $pStrFilterArrValue);
				}
			}
		}

		/* if table name is set then do needful */
		if((isset($pStrFilterArr['table'])) && (!empty($pStrFilterArr['table']))){
			/* if more then one table to do needful; */
			if(is_array($pStrFilterArr['table'])){
				/* iterating the loop */
				foreach($pStrFilterArr['table'] as $pStrFilterArrKey => $pStrFilterArrValue){
					/* Checking for aliease */
					if(strstr($pStrFilterArrValue,' as ')!=''){
						/* Creating Array */
						$pStrFilterArrValue	= explode(' as ',$pStrFilterArrValue);
						/* Setting aliease column name */
						$pStrFilterArrValue	= $pStrFilterArrValue[1];
					}
					/* Setting filter */
					$this->db->where($pStrFilterArrValue.'.deleted', 0);
				}
			/* for single table */
			}else{
				/* Setting filter */
				$this->db->where($pStrFilterArr['table'].'.deleted', 0);
			}
		}
	}
	
	/********************************************************************/
	/* Purpose 		: Setting filter clause to existing statement.
	/* Inputs		: $pStrMultipleTable :: Multiple table array,
					: $pStrJoinArr :: Table Join array,
					: $pStrColumnName :: Column name.
	/* Returns 		: None.
	/* Created By 	: Jaiswar Vipin Kumar R.
	/********************************************************************/
	private function _setMultipleTableJoin($pStrMultipleTable = array(), $pStrJoinArr = array(), $pStrColumnName = '*' ){
		/* if multiple table or join is not available then do needful */
		if(empty($pStrMultipleTable) ||empty($pStrJoinArr)){
			/* do not process ahead */
			return;
		}
		
		/* if number of join value count is not equal to the Join then do needful */
		if(count($pStrMultipleTable) != count($pStrJoinArr)){
			/* do not process ahead */
			return;
		}
		
		/* Iterating the table loop */
		foreach($pStrMultipleTable as $strTableIndex => $strTableName){
			/* if first index then do needful */
			if($strTableIndex == 0){
				/* Setting table name */
				$this->db->select($pStrColumnName)->from($strTableName);
			}else{
				/* if join found and index is set then do needful */
				if((isset($pStrJoinArr[$strTableIndex])) && (!empty($pStrJoinArr[$strTableIndex]))){
					/* getting join string */
					$strJoinString = isset($pStrJoinArr[$strTableIndex])?$pStrJoinArr[$strTableIndex]:'';
					
					/* if join string found then do needful */
					if(!empty($strJoinString)){
						/* variable initialization */
						$blnjoinTypeSet	= '';
						
						/* if array is set then do needful */
						if(is_array($strJoinString)){
							/* checking for join string */
							$strJoinString	= isset($pStrJoinArr[$strTableIndex]['table'])?$pStrJoinArr[$strTableIndex]['table']:'';
							/* checking for join type */
							$blnjoinTypeSet	= isset($pStrJoinArr[$strTableIndex]['type'])?$pStrJoinArr[$strTableIndex]['type']:'';
						}
						
						/* if join type is not set then do needful */
						if($blnjoinTypeSet == ''){
							/* Setting join table name */
							$this->db->join($strTableName, $strJoinString);
						}else{
							/* Setting join table name with type */
							$this->db->join($strTableName, $strJoinString, $blnjoinTypeSet);
						}
					}
				}
			}
		}
	}
	
	/********************************************************************/
	/* Purpose 		: Checking table exists or not.
	/* Inputs		: $pStrTabelName :: Table name.
	/* Returns 		: TRUE / FALSE.
	/* Created By 	: Jaiswar Vipin Kumar R.
	/********************************************************************/
	public function isTableExists($pStrTabelName = ''){
		/* if table name is passed then do needful */
		if((trim($pStrTabelName) == '') || (!$this->db->table_exists($pStrTabelName))){
			return false;
		}else{
			return true;
		}
		
	}
	
	/********************************************************************/
	/* Purpose 		: Checking table filed exists or not.
	/* Inputs		: $pStrTabelName :: Table name,
					: $pStrFiledName :: Filed name.
	/* Returns 		: TRUE / FALSE.
	/* Created By 	: Jaiswar Vipin Kumar R.
	/********************************************************************/
	public function isFiledExists($pStrTabelName = '', $pStrFiledName = ''){
		/* if table name is passed then do needful */
		if((trim($pStrTabelName) == '') || ($pStrFiledName == '')){
			return true;
		}else if(!$this->db->field_exists($pStrFiledName, $pStrTabelName)){
			return false;
		}else{
			return true;
		}
	}
	
	/********************************************************************/
	/* Purpose 		: Printing last query based on cookies set.
	/* Inputs		: None.
	/* Returns 		: None.
	/* Created By 	: Jaiswar Vipin Kumar R.
	/********************************************************************/
	private function _getlastQuery(){
		/* if cookies is set then do needful */
		if(isset($_COOKIE['debug'])){
			debugVar($this->db->last_query());
		}
	}
}
?>