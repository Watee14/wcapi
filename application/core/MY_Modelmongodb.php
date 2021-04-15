<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class MY_Modelmongodb extends CI_Model {

	function __construct() 
	{		
		parent::__construct();
		
	}

	public function Loadlibrary_mongodb(){
		$this->load->library('Mongo_db');
	}
	private function get_config(){

		$mongo_config = $this->config->item('mongo_db');
		$mongo_config_active = $mongo_config['active'];
		$mongo_config_hostname = $mongo_config[$mongo_config_active]['hostname'];
		$mongo_config_port = $mongo_config[$mongo_config_active]['port'];
		return 'mongodb://'.$mongo_config_hostname.':'.$mongo_config_port;

	}



	public function CreateDb($db_name){
		//$this->Loadlibrary_mongodb();
		try {
			$keys = array();
			$mongo_config = $this->config->item('mongo_db');
			$mongo_config_active = $mongo_config['active'];
			$mongo_config_hostname = $mongo_config[$mongo_config_active]['hostname'];
			$mongo_config_port = $mongo_config[$mongo_config_active]['port'];
			$con_string = $this->get_config();
			$db_name = 'wc_'.$db_name;
			$connection = new MongoClient($con_string);
			$db = $connection->$db_name;
			$keys['name_eng'] = 1;
			$contact = $db->createCollection(
				"c_contact",
				array(
					
					'autoIndexId' => true,
					)
				);
			$history = $db->createCollection(
				"c_contact_history",
				array(
					
					'autoIndexId' => true,
					)
				);
			$indexContact = $db->{'c_contact'}->createIndex($keys);
			$indexHistory = $db->{'c_contact_history'}->createIndex($keys);
			//$this->$db->c_history->createIndex( array('name'=> 1 ) );
			//db.collection.createIndex( { orderDate: 1 } )
					
			return true;

//the new Database should now exist, with no user Collections created yet
// foreach ($list as $collection) {
//     echo "$collection </br>";       
// }

		} catch (Exception $e) {
			return false.'$message='.$e;
		}
	}

	public function InsertContact($db_name,$collection,$data){
		//$this->Loadlibrary_mongodb();
		try {
			//$configg = $this->set_config($db_name);
			// var_dump($configg);					
			//var_dump($connect);
			$query = $this->mongo_db->insert($collection,$data);

			return $query;
			
//the new Database should now exist, with no user Collections created yet
// foreach ($list as $collection) {
//     echo "$collection </br>";       
// }

		} catch (Exception $e) {
			return false.$e;
		}
	}


	public function Insert_ContactById($db_name){
		//$this->Loadlibrary_mongodb();
		try {
			$con_string = $this->get_config();
			$connection = new MongoClient($con_string);
			$db_name = 'wc_'.$db_name;
			$db = $connection->$db_name;
			$list = $db->listCollections();
			return true;
			
//the new Database should now exist, with no user Collections created yet
// foreach ($list as $collection) {
//     echo "$collection </br>";       
// }

		} catch (Exception $e) {
			return false.$e;
		}
	}
	/** Insert new record */
	function save($member='') {
		if ($member != ''){
            if (!isset($member['id'])){ // new record
            	$this->posts->insert($member);
            	return $member['_id'];
            } else { // edit existing record
            	$memberid = $member['id'];
            	$this->posts->update(array('_id' => new MongoId($memberid)), $member, array("multiple" => false));
            	return $memberid;
            }
        }
    }

    /** Fetches all records with limit and orderby values's */
    function getAll($limit='', $orderby='') {
    	$members = $this->posts->find();
    	if ($limit != ''){ $members->limit($limit);}
    	if ($orderby != ''){$members = $members->sort($orderby);}
    	return $members;
    }

    /** Fetches a record by its' passed field and values's */
    function getByID($id='') {
    	$member = $this->posts->findOne(array('_id' => new MongoId($id)));
    	if ($member) {
    		return $member;
    	}
    	return false;
    }

    /** Fetches a record by its' passed field and values's */
    function getByColumn($field='id', $value='') {
    	$member = $this->posts->findOne(array($field => $value));
    	if ($member) {
    		return $member;
    	}
    	return false;
    }

    /** Deletes a record by it's primary key */
    function deleteById($id) {
    	$this->posts->remove(array('_id' => new MongoId($id)), array("justOne" => true));
    }


// //select the Books collection
// $books = $db->Books;

// //insert new documents into the Books collection
// $books->insert(array("title" => "Mastering JavaServer Faces 2.2", "author" => "Anghel Leonard"));
// $books->insert(array("title" => "JSF 2.0 Cookbook", "author" => "Anghel Leonard"));
// $books->insert(array("title" => "Rapid PrimeFaces", "author" => "Anghel Leonard"));
// $books->insert(array("title" => "JBoss Tools 3 Developers Guide", "author" => "Anghel Leonard"));
// $books->insert(array("title" => "Pro Hibernate and MongoDB", "author" => "Anghel Leonard"));
// $books->insert(array("title" => "Pro Java 7 NIO.2", "author" => "Anghel Leonard"));

// //list the documents from the Books collection
// $cursor = $collection->find();

// // iterate through the results
// foreach ($cursor as $document) {
//     echo '"_id": '.$document["_id"]."<br />";
//     echo '"title": '.$document["title"]."<br />";
//     echo '"author": '.$document["author"]."<br />";
//     echo '***************************************'."<br />";
// }

}