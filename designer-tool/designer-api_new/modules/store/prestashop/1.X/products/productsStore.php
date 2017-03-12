<?php
/* Check Un-authorize Access */
if(!defined('accessUser')) die("Error");

class ProductsStore extends UTIL {
	public function __construct(){
		parent::__construct();
		$this->datalayer = new Datalayer();
	}

	/**
	 * get simple product from store
	 *
	 * @param (int)id
	 * @param (int)confId
	 * @return  json array
	*/
	public function getSimpleProductClient(){
		$id = $this->_request['id'];
		$confId = $this->_request['confId'];
		$result = $this->datalayer->getSimpleProducts($id,$confId);
		if(empty($result)){
			$result = json_encode(array('No Records Found'));
			$error = true;
		}else{
			$resultArr = json_decode($result,true);
			$confProductId = $resultArr['pid'];
			$simpleProductId = $resultArr['pvid'];
			$this->_request['productid'] = $confProductId; 
			$this->_request['returns'] = true;
			$printsize = $this->getDtgPrintSizesOfProductSides($confProductId);	//dtg print size info		
			$resultArr['printsize'] = $printsize;
			$printareatype = $this->getPrintareaType($confProductId);			
			$resultArr['printareatype'] = $printareatype;
			// insert multiple boundary data; if available
            $multiBoundObj = Flight::multipleBoundary();
            $multiBoundData = $multiBoundObj->getMultiBoundMaskData($confProductId);
            if (!empty($multiBoundData)) {
                $resultArr['printareatype']['multipleBoundary'] = "true";
                $resultArr['multiple_boundary'] = $multiBoundData;
            }							
			$pCategories = $resultArr['category'];							
			$features = $this->fetchProductFeatures($confProductId, $pCategories);//get features details		
			$resultArr['features'] = $features;
			$templates = array();
			if(isset($confId) && $confId){
				$sql = "SELECT template_id FROM template_product_rel WHERE product_id = ".$confId;
				$res = $this->executeFetchAssocQuery($sql);
				foreach($res as $k=>$v){
					$templates[$k] = $v['template_id']; 
				}
			}
			$resultArr['templates'] = $templates;
			$resultArr['sizeAdditionalprices'] = $this->getSizeVariantAdditionalPriceClient($confProductId,$this->_request['print_method_id']);	// get variant additional price
			$surplusPrice = $resultArr['price'];
			if(isset($id) && $id){
				$sql = "SELECT ref_id,parent_id FROM ".TABLE_PREFIX."template_state_rel WHERE temp_id = ".$confId;
				$parentId = $this->executeFetchAssocQuery($sql);
				if(!empty($parentId)){
					$sql = "SELECT custom_price FROM ".TABLE_PREFIX."decorated_product WHERE product_id = ".$parentId[0]['parent_id']." and refid = ".$parentId[0]['ref_id'];
					$res = $this->executeFetchAssocQuery($sql);
					$customPrice = $res[0]['custom_price'];
					$resultArr['price'] = $surplusPrice - $customPrice;
					$resultArr['finalPrice'] = $surplusPrice;
				}
			}
            $sql = "SELECT distinct pk_id, print_method_id,price,is_whitebase
				FROM   product_additional_prices
				WHERE  product_id =".$confProductId." 
				AND variant_id =".$simpleProductId." ORDER BY pk_id";
			$rows = $this->executeFetchAssocQuery($sql);
			$priceDetails = array();
			if(!empty($rows)){
				foreach($rows as $k=>$v){
					$priceDetails[$k]['prntMthdId']    = $v['print_method_id'];
					$priceDetails[$k]['prntMthdPrice'] = $v['price'];
					$priceDetails[$k]['is_whitebase'] = intval($v['is_whitebase']);
				}
			}
			$resultArr['additionalprices'] = $priceDetails;
			$resultArr['is_product_template'] = false;
			$templateArr = $this->getProductTemplateByProductId($confProductId);	
			if(!empty($templateArr) && $templateArr['tepmlate_id']!=''){
				$resultArr['is_product_template']  = true;
				$resultArr['tepmlate_id'] = $templateArr['tepmlate_id'];
				if(!empty($templateArr['thumbsides']) && !empty($templateArr['sides'])){
					$resultArr['thumbsides'] = $templateArr['thumbsides'];
					$resultArr['sides'] = $templateArr['sides'];
				}else{
					$resultArr['thumbsides'] =[];
					$resultArr['sides'] = [];
				}
				$maskInfo = $this->getMaskData(sizeof($templateArr['side_id']));
				$resultArr['maskInfo']= json_decode($maskInfo);
			}else{
				$maskInfo = $this->getMaskData(sizeof($resultArr['sides']));
				$resultArr['maskInfo'] = json_decode($maskInfo);
			}
			$result = json_encode($resultArr);
			print_r($result);exit;
		}

	}
	/**
	* getVariants - Get Product Variant List
	*
	* @param int start
	* @param int range
	* @param int offset
	* @param int store
	* @param int conf_pid 
	* @return json of Product variants
	*
	*/
	public function getVariants(){
		$error = false;
		$start = 0;
		
		// Initialize variables //
		if(isset($this->_request['start']) && trim($this->_request['start'])!=''){
			$start = trim($this->_request['start']);
		}
		if(isset($this->_request['range']) && trim($this->_request['range'])!='' && trim($this->_request['range'])!=0) {
			$limit = trim($this->_request['range']);
		}else{
			$limit = 0;
		}
		$offset = (isset($this->_request['offset']) && trim($this->_request['offset'])!='')?trim($this->_request['offset']):1;
		$store = (isset($this->_request['store']) && trim($this->_request['store'])!='')?trim($this->_request['store']):1;
		$confId = $this->_request['conf_pid'];
		
		try{
			$filters = array("id_product" => $confId);
			$result = $this->datalayer->getVariants($filters);
			$start = (($offset-1)*$limit)+1;
			$end = ($start + $limit) - 1;
			$tot_counter=1;
			$resultNewArr = array();
			
			foreach($result as $key_res => $val_res){
				if($end == 0){ // all records to push to array //
					array_push($resultNewArr,$val_res);
				}else{
					if($tot_counter >= $start && $tot_counter <= $end){
						array_push($resultNewArr,$val_res);
					}else{
						// don't do anything.. //
					}
				}
				$tot_counter++;
			}
			/* foreach($resultNewArr as $k1 => $v1){
				$swatchDir = $this->getSwatchURL();
				$swatchPath = $this->getSwatchesPath();
				$swatchFilePath = $swatchPath.'/45x45/'.$v1['colorUrl'];
				$swatchFileDir = $swatchDir.'45x45/'.$v1['colorUrl'];
				if (file_exists($swatchFilePath)){
					$resultNewArr[$k1]['colorUrl'] = $swatchDir.'45x45/'.$v1['colorUrl'];
				}else{
					$resultNewArr[$k1]['colorUrl'] = "";
				}
			} */
			$resultArr = json_encode(array("variants" => $resultNewArr, "count" => count($result)));
		}catch(Exception $e){
			$resultArr = json_encode(array('isFault' => 1, 'faultMessage'=>$e->getMessage()));
			$error = true;
		}
		if(!$error){
			$this->response($resultArr, 200);
		}else{
			print_r(json_decode($resultArr));exit;                    
		}
	}
	/**
	 * Get all size and quantity  by product id from store
	 *
	 * @param (Int)productId
	 * @param (Int)simplePdctId
	 * @return json array 
	*/
	public function getSizeAndQuantity(){
		$error = false;
		$result = $this->storeApiLogin();
		if($this->storeApiLogin==true){
			if(!isset($this->_request['productId']) || trim($this->_request['productId'])=='') {
				$msg=array('status'=>'invalid productId','productId'=>$this->_request['productId']);
				$this->response($this->json($msg), 204);
			} else {
				$product_id = trim($this->_request['productId']);
			}
			if(!isset($this->_request['simplePdctId']) || trim($this->_request['simplePdctId'])=='') {
				$msg=array('status'=>'invalid simplePdctId','simplePdctId'=>$this->_request['simplePdctId']);
				$this->response($this->json($msg), 204);
			} else {
				$varient_id = trim($this->_request['simplePdctId']);
			}
			if(isset($this->_request['byAdmin'])) {
				$byAdmin = true;
			} else {
				$byAdmin = false;
			}
			if(!$error) {
				try {				
					if(!$byAdmin){
						$result	= $this->datalayer->getSizeAndQuantity($product_id,$varient_id);
					}else{
						$result	= $this->datalayer->getSizeVariants($product_id,$varient_id);
					}
				} catch (Exception $e) {
					$result = json_encode(array('isFault' => 1, 'faultMessage'=>$e->getMessage()));
					$error = true;
				}
			}
			print_r($result);exit;
		}else{
			$msg=array('status'=>'apiLoginFailed','error'=>json_decode($result));
			$this->response($this->json($msg), 200);
		}
	}
	/**
	* Used to get all products which are eligible to customize
	*
	* @param (Int)categoryid, 
	* @param (String)searchstring, 
	* @param (Int)start, 
	* @param (Int)limit, 
	* @param (boolean)loadVariants (To filter the product list)
	* @return  list of products which are eligible to customize
	*/
	public function getAllProducts(){
		$categoryid = (isset($this->_request['categoryid']) && trim($this->_request['categoryid'])!='')?trim($this->_request['categoryid']):0;
		$searchstring = (isset($this->_request['searchstring']) && trim($this->_request['searchstring'])!='')?trim($this->_request['searchstring']):'';
		$start = (isset($this->_request['start']) && trim($this->_request['start'])!='')?trim($this->_request['start']):0;
		$limit = (isset($this->_request['range']) && trim($this->_request['range'])!='')?trim($this->_request['range']):10;
		$offset = (isset($this->_request['offset']) && trim($this->_request['offset'])!='')?trim($this->_request['offset']):1;
		$loadVariants = (isset($this->_request['loadVariants']) && trim($this->_request['loadVariants'])==true)?true:false;
		$preDecorated = (isset($this->_request['preDecorated']) && trim($this->_request['preDecorated'])=='true')?true:false;
		$start = (int)$limit * ((int)$offset - 1);
		try{
			$result = $this->datalayer->getAllProducts($start,$limit,$searchstring,$categoryid,$loadVariants,$preDecorated);
			$result = json_decode($result,true);
			$finalResult = array();
			//fetch for print detauls by product id
			$sql ="SELECT distinct pm.pk_id as printid,pm.name as printName
					FROM ".TABLE_PREFIX."print_method pm
					JOIN ".TABLE_PREFIX."print_setting  pst ON pm.pk_id=pst.pk_id
					LEFT JOIN ".TABLE_PREFIX."print_method_setting_rel pmsr ON pst.pk_id=pmsr.print_setting_id where pst.is_default=1";
			$default_id = $this->executeFetchAssocQuery($sql);
			if(!empty($result['product'])){
				foreach($result['product'] as $k=>$product) {						
					$productPrintTypeSql ="SELECT distinct pm.pk_id, pm.name FROM ".TABLE_PREFIX."print_method pm 
					INNER JOIN ".TABLE_PREFIX."product_printmethod_rel ppr ON ppr.print_method_id=pm.pk_id 
					WHERE ppr.product_id=".$product['id'];
					$productPrintType = $this->executeGenericDQLQuery($productPrintTypeSql);
					if(!empty($productPrintType)){
						foreach($productPrintType as $k2=>$v2){
							$product['print_details'][$k2]['prntMthdId']= $v2['pk_id'];
							$product['print_details'][$k2]['prntMthdName']= $v2['name'];							
						}
					}else{
						$catIds = $product['category'];
						$catIds = implode(',',(array)$catIds);
						$catSql = 'SELECT DISTINCT pm.pk_id, pm.name
								FROM '.TABLE_PREFIX.'product_category_printmethod_rel AS pcpml
								JOIN '.TABLE_PREFIX.'print_method AS pm ON pm.pk_id = pcpml.print_method_id WHERE pcpml.product_category_id IN('.$catIds.')';
						$rows = $this->executeFetchAssocQuery($catSql);
						if(empty($rows)){
							$default_print_type = "SELECT pmsr.print_method_id,pm.name FROM ".TABLE_PREFIX."print_method_setting_rel AS pmsr JOIN ".TABLE_PREFIX."print_setting ps ON pmsr.print_setting_id=ps.pk_id JOIN ".TABLE_PREFIX."print_method AS pm ON pmsr.print_method_id=pm.pk_id WHERE ps.is_default='1' AND pm.is_enable='1' LIMIT 1";
							$res = $this->executeFetchAssocQuery($default_print_type);
							$product['print_details'][0]['prntMthdId']= $res[0]['print_method_id'];
							$product['print_details'][0]['prntMthdName']= $res[0]['name'];
						}else{
							foreach($rows as $k1=>$v1){
								$product['print_details'][$k1]['prntMthdId']= $v1['pk_id'];
								$product['print_details'][$k1]['prntMthdName']= $v1['name'];							
							}
						}
					}						
					$result['product'][$k] =$product;
				}
			}
			print_r(json_encode($result)); exit;
		} catch(Exception $e) {
			$result = json_encode(array('isFault' => 1, 'faultMessage'=>$e->getMessage()));
		}
	}
	/**
	 * get all categories from store
	 *
	 * @param  (Int)printId
	 * @return  json array
	*/
	public function getCategories() {
		$error='';
		$result = $this->storeApiLogin();
		$print_id = $this->_request['printId'];
		if($this->storeApiLogin==true){;
			try {
				$result = $this->datalayer->getCategories();					
				if(isset($print_id)&& $print_id!=0){
					$categories = json_decode($result,true);
					$category_result = array();
					$sql="SELECT product_category_id FROM ".TABLE_PREFIX."product_category_printmethod_rel WHERE print_method_id='$print_id'";
					$category =array();
					$rows = $this->executeGenericDQLQuery($sql);
					$category = $rows;
					foreach($categories['categories'] as $categories) {
						for ($j=0; $j < sizeof($category); $j++) {
							if ($categories['id'] == $category[$j]['product_category_id']) {
								$category_result[$j]['id']=$categories['id'];
								$category_result[$j]['name']=$categories['name'];
							}
						}
					}
					$result_arr = array();
					$result_arr['categories']=array_values($category_result);
					$this->response($this->json($result_arr), 200);
				}
			} catch(Exception $e) {
				$result = json_encode(array('isFault' => 1, 'faultMessage'=>$e->getMessage()));
				$error = true;
			}
				
			if(!$error){
				print_r($result);exit;
			} else {
				print_r(json_decode($result));exit;
			}
		}else{
			$msg=array('status'=>'apiLoginFailed','error'=>json_decode($result));
			$this->response($this->json($msg), 200);
		}
	}
	/**
	 * get all subcategories from store
	 *
	 * @param (int)selectedCategory
	 * @return  json array
	*/
	public function getsubCategories(){
		$error='';
		$result = $this->storeApiLogin();
		if($this->storeApiLogin==true){
			$key = $GLOBALS['params']['apisessId'];
			try {
				$result = $this->datalayer->getsubCategories($this->_request['selectedCategory']);
			} catch(Exception $e) {
				$result = json_encode(array('isFault' => 1, 'faultMessage'=>$e->getMessage()));
				$error = true;
			}					
			if(!$error){
					$categories = array();
					print_r($result);exit;
			} else {
					print_r(json_decode($result));exit;
			}
		}else{
			$msg=array('status'=>'apiLoginFailed','error'=>json_decode($result));
			$this->response($this->json($msg), 200);
		}
	}
	/**
	 * fetch product by print method id
	 *
	 * @param (Int)pid
	 * @return json array 
	*/
	public function getPrintMethodByProduct($additional_price = false) {
		if(isset($this->_request['pid']) && $this->_request['pid']){
			$result_arr = array();
			$confProductId = $this->_request['pid'];
			//  Do not send any print method ID for multiple boundary product
            $MultiBoundQry = "SELECT * FROM " . TABLE_PREFIX . "multi_bound_print_profile_rel WHERE product_id = '" . $confProductId . "'";
            $records = $this->executeFetchAssocQuery($MultiBoundQry);
            if (!empty($records)) {
                $result_arr[0]['print_method_id'] = 0;
                $result_arr[0]['name'] = "multiple";
                $result_arr[0]['fetched_from'] = 'DB';
            } else {
				$fieldSql = 'SELECT distinct pm.pk_id AS print_method_id, pm.name';
				if($additional_price)$fieldSql .= ', pst.additional_price';
				// Check whether product has specific print method assigned //
				$productPrintTypeSql = $fieldSql.' FROM '.TABLE_PREFIX."print_method pm 
				INNER JOIN ".TABLE_PREFIX."product_printmethod_rel ppr ON ppr.print_method_id=pm.pk_id 
				JOIN ".TABLE_PREFIX."print_setting AS pst ON pm.pk_id=pst.pk_id
				WHERE ppr.product_id=".$confProductId;
				$res = $this->executeFetchAssocQuery($productPrintTypeSql);
				$result_arr = array();
				if(empty($res)){
					try {
						//fetch  category_id by product id from store
						$result = $this->datalayer->getCategoriesByProduct($this->_request['pid']);
						$catIds = json_decode($result,true);
						$print = Flight::printProfile();
						if(!empty($catIds)){
							$catIds = implode(',',$catIds);
							$catSql = $fieldSql.' FROM '.TABLE_PREFIX.'product_category_printmethod_rel AS pcpml
									JOIN '.TABLE_PREFIX.'print_method AS pm ON pm.pk_id = pcpml.print_method_id 
									JOIN '.TABLE_PREFIX.'print_setting AS pst ON pm.pk_id=pst.pk_id
									LEFT JOIN '.TABLE_PREFIX.'print_method_setting_rel pmsr ON pst.pk_id=pmsr.print_setting_id 
									WHERE pcpml.product_category_id IN('.$catIds.')';
							$res = $this->executeFetchAssocQuery($catSql);
							foreach($res as $k=>$v){
								$result_arr[$k]['print_method_id'] = $v['print_method_id'];
								$result_arr[$k]['name'] = $v['name'];
								$result_arr[$k]['fetched_from'] = 'category';
							}
							if(empty($res)){
								$res = $print->getDefaultPrintMethodId();
								foreach($res as $k=>$v){
									$result_arr[$k]['print_method_id'] = $v['print_method_id'];
									$result_arr[$k]['name'] = $v['name'];
									$result_arr[$k]['fetched_from'] = 'default';
								}
							}
						}else{
							$res = $print->getDefaultPrintMethodId();
							foreach($res as $k=>$v){
								$result_arr[$k]['print_method_id'] = $v['print_method_id'];
								$result_arr[$k]['name'] = $v['name'];
								$result_arr[$k]['fetched_from'] = 'default';
							}
						}
					} catch(Exception $e) {
						$result_arr = json_encode(array('isFault' => 1, 'faultMessage'=>$e->getMessage()));					
					}
				}else{
					foreach($res as $k=>$v){
						$result_arr[$k]['print_method_id'] = $v['print_method_id'];
						$result_arr[$k]['name'] = $v['name'];
						$result_arr[$k]['fetched_from'] = 'product';
					}
				}
			}
			$this->response($this->json($result_arr), 200);
		}else{
			$msg=array('status'=>'apiLoginFailed','error'=>json_decode($result));
			$this->response($this->json($msg), 200);
		}
	}
	/**
	 * get simple product by product id from store
	 *
	 * @param   (int)id
	 * @param   (int)confId
	 * @param   (int)size
	 * @return  json array
	*/
	public function getSimpleProduct(){
		$error = false;
		$result = $this->storeApiLogin();
		if($this->storeApiLogin==true){
			if(!isset($this->_request['id']) || trim($this->_request['id'])=='') {
				$msg=array('status'=>'invalid id','id'=>$this->_request['id']);
				$this->response($this->json($msg), 204); //terminate
			} else {
				$id = trim($this->_request['id']);
			}
			if(!isset($this->_request['confId']) || trim($this->_request['confId'])==''){
				$confId = trim($this->_request['id']);				
			}else{
				$confId = trim($this->_request['confId']);
			}
			if(!isset($this->_request['size']) || trim($this->_request['size'])=='') {
				$size = '';
			} else {
				$size = trim($this->_request['size']);
			}
			$attributes = array();
			if($size!='') {
				$attributes['size'] = $size;
			}
			if(!$error) {
				try {
					//fetch simple product by product id from store//
					$result = $this->datalayer->getSimpleProducts($id,$confId);
					if(empty($result)){
						$result = json_encode(array('No Records Found'));
						$error = true;
					}else{
						$resultArr = json_decode($result,true);
						$confProductId = $resultArr['pid'];
						$simpleProductId = $resultArr['pvid'];
						$this->_request['productid'] = $confProductId; //Mask Info
						$this->_request['returns'] = true; 

						$printsize = $this->getDtgPrintSizesOfProductSides($confProductId);			
						$resultArr['printsize'] = $printsize;
						
						$printareatype = $this->getPrintareaType($confProductId);			
						$resultArr['printareatype'] = $printareatype;
						// insert multiple boundary data; if available
			            $multiBoundObj = Flight::multipleBoundary();
			            $multiBoundData = $multiBoundObj->getMultiBoundMaskData($confProductId);
			            if (!empty($multiBoundData)) {
			                $resultArr['printareatype']['multipleBoundary'] = "true";
			                $resultArr['multiple_boundary'] = $multiBoundData;
			            }

						$additionalprices = $this->getAdditionalPrintingPriceOfVariants($confProductId, $simpleProductId);			
						$resultArr['additionalprices'] = $additionalprices;

						$resultArr['sizeAdditionalprices'] = $this->getSizeVariantAdditionalPrice($confProductId);
						$pCategories = $resultArr['category'];							

						$features = $this->fetchProductFeatures($confProductId, $pCategories);			
						$resultArr['features'] = $features;
						$templates = array();
						if(isset($confId) && $confId){
							$sql = "SELECT template_id FROM ".TABLE_PREFIX."template_product_rel WHERE product_id = ".$confId;
							$res = $this->executeFetchAssocQuery($sql);
							foreach($res as $k=>$v){
								$templates[$k] = $v['template_id']; 
							}
						}
						$resultArr['templates'] = $templates;
						$resultArr['discountData'] = $this->getDiscountToProduct($confId);
						$resultArr['is_product_template'] = false;
						$templateArr = $this->getProductTemplateByProductId($confProductId);	
						if(!empty($templateArr) && $templateArr['tepmlate_id']!=''){
							$resultArr['is_product_template']  = true;
							$resultArr['tepmlate_id'] = $templateArr['tepmlate_id'];
							if(!empty($templateArr['thumbsides']) && !empty($templateArr['sides'])){
								$resultArr['thumbsides'] = $templateArr['thumbsides'];
								$resultArr['sides'] = $templateArr['sides'];
							}else{
								$resultArr['thumbsides'] =[];
								$resultArr['sides'] = [];
							}
							$maskInfo = $this->getMaskData(sizeof($templateArr['side_id']));
							$resultArr['maskInfo']= json_decode($maskInfo);
						}else{
							$maskInfo = $this->getMaskData(sizeof($resultArr['sides']));
							$resultArr['maskInfo'] = json_decode($maskInfo);
						}
						$result = json_encode($resultArr);	
					}	
				} catch (Exception $e) {
					$result = json_encode(array('isFault' => 1, 'faultMessage'=>$e->getMessage()));
					$error = true;
				}
			}
			print_r($result);exit;
		}else{
			$msg=array('status'=>'apiLoginFailed','error'=>json_decode($result));
			$this->response($this->json($msg), 200);
		}

	}
	/**
	 * Check whether the given sku exists or doesn't
	 *
	 * @param   $sku_arr 
	 * @return  true/false
	 */
	public function checkDuplicateSku(){// chk for storeid
		$error = false;
		$result = $this->storeApiLogin();
		if(!empty($this->_request) && $this->storeApiLogin==true){
			if(!$error) {
				$filters = array(
					'sku_arr' => $this->_request['sku_arr']
				);
				try {
					$result	= $this->json(array());
				} catch (Exception $e) {
					$result = json_encode(array('isFault inside apiv4: ' => 1, 'faultMessage'=>$e->getMessage()));
					$error = true;
				}
			}
			$this->closeConnection();
			print_r($result);exit;
		}else{
			$msg=array('status'=>'apiLoginFailed','error'=>json_decode($result));
			$this->response($this->json($msg), 200);
		}
	}
	/**
	 * Fetch all size attribute from store
	 *
	 * @param   nothing
	 * @return  array contains all the xe_size inside store
	 */
	 
	public function getSizeArr(){
		$error='';$result = $this->storeApiLogin();
		if($this->storeApiLogin == true){
			try {
				$result = $this->datalayer->getSizeArr();
			} catch(Exception $e) {
				$result = json_encode(array('isFault' => 1, 'faultMessage'=>$e->getMessage()));
				$error = true;
			}
			if(!$error){
				$categories = array();
				print_r($result);exit;
			} else {
				print_r(json_decode($result));exit;
			}
		}else{
			$msg=array('status'=>'apiLoginFailed','error'=>json_decode($result));
			$this->response($this->json($msg), 200);
		}
	}
	/**
	 * Check whether xetool is enabled or disabled
	 *
	 * @param   nothing
	 * @return  true/false
	 */
	public function checkDesignerTool($t=0) {
		if($t) return $result ='Enabled';
		else return $result ='Disabled';
	}
	/**
	 * Check magento version
	 *
	 * @param   nothing
	 * @return  string $version
	 */
	public function storeVersion(){
		$result = $this->storeApiLogin();
		if($this->storeApiLogin == true){
			try {
				$result	= $this->datalayer->storeVersion();
				return $version = (!empty($result))?strchr($result,'.',true):1;
			} catch(Exception $e) {
				$result = json_encode(array('isFault' => 1, 'faultMessage'=>$e->getMessage()));
				$error = true;
			}
		}else{
			$msg=array('status'=>'apiLoginFailed','error'=>json_decode($result));
			$this->response($this->json($msg), 200);
		}
	}
	/**
	*
	*date created (dd-mm-yy)
	*date modified 15-4-2016 (dd-mm-yy)
	*Build Product Array
	*
	*@param (String)apikey
	*@param (Array)cartArr
	*@param (Int)refid
	*@return Array or boolean value
	* 
	*/
	public function buildProductArray($cartArr, $refid){
		try{
			$configProductId = $cartArr['id'];
			$custom_price = $cartArr['addedprice'];
			//$cutom_design_refId = $cartArr['refid'];
			$cutom_design_refId = $refid;
			$quantity = $cartArr['qty'];
			$simpleProductId = $cartArr['simple_product']['simpleProductId'];
			//$color1 = $cartArr['simple_product']['color1'];
			$xeColor = $cartArr['simple_product']['xe_color'];
			$xeSize = $cartArr['simple_product']['xe_size'];
			$product = array(
							 "product_id" => $configProductId, 
							 "qty" => $quantity,
							 "simpleproduct_id" => $simpleProductId,
							 "options"=>array('xe_color'=>$xeColor, 'xe_size'=>$xeSize),
							 "custom_price" => $custom_price,
							 "custom_design" => $cutom_design_refId,
					 );
			if($quantity>0)		 
				return $product;
			else
			return false;
		}catch(Exception $e){
			$result = array('Caught exception:'=>$e->getMessage());
			return $result;
		}
	}
	/**
	*
	*date created (dd-mm-yy)
	*date modified 15-4-2016(dd-mm-yy)
	*Fetch color swatches 
	* 
	* @param (String)apikey
	* @return json data
	* 
	*/
	public function fetchColorSwatch(){
		$isSameClass = true;
		$colorOptions = $this->getColorArr($isSameClass);
		if(!is_array($colorOptions)){
			$colorOptions = $this->formatJSONToArray($colorOptions);
		}
		$dir = $this->getSwatchURL();
		$filePath = $this->getSwatchesPath();
		try{			
			foreach($colorOptions as $key=>$value){
				//$swatchImageFile = $filePath.'/'.$value->value.'.png';
				$swatchFilePath = $filePath.'/45x45/'.$value->value.'.png';
				$swatchFileDir = $dir.'45x45/'.$value->value.'.png';
				if (file_exists($swatchFilePath)){
					//$colorOptions[$key]->swatchImage = $dir.$value->value.'.png';
					$colorOptions[$key]->swatchImage = $swatchFileDir;
				}else{
					$colorOptions[$key]->swatchImage = '';
				}	
				$colorOptions[$key]->width = 45;					
				$colorOptions[$key]->height = 45;					
			}
		}catch(Exception $e){
			$result = array('Caught exception:'=>$e->getMessage());
			$this->response($this->json($result), 200);
		}
		$this->response(json_encode($colorOptions), 200);
	}
	/**
	 * Get all  customisation product id from store
	 *
	 * @param nothing
	 * @return  array 
	*/
	public function checkIsCustomiseProduct(){
		$isCustomize = $this->datalayer->checkIsCustomiseProduct();
		print_r($isCustomize);exit;
	}
	/**
	*Check customisation by productid 
	*
	* @param (Int)productid
	* @return json array 
	*
	*/
	public function checkIsCustomiseByProductId(){
		$result = $this->datalayer->checkIsCustomiseByProductId($this->_request['productid']);
		$this->response($this->json($result), 200);
	}
	/**
	*Fetch product by cart id froom store
	*
	* @param (Int)cartId
	* @return json array 
	*
	*/
	public function fetchProductBycartId(){
		$cartId =$this->_request['cartId'];
		$isCustomize = $this->datalayer->fetchProductBycartId($cartId);
		$this->response($this->json($isCustomize), 200);
	}
	/**
	*Module install/active in store backend 
	*
	* @param nothing
	* @return string 
	*
	*/
	public function installModule(){
		echo $installModule = $this->datalayer->installModule();exit;
	}
	/**
	*get cms page id
	*
	* @param nothing
	* @return int 
	*
	*/
	public function getCmsPageId(){
		echo $id = $this->datalayer->getCmsPageId();exit;
	}
	/**
	*Add product attribue in store backend 
	*
	* @param nothing
	* @return string 
	*
	*/
	public function addProduct(){
		echo $product = $this->datalayer->addProduct();exit;
	}
	/**
	 * Used to get all the color inside prestashop
	 *
	 * @param (Int)lastLoaded
	 * @param (Int)loadCount
	 * @return  array contains all the color inside store
	*/
	public function getColorArr($isSameClass=false){
		$productid =$this->_request['productId']?$this->_request['productId']:0;
		$error='';$result = $this->storeApiLogin();
		if($this->storeApiLogin == true){
			try {
				$result = $this->datalayer->getColorArr($this->_request['lastLoaded'],$this->_request['loadCount'],$productid);
				print_r($result);exit;
			} catch(Exception $e) {
				$result = json_encode(array('isFault' => 1, 'faultMessage'=>$e->getMessage()));
				$error = true;
			}
			if(!$error){
				if($isSameClass){
					return $result;
				}else{
					print_r($result);exit;
				}
			} else {
					print_r(json_decode($result));exit;
			}
		}else{
			$msg=array('status'=>'apiLoginFailed','error'=>json_decode($result));
			$this->response($this->json($msg), 200);
		}
	}
	/**
	 * fetch product by print method id
	 *
	 * @param (Int)productid
	 * @return json array 
	*/
	public function getProductPrintMethod(){
		$productId = $this->_request['productid'];
		$key=$this->_request['apikey'];
		if(!empty($productId)){
			$error=false;
			$productPrintTypeSql ="SELECT distinct pm.pk_id, pm.name FROM print_method pm 
			INNER JOIN product_printmethod_rel ppr ON ppr.print_method_id=pm.pk_id 
			JOIN print_setting AS pst ON pm.pk_id=pst.pk_id
			WHERE ppr.product_id=".$productId;
			$productPrintType = $this->executeGenericDQLQuery($productPrintTypeSql);
			if(!empty($productPrintType)){
				foreach($productPrintType as $k2=>$v2){
					$printDetails[$k2]['print_method_id']= $v2['pk_id'];
					$printDetails[$k2]['name']= $v2['name'];	
				}
			}else{	
				try {
					//fetch  category_id by product id from store
					$result = $this->datalayer->getCategoriesByProduct($productId);
					$catIds = json_decode($result);
					$catIds = implode(',',(array)$catIds);
						$catSql = 'SELECT DISTINCT pm.pk_id, pm.name
						FROM product_category_printmethod_rel AS pcpml
						JOIN print_method AS pm ON pm.pk_id = pcpml.print_method_id 
						JOIN print_setting AS pst ON pm.pk_id=pst.pk_id
						LEFT JOIN print_method_setting_rel pmsr ON pst.pk_id=pmsr.print_setting_id 
						WHERE pcpml.product_category_id IN('.$catIds.')';
						$rows = $this->executeFetchAssocQuery($catSql);
					$printDetails = array();
					if(empty($rows)){
						$default_print_type = "SELECT pm.pk_id,pm.name
						from print_method AS pm 
						JOIN print_setting ps ON pm.pk_id=ps.pk_id
						LEFT JOIN print_method_setting_rel pmsr ON ps.pk_id=pmsr.print_setting_id
						WHERE ps.is_default='1' AND pm.is_enable='1' AND ps.is_default='1'";	

						$res = $this->executeFetchAssocQuery($default_print_type);
						$printDetails[0]['print_method_id']= $res[0]['pk_id'];
						$printDetails[0]['name']= $res[0]['name'];
					}else{
						
						foreach($rows as $k1=>$v1){
						$printDetails[$k1]['print_method_id']= $v1['pk_id'];
						$printDetails[$k1]['name']= $v1['name'];	
						}
					}
					
				} catch(Exception $e) {
					$result = json_encode(array('isFault' => 1, 'faultMessage'=>$e->getMessage()));
					$error=true;
				}
			}
			if(!$error){
				$resultArr = $printDetails;
				$result = json_encode($resultArr);
				$this->response($this->json($resultArr), 200);					
			}else{
				print_r($result);exit;
			}
		}else{
			$msg=array("status"=>"invalid Product Id");
			$this->response($this->json($msg), 200);
		}    
	}
	/**
	*
	*date created 31-05-2016(dd-mm-yy)
	*date modified (dd-mm-yy)
		*Add template as product
	*
	* 
	*/
	public function addTemplateProducts(){
		$error = false;
		if(!empty($this->_request['data'])){
			$data = $this->_request['data'];
			$apikey = $this->_request['apikey'];
			$result = $this->storeApiLogin();
			if($this->storeApiLogin==true){
			if(!$error) {
				try {
					$arr = array('data'=>$data,'configFile'=>$data['images'],'oldConfId'=>$data['simpleproduct_id'],'varColor'=>$data['color_id'],'varSize'=>$data['sizes']);
					$result = $this->datalayer->addTemplateProducts($arr);
					$resultData = json_decode($result,true);
					$this->customRequest(array('productid' => $data['simpleproduct_id'], 'isTemplate' => 1));
					$sides = sizeof($data['images']);
					$productTemplate = $this->getProductTemplateByProductId($data['simpleproduct_id']);
					$maskData = $this->getMaskData($sides);
					$maskData = json_decode($maskData,true);
					$printArea = array();
					$printArea = $this->getPrintareaType($data['simpleproduct_id']);
					$this->customRequest(array('maskScalewidth' => $maskData[0]['mask_width'], 'maskScaleHeight' => $maskData[0]['mask_height'],'maskPrice' => $maskData[0]['mask_price'],'scaleRatio' => $maskData[0]['scale_ratio'],'scaleRatio_unit' => $maskData[0]['scaleRatio_unit'],'maskstatus' => $printArea['mask'], 'unitid' => $printArea['unit_id'],'pricePerUnit' => $printArea['pricePerUnit'],'maxWidth' => $printArea['maxWidth'],'maxHeight' => $printArea['maxHeight'],'boundsstatus' => $printArea['bounds'],'customsizestatus' => $printArea['custom_size'],'customMask' => $printArea['customMask']));
					

					$printSizes = $this->getDtgPrintSizesOfProductSides($data['simpleproduct_id']);
					$this->customRequest(array('productid' => $resultData['conf_id'], 'jsondata' => json_encode($maskData),'printsizes' => $printSizes));
					
					$this->saveMaskData();
					if($printSizes['status'] != 'nodata'){
						$this->setDtgPrintSizesOfProductSides();
					}
					
					$this->saveProductTemplateData($data['print_method_id'],$data['ref_id'],$data['simpleproduct_id'],$resultData['conf_id']);
					
					if(!empty($productTemplate['tepmlate_id'])){
						$this->customRequest(array('pid' => $resultData['conf_id'], 'productTempId' => $productTemplate['tepmlate_id']));
						$test = $this->addTemplateToProduct();
						
						
					}
				} catch (Exception $e) {
					$result = json_encode(array('isFault' => 1, 'faultMessage'=>$e->getMessage()));
					$error = true;
				}
			}
			echo $result;exit;
			}else{
				$msg=array('status'=>'apiLoginFailed','error'=>json_decode($result));
				$this->response($this->json($msg), 200);
			}
		}
	}
	/*
    *date created 07-06-2016(dd-mm-yy)
	*date modified (dd-mm-yy)
    *Save product template data
    *
    *@param (Int)old productid
    *@param (Int)new productid
    *@param (Int)refId
    * 
    */		
	public function saveProductTemplateData($printMethodId,$refId,$oldId,$newId){
		$apiKey = $this->_request['apikey'];
		if($this->isValidCall($apiKey)){
			try{
				$sql="delete from ".TABLE_PREFIX."template_state_rel where temp_id=".$newId."";
				$result = $this->executeGenericDMLQuery($sql);
				$sql="delete from ".TABLE_PREFIX."product_printmethod_rel where product_id=".$newId."";
				$result = $this->executeGenericDMLQuery($sql);
				$values = '';$pValues = '';$status = 0;
				$values .= ",(".$refId.",".$newId.",".$oldId.")";        
				$pValues .= ",(".$newId.",".$printMethodId.")";        
				if(strlen($values)){
					$sql = "INSERT INTO ".TABLE_PREFIX."template_state_rel (ref_id,temp_id,parent_id) VALUES".substr($values,1);
					$status = $this->executeGenericDMLQuery($sql);
				}
				if(strlen($pValues)){
					$sql = "INSERT INTO ".TABLE_PREFIX."product_printmethod_rel (product_id,print_method_id) VALUES".substr($pValues,1);
					$status = $this->executeGenericDMLQuery($sql);
				}
				if($status) {
				$msg= array("status" => "success");
				} else {
				 $msg=array("status"=>"failed");
				}
				return $this->json($msg);
			}catch(Exception $e) {
				$result = array('Caught exception:'=>$e->getMessage());
				$this->response($this->json($result), 200);
			}
		}                   
	}	

}
