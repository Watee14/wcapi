<?php 
defined('BASEPATH') OR exit('No direct script access allowed');
require(APPPATH.'core/MY_Modelmongodb.php');
//error_reporting( E_ERROR);  
class Contact_model_mongodb extends MY_Modelmongodb {
	public function __construct()
	{
		parent::__construct();
	}
	public function create_contact($params){

		//$fine = array('_id' => new MongoId( $id ));
		//$arrayName = array($id);
		//$find = array('loc' =>$arrayName);
		//$arrayName = array($id);
		//$find = floatval($id);
		//$this->mongo_db->where_in('loc',array($find));
		//$this->mongo_db->select(array('_id'));
		//$query = $this->mongo_db->get('test');//ดึงข้อมูลจาก items collection	
		//$query = $this->db->where('id',$id);
		//$query = $this->db->get('contact');
		//$CI->mongo_db->close();
		$query = $this->CreateDb($params['region'].'_'.$params['tenant']);
		return $query;

	}

		

	public function insert_document($collection,$data){
		$query = $this->mongo_db->insert($collection,$data);
		return $query;
	}


	public function update_document($collection,$data,$refer){
        //$id = array('_id' => new MongoId( $data['id'] ));    
		$id = array( $refer  =>  $data[ $refer ]  );
       // print_r($id) ;
		unset($data[ $refer]);
		$query =  $this->mongo_db->where($id)->set($data)->update($collection);
		return $query;
	}
	public function delete_database($db_name){
		$rename = $this->mongo_db->rename_db($db_name,$db_name.'_bk');
		if ($rename === true) {
			$query =  $this->mongo_db->drop_db($db_name);
		}
		else {
			$query = $rename;
		}
		
		return $query;
	}


	public function add_contact($data){

		$arrayName = array();
		if (isset($data['email']['main'])) {
			$arrayName['email']['main'] = $data['email']['main'];
		}
		if (isset($data['telephone']['main'])) {
			$arrayName['telephone']['main'] = $data['telephone']['main'];
		}
		if (isset($data['mobile']['main'])) {
			$arrayName['mobile']['main'] = $data['mobile']['main'];
		}
		if (count($arrayName)==0 || $this->mongo_db->find_one('c_contact')===false) {
			$query = $this->mongo_db->insert('c_contact',$data);
		}else{
			$this->mongo_db->where($arrayName);
			$list = $this->mongo_db->get('c_contact');
			
			if (count($list)>0 && $list!="" && $list!= null) {
				$id = array('_id' => new MongoId( $list[0]['_id']));
				$update =  $this->mongo_db->where($id)->set($data)->update('c_contact');
				if ($update = true) {
					$query = new MongoId( $list[0]['_id']);
				}
			}
			else{
				$query = $this->mongo_db->insert('c_contact',$data);
			}
		}
		return $query;
	}




	public function update_contact($dataid,$data){
		$id = array('_id' => new MongoId( $dataid ));
		$arraySet = array();
		foreach ($data as $key1 => $value1) {
			if (is_array($value1)) {
				foreach ( $value1 as $key2 => $value2) {
					$arraySet[$key1.'.'.$key2] = $value2;
				}
			}else {
				$arraySet[$key1] = $value1;
			}
		}

		$update =  $this->mongo_db->where($id)->set($arraySet)->update('c_contact');
		if ($update = true) {
			$query = new MongoId( $dataid );
		}
		else{

			$query = false;
		}
		return $query;
	}

	public function get_contact($dataid,$data ,$field)
	{
        //echo 'XX'; die();
		$collection = 'c_contact';
		if ($dataid !='' && $dataid != null) {
			//$id = array('_id' => new MongoId( $dataid));
            $id = array( $field => $dataid );
			$this->mongo_db->where( $id );
			$query = $this->mongo_db->get($collection);
            //print_r($query ) ; die();
			if (is_string($query)) {
				if ($this->GetIndexedString($query,"status")==="false") {
					return $query;
				}
			}
		} 
        return $query;
    }

    public function delete_contact($dataid){

    	$id = array('_id' => new MongoId( $dataid ));
		//unset($data['id']);
    	$query =  $this->mongo_db->where($id)->delete('c_contact');
    	return $query;
    }


public function add_contact_history($data){
	$contacID = new MongoId($data['contact']['id']);
	$data['contact']['id'] = $contacID;
	$query = $this->mongo_db->insert('c_contact_history',$data);
	return $query;
}

    public function update_contact_history($dataid,$data){
    	$id = array('_id' => new MongoId( $dataid ));
    	$arraySet = array();
    	foreach ($data as $key1 => $value1) {
    		if (is_array($value1)) {
    			foreach ( $value1 as $key2 => $value2) {
    				$arraySet[$key1.'.'.$key2] = $value2;
    			}
    		}else {
    			$arraySet[$key1] = $value1;
    		}
    	}

    	$update =  $this->mongo_db->where($id)->set($arraySet)->update('c_contact_history');
    	if ($update = true) {
    		$query = new MongoId( $dataid );
    	}
    	else{

    		$query = false;
    	}
    	return $query;
    }

    public function delete_contact_history($dataid){

    	$id = array('_id' => new MongoId( $dataid ));
		//unset($data['id']);
    	$query =  $this->mongo_db->where($id)->delete('c_contact_history');
    	return $query;
    }


// public function get_contact_history($dataid,$data)
// {
// 	$collection = 'c_contact_history';
// 	if ($dataid !='' && $dataid != null) {
// 		$id = array('_id' => new MongoId( $dataid));
// 		$this->mongo_db->where($id);
// 		$query = $this->mongo_db->get($collection);
// 	}else{
// 		$page = isset($data['page'])===true?$data['page']:1;//$request->input('page', 1);
//         $pageLimit = isset($data['pageLimit'])===true?$data['pageLimit']:5;//$request->input('pageLimit', 5);
//         $orderBy = isset($data['orderBy'])===true?$data['orderBy']:'create_date';//$request->input('orderBy', 'ID');
//         $orderDirection = isset($data['orderDirection'])===true?$data['orderDirection']:'desc';//$request->
//         $keyword = isset($data['keyword'])===true?$data['keyword']:'';
//         $skip = ($page-1)*$pageLimit;	
//         $inorder_by = array($orderBy => $orderDirection);
//         $arrayseachby = array();
//         $that = $this->mongo_db;

//         $orderDirection = $orderDirection=='desc'?-1:1;

//         if ($keyword!='') {
//         	$searchby = array(0=>'contact.name',
//         		1=>'name',
//         		2=>'email',
//         		3=>'ani',
//         		4=>'msg',
//         		5=>'contact_docs.name_eng',
//         		6=>'contact_docs.email.main',
//         		7=>'contact_docs.mobile.main');
//         	foreach ($searchby as $key => $value) {
//         		$arrayseachby[$value] = $keyword ;
//         	}
//         	foreach ($arrayseachby as $wh => $val)
//         	{
//         		$regex = new MongoRegex("/$val/i");
//         		$match['$match']['$or'][] = array($wh=>$regex);

//         	}
//         	$lookup['$lookup'] =  array('from' => 'c_contact',
//         		'localField' => 'contact.id',
//         		'foreignField' => "_id",
//         		'as' => "contact_docs");		

//         	$skips['$skip'] = (int)$skip;
//         	$limits['$limit'] = (int)$pageLimit;
//         	$sorts['$sort'] = array($orderBy => $orderDirection);
//         	$join1 = array(0 => $lookup,1 => $match ,2 => $skips,3 =>$limits,4=>$sorts);
//         	$join2 = array(0 => $lookup, 1 => $match );
//         	$list = $that->aggregate($collection,$join1);
//         	$total = $this->mongo_db->aggregate($collection,$join2);
//         	$totals = count($total['result']);	
//         	$lists = $list['result'];	
//         }  else{
//         	$lookup['$lookup'] =  array('from' => 'c_contact',
//         		'localField' => 'contact.id',
//         		'foreignField' => "_id",
//         		'as' => "contact_docs");
//         	$skips['$skip'] = (int)$skip;
//         	$limits['$limit'] = (int)$pageLimit;
//         	$sorts['$sort'] = array($orderBy => $orderDirection);
//         	$join1 = array(0 => $lookup, 1 => $skips,2 =>$limits,3=>$sorts);
//         	$join2 = array(0 => $lookup);
//         	//$list = $that->offset($skip)->limit($pageLimit)->order_by($inorder_by)->aggregate($collection,$join);
//         	$list = $that->aggregate($collection,$join1);
//         	$total = $this->mongo_db->aggregate($collection,$join2);
//         	$totals = count($total['result']);	
//         	$lists = $list['result'];




//         }

//         $results = array();
//         $results['pagination']['totalPages'] = ceil($totals / $pageLimit);
//         $results['pagination']['currentPage'] = (int)$page;
//         $results['pagination']['itemsPerPage'] = (int)$pageLimit;
//         $results['pagination']['totalItems'] = (int)$totals;
//         $results['pagination']['itemsInPage'] = count($lists);
//         $results['data'] = $lists;
//         $query = $results ;
//     }


//     return $query;
// }
    public function get_contact_history($dataid,$data)
    {
    	$collection = 'c_contact_history';
    	if ($dataid !='' && $dataid != null) {
    		$id = array('_id' => new MongoId( $dataid));
    		$this->mongo_db->where($id);
    		$query = $this->mongo_db->get($collection);
    		if (is_string($query)) {
    			if ($this->GetIndexedString($query,"status")==="false") {
    				return $query;
    			}
    		}
    	}else{
		$page = isset($data['page'])===true?$data['page']:1;//$request->input('page', 1);
        $pageLimit = isset($data['pageLimit'])===true?$data['pageLimit']:5;//$request->input('pageLimit', 5);
        $orderBy = isset($data['orderBy'])===true?$data['orderBy']:'create_date';//$request->input('orderBy', 'ID');
        $orderDirection = isset($data['orderDirection'])===true?$data['orderDirection']:'desc';//$request->
        $keyword = isset($data['keyword'])===true?$data['keyword']:'';
        $status = isset($data['status'])===true?$data['status']:'';
        $skip = ($page-1)*$pageLimit;	
        $arrayfilterby = array();
        $arrayseachby = array();
        
        $match =array();
        $that = $this->mongo_db;
        $orderDirection = $orderDirection=='desc'?-1:1;
        $inorder_by = array($orderBy => $orderDirection);
        if ($keyword!='') {
        	$searchby = array(
        		0=>'contact.name',
        		1=>'name',
        		2=>'email',
        		3=>'ani',
        		4=>'msg',
                5=>'contact_docs.name_eng',
                6=>'contact_docs.email.main',
                7=>'contact_docs.mobile.main');
        	foreach ($searchby as $key => $value) {
        		$arrayseachby[$value] = $keyword ;
        	}
        	foreach ($arrayseachby as $wh => $val)
        	{
        		$regex = new MongoRegex("/$val/i");
        		$match['$match']['$or'][] = array($wh=>$regex);

        	}
        	if ($status!='') {
        		$match['$match']['$and'][] = array('transaction.trans_status' => $status);
        	}
             $lookup['$lookup'] =  array('from' => 'c_contact',
             'localField' => 'contact.id',
             'foreignField' => "_id",
             'as' => "contact_docs");    

        	$skips['$skip'] = (int)$skip;
        	$limits['$limit'] = (int)$pageLimit;
        	$sorts['$sort'] = array($orderBy => $orderDirection);
        	$statment1 = array(0 => $lookup, 1 => $match ,2 => $skips,3 =>$limits,4=>$sorts);
        	$statment2 = array(0 => $lookup, 1 => $match );
        	$list = $that->aggregate($collection,$statment1);
        	if (is_string($list)) { 
        		if ($this->GetIndexedString($list,"status")==="false") {
        			return $list;
        		}
        	}
        	$total =$that->aggregate($collection,$statment2);
        	if (is_string($total)) {
        		if ($this->GetIndexedString($total,"status")==="false") {
        			return $total;
        		}
        	}
        	$totals = count($total['result']);	
        	$lists = $list['result'];		
        }  else{
        	
            $lookup['$lookup'] =  array('from' => 'c_contact',
             'localField' => 'contact.id',
             'foreignField' => "_id",
             'as' => "contact_docs");
            $skips['$skip'] = (int)$skip;
            $limits['$limit'] = (int)$pageLimit;
            $sorts['$sort'] = array($orderBy => $orderDirection);
          
            if ($status!='') {
                $match['$match']['$and'][] = array('transaction.trans_status' => $status);
                $statment1 = array(0 => $lookup, 1 => $match ,2=>$sorts,3 => $skips,4 =>$limits);
                $statment2 = array(0 => $lookup, 1 => $match );
                //$arrayfilterby = array('transaction.trans_status' => $status);
            }else{
                $statment1 = array(0 => $lookup,1=>$sorts,2 => $skips,3 =>$limits);
                $statment2 = array(0 => $lookup);
            }
          
            $list = $that->aggregate($collection,$statment1);
            if (is_string($list)) { 
                if ($this->GetIndexedString($list,"status")==="false") {
                    return $list;
                }
            }

            $total =$that->aggregate($collection,$statment2);
            if (is_string($total)) {
                if ($this->GetIndexedString($total,"status")==="false") {
                    return $total;
                }
            }
            //echo '<pre>';
            //print_r( $total) ;
            $totals = count($total[0]['result']);  
            $lists = $list[0]['result'];       
        	// $lists = $that->where($arrayfilterby)->offset($skip)->limit($pageLimit)->order_by($inorder_by)->get($collection);
        	// if (is_string($lists)) { 
        	// 	if ($this->GetIndexedString($lists,"status")==="false") {
        	// 		return $lists;
        	// 	}
        	// }
        	// $totals = $that->where($arrayfilterby)->count($collection);		
        	// if (is_string($totals)) { 

        	// 	if ($this->GetIndexedString($totals,"status")==="false") {
        	// 		return $totals;
        	// 	}
        	// }
        }
        
        $results = array();
        $results['pagination']['totalPages'] = ceil($totals / $pageLimit);
        $results['pagination']['currentPage'] = (int)$page;
        $results['pagination']['itemsPerPage'] = (int)$pageLimit;
        $results['pagination']['totalItems'] = (int)$totals;
        $results['pagination']['itemsInPage'] = count($lists);
        $results['data'] = $lists;
        $query = $results;
    }


    return $query;
}

public function get_contact_historyBy_contactID($dataid,$data)
{
	$collection = 'c_contact_history';
		$page = isset($data['page'])===true?$data['page']:1;//$request->input('page', 1);
        $pageLimit = isset($data['pageLimit'])===true?$data['pageLimit']:5;//$request->input('pageLimit', 5);
        $orderBy = isset($data['orderBy'])===true?$data['orderBy']:'create_date';//$request->input('orderBy', 'ID');
        $orderDirection = isset($data['orderDirection'])===true?$data['orderDirection']:'desc';//$request->
        $skip = ($page-1)*$pageLimit;	
        $inorder_by = array($orderBy => $orderDirection);

        $id =  array('contact.id'=> new MongoId( $dataid));
        $query = $this->mongo_db->where($id)->offset($skip)->limit($pageLimit)->order_by($inorder_by)->get($collection);
        if (is_string($query)) {
        	if ($this->GetIndexedString($query,"status")==="false") {
        		return $query;
        	}
        }
        $total = $this->mongo_db->where($id)->count($collection);
        if (is_string($total)) {
        	if ($this->GetIndexedString($total,"status")==="false") {
        		return $total;
        	}
        }
        $results = array();
        $results['pagination']['totalPages'] = ceil($total / $pageLimit);
        $results['pagination']['currentPage'] = (int)$page;
        $results['pagination']['itemsPerPage'] = (int)$pageLimit;
        $results['pagination']['totalItems'] = (int)$total;
        $results['pagination']['itemsInPage'] = count($query);
        $results['data'] = $query;
        return $results;
    }

	// 	private function array_push_assoc($array, $key, $value){
	// 		$array[$key] = $value;
	// 		return $array;
	// 	}

    private function GetIndexedString($inputString,$category)
    {
    	$str = "";
    	$result = "";
    	$returnArray = explode("&",$inputString);

    	$r = 0; $n = 0;
    	foreach ($returnArray as $s) {
    		$r++;
    	}
    	for ($i = 0; $i <= $r-1; $i++)
    	{
    		$tmpcategory = explode("=",$returnArray[$i]);

    		foreach ($tmpcategory as $c)
    			{ $n++; }

    		if ($n > 1)
    		{
    			$str = $tmpcategory[0];
    			if ($str == $category)
    			{
    				$result = $tmpcategory[1];
    				break;
    			}
    			else
    			{
    				$result = "";
    			}
    		}

    	}
    	return $result;
    }
}

