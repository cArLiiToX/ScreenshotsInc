<?php
/* Check Un-authorize Access */
if(!defined('accessUser')) die("Error");

class ColorSwatchStore extends UTIL {
	/**
	 * Add all the color inside prestashop
	 *
	 * @param (String)colorname
	 * @param (String)imagename
	 * @return  array contains all the color inside store
	*/
	public function addColorSwatch(){
		$error='';
		$result = $this->storeApiLogin();
		if($this->storeApiLogin == true){
			if(isset($this->_request['colorname']) && trim($this->_request['colorname'])!='') {
				$colorname = $this->_request['colorname'];
			} 
			if(isset($this->_request['imagename']) && trim($this->_request['imagename'])!='') {
				$imagename = trim($this->_request['imagename']);
			} 
			try {
				$result = $this->datalayer->addAttributeColorOptionValue($colorname,$imagename);

				$rsultrsponse = json_decode($result,true);
				if(isset($imagename) && $imagename!=''){
					$this->_request['value'] = $rsultrsponse['attribute_id'];
					$this->_request['imgData'] = $imagename;
					$this->_request['swatchWidth'] = 45;
					$this->_request['swatchHeight'] = 45;
					$this->_request['base64Data'] = base64_decode($imagename);	
					$saveSucss = $this->saveColorSwatch('add'); 
					$rsultrsponse['swatchImage'] =  $saveSucss['swatchImage'];
				}		
				 $result = json_encode($rsultrsponse);
			} catch(Exception $e) {
				$result = json_encode(array('isFault' => 1, 'faultMessage'=>$e->getMessage()));
				$error = true;
			}
			if(!$error){
					print_r($result);
			} else {
					print_r(json_decode($result));
			}
		}else{
			$msg=array('status'=>'apiLoginFailed','error'=>json_decode($result));
			$this->response($this->json($msg), 200);
		}
	}
	/**
	 * Update all the color inside prestashop
	 *
	 * @param (Int)option_id
	 * @param (String)colorname
	 * @param (String)imagename
	 * @return  array contains all the color inside store
	*/
	public function editSwachColor(){
		$error='';
		$result = $this->storeApiLogin();
		if($this->storeApiLogin == true){
			if(isset($this->_request['option_id']) && trim($this->_request['option_id'])!='') {
			  $option_id = $this->_request['option_id'];
			} 
			if(isset($this->_request['colorname']) && trim($this->_request['colorname'])!='') {
			  $colorname = $this->_request['colorname'];
			}
			if(isset($this->_request['imagename']) && trim($this->_request['imagename'])!='') {
				$imagename = trim($this->_request['imagename']);
			} 
			try {
				$result = $this->datalayer->editAttributeColorOptionValue($option_id,$colorname,$imagename);
				$rsultrsponse = json_decode($result,true);
				if(isset($imagename) && $imagename!=''){
					$this->_request['value'] = $option_id;
					$this->_request['imgData'] = $imagename;
					$this->_request['swatchWidth'] = 45;
					$this->_request['swatchHeight'] = 45;
					$this->_request['base64Data'] = base64_decode($imagename);	
					$saveSucss = $this->saveColorSwatch('add'); 
					$rsultrsponse['swatchImage'] =  $saveSucss['swatchImage']; 
				}		
				$result = json_encode($rsultrsponse);
			} catch(Exception $e) {
				$result = json_encode(array('isFault' => 1, 'faultMessage'=>$e->getMessage()));
				$error = true;
			}
			if(!$error){
				print_r($result);
			} else {
				print_r(json_decode($result));
			}
		}else{
			$msg=array('status'=>'apiLoginFailed','error'=>json_decode($result));
			$this->response($this->json($msg), 200);
		}
	}
}
