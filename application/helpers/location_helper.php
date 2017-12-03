<?php 
/*******************************************************************************/
/* Purpose 		: Managing the Company Location related request and response.
/* Created By 	: Jaiswar Vipin Kumar R.
/*******************************************************************************/
defined('BASEPATH') OR exit('No direct script access allowed');

class Location{
	private $_databaseObject	= null;
	private $_intCompanyCode	= 0;
	private $_strTableName		= "master_location";
	/***************************************************************************/
	/* Purpose	: Initialization
	/* Inputs 	: pDatabaesObjectRefrence :: Database object reference,
				: $pIntCompanyCode :: company code
	/* Returns	: None.
	/***************************************************************************/
	public function __construct($pDatabaesObjectRefrence, $pIntCompanyCode = 0){
		/* database reference */
		$this->_databaseObject	= $pDatabaesObjectRefrence;
		/* Company Code */
		$this->_intCompanyCode	= $pIntCompanyCode;
	}
	
	/***************************************************************************/
	/* Purpose	: get Location Details
	/* Inputs 	: pIntLocationType	:: Location type code,
	/*			: pIntLocationCode	:: Location code,
	/*			: pIntLocationParentCode :: Location Parent code.
	/* Returns	: Location details.
	/***************************************************************************/
	public function getLocationDetails($pIntLocationType = 1, $pIntLocationCode = array(), $pIntLocationParentCode = array()){
		/* Variable initialization */
		$strWhereArr	= array('company_code'=>$this->_intCompanyCode);
		
		/* if location code is passed then do needful */
		if(!empty($pIntLocationCode)){
			/* Setting Location id as filter details */
			$strWhereArr	= array_merge($strWhereArr , array('id' => $pIntLocationCode));
		}
		
		/* if parent location code is passed then do needful */
		if(!empty($pIntLocationParentCode)){
			/* Setting parent code as filter details */
			$strWhereArr	= array_merge($strWhereArr , array('parent_code' => $pIntLocationParentCode));
		}
		
		/* if above 2 cases is not valid the do needful */
		if(count($strWhereArr) >= 1){
			$strWhereArr	= array_merge($strWhereArr , array('location_type'=>$pIntLocationType));
		}
		
		/* Query builder Array */
		$strFilterArr	= array(
									'table'=>$this->_strTableName,
									'where'=>$strWhereArr,
									'column'=>array('id', 'description', 'parent_code')
							);
		
		/* getting record from location */
		return $this->_databaseObject->getDataFromTable($strFilterArr);
		
		/* removed used variables */
		unset($strFilterArr);
	}
	
	/***************************************************************************/
	/* Purpose	: get employee list by different location type.	
	/* Inputs 	: pIntLocationType	:: Location type code,
	/*			: pIntLocationCode	:: Location code,
	/*			: pIntLocationParentCode :: Location Parent code.
	/* Returns	: Location details.
	/***************************************************************************/
	public function getEmployeeByLocations($intLocationType = 1, $pIntLocationParentCodeArr = array()){
		/* Variable initialization */
		$strWhereArr	= array('zone.company_code'=>$this->_intCompanyCode);
		$strFilterArr	= array();
		
		/* Based on the location type develop the query */
		switch($intLocationType){
			/* ZONE */
			case 1:
				/* Setting where */
				$strWhereArr	= array_merge($strWhereArr , array('zone.id'=>$pIntLocationParentCodeArr));
				break;
			/* REGION */
			case 2:
				/* Setting where */
				$strWhereArr	= array_merge($strWhereArr , array('region.id'=>$pIntLocationParentCodeArr));
				break;
			/* CITY */
			case 3:
				/* Setting where */
				$strWhereArr	= array_merge($strWhereArr , array('city.id'=>$pIntLocationParentCodeArr));
				break;
			/* AREA */
			case 4:
				/* Setting where */
				$strWhereArr	= array_merge($strWhereArr , array('area.id'=>$pIntLocationParentCodeArr));
				break;
			/* BRANCH */
			case 5:
				/* Setting where */
				$strWhereArr	= array_merge($strWhereArr , array('branch.id'=>$pIntLocationParentCodeArr));
				break;
		}
		
		/* Setting Filter */
		$strFilterArr	= array(
									'table'=>array($this->_strTableName.' as zone',$this->_strTableName.' as region',$this->_strTableName.' as city',$this->_strTableName.' as area',$this->_strTableName.' as branch','trans_user_location','master_user','master_role'),
									'join'=>array('','zone.id = region.parent_code','region.id = city.parent_code','city.id = area.parent_code','area.id = branch.parent_code','branch.id = trans_user_location.branch_code','master_user.id = trans_user_location.user_code','master_user.role_code = master_role.id'),
									'column'=>array('zone.description zone_name','zone.id zone_code','region.description region_name','region.id region_code','city.description city_name','city.id city_code','area.description area_name','area.id area_code','branch.description branch_name','branch.id branch_code',"CONCAT(user_name, '-', master_role.description) as user_name",'master_user.id as user_code'),
									'where'=>$strWhereArr
							);
		
		/* getting record from location */
		return $this->_databaseObject->getDataFromTable($strFilterArr);
		
		/* removed used variables */
		unset($strFilterArr);
	}
	
	
	/***************************************************************************/
	/* Purpose	: get location list by zone code.	
	/* Inputs 	: pIntZoneArr	:: Zone code array.
	/* Returns	: Location details.
	/***************************************************************************/
	public function getLocationsByZoneCode($pIntZoneArr = array()){
		/* Zone Code is not passed then do needful */
		if(empty($pIntZoneArr)){
			/* Return empty array */
			return array();
		}
		
		/* Variable initialization */
		$strWhereArr	= array('zone.company_code'=>$this->_intCompanyCode,'zone.id'=>$pIntZoneArr);
		
		/* Setting Filter */
		$strFilterArr	= array(
									'table'=>array($this->_strTableName.' as zone',$this->_strTableName.' as region',$this->_strTableName.' as city',$this->_strTableName.' as area',$this->_strTableName.' as branch'),
									'join'=>array('','zone.id = region.parent_code','region.id = city.parent_code','city.id = area.parent_code','area.id = branch.parent_code'),
									'column'=>array('zone.description zone_name','zone.id zone_code','region.description region_name','region.id region_code','city.description city_name','city.id city_code','area.description area_name','area.id area_code','branch.description branch_name','branch.id branch_code'),
									'where'=>$strWhereArr
							);
		
		/* getting record from location */
		return $this->_databaseObject->getDataFromTable($strFilterArr);
		
		/* removed used variables */
		unset($strFilterArr);
	}
	
	/***************************************************************************/
	/* Purpose	: get location list by user code.	
	/* Inputs 	: pIntUserCode	:: user code.
	/* Returns	: Location details.
	/***************************************************************************/
	public function getLocationsByUserCode($pIntUserCode = 0){
		/* User code is not passed then do needful */
		if($pIntUserCode == 0 ){
			/* Return empty array */
			return array();
		}
		
		/* Company administrator */
		if($pIntUserCode == -1 ){
			/* Variable initialization */
			$strWhereArr	= array('zone.company_code'=>$this->_intCompanyCode);
			
			/* Setting Filter */
			$strFilterArr	= array(
										'table'=>array($this->_strTableName.' as zone',$this->_strTableName.' as region',$this->_strTableName.' as city',$this->_strTableName.' as area',$this->_strTableName.' as branch'),
										'join'=>array('','zone.id = region.parent_code','region.id = city.parent_code','city.id = area.parent_code','area.id = branch.parent_code'),
										'column'=>array('zone.description zone_name','zone.id zone_code','region.description region_name','region.id region_code','city.description city_name','city.id city_code','area.description area_name','area.id area_code','branch.description branch_name','branch.id branch_code'),
										'where'=>$strWhereArr
								);
			/* Setting the default user configured region and branch code */
		}else if($pIntUserCode == -2 ){
			/* Variable initialization */
			$strWhereArr	= array('company_code'=>$this->_intCompanyCode);
			
			/* Setting Filter */
			$strFilterArr	= array(
										'table'=>'master_user_config',
										'where'=>$strWhereArr
								);
		}else{
			/* Variable initialization */
			$strWhereArr	= array('zone.company_code'=>$this->_intCompanyCode,'trans_user_location.user_code'=>$pIntUserCode);
			
			/* Setting Filter */
			$strFilterArr	= array(
										'table'=>array($this->_strTableName.' as zone',$this->_strTableName.' as region',$this->_strTableName.' as city',$this->_strTableName.' as area',$this->_strTableName.' as branch','trans_user_location'),
										'join'=>array('','zone.id = region.parent_code','region.id = city.parent_code','city.id = area.parent_code','area.id = branch.parent_code','branch.id = trans_user_location.branch_code'),
										'column'=>array('zone.description zone_name','zone.id zone_code','region.description region_name','region.id region_code','city.description city_name','city.id city_code','area.description area_name','area.id area_code','branch.description branch_name','branch.id branch_code'),
										'where'=>$strWhereArr
								);
		}
		
		/* getting record from location */
		return $this->_databaseObject->getDataFromTable($strFilterArr);
		
		/* removed used variables */
		unset($strFilterArr);
	}
}