<?php
/* Check Un-authorize Access */
if(!defined('accessUser')) die("Error");

class PrintProfile extends UTIL {
	
	public function __construct(){
		parent::__construct();
		//$setting = Flight::setting();
	}
	
	/**
	*
	*date created (dd-mm-yy)
	*date modified 15-4-2016(dd-mm-yy)
	*Get all print methods
	*
	*@param (String)apikey
	*@param (String)name
	*@param (int)id
	*@return json data
	* 
	*/
	public function getAllPrintMethods(){
		try{
			$sql ='SELECT DISTINCT pmsr.print_method_id AS pk_id, pm.name, pm.file_type, pm.is_enable, ps.is_default FROM '.TABLE_PREFIX.'print_method_setting_rel AS pmsr JOIN '.TABLE_PREFIX.'print_method AS pm ON pm.pk_id=pmsr.print_method_id JOIN '.TABLE_PREFIX.'print_setting AS ps ON ps.pk_id=pmsr.print_setting_id ORDER BY pm.pk_id';

			$rows=$this->executeFetchAssocQuery($sql);
			$activePrintSetting = array();
			if(!empty($rows)){
				foreach($rows as $k=>$v){
					$activePrintSetting[$k]['id'] = $v['pk_id'];
					$activePrintSetting[$k]['name'] = $v['name'];
					$activePrintSetting[$k]['is_enable'] = $v['is_enable'];
					$activePrintSetting[$k]['is_default'] = $v['is_default'];
					if(isset($v['file_type'])){
						$activePrintSetting[$k]['image'] = 'pm_'.$v['pk_id'].'.'.$v['file_type'];
					}else{
						$activePrintSetting[$k]['image'] = 'no-img.png';
					}
				}
			}
			$this->response($this->json($activePrintSetting), 200);
		}catch(Exception $e) {
			$result = array('Caught exception:'=>$e->getMessage());
			$this->response($this->json($result), 200);
		}
	}
	
	/**
	*
	*date created (dd-mm-yy)
	*date modified 15-4-2016(dd-mm-yy)
	*Add print settings in admin
	* 
	*@param (String)apikey
	*@param (String)name
	*@param (int)print_id
	*@return json data
	* 
	*/
	public function addNewPrintSettingsAdmin(){// Adding new print method			
		$req_arr = $this->_request;
		$status=0;
		if(!empty($req_arr) && isset($req_arr) && isset($req_arr['apikey']) && isset($req_arr['name'])){
			extract($req_arr);
			try{
				if(isset($print_id) && $print_id){
					$update_sql = "UPDATE ".TABLE_PREFIX."print_method SET updated_on=NOW(), name='".$name."'";
					//if ( base64_encode(base64_decode($base64, true)) === $base64){
					if (strpos($base64, ';base64') != false){
						//if (base64_decode($base64, true)){
						$dir = $this->getPrintMethodImagePath();
						if(!$dir) $this->response('',204); //204 - immediately termiante this request
						if (!file_exists($dir)) mkdir($dir, 0777, true);
						$fname = 'pm_'.$print_id.'.'.$type;									
						if(file_exists($dir.$fname)) unlink($dir.$fname);
						$base64 = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $base64));	
						//$thumbBase64Data = base64_decode($base64);
						$status = file_put_contents($dir.$fname, $base64);
						$update_sql .= ", file_type='".$type."'";
					}
					$update_sql .= " WHERE pk_id='".$print_id."'";
					$this->executeGenericDMLQuery($update_sql);
					$setting = Flight::setting();
					$setting->allSettingsDetails(1);
					$this->getAllPrintSettings($print_id);
				}else{
					$sql = "INSERT INTO ".TABLE_PREFIX."print_method(name,added_on) VALUES('".$name."',NOW())";
					$print_id = $this->executeGenericInsertQuery($sql);
					//for print_quantity range//
					$isql1  = "INSERT INTO ".TABLE_PREFIX."print_quantity_range(pk_id) VALUES(NULL)";
					$print_quantity_range_id = $this->executeGenericInsertQuery($isql1);
					$isql2  = "INSERT INTO ".TABLE_PREFIX."print_method_quantity_range_rel(print_method_id,print_quantity_range_id) VALUES('".$print_id."','".$print_quantity_range_id."')";
					$status = $this->executeGenericDMLQuery($isql2);
					$base64 = $req_arr['base64'];
					$type = $req_arr['type'];
					if(isset($base64) && $base64 && isset($type) && $type){
						$dir = $this->getPrintMethodImagePath();
						if(!$dir) $this->response('',204); //204 - immediately termiante this request
						if (!file_exists($dir)) mkdir($dir, 0777, true);
						$fname = 'pm_'.$print_id.'.'.$type;						
						$base64 = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $base64));							
						$status = file_put_contents($dir.$fname, $base64);
						$sql = "UPDATE ".TABLE_PREFIX."print_method SET file_type='".$type."' WHERE pk_id=".$print_id;
						$this->executeGenericDMLQuery($sql);
					}
					//For Print setting 
					$col_sql  = '';
					$val_sql = '';
					if(!empty($req_arr['settings'])){
						foreach($req_arr['settings'] as $k=>$v){
							if($k != 'reqmethod' && $k != 'id' && $k != 'pk_id'){
								$col_sql .= ','.$k;
								 $val_sql .= ",'$v'";
							}
						}
						$setting_sql = 'INSERT INTO '.TABLE_PREFIX.'print_setting('.substr($col_sql,1).') VALUES('.substr($val_sql,1).')';
					}else{
						$setting_sql = "INSERT INTO ".TABLE_PREFIX."print_setting(added_on) VALUES (NOW())";
					}
					$print_setting_id = $this->executeGenericInsertQuery($setting_sql);
					if($print_setting_id){
						$setting_rel_sql = "INSERT INTO ".TABLE_PREFIX."print_method_setting_rel(print_setting_id,print_method_id) VALUES('".$print_setting_id."','".$print_id."')";
						$status =$this->executeGenericDMLQuery($setting_rel_sql);
						if($status){
							$setting = Flight::setting();
							$setting->allSettingsDetails(1);
							$this->getAllPrintSettings($print_id);
						}
						else{
							$msg=array("status"=>"failed");	
							$this->response($this->json($msg), 200);
						}
					}
				}
			}catch(Exception $e){
				$result = array('Caught exception:'=>$e->getMessage());
				$this->response($this->json($result), 200);
			}
		}
	}
	
	/**
	*
	*date created (dd-mm-yy)
	*date modified 15-4-2016(dd-mm-yy)
	*Add and update print feature by print_method_id
	* 
	*@param (String)apikey
	*@param (int) print_method_id
	*@return json data
	* 
	*/
	public function addUpdatePrintFeature(){//Related Table : Multiple Entry
		try{
			$status =0;
			if(!empty($this->_request) && isset($this->_request['print_method_id'])){//update case
				// Delete from print_method_feature_rel During Update
				$sql = "DELETE FROM ".TABLE_PREFIX."print_method_feature_rel WHERE print_method_id='".$this->_request['print_method_id']."'";
				$this->executeGenericDMLQuery($sql);
			}
			// For Multiple Insertion
			if(!empty($this->_request) && isset($this->_request['features'])){
					$feature_rel  = "INSERT INTO ".TABLE_PREFIX."print_method_feature_rel(print_method_id,feature_id) VALUES";
					foreach($this->_request['features'] as $v){
					$feature_rel .= "('".$this->_request['print_method_id']."','".$v['id']."'),";
				}
				$feature_rel = substr($feature_rel,0,strlen($feature_rel)-1);    
				$status = $this->executeGenericDMLQuery($feature_rel);
				$setting = Flight::setting();
				$setting->allSettingsDetails(1);
				if($status) $this->getAllPrintSettings($this->_request['print_method_id']);    
				$msg=array("status"=>'failed');
				$this->response($this->json($msg), 200);
			}
		}catch(Exception $e) {
			$result = array('Caught exception:'=>$e->getMessage());
			$this->response($this->json($result), 200);
		}exit;
	}
	
	/**
	*
	*date created (dd-mm-yy)
	*date modified 15-4-2016(dd-mm-yy)
	*For print_setting Table
	* 
	*@param (String)apikey
	*@param (Array)textline_price
	*@param (int)id
	*@return json data
	* 
	*/		
	public function updatePrintSetting(){
		try{
			$req_arr = (!empty($this->_request))?$this->_request:array();
			$status = 0;
			if(!empty($req_arr)){//chk Api key here
				if(isset($req_arr['id']) &&  $req_arr['id']){//id => print_method_id
					$print_method_id = $req_arr['id'];$print_setting_id = 0;
					extract($req_arr);
					$status = 0;
					$sql ="DELETE FROM ".TABLE_PREFIX."print_textline_price_rel WHERE print_method_id='".$print_method_id."'";
					$status = $this->executeGenericDMLQuery($sql);
					if(!empty($textline_price)){
						foreach($textline_price['line_price'] as $v1){
							$isql = "INSERT INTO ".TABLE_PREFIX."print_textline_price_rel(print_method_id,text_price,no_of_allowed) VALUES ('".$print_method_id."','".$v1."','".$textline_price['allowded_lines']."');";
							$status = $this->executeGenericDMLQuery($isql);
						}
					}
					//for image uplaod price
					if(!empty($img_upload_price)){
					$sql_delete ="DELETE FROM ".TABLE_PREFIX."print_image_upload_price WHERE print_method_id='".$print_method_id."'";
					$status = $this->executeGenericDMLQuery($sql_delete);
						foreach($img_upload_price['image_price'] as $v2){
							$isql = "INSERT INTO ".TABLE_PREFIX."print_image_upload_price(print_method_id,no_of_allowed,image_price) VALUES ('".$print_method_id."','".$img_upload_price['allowed_sides']."','".$v2."');";
							$status = $this->executeGenericDMLQuery($isql);
						}
					}
					if(!empty($req_arr['settings']) && isset($req_arr['settings']['pk_id']) &&  $req_arr['settings']['pk_id']){// Update Case
						$print_setting_id = $req_arr['settings']['pk_id'];
						$sql  = '';
						foreach($req_arr['settings'] as $k=>$v){
							if($k != 'printStatus')$sql .= ",$k='$v'";
						}
						$sql = "UPDATE ".TABLE_PREFIX."print_setting SET ".substr($sql,1)." WHERE pk_id = '".$print_setting_id."'";
						$status = $this->executeGenericDMLQuery($sql);
						//$this->log('update sql:'.$sql, true, 'ipsita.txt');
						
						$sql = "DELETE FROM ".TABLE_PREFIX."print_method_setting_rel WHERE print_method_id='".$print_method_id."'";
						$this->executeGenericDMLQuery($sql);							
					}
					
					if($print_setting_id){
						// Insert Into print_method_setting_rel
						$sql = "INSERT INTO ".TABLE_PREFIX."print_method_setting_rel(print_setting_id,print_method_id) VALUES('".$print_setting_id."','".$print_method_id."')";
						$this->executeGenericDMLQuery($sql);
					}
					$setting = Flight::setting();
					$setting->allSettingsDetails(1);
					$this->getAllPrintSettings($print_method_id);
				}	
			}
		}catch(Exception $e) {
			$result = array('Caught exception:'=>$e->getMessage());
			$this->response($this->json($result), 200);
		}
	}
	
	/**
	*
	*date created (dd-mm-yy)
	*date modified 15-4-2016(dd-mm-yy)
	*add all category by print id
	* 
	* @param (int)print_method_id 
	* @param (String)apikey
	* @param (Array)product_category
	* @param (Array)font_category	
	* @param (Array)template_category
	* @param (Array)palette_category			
	* @return json data
	* 
	*/
	public function addAllCategoryByPrintId($data=array()){
		$req_arr = (!empty($this->_request))?$this->_request:$data;
		$status =0;
		$print_method_id = $req_arr['print_method_id'];
		if(isset($print_method_id) && $print_method_id!=''){
			try{
				$delete_sql_pc = "DELETE FROM ".TABLE_PREFIX."product_category_printmethod_rel where print_method_id='".$print_method_id."'";
				$status = $this->executeGenericDMLQuery($delete_sql_pc);
				$delete_sql_fc = "DELETE FROM ".TABLE_PREFIX."font_category_printmethod_rel where print_method_id='".$print_method_id."'";
				$status = $this->executeGenericDMLQuery($delete_sql_fc);
				$delete_sql_dc = "DELETE FROM  ".TABLE_PREFIX."design_category_printmethod_rel where print_method_id='".$print_method_id."'";
				$status = $this->executeGenericDMLQuery($delete_sql_dc);
				$delete_sql_tc = "DELETE FROM  ".TABLE_PREFIX."template_category_printmethod_rel where print_method_id='".$print_method_id."'";
				$status = $this->executeGenericDMLQuery($delete_sql_tc);
				$delete_sql_palc = "DELETE FROM ".TABLE_PREFIX."print_method_palette_category WHERE print_method_id='".$print_method_id."'";
				$status = $this->executeGenericDMLQuery($delete_sql_palc);
				$delete_sql_bc = "DELETE FROM ".TABLE_PREFIX."design_back_cate_printmethod_rel WHERE print_method_id='".$print_method_id."'";
				$status = $this->executeGenericDMLQuery($delete_sql_bc);
				$delete_sql_bp = "DELETE FROM ".TABLE_PREFIX."back_pattern_cate_printmethod_rel WHERE print_method_id='".$print_method_id."'";
				$status = $this->executeGenericDMLQuery($delete_sql_bp);
			  
				if(!empty($req_arr['product_category'])){
					$sql_pc  = "INSERT INTO ".TABLE_PREFIX."product_category_printmethod_rel(print_method_id,product_category_id,is_enable) VALUES";
					foreach( $req_arr['product_category'] as $product_category){
						$sql_pc .= "('".$print_method_id."', '".$product_category['id']."', '".$product_category['is_enable']."'),";
					}
					$sql_pc = substr($sql_pc,0,strlen($sql_pc)-1);			   
					$status = $this->executeGenericDMLQuery($sql_pc);
				}
				if(!empty($req_arr['font_category'])){
					$sql_fc  = "INSERT INTO ".TABLE_PREFIX."font_category_printmethod_rel(print_method_id,font_category_id,is_enable) VALUES";
					foreach( $req_arr['font_category'] as $font_category){
							$sql_fc .= "('".$print_method_id."', '".$font_category['id']."', '".$font_category['is_enable']."'),";
					}
					$sql_fc = substr($sql_fc,0,strlen($sql_fc)-1);		   
					$status = $this->executeGenericDMLQuery($sql_fc);
				}
				if(!empty($req_arr['design_category'])){
					$sql_dc  = "INSERT INTO ".TABLE_PREFIX."design_category_printmethod_rel(print_method_id,design_category_id,is_enable) VALUES";
					foreach( $req_arr['design_category'] as $design_category){
						$sql_dc .= "('".$print_method_id."', '".$design_category['id']."', '".$design_category['is_enable']."'),";
					}
					$sql_dc = substr($sql_dc,0,strlen($sql_dc)-1);
					$status = $this->executeGenericDMLQuery($sql_dc);
				}
				if(!empty($req_arr['template_category'])){
					$sql_tc  = "INSERT INTO ".TABLE_PREFIX."template_category_printmethod_rel(print_method_id,temp_category_id,is_enable) VALUES";
					foreach( $req_arr['template_category'] as $template_category){
						$sql_tc .= "('".$print_method_id."', '".$template_category['id']."', '".$template_category['is_enable']."'),";
					}
					$sql_tc = substr($sql_tc,0,strlen($sql_tc)-1);			   
					$status = $this->executeGenericDMLQuery($sql_tc);
				}
				if(!empty($req_arr['palette_category'])){
					$sql_palc  = "INSERT INTO ".TABLE_PREFIX."print_method_palette_category(print_method_id, palette_category_id, is_enable) VALUES";
					foreach($req_arr['palette_category'] as $palette_category){
							$sql_palc .= "('".$print_method_id."', '".$palette_category['pk_id']."', '".$palette_category['is_enable']."'),";
					}
					$sql_palc = substr($sql_palc,0,strlen($sql_palc)-1);
					$status = $this->executeGenericDMLQuery($sql_palc);
				}
				if(!empty($req_arr['background_category'])){
					$sql_bc  = "INSERT INTO ".TABLE_PREFIX."design_back_cate_printmethod_rel(print_method_id,background_category_id,is_enable) VALUES";
					foreach( $req_arr['background_category'] as $background_category){
						$sql_bc .= "('".$print_method_id."', '".$background_category['id']."', '".$background_category['is_enable']."'),";
					}
					$sql_bc = substr($sql_bc,0,strlen($sql_bc)-1);	    
					$status = $this->executeGenericDMLQuery($sql_bc);
				}
				if(!empty($req_arr['pattern_category'])){
					$sql_bp  = "INSERT INTO ".TABLE_PREFIX."back_pattern_cate_printmethod_rel(print_method_id,	pattern_category_id,is_enable) VALUES";
					foreach( $req_arr['pattern_category'] as $pattern_category){
						$sql_bp .= "('".$print_method_id."', '".$pattern_category['id']."', '".$pattern_category['is_enable']."'),";
					}
					$sql_bp = substr($sql_bp,0,strlen($sql_bp)-1);	    
					$status = $this->executeGenericDMLQuery($sql_bp);
				}
				unset($this->_request['print_method_id']);
				$setting = Flight::setting();
				$setting->allSettingsDetails(1);
				if($status)
				$this->getAllPrintSettings($print_method_id);
				else{
					$msg=array("status"=>"failed");
				}
			}catch(Exception $e){
				$msg = array('Caught exception:'=>$e->getMessage());
			}
		}else{
			$msg=array("status"=>"nodata");
		}
		$this->response($this->json($msg), 200);
	}
	
	/**
	*
	*date created (dd-mm-yy)
	*date modified 15-4-2016(dd-mm-yy)
	*update text color to print method
	* 
	*@param (String)apikey
	*@param (int)print_method_id
	*@param (String)stockcolor
	*@param (String)wc_color1
	*@param (String)wc_color2
	*@param (String)wc_color3
	*@return json data
	* 
	*/
	public function updateTextColorToPrintMethod(){
		if(!empty($this->_request) && isset($this->_request['print_method_id']) && isset($this->_request['fillcolor']) && isset($this->_request['stockcolor']) && isset($this->_request['wc_color1'])&& isset($this->_request['wc_color2']) && isset($this->_request['wc_color3'])){
			$status=0;
			extract($this->_request);
			try{
				$sql="update ".TABLE_PREFIX."print_method set text_fillcolor='".$fillcolor."',text_strokecolor='".$stockcolor."',wc_color1='".$wc_color1."',wc_color2='".$wc_color2."',wc_color3='".$wc_color3."' where pk_id=$print_method_id";
				$status=$this->executeGenericDMLQuery($sql);
				unset($this->_request['print_method_id']);
				$setting = Flight::setting();
				$setting->allSettingsDetails(1);
				if($status)
					$this->getAllPrintSettings($print_method_id);
				else{
					$msg=array("status"=>"failed");
					$this->response($this->json($msg), 200);
				}
			}catch(Exception $e){
				$result = array('Caught exception:'=>$e->getMessage());
				$this->response($this->json($result), 200);
			}
		}
	}
	
	/**
	*
	*date of created 2-3-2016(dd-mm-yy)
	*date of Modified 13-4-2016(dd-mm-yy)
	*update Quantity Range color area price by print_id 
	*
	* @param (String)apikey 
	* @param (int)print_method_id 
	* @param (int)is_color_table 
	* @param (int)is_print_size 
	* @param (int)is_percentage
	* @param (Array)color_print_area 
	* @param (Array)quantity_range 		
	* @return to update allSettingsDetails(),and return getAllPrintSettings();
	* 
	*/ 
	public function updateQuantityRangeByPrintId(){
		if(!empty($this->_request)  && isset($this->_request['apikey']) && $this->isValidCall($this->_request['apikey'])){
			$req_arr =$this->_request;
			extract($req_arr);
			$status = 0;
			try{
				if(isset($is_color_table) && isset($is_print_size) && isset($is_percentage) && isset($is_color_area)){
					$sql = "UPDATE ".TABLE_PREFIX."print_setting AS ps , ".TABLE_PREFIX."print_method_setting_rel AS pmsr
						SET ps.is_color_area_price='".$is_color_area."',ps.is_print_size='".$is_print_size."',ps.is_percentage='".$is_percentage."',ps.is_color_price_range='".$is_color_table."',ps.screen_cost='".$screen_cost."'
						WHERE ps.pk_id=pmsr.print_setting_id and pmsr.print_method_id=".$print_method_id;
					$status = $this->executeGenericDMLQuery($sql);
				}
				$sql = "DELETE FROM ".TABLE_PREFIX."print_method_color_area_price_rel WHERE print_method_id='".$print_method_id."'";
				$status = $this->executeGenericDMLQuery($sql);
				if(!empty($req_arr['color_print_area'])){
					// For Multiple Insertion
					$val = array();
					foreach( $req_arr['color_print_area'] as $v2){
						$val[] = "('".$v2['id']."', '".$print_method_id."', '".$v2['price']."', '".$v2['percentage']."')";
					}
					if(!empty($val)){
						$sql  = "INSERT INTO ".TABLE_PREFIX."print_method_color_area_price_rel
						(print_size_id, print_method_id, price, percentage) VALUES".implode(',',$val);
						$status = $this->executeGenericDMLQuery($sql);
					}
				}
				$sql = "DELETE FROM ".TABLE_PREFIX."print_method_quantity_range_rel WHERE print_method_id='".$print_method_id."'";
				$status = $this->executeGenericDMLQuery($sql);
				$usql  = "UPDATE ".TABLE_PREFIX."print_quantity_range SET ";
				$isql1  = "INSERT INTO ".TABLE_PREFIX."print_quantity_range(from_range,to_range) VALUES";
				if(!empty($req_arr['quantity_range'])){
					$val1 = '';$isql = '';
					foreach($req_arr['quantity_range'] as $v){
						if($v['range_id'] && $v['range_id']>=1){// update a range
							$sql1 = $usql."from_range='".$v['from']."',to_range='".$v['to']."' 
							WHERE pk_id='".$v['range_id']."'";
							$status = $this->executeGenericDMLQuery($sql1);
							$print_quantity_range_id = $v['range_id'];
						}else{
							$val1 = $isql1."('".$v['from']."','".$v['to']."')";
							$print_quantity_range_id = $this->executeGenericInsertQuery($val1);
						}
						if($v['wht_base']){
								$sql_data = "INSERT INTO ".TABLE_PREFIX."print_method_quantity_range_rel(print_method_id,print_quantity_range_id,no_of_colors,
								color_price,white_base_price,color_percentage,white_base_percentage,is_fixed,is_check,is_exist)  
								VALUES('".$print_method_id."','".$print_quantity_range_id."','".$no_of_colors."',
								'','".$v['wht_base']['price']."','','".$v['wht_base']['perc']."','".$v['wht_base']['is_fixed']."','1','0')";//for add whitebase price
								$status = $this->executeGenericDMLQuery($sql_data);
							}
						foreach($v['clr_price'] as $v2){
							$sql = "INSERT INTO ".TABLE_PREFIX."print_method_quantity_range_rel(print_method_id,print_quantity_range_id,no_of_colors,
								color_price,white_base_price,color_percentage,white_base_percentage,is_fixed,is_check,is_exist)  
							VALUES('".$print_method_id."','".$print_quantity_range_id."',
							'".$no_of_colors."','".$v2['price']."','','".$v2['perc']."','','".$v2['is_fixed']."','0','0')";//for add print area price
							$status = $this->executeGenericDMLQuery($sql);
						}
					}
				}
				unset($this->_request['print_method_id']);
			}catch(Exception $e) {
					$result = array('Exceptionerror'=>$e->getMessage());
					$this->response($this->json($result), 200);
				}
			if($status){
			$setting = Flight::setting();
			$setting->allSettingsDetails(1);
			$this->getAllPrintSettings($print_method_id);
			}
		}else{
			$msg = array('stattus'=>'invaliedkey');
			$this->response($this->json($msg), 200);
		}
	}
	
	/**
	*
	*date created (dd-mm-yy)
	*date modified 15-4-2016(dd-mm-yy)
	*Get All Print settings  by print_method_id
	* 
	*@param (String)apikey
	*@param (int)print_method_id
	*@param (int)type
	*@return json data
	* 
	*/
	public function getAllPrintSettings($print_method='',$type=0){
		//fetch all print setting details 
		try{
			$print_method_id = 0;
			if(isset($print_method) && $print_method!=''){
				$print_method_id = $print_method;
			}else if(isset($this->_request['print_method_id']) && $this->_request['print_method_id']!=''){
				$print_method_id = $this->_request['print_method_id'];
			}
			$sql ="SELECT distinct pm.pk_id,pm.name,pm.is_enable,pm.file_type,pm.text_fillcolor,pm.text_strokecolor,pm.wc_color1,pm.wc_color2,pm.wc_color3,
				pst.is_default,pst.pk_id as print_setting_id, pst.is_min_order, pst.min_order_quantity, pst.is_white_base, pst.white_base_price, pst.is_clip_art,pst.is_font,pst.is_additional_price, pst.additional_price, pst.is_setup_cost, pst.setup_cost, pst.is_scalling, pst.scalling_price,pst.is_color_price_range, pst.is_percentage,pst.is_print_size,pst.is_color_group_price,pst.is_used_colors,pst.is_color_chooser,				   pst.is_product_side,pst.is_single_order,pst.is_no_of_used_colors,pst.other_color_group_price,pst.is_max_palettes,pst.max_palettes,pst.is_gray_scale,pst.is_qrcode_whitebase,pst.screen_cost,pst.is_forcecolor,pst.is_palette,pst.is_color_area_price,pst.is_print_area_percentage,pst.is_multiline_text_price,pst.is_background,pst.is_image_upload_price,pst.is_calulate_multiple_side,pst.image_upload_price,pst.is_engrave,pst.is_browse_allow
				FROM ".TABLE_PREFIX."print_method pm
				LEFT JOIN ".TABLE_PREFIX."print_setting  pst ON pm.pk_id=pst.pk_id
				LEFT JOIN ".TABLE_PREFIX."print_method_setting_rel pmsr ON pst.pk_id=pmsr.print_setting_id ";
			if(isset($print_method_id) && $print_method_id){
				$sql .= " where pm.pk_id='".$print_method_id."'";
			}
			$values = $this->executeGenericDQLQuery($sql);				
			

			$getPrintData = array();
			$i=0;
			foreach($values as $rows){
				$getPrintData[$i]['pk_id']       					= $rows['pk_id'];
				$getPrintData[$i]['name']       					= $rows['name'];
				$getPrintData[$i]['is_enable']   					= intval($rows['is_enable']);
				$getPrintData[$i]['image']      					= (isset($rows['file_type']) && $rows['file_type'])?'pm_'.$rows['pk_id'].'.'.$rows['file_type']:'no-img.png';
				$getPrintData[$i]['text_fillcolor']  				= $rows['text_fillcolor'];
				$getPrintData[$i]['text_strokecolor']  				= $rows['text_strokecolor'];
				$getPrintData[$i]['wc_color1']    					= $rows['wc_color1'];
				$getPrintData[$i]['wc_color2']    					= $rows['wc_color2'];
				$getPrintData[$i]['wc_color3']    					= $rows['wc_color3'];
				$getPrintData[$i]['settings']['pk_id']              = $rows['print_setting_id'];
				$getPrintData[$i]['settings']['is_min_order']       = intval($rows['is_min_order']);
				$getPrintData[$i]['settings']['min_order_quantity'] = $rows['min_order_quantity'];
				$getPrintData[$i]['settings']['is_white_base']      = intval($rows['is_white_base']);
				$getPrintData[$i]['settings']['white_base_price']   = $rows['white_base_price'];
				$getPrintData[$i]['settings']['is_clip_art']        = intval($rows['is_clip_art']);
				$getPrintData[$i]['settings']['is_font']            = intval($rows['is_font']);
				$getPrintData[$i]['settings']['is_additional_price']= intval($rows['is_additional_price']);
				$getPrintData[$i]['settings']['additional_price']   = $rows['additional_price'];
				$getPrintData[$i]['settings']['is_setup_cost']      = intval($rows['is_setup_cost']);
				$getPrintData[$i]['settings']['setup_cost']         = $rows['setup_cost'];
				$getPrintData[$i]['settings']['is_scalling']        = intval($rows['is_scalling']);
				$getPrintData[$i]['settings']['scalling_price']     = $rows['scalling_price'];
				$getPrintData[$i]['settings']['is_color_price_range']= intval($rows['is_color_price_range']);
				$getPrintData[$i]['settings']['is_percentage']       = intval($rows['is_percentage']);
				$getPrintData[$i]['settings']['is_print_size']       = intval($rows['is_print_size']);
				$getPrintData[$i]['settings']['is_used_colors']      = intval($rows['is_used_colors']);
				$getPrintData[$i]['settings']['is_color_chooser']    = intval($rows['is_color_chooser']);
				$getPrintData[$i]['settings']['is_color_group_price']= intval($rows['is_color_group_price']);
				$getPrintData[$i]['settings']['is_product_side']     = intval($rows['is_product_side']);
				$getPrintData[$i]['settings']['is_single_order']     = intval($rows['is_single_order']);
				$getPrintData[$i]['settings']['is_no_of_used_colors']= intval($rows['is_no_of_used_colors']);
				$getPrintData[$i]['settings']['other_color_group_price']  = $rows['other_color_group_price'];
				$getPrintData[$i]['settings']['is_default']  		 = intval($rows['is_default']);
				$getPrintData[$i]['settings']['max_palettes']  		 = intval($rows['max_palettes']);
				$getPrintData[$i]['settings']['is_max_palettes']  	 = intval($rows['is_max_palettes']);
				$getPrintData[$i]['settings']['is_gray_scale']  	 = intval($rows['is_gray_scale']);
				$getPrintData[$i]['settings']['is_qrcode_whitebase'] = intval($rows['is_qrcode_whitebase']);
				$getPrintData[$i]['settings']['screen_cost']  		 = $rows['screen_cost'];
				$getPrintData[$i]['settings']['is_forcecolor']  	 = intval($rows['is_forcecolor']);
				$getPrintData[$i]['settings']['is_palette']  		 = intval($rows['is_palette']);		
				$getPrintData[$i]['settings']['is_color_area_price'] = intval($rows['is_color_area_price']);
				$getPrintData[$i]['settings']['is_print_area_percentage'] = intval($rows['is_print_area_percentage']);
				$getPrintData[$i]['settings']['is_multiline_text_price'] = intval($rows['is_multiline_text_price']);
				$getPrintData[$i]['settings']['is_background'] = intval($rows['is_background']);
				$getPrintData[$i]['settings']['is_image_upload_price'] = intval($rows['is_image_upload_price']);
				$getPrintData[$i]['settings']['is_calulate_multiple_side'] = intval($rows['is_calulate_multiple_side']);
				$getPrintData[$i]['settings']['image_upload_price'] = $rows['image_upload_price'];
				$getPrintData[$i]['settings']['is_engrave'] = intval($rows['is_engrave']);
				$getPrintData[$i]['settings']['is_browse_allow'] = intval($rows['is_browse_allow']);
				
				//$getPrintData[$i]['settings']['is_pattern'] = intval($rows['is_pattern']);
				
				//fetch all color price group
				$sql = "SELECT count(*) AS nos FROM ".TABLE_PREFIX."color_price_group_rel AS cpgr JOIN ".TABLE_PREFIX."print_method_color_group_rel as pmcgr ON pmcgr.color_group_id=cpgr.color_price_group_id WHERE pmcgr.print_method_id='".$rows['pk_id']."'";
				$res = $this->executeFetchAssocQuery($sql);
				if(!empty($res) && $res[0]['nos']){
					$sql="SELECT distinct cpg.pk_id,cpg.name,cpg.price,cpgr.color_id,p.value
					   FROM ".TABLE_PREFIX."print_method pm
					   INNER JOIN ".TABLE_PREFIX."print_method_color_group_rel pmcgr
					   ON pmcgr.print_method_id=pm.pk_id
					   INNER JOIN ".TABLE_PREFIX."color_price_group cpg
					   ON cpg.pk_id=pmcgr.color_group_id
					   left JOIN ".TABLE_PREFIX."color_price_group_rel cpgr
					   ON cpg.pk_id=cpgr.color_price_group_id 
					   INNER JOIN ".TABLE_PREFIX."palettes p
					   ON p.id=cpgr.color_id WHERE pm.pk_id=".$rows['pk_id']."";
				}else{
					$sql="SELECT distinct cpg.pk_id,cpg.name,cpg.price
					   FROM ".TABLE_PREFIX."print_method pm
					   INNER JOIN ".TABLE_PREFIX."print_method_color_group_rel pmcgr
					   ON pmcgr.print_method_id=pm.pk_id
					   INNER JOIN ".TABLE_PREFIX."color_price_group cpg
					   ON cpg.pk_id=pmcgr.color_group_id
					   WHERE pm.pk_id=".$rows['pk_id']."";
				}
				$rows1  = $this->executeGenericDQLQuery($sql);
				$countRows=sizeof($rows1);
				$colorPriceGroup=array();
				$colorPrice = array();
				$counter = 0;$tempId = 0;
				if($countRows){
					for($j=0;$j<$countRows;$j++){
						if($j==0 || $tempId != $rows1[$j]['pk_id']){      
							if($j==0 || $tempId != $rows1[$j]['pk_id']) {
								$tempId = $rows1[$j]['pk_id'];
								$colorPriceGroup[$j]['pk_id']             = $rows1[$j]['pk_id'];
								$colorPriceGroup[$j]['name']              = $rows1[$j]['name'];
								$colorPriceGroup[$j]['price']             = $rows1[$j]['price'];
								$colorPriceGroup[$j]['color_ids'][]       = isset($rows1[$j]['color_id'])?$rows1[$j]['color_id']:0;
								$colorPriceGroup[$j]['color_values'][]    = isset($rows1[$j]['value'])?$rows1[$j]['value']:0;
								$counter = $j;
							}
						}else {
							$colorPriceGroup[$counter]['color_ids'][] = isset($rows1[$j]['color_id'])?$rows1[$j]['color_id']:0;
							$colorPriceGroup[$counter]['color_values'][] = isset($rows1[$j]['value'])?$rows1[$j]['value']:0;
						}
						//var_dump($colorPriceGroup);exit;
						$getPrintData[$i]['color_price_group'] = array_values($colorPriceGroup);
					}
				}else{
					$getPrintData[$i]['color_price_group'] = array();
				}
				//fetch product category by print_metho_id
				$pc_sql="SELECT product_category_id,is_enable FROM ".TABLE_PREFIX."product_category_printmethod_rel WHERE print_method_id=".$rows['pk_id']."";
				$categoryRows  = $this->executeGenericDQLQuery($pc_sql);
				$productCategory=array();
				for($j=0;$j<sizeof($categoryRows);$j++) {
					$productCategory[$j]['product_category_id'] = $categoryRows[$j]['product_category_id'];
					$productCategory[$j]['is_enable'] = intval($categoryRows[$j]['is_enable']);
				}
				 $getPrintData[$i]['product_category']=$productCategory;
				 //fetch font category by print_method_id
				$fc_sql="SELECT font_category_id,is_enable FROM ".TABLE_PREFIX."font_category_printmethod_rel WHERE print_method_id=".$rows['pk_id']."";
				$fcategoryRows  = $this->executeGenericDQLQuery($fc_sql);
				$fontCategory=array();
				for($j=0;$j<sizeof($fcategoryRows);$j++) {
					$fontCategory[$j]['font_category_id'] = $fcategoryRows[$j]['font_category_id'];
					$fontCategory[$j]['is_enable'] = intval($fcategoryRows[$j]['is_enable']);
				}
				$getPrintData[$i]['font_category']=$fontCategory;
				//fetch design category by print_method_id
				$dc_sql="SELECT design_category_id,is_enable FROM ".TABLE_PREFIX."design_category_printmethod_rel WHERE print_method_id=".$rows['pk_id']."";
				$dcategoryRows  = $this->executeGenericDQLQuery($dc_sql);
				$designCategory=array();
				for($j=0;$j<sizeof($dcategoryRows);$j++) {
					$designCategory[$j]['design_category_id'] = $dcategoryRows[$j]['design_category_id'];
					$designCategory[$j]['is_enable'] = intval($dcategoryRows[$j]['is_enable']);
				}
				$getPrintData[$i]['design_category']=$designCategory;
				//fetch all template category by print_method_id
				$tc_sql="SELECT temp_category_id,is_enable FROM ".TABLE_PREFIX."template_category_printmethod_rel WHERE print_method_id=".$rows['pk_id']."";
				$tcategoryRows  = $this->executeGenericDQLQuery($tc_sql);
				$templateCategory=array();
				for($j=0;$j<sizeof($tcategoryRows);$j++) {
					$templateCategory[$j]['template_category_id'] = $tcategoryRows[$j]['temp_category_id'];
					$templateCategory[$j]['is_enable'] = intval($tcategoryRows[$j]['is_enable']);
				}
				$getPrintData[$i]['template_category']=$templateCategory;
				
				//fetch all pallete category by printId   //palette_category_rel
				$sql="SELECT distinct pc.id,pc.name,pmpc.is_enable
				   FROM ".TABLE_PREFIX."print_method pm
				   INNER JOIN ".TABLE_PREFIX."print_method_palette_category pmpc
				   ON pmpc.print_method_id=pm.pk_id
				   INNER JOIN ".TABLE_PREFIX."palette_category pc
				   ON pc.id=pmpc.palette_category_id
				   WHERE pm.pk_id=".$rows['pk_id']."";
				   
				   
				   
				$rows2  = $this->executeGenericDQLQuery($sql);
				$palleteCategory = array();$palletCategoryIds = '';
				for($j=0;$j<sizeof($rows2);$j++) {
				   $palletCategoryIds = $palletCategoryIds.','.$rows2[$j]['id'];
				   $palleteCategory[$j]['id']           = $rows2[$j]['id'];
				   $palleteCategory[$j]['name']         = $rows2[$j]['name'];
				   $palleteCategory[$j]['is_enable']    = intval($rows2[$j]['is_enable']);
				}
				$getPrintData[$i]['pallete_category']=$palleteCategory;

				//fetch all features by print metod id
				$sql="SELECT pmfr.feature_id,f.type FROM ".TABLE_PREFIX."features f
				JOIN ".TABLE_PREFIX."print_method_feature_rel pmfr
				ON pmfr.print_method_id=".$rows['pk_id']."
				AND pmfr.feature_id=f.id";
				$rows3 = $this->executeGenericDQLQuery($sql);

				$countRows=sizeof($rows3);
				$features =array();
				for($j=0;$j<$countRows;$j++){ 
				   $features[$j]['id']  = $rows3[$j]['feature_id'];
				   $features[$j]['name']  = $rows3[$j]['type'];
				}
				$getPrintData[$i]['features'] = $features;
					  
				//fetching print size
				$sql="SELECT distinct ps.pk_id,ps.name,ps.is_user_defined,ps.height,ps.width,psmr.price,psmr.percentage
				FROM ".TABLE_PREFIX."print_method pm
				INNER JOIN ".TABLE_PREFIX."print_size_method_rel psmr
				ON psmr.print_method_id=pm.pk_id
				INNER JOIN ".TABLE_PREFIX."print_size ps
				ON ps.pk_id=psmr.print_size_id
				WHERE pm.pk_id=".$rows['pk_id']."";
				$rows4 = $this->executeGenericDQLQuery($sql);
				$countRows=sizeof($rows4);
				$printSize=array();
					
				for($j=0;$j<$countRows;$j++){ 
					$printSize[$j]['pk_id']                = $rows4[$j]['pk_id'];
					$printSize[$j]['name']                 = $rows4[$j]['name'];
					$printSize[$j]['price']                = $rows4[$j]['price'];
					$printSize[$j]['percentage']           = $rows4[$j]['percentage'];
					$printSize[$j]['is_user_defined']      = intval($rows4[$j]['is_user_defined']);
					$printSize[$j]['height']               = $rows4[$j]['height'];
					$printSize[$j]['width']                = $rows4[$j]['width'];
				}
				$getPrintData[$i]['print_size'] = $printSize;

				//fetch quantity_range details
				$sql  ="SELECT pqr.pk_id,pqr.from_range,pqr.to_range,pmqrr.white_base_price, pmqrr.no_of_colors,pmqrr.color_price
				FROM ".TABLE_PREFIX."print_method pm
				INNER JOIN ".TABLE_PREFIX."print_method_quantity_range_rel pmqrr
				ON pmqrr.print_method_id=pm.pk_id
				INNER JOIN ".TABLE_PREFIX."print_quantity_range pqr
				ON pqr.pk_id=pmqrr.print_quantity_range_id 
				WHERE pm.pk_id='".$rows['pk_id']."' ORDER BY pqr.pk_id ASC, pmqrr.pk_id DESC";

				$rows5  = $this->executeFetchAssocQuery($sql);
				$countRows=sizeof($rows5);
				$printQuantity=array();
				$colorPrice = array();
				$counter = 0;$tempId = 0;
				$getPrintData[$i]['quantity_range']['no_of_colors']= isset($rows5[0]['no_of_colors'])?$rows5[0]['no_of_colors']:0;
				if(!empty($rows5)){
					$j = 0;
					foreach($rows5 as $k=>$v){
						if($k==0 || $v['pk_id'] != $tempId) {
							$tempId 								= $v['pk_id'];
							$printQuantity[$k]['pk_id'] 			= $v['pk_id'];
							$printQuantity[$k]['from'] 				= $v['from_range'];
							$printQuantity[$k]['to'] 				= $v['to_range'];
							$printQuantity[$k]['white_base_price'] 	= $v['white_base_price'];
							$printQuantity[$k]['color_price'][] 	= $v['color_price'];
							$counter 								= $k++;
						}else{
							$printQuantity[$counter]['color_price'][] = $v['color_price'];
						}
						$j++;
					}
				}else{
					$printQuantity[0]['pk_id'] = '';
					$printQuantity[0]['from'] = '';
					$printQuantity[0]['to'] = '';
					$printQuantity[0]['white_base_price'] = '';
					$printQuantity[0]['color_price'][] = '';
				}
				$getPrintData[$i]['quantity_range']['details'] = array_values($printQuantity);
				$getPrintData[$i]['print_area_range'] = $this->getPrintAreaPriceByPrintid($rows['pk_id']);
				$getPrintData[$i]['color_range_price'] = $this->getQuantintyrangeByPrintId($rows['pk_id']);
				$sql="SELECT distinct ps.pk_id,ps.name,psmr.price,psmr.percentage
				FROM ".TABLE_PREFIX."print_method pm
				INNER JOIN ".TABLE_PREFIX."print_method_color_area_price_rel psmr ON psmr.print_method_id=pm.pk_id
				INNER JOIN ".TABLE_PREFIX."print_size ps ON ps.pk_id=psmr.print_size_id
				WHERE pm.pk_id='".$rows['pk_id']."'";
				$rowsData = $this->executeGenericDQLQuery($sql);
				$countRows=sizeof($rowsData);
				$colorArea=array();
				for($n=0;$n<$countRows;$n++){ 
					$colorArea[$n]['id'] = $rowsData[$n]['pk_id'];
					$colorArea[$n]['name'] = $rowsData[$n]['name'];
					$colorArea[$n]['price'] = $rowsData[$n]['price'];
					$colorArea[$n]['percentage'] = $rowsData[$n]['percentage'];
				}
				$getPrintData[$i]['color_print_area'] = $colorArea;
				//fetch color name by printId
				$palletCategoryIds = (strlen($palletCategoryIds))?substr($palletCategoryIds,1):0;
				$pallet = Flight::colorPallete();
				$getPrintData[$i]['palettes']=$pallet->getPatelletByCategory($print_method_id,$palletCategoryIds );
				$sql_fetch = "SELECT text_price,no_of_allowed FROM ".TABLE_PREFIX."print_textline_price_rel WHERE print_method_id='".$rows['pk_id']."' ORDER BY id ASC";
				$rowData = $this->executeGenericDQLQuery($sql_fetch);
				$size = sizeof($rowData);
				$resultTextPrice = array();
				if($rowData){
					$resultTextPrice['allowded_lines'] = $rowData[0]['no_of_allowed'];
					foreach($rowData as $k=>$v){
						$text_price[$k] =$v['text_price'];
					}
					$resultTextPrice['line_price'] = $text_price;
				}else{
					$resultTextPrice['allowded_lines'] = '1';
					$resultTextPrice['line_price'][] = 0;
				}
				$getPrintData[$i]['textline_price'] = $resultTextPrice;
				$getPrintData[$i]['mask_list']  = $this->getMaskPrice();
				//fetch all backgroud category by print_method_id
				$bc_sql="SELECT background_category_id,is_enable FROM ".TABLE_PREFIX."design_back_cate_printmethod_rel WHERE print_method_id=".$rows['pk_id']."";
				$bcategoryRows  = $this->executeGenericDQLQuery($bc_sql);
				$backgroundCategory=array();
				for($p=0;$p<sizeof($bcategoryRows);$p++) {
					$backgroundCategory[$p]['background_category_id'] = $bcategoryRows[$p]['background_category_id'];
					$backgroundCategory[$p]['is_enable'] = intval($bcategoryRows[$p]['is_enable']);
				}
				$getPrintData[$i]['background_category']=$backgroundCategory;
				//fetch image upload price 
				$sql_fetch_price = "SELECT no_of_allowed,image_price FROM ".TABLE_PREFIX."print_image_upload_price WHERE print_method_id='".$rows['pk_id']."' ORDER BY id ASC";
				$rowsData = $this->executeGenericDQLQuery($sql_fetch_price);
				$uploadImagePrice = array();
				if($rowsData){
					$uploadImagePrice['allowed_sides'] = $rowsData[0]['no_of_allowed'];
					foreach($rowsData as $k5=>$v5){
						$image_price[$k5] =$v5['image_price'];
					}
					$uploadImagePrice['image_price'] = $image_price;
				}else{
					$uploadImagePrice['allowed_sides'] = '1';
					$uploadImagePrice['image_price'][] = 0;
				}
				$getPrintData[$i]['img_upload_price'] = $uploadImagePrice;
				
				//fetch all backgroud_pattern category by print_method_id
				$pc_sql="SELECT pattern_category_id,is_enable FROM ".TABLE_PREFIX."back_pattern_cate_printmethod_rel WHERE print_method_id=".$rows['pk_id']."";
				$pcategoryRows  = $this->executeGenericDQLQuery($pc_sql);
				$patternCategory=array();
				for($p=0;$p<sizeof($pcategoryRows);$p++) {
					$patternCategory[$p]['pattern_category_id'] = $pcategoryRows[$p]['pattern_category_id'];
					$patternCategory[$p]['is_enable'] = intval($pcategoryRows[$p]['is_enable']);
				}
				$getPrintData[$i]['pattern_category']=$patternCategory;
				$i++;
			}
			if($getPrintData){
				//$getPrintData=array_values($getPrintData);
				if($type==1)return $this->json($getPrintData);
				$this->response($this->json($getPrintData), 200);
			}else{
				$msg=array("status"=>"invalid");
				$this->response($this->json($msg), 200);
			}
		}catch(Exception $e) {
			$result = array('Caught exception:'=>$e->getMessage());
			$this->response($this->json($result), 200);
		}
	}
	
	/**
	*
	*date created (dd-mm-yy)
	*date modified 15-4-2016(dd-mm-yy)
	*Delete all print_setting
	*TRUNCATE print_method;TRUNCATE print_setting;TRUNCATE print_method_setting_rel;TRUNCATE print_size_method_rel;TRUNCATE print_method_quantity_range_rel;TRUNCATE print_quantity_range;
	*TRUNCATE print_method_color_group_rel;TRUNCATE print_method_fonts_rel;TRUNCATE print_method_design_rel;TRUNCATE print_method_palette_rel;
	*TRUNCATE print_method_palette_category;TRUNCATE print_method_feature_rel;TRUNCATE size_variant_additional_price;
	* 
	*@param (String)apikey
	*@param (int)id
	*@return json data
	* 
	*/
	public function deletePrintMethod($req_arr = array()){
		try{
			if(!empty($this->_request) && $this->_request['id']){
				$print_method_id = $this->_request['id'];
				//Unlink print image
				$sql = 'SELECT file_type FROM '.TABLE_PREFIX.'print_method WHERE pk_id='.$print_method_id.' LIMIT 1';
				$res = $this->executeFetchAssocQuery($sql);
				if(!empty($res) && isset($res[0]['file_type'])){
					$fname = 'pm_'.$print_method_id.'.'.$res[0]['file_type'];
					$dir = $this->getPrintMethodImagePath();
					if(!$dir) $this->response('',204); //204 - immediately termiante this request
					if(file_exists($dir.$fname))unlink($dir.$fname);						
				}
				$sql = "DELETE FROM ".TABLE_PREFIX."print_method WHERE pk_id='".$print_method_id."'";
				$status = $this->executeGenericDMLQuery($sql);
				
				//Print Setting
				$sql = "DELETE psl,ps FROM ".TABLE_PREFIX."print_method_setting_rel AS psl INNER JOIN 
				".TABLE_PREFIX."print_setting AS ps ON psl.print_setting_id=ps.pk_id WHERE psl.print_method_id=".$print_method_id;
				$status = $this->executeGenericDMLQuery($sql);
				
				//Related tables for Print size
				$sql = "DELETE FROM ".TABLE_PREFIX."print_size_method_rel WHERE print_method_id='".$print_method_id."'";
				$status = $this->executeGenericDMLQuery($sql);
				
				//Quantity Range
				$sql = "DELETE pql,pq FROM ".TABLE_PREFIX."print_method_quantity_range_rel AS pql INNER JOIN ".TABLE_PREFIX."print_quantity_range AS pq ON pql.print_method_id=pq.pk_id WHERE pql.print_method_id=".$print_method_id;
				$status = $this->executeGenericDMLQuery($sql);
				
				$sql = "DELETE pcgl,cgp FROM ".TABLE_PREFIX."print_method_color_group_rel AS pcgl INNER JOIN ".TABLE_PREFIX."color_price_group AS cgp ON pcgl.print_method_id=cgp.pk_id WHERE pcgl.print_method_id=".$print_method_id;
				$status = $this->executeGenericDMLQuery($sql);
				
				//All other Related table for print_size,Quantity Range,Palette, Color Group, Design, Feature, Fonts
				//$sql = "DELETE FROM print_method_color_group_rel WHERE print_method_id='".$print_method_id."'";$status = $this->executeGenericDMLQuery($sql);
				$sql = "DELETE FROM ".TABLE_PREFIX."size_variant_additional_price WHERE print_method_id='".$print_method_id."'";$status = $this->executeGenericDMLQuery($sql);
				$sql = "DELETE FROM ".TABLE_PREFIX."print_method_fonts_rel WHERE print_method_id='".$print_method_id."'";$status = $this->executeGenericDMLQuery($sql);
				$sql = "DELETE FROM ".TABLE_PREFIX."print_method_design_rel WHERE print_method_id='".$print_method_id."'";$status = $this->executeGenericDMLQuery($sql);
				$sql = "DELETE FROM ".TABLE_PREFIX."print_method_palette_rel WHERE print_method_id='".$print_method_id."'";$status = $this->executeGenericDMLQuery($sql);
				$sql = "DELETE FROM ".TABLE_PREFIX."print_method_palette_category WHERE print_method_id='".$print_method_id."'";
				$sql = "DELETE FROM ".TABLE_PREFIX."print_method_feature_rel WHERE print_method_id='".$print_method_id."'";
			}
			$setting = Flight::setting();
			$setting->allSettingsDetails(1);
			$this->getAllPrintMethods();
		}catch(Exception $e) {
			$result = array('Caught exception:'=>$e->getMessage());
			$this->response($this->json($result), 200);
		}
	}
	
	/**
	*
	*date created (dd-mm-yy)
	*date modified 15-4-2016(dd-mm-yy)
	*add printing name
	* 
	*@param (String)apikey
	*@param (String)name
	*@param (int)isEnable
	*@return json data
	* 
	*/
	public function addPrintMethod(){
		try{
			$name = $this->_request['name'];
			$isEnable = $this->_request['isEnable'];
			$sql  = "insert into ".TABLE_PREFIX."print_method(name,is_enable,added_on) values('$name','$isEnable',now())";
			$id = $this->executeGenericInsertQuery($sql);
			if($id){						
				$msg=array("status"=>"success","name"=>$name);
			}else{
				$msg=array("status"=>"Can't save the data. ::failed");
			}					
			$this->response($this->json($msg), 200);
		}catch(Exception $e) {
			$result = array('Caught exception:'=>$e->getMessage());
			$this->response($this->json($result), 200);
		}						
	}
	
	/**
	*
	*date created (dd-mm-yy)
	*date modified 15-4-2016(dd-mm-yy)
	*To add new print method
	*
	*@param (String)apikey
	*@param (String)name
	*@param (int)id
	*@return json data
	* 
	*/
	public function addUpdatePrintMethod($req_arr = array()){// Update print method name
		try{
			$req_arr = (isset($this->_request) && count($this->_request)>1)?$this->_request:$req_arr;
			if(!empty($req_arr) && isset($req_arr['name'])){			
				if(isset($req_arr['id']) && $req_arr['id']){
					$print_method_id = $req_arr['id'];
					$sql = "UPDATE ".TABLE_PREFIX."print_method SET updated_on=NOW(),name='".$req_arr['name']."' WHERE pk_id='".$print_method_id."'";
					$this->executeGenericDMLQuery($sql);
				}else{
					$sql = "INSERT INTO ".TABLE_PREFIX."print_method(name,added_on) VALUES('".$req_arr['name']."',NOW())";
					$print_method_id = $this->executeGenericInsertQuery($sql);
				}
				if(isset($print_method_id) && $print_method_id)$status = 'success';
				//return $print_method_id;
			}
			$this->getAllPrintMethods();
		}catch(Exception $e) {
			$result = array('Caught exception:'=>$e->getMessage());
			$this->response($this->json($result), 200);
		}
	}
	
	/**
	*
	*date created (dd-mm-yy)
	*date modified 15-4-2016(dd-mm-yy)
	*Upadte Multiple Print Method
	*
	*@param (String)apikey
	*@param (int)status
	*@param (int)id
	*@return json data
	* 
	*/
	public function updateMultiplePrintMethod(){
		$status = 0;
		try{
			if(!empty($this->_request) && isset($this->_request) && !empty($this->_request['id']) && !empty($this->_request['status'])){
				$arr = array_combine($this->_request['id'],$this->_request['status']);	
				$usql1 = '';$usql2 = '';
				foreach($arr as $k=>$v){
					$usql1 .= ' WHEN '.$k." THEN '".$v."'";
					$usql2 .= ','.$k;
				}
				$usql2 = substr($usql2,1);
				$usql = 'UPDATE '.TABLE_PREFIX.'print_method SET is_enable = CASE pk_id'.$usql1.' END WHERE pk_id IN('.$usql2.')';
				$status = $this->executeGenericDMLQuery($usql);
				$this->getAllPrintMethods();
			}
		}catch(Exception $e) {
			$result = array('Caught exception:'=>$e->getMessage());
			$this->response($this->json($result), 200);
		}
	}
	
	/**
	*
	*date created (dd-mm-yy)
	*date modified 15-4-2016(dd-mm-yy)
	*Add and Update Print Settings
	* 
	*@param (String)apikey
	*@param (String)name
	*@param (Array)settings
	*@return json data
	* 
	*/
	public function addUpdatePrintSettings(){// Adding new print method
		try{
			$req_arr = $this->_request;
			if(!empty($req_arr) && isset($req_arr) && isset($req_arr['apikey']) && isset($req_arr['name']) && $req_arr['name']){// && $this->isValidCall($req_arr['apikey'])){
				$sql = "INSERT INTO ".TABLE_PREFIX."print_method(name,added_on) VALUES('".$req_arr['name']."',NOW())";
				$print_method_id = $this->executeGenericInsertQuery($sql);
				
				//For Print setting 
				$col_sql  = '';$val_sql = '';
				if(!empty($req_arr['settings'])){
					foreach($req_arr['settings'] as $k=>$v){
						if($k != 'reqmethod' && $k != 'id' && $k != 'pk_id'){
							$col_sql .= ','.$k; $val_sql .= ",'$v'";
						}
					}
					$setting_sql = "INSERT INTO ".TABLE_PREFIX."print_setting('.substr($col_sql,1).') VALUES('.substr($val_sql,1).')";
				}else{
					$setting_sql = "INSERT INTO ".TABLE_PREFIX."print_setting(added_on) VALUES (NOW())";
				}
				$print_setting_id = $this->executeGenericInsertQuery($setting_sql);
				
				if($print_setting_id){
					$setting_rel_sql = "INSERT INTO ".TABLE_PREFIX."print_method_setting_rel(print_setting_id,print_method_id) VALUES('".$print_setting_id."','".$print_method_id."')";
					$this->executeGenericDMLQuery($setting_rel_sql);
				}				
				//For Quantity Range 
				$isql1  = "INSERT INTO ".TABLE_PREFIX."print_quantity_range(from_range,to_range) VALUES";
				$isql2  = "INSERT INTO ".TABLE_PREFIX."print_method_quantity_range_rel(print_method_id,print_quantity_range_id,no_of_colors,color_price,white_base_price) VALUES";
				if(!empty($req_arr['quantity_range']) && !empty($req_arr['quantity_range']['details'])){
					$val1 = array();$isql = array();
					$k = count($req_arr['quantity_range']['details']);
					for($i=0;$i<$k;$i++){
						$val1[$k] = $isql1."('".$req_arr['quantity_range']['details'][$i]['from_range']."','".$req_arr['quantity_range']['details'][$i]['to_range']."')";
						$print_quantity_range_id[$k] = $this->executeGenericInsertQuery($val1[$k]);
						$val2[$k] = '';
						$k1 = count($req_arr['quantity_range']['details'][$i]['color_price']);
						for($i1=0;$i1<$k1;$i1++){
							$val2[$k] .= "('".$print_method_id."','".$print_quantity_range_id[$k]."','".$req_arr['quantity_range']['no_of_colors']."','".$req_arr['quantity_range']['details'][$i]['color_price'][$i1]."','".$req_arr['quantity_range']['details'][$i]['white_base_price']."'),";								
						}
						$isql[$k] = $isql2.$val2[$k];
						$isql[$k] = substr($isql[$k],0,strlen($isql[$k])-1);
						$this->executeGenericDMLQuery($isql[$k]);						
					}													
				}else{
					$isql1  = "INSERT INTO ".TABLE_PREFIX."print_quantity_range(pk_id) VALUES('')";
					$print_quantity_range_id = $this->executeGenericInsertQuery($isql1);
					
					$isql2  = "INSERT INTO ".TABLE_PREFIX."print_method_quantity_range_rel(print_method_id,print_quantity_range_id) VALUES('".$print_method_id."','".$print_quantity_range_id."')";
					$status = $this->executeGenericDMLQuery($isql2);
				}
			}
			//To fetch records and make the response
			$this->getAllPrintSettings();
		}catch(Exception $e) {
			$result = array('Caught exception:'=>$e->getMessage());
			$this->response($this->json($result), 200);
		}
	}
	
	
	/**
	*
	*date created (dd-mm-yy)
	*date modified 15-4-2016(dd-mm-yy)
	*Add and Update Print Methid Image
	* 
	*@param (String)apikey
	*@param (String)type
	*@param (String)base64
	*@param (int)print_method_id
	*@return json data
	* 
	*/
	public function addUpdatePrintImage(){
		$status = 0;
		try{
			if(!empty($this->_request) && isset($this->_request['print_method_id'])){
				extract($this->_request);
				if(isset($base64) && $base64 && isset($type) && $type){
					$dir = $this->getPrintMethodImagePath();
					if(!$dir) $this->response('',204); //204 - immediately termiante this request
					if (!file_exists($dir)) mkdir($dir, 0777, true);					
				
					$fname = 'pm_'.$print_method_id.'.'.$type;
					$thumbBase64Data = base64_decode($base64);
					file_put_contents($dir.$fname, $thumbBase64Data);
					$sql = "UPDATE ".TABLE_PREFIX."print_method SET file_type='".$type."' WHERE pk_id=".$print_method_id;
					$status = $this->executeGenericDMLQuery($sql);
				}
				if(isset($is_default)){
					$sql = "UPDATE ".TABLE_PREFIX."print_setting SET is_default='0'";$this->executeGenericDMLQuery($sql);
					$sql = "UPDATE ".TABLE_PREFIX."print_setting AS ps JOIN ".TABLE_PREFIX."print_method_setting_rel AS pmsr ON ps.pk_id=pmsr.print_setting_id SET ps.is_default='1' WHERE pmsr.print_method_id=".$print_method_id;
					$status = $this->executeGenericDMLQuery($sql);
				}
			}
			if($status){
				$msg =array();
				$printData = $this->getAllPrintSettings($print_method_id);
				$printMethod = $this->getAllPrintMethods();
				$rsult = array("status"=>$printData,"printMethod"=>$printMethod); 
				$msg = $this->formatJSONToArray($rsult);
			}else{
				$msg = array("status"=>"Failure"); 
			}
			//$msg['status'] = ($status)?$this->getAllPrintSettings($print_method_id):'Failure';
			//$msg['status'] = ($status)?$this->getAllPrintMethods():'Failure';
			$this->response($this->json($msg), 200);
		}catch(Exception $e) {
			$result = array('Caught exception:'=>$e->getMessage());
			$this->response($this->json($result), 200);
		}
	}
	
	/**
	*
	*date created (dd-mm-yy)
	*date modified 15-4-2016(dd-mm-yy)
	* For print_size_method_rel Table 
	*Multiple record inserted in print_size_method_rel Table. During Update All records deleted and again inserted 
	* 
	*@param (String)apikey
	*@param (Array)quantity_range
	*@param (int)print_method_id
	*@return json data
	* 
	*/	
	public function updatePrintSize(){//Related Table : Multiple Entry
		try{
			$status = 0;
			if(!empty($this->_request) && isset($this->_request['print_method_id']) && $this->_request['print_method_id']){
				$print_method_id = $this->_request['print_method_id'];
				// Delete from print_size_method_rel During Update
				$sql = "DELETE FROM ".TABLE_PREFIX."print_size_method_rel WHERE print_method_id='".$print_method_id."'";
				$status = $this->executeGenericDMLQuery($sql);
			
				if(!empty($this->_request['print_size'])){
					// For Multiple Insertion
					$val = array();
					foreach( $this->_request['print_size'] as $v){
						$val[] = "('".$v['id']."', '".$print_method_id."', '".$v['price']."', '".$v['percentage']."')";
					}
					if(!empty($val)){
						$val = array_reverse($val);
						$sql  = "INSERT INTO ".TABLE_PREFIX."print_size_method_rel(print_size_id, print_method_id, price, percentage) VALUES".implode(',',$val);
						$status = $this->executeGenericDMLQuery($sql);
					}
				}
				$setting = Flight::setting();
				$setting->allSettingsDetails(1);
			}
			//$this->addLatestRevision();
			$this->getAllPrintSettings($print_method_id);
		}catch(Exception $e) {
			$result = array('Caught exception:'=>$e->getMessage());
			$this->response($this->json($result), 200);
		}
	}
	
	/**
	*
	*date created (dd-mm-yy)
	*date modified 15-4-2016(dd-mm-yy)
	*Add/Updated in the print_quantity_range Table
	*Multiple record inserted in print_method_quantity_range_rel Table. During Update All records deleted and again inserted 
	*TRUNCATE print_method;TRUNCATE print_method_quantity_range_rel;TRUNCATE print_method_setting_rel;TRUNCATE print_quantity_range;TRUNCATE print_setting;TRUNCATE print_size_method_rel
	* 
	*@param (String)apikey
	*@param (Array)quantity_range
	*@param (int)print_method_id
	*@return json data
	* 
	*/	
	public function updateQuantityRange($data=array()){
		try{
			$req_arr = (!empty($this->_request))?$this->_request:$data;
			$status = 0;
			if(!empty($req_arr)){
			if(isset($req_arr['print_method_id']) && $req_arr['print_method_id']){
					$print_method_id = $req_arr['print_method_id'];
					$sql = "DELETE FROM ".TABLE_PREFIX."print_method_quantity_range_rel WHERE print_method_id='".$print_method_id."'";
					$status = $this->executeGenericDMLQuery($sql);
					$usql  = "UPDATE ".TABLE_PREFIX."print_quantity_range SET ";
					$isql1  = "INSERT INTO ".TABLE_PREFIX."print_quantity_range(from_range,to_range) VALUES";
					if(!empty($req_arr['quantity_range']) && !empty($req_arr['quantity_range']['details'])){
						$val1 = '';$isql = '';
						$k = count($req_arr['quantity_range']['details']);
						for($i=0;$i<$k;$i++){
							if($req_arr['quantity_range']['details'][$i]['pk_id'] && $req_arr['quantity_range']['details'][$i]['pk_id']==1){// update a range
								$sql1 = $usql."from_range='".$req_arr['quantity_range']['details'][$i]['from']."',to_range='".$req_arr['quantity_range']['details'][$i]['to']."' WHERE pk_id='".$req_arr['quantity_range']['details'][$i]['pk_id']."'";
								$status = $this->executeGenericDMLQuery($sql1);
								$print_quantity_range_id = $req_arr['quantity_range']['details'][$i]['pk_id'];
							}else{
								$val1 = $isql1."('".$req_arr['quantity_range']['details'][$i]['from']."','".$req_arr['quantity_range']['details'][$i]['to']."')";
								$print_quantity_range_id = $this->executeGenericInsertQuery($val1);
							}
							$val2 = array();
							$k1 = count($req_arr['quantity_range']['details'][$i]['color_price']);
							for($i1=0;$i1<$k1;$i1++){
								$val2[] = "('".$print_method_id."','".$print_quantity_range_id."','".$req_arr['quantity_range']['no_of_colors']."','".$req_arr['quantity_range']['details'][$i]['color_price'][$i1]."','".$req_arr['quantity_range']['details'][$i]['white_base_price']."')";								
							}
							if(!empty($val2)){
								$val2 = array_reverse($val2);
								$isql = "INSERT INTO ".TABLE_PREFIX."print_method_quantity_range_rel(print_method_id,print_quantity_range_id,no_of_colors,color_price,white_base_price) VALUES".implode(',',$val2);
								$status = $this->executeGenericDMLQuery($isql);	
							}
						}													
					}
				}
				$setting = Flight::setting();
				$setting->allSettingsDetails(1);
			}
			$this->getAllPrintSettings($print_method_id);
		}catch(Exception $e) {
			$result = array('Caught exception:'=>$e->getMessage());
			$this->response($this->json($result), 200);
		}
	}
	
	/**
	*
	*date of created 2-3-2016(dd-mm-yy)
	*date of Modified 13-4-2016(dd-mm-yy)
	*get print area price table data by print_id 
	*
	* @param (String)apikey 
	* @param (int)print_id 
	* @return JSON  data
	* 
	*/ 
	public function getPrintAreaPriceByPrintid($print_id=0){		
		try{
			$sql= "SELECT  print_size_range_id from ".TABLE_PREFIX."print_size_method_rel 
			where print_method_id='".$print_id."'"; 
			$data = $this->executeGenericDQLQuery($sql);
			if($data[0]['print_size_range_id'] >=1){
				$sql_fetch = "SELECT DISTINCT psr.pk_id,psr.from_range,psr.to_range
				from ".TABLE_PREFIX."print_size_range psr,
				".TABLE_PREFIX."print_size_method_rel psmr
				where psmr.print_size_range_id =psr.pk_id
				and psmr.print_method_id='".$print_id."' group by psmr.print_size_range_id";
				$rows = $this->executeGenericDQLQuery($sql_fetch);
				$result= array();
				$rowoSize = sizeof($rows);
				for ($i= 0; $i <$rowoSize; $i++) {
					$result[$i]['range_id'] = $rows[$i]['pk_id'];
					$result[$i]['from'] = $rows[$i]['from_range'];
					$result[$i]['to'] = $rows[$i]['to_range'];
					$sql ="SELECT DISTINCT psmr.is_fixed,psmr.price,psmr.percentage,psmr.is_whitebase 
					from ".TABLE_PREFIX."print_size_method_rel psmr 
					where psmr.print_method_id='".$print_id."'
					and psmr.print_size_range_id='".$rows[$i]['pk_id']."'
					and psmr.print_size_id ='0'
					group by psmr.print_size_range_id";
					$rows2 = $this->executeGenericDQLQuery($sql);
					//$l =0;
					foreach($rows2 as $k2=>$v2){
						$whiteBaseArr['price']= $v2['price'];
						$whiteBaseArr['percentage']= $v2['percentage'];
						$whiteBaseArr['is_whitebase']= intval($v2['is_whitebase']);
						$whiteBaseArr['is_fixed']= intval($v2['is_fixed']);
						//$l++;
					}
					$result[$i]['wht_base']= $whiteBaseArr;
					$sql_data= "SELECT ps.name,ps.pk_id,psmr.is_fixed,psmr.price,psmr.percentage
					from ".TABLE_PREFIX."print_size_method_rel psmr,".TABLE_PREFIX."print_size ps
					where ps.pk_id = psmr.print_size_id 
					and psmr.print_size_range_id ='".$rows[$i]['pk_id']."'
					and  psmr.print_method_id='".$print_id."' 
					order by ps.pk_id ASC";
					$rows1 = $this->executeGenericDQLQuery($sql_data);
					$printAreaArr = array();
					$size= sizeof($rows1);
					for ($j= 0; $j <$size; $j++) {
						$printAreaArr[$j]['id']= $rows1[$j]['pk_id'];
						$printAreaArr[$j]['name']= $rows1[$j]['name'];
						$printAreaArr[$j]['price']= $rows1[$j]['price'];
						$printAreaArr[$j]['percentage']= $rows1[$j]['percentage'];
						$printAreaArr[$j]['is_fixed']= intval($rows1[$j]['is_fixed']);
					}
					$result[$i]['area_prc']= $printAreaArr;
				}
			}else{
				$result[0]['range_id'] = '1';
				$result[0]['from'] = '1';
				$result[0]['to'] = '1';
				$result[0]['wht_base']['price']= 0;
				$result[0]['wht_base']['percentage']= 0;
				$result[0]['wht_base']['is_fixed']= 0;
				$result[0]['wht_base']['is_whitebase']= 0;
				$sql_data= "SELECT DISTINCT ps.name,ps.pk_id,psmr.is_fixed,psmr.price,psmr.percentage
					from ".TABLE_PREFIX."print_size_method_rel psmr,".TABLE_PREFIX."print_size ps
					where ps.pk_id = psmr.print_size_id 
					and psmr.print_size_range_id ='0'
					and psmr.is_fixed='0' group by ps.pk_id order by ps.pk_id ASC";
				$rows5 = $this->executeGenericDQLQuery($sql_data);
				$newArr = array();
				$j =0;
				foreach($rows5 as $k=>$v){
					$newArr[$k]['id']= $v['pk_id'];
					$newArr[$k]['name']= $v['name'];
					$newArr[$k]['price']= $v['price'];
					$newArr[$k]['percentage']= $v['percentage'];
					$newArr[$k]['is_fixed']= intval($v['is_fixed']);
					$j++;
				}
				$result[0]['area_prc']= $newArr;
			}
			$resultArr = array();
			$resultArr['details']  = array_values($result);
			return $resultArr;
		}catch(Exception $e) {
			$result = array('Caught exception in AreaPrice:'=>$e->getMessage());
			return $result;
		}	
	}
	/**
	*
	*date of created 2-3-2016(dd-mm-yy)
	*date of Modified 13-4-2016(dd-mm-yy)
	*get color area price by print_id 
	*
	* @param (String)apikey 
	* @param (int)print_method_id 
	* @return JSON  data
	* 
	*/ 
	public function getQuantintyrangeByPrintId($print_method_id=0){
		try{
			$sql_data ="SELECT print_method_id FROM ".TABLE_PREFIX."print_method_quantity_range_rel WHERE 
			print_method_id='".$print_method_id."'";
			$rowData = $this->executeGenericDQLQuery($sql_data);
			if($rowData){
				$sql="SELECT is_exist FROM ".TABLE_PREFIX."print_method_quantity_range_rel WHERE 
				print_method_id='".$print_method_id."' and is_exist='1'";
				$rows = $this->executeGenericDQLQuery($sql);
				if(empty($rows)){
					$sql_fetch = "SELECT DISTINCT pqr.pk_id,pqr.from_range,pqr.to_range,pmqrr.no_of_colors
					from ".TABLE_PREFIX."print_quantity_range pqr,
					".TABLE_PREFIX."print_method_quantity_range_rel pmqrr
					where pmqrr.print_quantity_range_id =pqr.pk_id
					and pmqrr.print_method_id='".$print_method_id."' group by pmqrr.print_quantity_range_id";
					$rows = $this->executeGenericDQLQuery($sql_fetch);
					$result= array();
					$resultArrNew['no_of_colors']= $rows[0]['no_of_colors'];
					$rowoSize = sizeof($rows);
					for ($i= 0; $i <$rowoSize; $i++) {
						$result[$i]['range_id'] = $rows[$i]['pk_id'];
						$result[$i]['from'] = $rows[$i]['from_range'];
						$result[$i]['to'] = $rows[$i]['to_range'];

						$sql ="SELECT DISTINCT pmqrr.is_fixed,pmqrr.white_base_price,pmqrr.white_base_percentage
						from  ".TABLE_PREFIX."print_method_quantity_range_rel pmqrr
						where pmqrr.print_method_id='".$print_method_id."'
						and pmqrr.print_quantity_range_id='".$rows[$i]['pk_id']."'
						and pmqrr.is_check ='1'
						group by pmqrr.print_quantity_range_id";
						$rows2 = $this->executeGenericDQLQuery($sql);
						foreach($rows2 as $k2=>$v2){
							$whiteBaseArr['price']= $v2['white_base_price'];
							$whiteBaseArr['perc']= $v2['white_base_percentage'];
							$whiteBaseArr['is_fixed']= intval($v2['is_fixed']);
						}
						$result[$i]['wht_base']= $whiteBaseArr;
						$sql_data= "SELECT pmqrr.is_fixed,pmqrr.color_price,pmqrr.color_percentage
						from  ".TABLE_PREFIX."print_method_quantity_range_rel pmqrr
						where pmqrr.print_method_id='".$print_method_id."'
						and pmqrr.print_quantity_range_id ='".$rows[$i]['pk_id']."'	
						and pmqrr.is_check ='0' order by pmqrr.pk_id ASC";
						$rows5 = $this->executeGenericDQLQuery($sql_data);
						$printAreaArr = array();
						$j =0;
						foreach($rows5 as $k=>$v){
							$printAreaArr[$k]['price']= $v['color_price'];
							$printAreaArr[$k]['perc']= $v['color_percentage'];
							$printAreaArr[$k]['is_fixed']= intval($v['is_fixed']);
							$j++;
						}
						$result[$i]['clr_price']= $printAreaArr;
					}
				}
				else{
					$sql_fetch = "SELECT DISTINCT pqr.pk_id,pqr.from_range,pqr.to_range,pmqrr.no_of_colors
					from ".TABLE_PREFIX."print_quantity_range pqr,
					".TABLE_PREFIX."print_method_quantity_range_rel pmqrr
					where pmqrr.print_quantity_range_id =pqr.pk_id
					and pmqrr.print_method_id='".$print_method_id."' group by pmqrr.print_quantity_range_id";
					$rows = $this->executeGenericDQLQuery($sql_fetch);
					$result= array();
					$resultArrNew['no_of_colors']= isset($rows[0]['no_of_colors'])?$rows[0]['no_of_colors']:0;
					$rowoSize = sizeof($rows);
					for ($i= 0; $i <$rowoSize; $i++) {
						$result[$i]['range_id'] = $rows[$i]['pk_id'];
						$result[$i]['from'] = $rows[$i]['from_range'];
						$result[$i]['to'] = $rows[$i]['to_range'];
						$sql ="SELECT DISTINCT pmqrr.is_fixed,pmqrr.white_base_price,pmqrr.white_base_percentage
						from  ".TABLE_PREFIX."print_method_quantity_range_rel pmqrr
						where pmqrr.print_method_id='".$print_method_id."'
						and pmqrr.print_quantity_range_id='".$rows[$i]['pk_id']."'
						group by pmqrr.print_quantity_range_id";
						$rows2 = $this->executeGenericDQLQuery($sql);
						foreach($rows2 as $k2=>$v2){
							$whiteBaseArr['price']= $v2['white_base_price'];
							$whiteBaseArr['perc']= $v2['white_base_percentage'];
							$whiteBaseArr['is_fixed']= intval($v2['is_fixed']);
						}
						$result[$i]['wht_base']= $whiteBaseArr;
						$sql_data= "SELECT pmqrr.is_fixed,pmqrr.color_price,pmqrr.color_percentage
						from  ".TABLE_PREFIX."print_method_quantity_range_rel pmqrr
						where pmqrr.print_method_id='".$print_method_id."'
						and pmqrr.print_quantity_range_id ='".$rows[$i]['pk_id']."'	
						and pmqrr.is_check ='0' order by pmqrr.pk_id ASC";
						$rows5 = $this->executeGenericDQLQuery($sql_data);
						$printAreaArr = array();
						$j =0;
						foreach($rows5 as $k=>$v){
							$printAreaArr[$k]['price']= $v['color_price'];
							$printAreaArr[$k]['perc']= $v['color_percentage'];
							$printAreaArr[$k]['is_fixed']= intval($v['is_fixed']);
							$j++;
						}
						$result[$i]['clr_price']= $printAreaArr;
					}
				}
				$resultArrNew['details'] = $result;
			}
			else {
				$resultArrNew['no_of_colors'] = '1';
				$result[0]['id'] = '';
				$result[0]['from'] = '1';
				$result[0]['to'] = '10';
				$result[0]['wht_base']['price'] ='0';
				$result[0]['wht_base']['perc'] ='0';
				$result[0]['wht_base']['is_fixed'] ='0';
				$printAreaArr[0]['price'] ='0';
				$printAreaArr[0]['perc'] ='0';
				$printAreaArr[0]['is_fixed'] ='0';
				$result[0]['clr_price']=$printAreaArr;
				$resultArrNew['details'] = $result;
			}	
			return $resultArrNew;
		}catch(Exception $e){
			$result = array('Caught exception in quality range price:'=>$e->getMessage());
			return $result;
		}	
	}
	
	/**
	*
	*date created (dd-mm-yy)
	*date modified 15-4-2016 (dd-mm-yy)
	*Get Default Print method data
	*
	*@param (String)apikey
	*@return Array value
	* 
	*/
	function getDefaultPrintMethodId(){
		try{
			$sql = "SELECT pmsr.print_method_id,pm.name FROM ".TABLE_PREFIX."print_method_setting_rel AS pmsr INNER JOIN ".TABLE_PREFIX."print_setting ps ON pmsr.print_setting_id=ps.pk_id INNER JOIN ".TABLE_PREFIX."print_method AS pm ON pm.pk_id=pmsr.print_method_id WHERE ps.is_default='1' LIMIT 1";
			$rec = $this->executeFetchAssocQuery($sql);
			return $rec;
		}catch(Exception $e){
			$result = array('Caught exception:'=>$e->getMessage());
			$this->response($this->json($result), 200);
		}
	}
	
	/**
	*
	*date created (dd-mm-yy)
	*date modified 15-4-2016(dd-mm-yy)
	*Update paletta range price
	*
	*@param (String)apikey
	*@param (int)printTypeId
	*@param (Array)lowerRangeArray
	*@param (Array)upperRangeArray
	*@param (Array)numPalettes
	*@param (Array)palettePriceArray
	*@param (Array)whitebasePriceArray
	*@return json data
	* 
	*/
	public function updatePaletteRangePrice()
	{      
		$apiKey = $this->_request['apikey'];
		if($this->isValidCall($apiKey))
		{
			$printTypeId = $this->_request['printTypeId'];
			$lowerRangeArray = $this->_request['lowerRangeArray'];
			$upperRangeArray = $this->_request['upperRangeArray'];
			$numPalettes = $this->_request['numPalettes'];
			$palettePriceArray = $this->_request['palettePriceArray'];
			$whitebasePriceArray = $this->_request['whitebasePriceArray'];
			$orderRangeIdArray = array();
			try{
				$sql="SELECT id FROM ".TABLE_PREFIX."print_order_range WHERE printtype_id=$printTypeId";
				$rangeIdFromValue = mysqli_query($this->db,$sql);
				while($rows = mysqli_fetch_array($rangeIdFromValue)) {
					$sql = "delete from ".TABLE_PREFIX."palette_range_price where order_range_id=".$rows['id'];
					$status = $this->executeGenericDMLQuery($sql);
				}
				
				$sql = "delete from ".TABLE_PREFIX."print_order_range where printtype_id=$printTypeId";
				$status = $this->executeGenericDMLQuery($sql);
				
				if(sizeof($lowerRangeArray))
				{
					$status = 0;
					for($i=0;$i<sizeof($lowerRangeArray);$i++) {
						$sql = "insert into ".TABLE_PREFIX."print_order_range(lower_limit,upper_limit,printtype_id,whitebase_price) values($lowerRangeArray[$i],$upperRangeArray[$i],$printTypeId,$whitebasePriceArray[$i])";
						$status = $this->executeGenericDMLQuery($sql);
						$sql="SELECT id FROM ".TABLE_PREFIX."print_order_range WHERE printtype_id=$printTypeId && lower_limit=$lowerRangeArray[$i] && upper_limit=$upperRangeArray[$i]";
						$result = mysqli_query($this->db,$sql);
						$row = mysqli_fetch_assoc($result);
						$orderRangeId = $row['id'];
						
						for($j=0;$j<$numPalettes;$j++) {
							$num = $j+1;
							$palettePrice = $palettePriceArray[$j][$i];
							$sql = "insert into ".TABLE_PREFIX."palette_range_price(order_range_id,num_palettes,price) values($orderRangeId,$num,$palettePrice)";
							$status .= $this->executeGenericDMLQuery($sql);
						}
					}
				}
				if($status){
					$this->_request['returns'] = true;
					$msg = $this->getPrintingDetails();
					$msg['status'] = 'success';
				}
				else $msg=array("status"=>"failed");
				
				$this->closeConnection();
				$this->response($this->json($msg), 200);
			}catch(Exception $e) {
				$result = array('Caught exception:'=>$e->getMessage());
				$this->response($this->json($result), 200);
			}
		}else{
			$msg=array("status"=>"invalid");
			$this->response($this->json($msg), 200);
		}
	}	
	
	/**
	*
	*date created (dd-mm-yy)
	*date modified 15-4-2016(dd-mm-yy)
	*Update minorder print quantity 
	*
	*@param (String)apikey
	*@param (int)printTypeId
	*@param (int)minOrderLim
	*@return json data
	* 
	*/
	public function updateMinPrintingQuantity(){      
		$apiKey = $this->_request['apikey'];
		if($this->isValidCall($apiKey))
		{
			$printTypeId = $this->_request['printTypeId'];
			$minOrderLim = $this->_request['minOrderLim'];
			try{
				$sql = "UPDATE ".TABLE_PREFIX."printing_details SET min_quantity = $minOrderLim WHERE id = $printTypeId";
				$status = $this->executeGenericDMLQuery($sql);
				$msg['status'] = ($status)?'success':'failed';
			}catch(Exception $e) {
				$msg = array('Caught exception:'=>$e->getMessage());
			}
		}else{
			$msg=array("status"=>"invalid");
		}
		$this->response($this->json($msg), 200);
	}
	
	/**
	*
	*date created (dd-mm-yy)
	*date modified 15-4-2016(dd-mm-yy)
	*Update setup price
	*
	*@param (String)apikey
	*@param (int)printTypeId
	*@param (float)setupPrice
	*@return json data
	* 
	*/	
	public function updateSetupPrice(){      
		$apiKey = $this->_request['apikey'];
		if($this->isValidCall($apiKey))
		{
			$printTypeId = $this->_request['printTypeId'];
			$setupPrice = $this->_request['setupPrice'];
			try{
				$sql = "UPDATE ".TABLE_PREFIX."printing_details SET setup_price = $setupPrice WHERE id = $printTypeId";
				$status = $this->executeGenericDMLQuery($sql);
			}catch(Exception $e) {
				$msg = array('Caught exception:'=>$e->getMessage());
			}
			$msg['status'] = ($status)?'success':'failed';
		}else{
			$msg=array("status"=>"invalid");
		}
		$this->response($this->json($msg), 200);
	}
	
	/**
	*
	*date created (dd-mm-yy)
	*date modified 15-4-2016(dd-mm-yy)
	*Update whitebase price
	*
	*@param (String)apikey
	*@param (int)printTypeId
	*@param (float)whitebasePrice
	*@return json data
	* 
	*/	
	public function updateWhitebasePrice(){      
		$apiKey = $this->_request['apikey'];
		if($this->isValidCall($apiKey))
		{
			$printTypeId = $this->_request['printTypeId'];
			$whitebasePrice = $this->_request['whitebasePrice'];
			try{
				$sql = "UPDATE ".TABLE_PREFIX."printing_details SET whitebase_price = $whitebasePrice WHERE id = $printTypeId";
				$status = $this->executeGenericDMLQuery($sql);
			}catch(Exception $e) {
				$msg = array('Caught exception:'=>$e->getMessage());
			}
			$msg['status'] = ($status)?'success':'failed';
		}else{
			$msg=array("status"=>"invalid");
		}
		$this->response($this->json($msg), 200);
	}
	
	/**
	*
	*date created (dd-mm-yy)
	*date modified 15-4-2016(dd-mm-yy)
	*To update color group price
	* 
	*@param (String)apikey
	*@param (Float)color_group_price
	*@param (int)print_method_id
	*@return json data
	* 
	*/
	public function updateColorGroupPrice($data=array()){	
		 //TRUNCATE print_method_color_group_rel;TRUNCATE color_price_group_rel;
		$status = 0;
		try{
			if(!empty($this->_request) && isset($this->_request['print_method_id']) && $this->_request['print_method_id'] && !empty($this->_request['color_group_price'])){
				extract($this->_request);
				$sql = "UPDATE ".TABLE_PREFIX."print_method_setting_rel pl, ".TABLE_PREFIX."print_setting ps SET ps.other_color_group_price = '".$other_color_group_price."' WHERE  ps.pk_id=pl.print_setting_id AND pl.print_method_id ='".$print_method_id."'";
				$status = $this->executeGenericDMLQuery($sql);
				$psql = "DELETE FROM ".TABLE_PREFIX."print_method_color_group_rel WHERE print_method_id ='".$print_method_id."'";
				$status = $this->executeGenericDMLQuery($psql);
				$s = '';
				if($color_group_price['pk_id'] == 1){
					$sql1 = "UPDATE ".TABLE_PREFIX."color_price_group SET price='".$color_group_price['price']."' WHERE pk_id='".$color_group_price['pk_id']."'";
					$status = $this->executeGenericInsertQuery($sql1);
				}
				if(!empty($color_group_price['color_ids'])){
					$ipgsql = '';
					foreach($color_group_price['color_ids'] as $v1){
						$ipgsql .= ",('".$v1."','".$color_group_price['pk_id']."')";
					}
					$s .= $ipgsql;
				}
				$pisql = "INSERT INTO ".TABLE_PREFIX."print_method_color_group_rel(print_method_id, color_group_id) VALUES ('".$print_method_id."','".$color_group_price['pk_id']."')";
				$status = $this->executeGenericDMLQuery($pisql);
				$pgsql = 'DELETE cpgrl,cpg FROM ".TABLE_PREFIX."color_price_group_rel AS cpgrl INNER JOIN ".TABLE_PREFIX."color_price_group AS cpg ON cpgrl.color_price_group_id=cpg.pk_id WHERE cpgrl.color_price_group_id='.$color_group_price['pk_id'];
				//$pgsql = "DELETE FROM color_price_group_rel WHERE color_price_group_id='".$color_group_price['pk_id']."'";						
				$status = $this->executeGenericDMLQuery($pgsql);
				if(!empty($color_group_price['color_ids'])){
					$ipgsql_query = "INSERT INTO ".TABLE_PREFIX."color_price_group_rel(color_id,color_price_group_id) VALUES".substr($s,1);
					$status = $this->executeGenericDMLQuery($ipgsql_query);
				}
				$setting = Flight::setting();
				$setting->allSettingsDetails(1);
			}
			$this->getAllPrintSettings($print_method_id);
		}catch(Exception $e) {
			$result = array('Caught exception:'=>$e->getMessage());
			$this->response($this->json($result), 200);
		}
	}
	
	/**
	*
	*date created (dd-mm-yy)
	*date modified 15-4-2016(dd-mm-yy)
	*set product  print method
	*Needs to be checked: when all the print types unchecked and submited.
	* 
	*@param (String)apikey
	*@param (int)productId
	*@param (int)prntmethodid
	*@return json data
	* 
	*/
	public function setProductPrintmethod(){
		$productId =$this->_request['productId'];
		$key = $this->_request['apikey'];
		if(isset($productId) && !empty($key) && $this->isValidCall($key)) {
			$printmethodid=	$this->_request['prntmethodid'];
			$printmethodId = explode(',',$printmethodid);
			$status = 0;
			$sql = 'DELETE FROM '.TABLE_PREFIX.'product_printmethod_rel WHERE product_id='.$productId;
			$this->executeGenericDMLQuery($sql);
			if(empty($this->_request['prntmethodid'])){
				$sql = 'DELETE FROM '.TABLE_PREFIX.'size_variant_additional_price WHERE product_id='.$productId;
				$status = $this->executeGenericDMLQuery($sql);
			}else{
				try{
					$pmid = '';$value = '';$c = sizeof($printmethodId);
					for($k = 0; $k < $c; $k++){
						$pmid .= ','.$printmethodId[$k];
						$value .= ",('$productId','$printmethodId[$k]')";
					}
					if(strlen($value)){
						$sql = 'insert into '.TABLE_PREFIX.'product_printmethod_rel(product_id,print_method_id) values'.substr($value,1);
						$status = $this->executeGenericInsertQuery($sql);
						
						$sql = 'DELETE FROM '.TABLE_PREFIX.'size_variant_additional_price WHERE product_id='.$productId.' AND print_method_id NOT IN('.substr($pmid,1).')';
						$status = $this->executeGenericDMLQuery($sql);
					}
				}catch(Exception $e) {
					$result = array('Caught exception:'=>$e->getMessage());
					$this->response($this->json($result), 200);
				}
			}
			$msg['status'] = ($status)?'success':'failed';
		}else
			$msg=array("status"=>"invalidkey");
		$this->response($this->json($msg), 200);
	}
	
	/**
	*
	*date created (dd-mm-yy)
	*date modified 15-4-2016(dd-mm-yy)
	*Add and update colo9r price group
	* 
	*@param (String)apikey
	*@param (int)print_method_id
	*@param (Float)color_grp_price
	*@param (int)pk_id
	*@return json data
	* 
	*/
	//TRUNCATE color_price_group;TRUNCATE color_price_group_rel;		
	public function addUpdateColorPriceGroup(){
		try{
			$status = 0;
			if(!empty($this->_request) && $this->_request['print_method_id'] && !empty($this->_request['color_grp_price'])){
				extract($this->_request);extract($color_grp_price);
				$sql = "UPDATE ".TABLE_PREFIX."print_setting SET other_color_group_price='".$other_color_group_price."'";
				$status = $this->executeGenericDMLQuery($sql);
				
				if(isset($color_grp_price['pk_id']) && $color_grp_price['pk_id']){
					//$sql = 'DELETE cpgrl,cpg FROM ".TABLE_PREFIX."color_price_group_rel AS cpgrl INNER JOIN ".TABLE_PREFIX."color_price_group AS cpg ON cpgrl.color_price_group_id=cpg.pk_id WHERE cpgrl.color_price_group_id='.$pk_id;				
					$sql = "DELETE FROM ".TABLE_PREFIX."color_price_group_rel WHERE color_price_group_id='".$pk_id."'";
					$status = $this->executeGenericDMLQuery($sql);
					
					$sql = "DELETE FROM ".TABLE_PREFIX."print_method_color_group_rel WHERE color_group_id='".$pk_id."' AND print_method_id='".$print_method_id."'";
					$status = $this->executeGenericDMLQuery($sql);
					
					$sql = "UPDATE ".TABLE_PREFIX."color_price_group SET price='".$price."' WHERE pk_id='".$pk_id."'";
					$status = $this->executeGenericDMLQuery($sql);
				}else{
					$sql = "INSERT INTO ".TABLE_PREFIX."color_price_group(name,price) VALUES('Basic','".$price."')";
					$pk_id = $this->executeGenericInsertQuery($sql);
					
				}
				
				$color_price_sql = "INSERT INTO ".TABLE_PREFIX."print_method_color_group_rel(print_method_id,color_group_id) VALUES ('".$print_method_id."','".$pk_id."')";
				$status = $this->executeGenericDMLQuery($color_price_sql);						   
				$isql = '';
				if(!empty($color_ids)){
					//$color_ids = array_unique($color_ids);
					foreach($color_ids as $v){
						$isql .= ",('".$v."','".$pk_id."')";
					}
					$sql = "INSERT INTO ".TABLE_PREFIX."color_price_group_rel(color_id,color_price_group_id) VALUES".substr($isql,1);
					$status = $this->executeGenericDMLQuery($sql);
				}
			}
		}catch(Exception $e) {
			$result = array('Caught exception:'=>$e->getMessage());
			$this->response($this->json($result), 200);
		}
	}
	
	/**
	*
	*date created (dd-mm-yy)
	*date modified 15-4-2016(dd-mm-yy)
	*Add product_category to print method
	* 
	*@param (String)apikey
	*@param (int)pid
	*@param (Array)product_category
	*@return json data
	* 
	*/
	public function addProductToPrintMethod($data=array()) {
		$req_arr = (!empty($this->_request))?$this->_request:$data;
		$status =0;
		if(isset($req_arr['pid']) && $req_arr['pid']!=''){
			$print_method_id = $req_arr['pid'];
			try{
				$delete_sql = "delete from ".TABLE_PREFIX."product_category_printmethod_rel where print_method_id='".$print_method_id."'";
				$status = $this->executeGenericDMLQuery($delete_sql);
				if(!empty($req_arr['product_category'])){
				// For Multiple Insertion
					$sql  = "INSERT INTO ".TABLE_PREFIX."product_category_printmethod_rel(print_method_id,product_category_id,is_enable) VALUES";
					foreach( $req_arr['product_category'] as $product_category){
						$sql .= "('".$print_method_id."', '".$product_category['id']."', '".$product_category['is_enable']."'),";
					}
					$sql = substr($sql,0,strlen($sql)-1);
					//echo $sql;exit;				   
					$status = $this->executeGenericDMLQuery($sql);
				}
				$setting = Flight::setting();
				$setting->allSettingsDetails(1);
				if($status)
				$this->getAllPrintSettings($print_method_id);
				else{
					$msg=array("status"=>"failed");
					$this->response($this->json($msg), 200);
				}
			}catch(Exception $e){
				$msg = array('Caught exception:'=>$e->getMessage());
			}
		}else{
			$msg=array("status"=>"nodata");
			$this->response($this->json($msg), 200);
		
		}
	}
	
	/**
	*
	*date created (dd-mm-yy)
	*date modified 15-4-2016(dd-mm-yy)
	*Add Fonts category to print method
	* 
	*@param (String)apikey
	*@param (int)pid
	*@param (Array)font_category
	*@return json data
	* 
	*/
	public function addFontToPrintMethod($data=array()) {
		$req_arr = (!empty($this->_request))?$this->_request:$data;
		$status =0;
		if(isset($req_arr['pid']) && $req_arr['pid'] !=''){
			$print_method_id = $req_arr['pid'];
			try{
				$delete_sql = "delete from ".TABLE_PREFIX."font_category_printmethod_rel where print_method_id='".$print_method_id."'";
				$status = $this->executeGenericDMLQuery($delete_sql);
				
				if(!empty($req_arr['font_category'])){
				// For Multiple Insertion
					$sql  = "INSERT INTO ".TABLE_PREFIX."font_category_printmethod_rel(print_method_id,font_category_id,is_enable) VALUES";
					foreach( $req_arr['font_category'] as $font_category){
						$sql .= "('".$print_method_id."', '".$font_category['id']."', '".$font_category['is_enable']."'),";
					}
					$sql = substr($sql,0,strlen($sql)-1);//echo $sql;exit;				   
					$status = $this->executeGenericDMLQuery($sql);
				}
				$setting = Flight::setting();
				$setting->allSettingsDetails(1);
				if($status)
					$this->getAllPrintSettings($print_method_id);
				else{
					$msg=array("status"=>"failed");
					$this->response($this->json($msg), 200);
				}
			}catch(Exception $e){
				$msg = array('Caught exception:'=>$e->getMessage());
			}
		}else{
			$msg=array("status"=>"nodata");
			$this->response($this->json($msg), 200);
		}
	}
	
	/**
	*
	*date created (dd-mm-yy)
	*date modified 15-4-2016(dd-mm-yy)
	*Add design category to print method
	* 
	*@param (String)apikey
	*@param (int)pid
	*@param (Array)design_category
	*@return json data
	* 
	*/
	public function addDesignToPrintMethod($data=array()) {
		$req_arr = (!empty($this->_request))?$this->_request:$data;
		$status =0;
		if(isset($req_arr['pid']) && $req_arr['pid']!=''){
			$print_method_id = $req_arr['pid'];
			try{
				$delete_sql = "delete from  ".TABLE_PREFIX."design_category_printmethod_rel where print_method_id='".$print_method_id."'";
				$status = $this->executeGenericDMLQuery($delete_sql);
				
				if(!empty($req_arr['design_category'])){
					// For Multiple Insertion
					$sql  = "INSERT INTO ".TABLE_PREFIX."design_category_printmethod_rel(print_method_id,design_category_id,is_enable) VALUES";
					foreach( $req_arr['design_category'] as $design_category){
						$sql .= "('".$print_method_id."', '".$design_category['id']."', '".$design_category['is_enable']."'),";
					}
					$sql = substr($sql,0,strlen($sql)-1);
					$status = $this->executeGenericDMLQuery($sql);
				}
				$setting = Flight::setting();
				$setting->allSettingsDetails(1);
				if($status)
					$this->getAllPrintSettings($print_method_id);
				else{
					$msg=array("status"=>"failed");
					$this->response($this->json($msg), 200);
				}
			}catch(Exception $e){
				$msg = array('Caught exception:'=>$e->getMessage());
			}					
		}else{
			$msg=array("status"=>"nodata");
			$this->response($this->json($msg), 200);
		}
	}
	
	/**
	*
	*date created (dd-mm-yy)
	*date modified 15-4-2016(dd-mm-yy)
	*Add template category to print method
	* 
	*@param (String)apikey
	*@param (int)pid
	*@param (Array)template_category
	*@return json data
	* 
	*/
	public function addTemplateToPrintMethod($data=array()) {
		$req_arr = (!empty($this->_request))?$this->_request:$data;
		$status =0;
		if(isset($req_arr['pid']) && $req_arr['pid'] !=''){
			$print_method_id = $req_arr['pid'];
			try{
				$delete_sql = "delete from  ".TABLE_PREFIX."template_category_printmethod_rel where print_method_id='".$print_method_id."'";
				$status = $this->executeGenericDMLQuery($delete_sql);
				if(!empty($req_arr['template_category'])){
				// For Multiple Insertion
					$sql  = "INSERT INTO ".TABLE_PREFIX."template_category_printmethod_rel(print_method_id,temp_category_id,is_enable) VALUES";
					foreach( $req_arr['template_category'] as $template_category){
						$sql .= "('".$print_method_id."', '".$template_category['id']."', '".$template_category['is_enable']."'),";
					}
					$sql = substr($sql,0,strlen($sql)-1);//echo $sql;exit;				   
					$status = $this->executeGenericDMLQuery($sql);
				}
				$setting = Flight::setting();
				$setting->allSettingsDetails(1);
				if($status)
					$this->getAllPrintSettings($print_method_id);
				else{
					$msg=array("status"=>"failed");
					$this->response($this->json($msg), 200);
				}
			}catch(Exception $e){
				$msg = array('Caught exception:'=>$e->getMessage());
			}
		}else{
			$msg=array("status"=>"nodata");
			$this->response($this->json($msg), 200);
		}
	}

	/**
	*
	*date of created 2-3-2016(dd-mm-yy)
	*date of Modified 13-4-2016(dd-mm-yy)
	*update print area price table data by print_method_id 
	*
	* @param (String)apikey 
	* @param (int)print_method_id 
	* @param (array)details  
	* @param (array)wht_base  
	* @param (array)area_prc  
	* @return JSON  success/failed
	* 
	*/ 
	public function addUpdatePrintAreaPrice(){
		extract($this->_request);
		$req_arr = $this->_request;
		if(isset($apikey) && $this->isValidCall($apikey)){
			try{
				$status  = 0;
				if((isset($is_color_table) && $is_color_table !='') || (isset($is_print_size) && $is_print_size !='')){
					$sql = "UPDATE ".TABLE_PREFIX."print_setting AS ps , ".TABLE_PREFIX."print_method_setting_rel AS pmsr
							SET ps.is_color_price_range='".$is_color_table."',ps.is_print_size='".$is_print_size."',ps.is_print_area_percentage='".$is_print_area_percentage."'
							WHERE ps.pk_id=pmsr.print_setting_id and pmsr.print_method_id=".$print_method_id;
					$status = $this->executeGenericDMLQuery($sql);
				}
				$sql_delete ="DELETE FROM ".TABLE_PREFIX."print_size_method_rel WHERE print_method_id='".$print_method_id."'";
				$result = $this->executeGenericDMLQuery($sql_delete);
				$usql  = "UPDATE ".TABLE_PREFIX."print_size_range SET ";
				$isql1  = "INSERT INTO ".TABLE_PREFIX."print_size_range(from_range,to_range) VALUES";
				if(!empty($req_arr['details'])){
					$val1 = '';$sql1 = '';
					foreach($req_arr['details'] as $v){
						if($v['range_id'] && $v['range_id']>=1){//for update range by range_id
							$sql1 = $usql."from_range='".$v['from']."',
							to_range='".$v['to']."'
							WHERE pk_id='".$v['range_id']."'";
							$status = $this->executeGenericDMLQuery($sql1);
							$rangeIds = $v['range_id'];
						}
						else{
							$val1 = $isql1."('".$v['from']."','".$v['to']."')";//for isert range by range_id
							$rangeIds = $this->executeGenericInsertQuery($val1);
						}
						foreach($v['area_prc'] as $v2){
							$sql = "INSERT INTO ".TABLE_PREFIX."print_size_method_rel
							(print_size_id,print_method_id,price,percentage,is_fixed,is_whitebase,print_size_range_id) 
							VALUES('".$v2['id']."','".$print_method_id."','".$v2['price']."','".$v2['percentage']."','".$v2['is_fixed']."','0'
							,'".$rangeIds."')";//for add print area price
							$status = $this->executeGenericDMLQuery($sql);
						}							
						if($v['wht_base']){
							$sql = "INSERT INTO ".TABLE_PREFIX."print_size_method_rel
							(print_size_id,print_method_id,price,percentage,is_fixed,is_whitebase,print_size_range_id) 
							VALUES('0','".$print_method_id."','".$v['wht_base']['price']."','".$v['wht_base']['percentage']."','".$v['wht_base']['is_fixed']."','".$v['wht_base']['is_whitebase']."'
								,'".$rangeIds."')";//for add white base price 
							$status = $this->executeGenericDMLQuery($sql);
						}
					}
				}
				unset($this->_request['print_method_id']);
				if($status){
				$settingsObj = Flight::setting();
				$settingsObj->allSettingsDetails(1);
				$this->getAllPrintSettings($print_method_id);
				}
			}catch(Exception $e) {
				$result = array('Caught exception print area price:'=>$e->getMessage());
				$this->response($this->json($result), 200);
			}
		}else $msg['status'] = 'invaliedkey';
		$this->response($this->json($msg), 200);
	}
}
