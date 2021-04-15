<?php
/*
 *-----------------------------------------------------------------------
 * PROJECT		:	WELCOME | DIGITAL CHANNEL COMMUNICATIONS
 * AUTHORS 		:	Callvoice Communications Co., Ltd., Bangkok, Thailand
 * URL 			:	https://www.welcomedcc.com
 * 					http://www.callvoice.co.th
 * VERSION 		:	1.4.2
 * CREATED 		:	28-11-2016
 * LAST CHANGE 	: 	12-09-2017
 *-----------------------------------------------------------------------
 */
defined('BASEPATH') OR exit('No direct script access allowed');

class Common_model extends CI_Model
{

	public function __construct()
	{
		parent::__construct();
		$this->load->dbforge();
	}

	public function get_table($table)
	{
		return $this->db->select('*')->from($table)->get()->result_array();
	}

	public function get_table_limit($table, $limit = FALSE, $offset = 0)
	{
		if ($limit !== FALSE)
		{
			$this->db->limit($limit, $offset);
		}
		return $this->db->get($table)->result_array();
	}

	public function get_table_order($table, $order_by = '', $order_type = '')
	{
		return $this->db->from($table)->order_by($order_by, $order_type)->get()->result_array();
	}

	public function get_where_custom($table, $field_name, $value)
	{
		return $this->db->where($field_name, $value)->get($table)->result_array();
	}

	public function get_where_custom_and($table, $where)
	{
		return $this->db->where($where)->get($table)->result_array();
	}

	public function get_where_custom_field($table, $field_name, $value, $field_select)
	{
		return $this->db->select($field_select)->where($field_name, $value)->get($table)->result_array();
	}

	public function get_where_custom_order($table, $col, $value, $order_by, $order_type)
	{
		return $this->db->where($col, $value)->order_by($order_by, $order_type)->get($table)->result_array();
	}

	public function get_where_custom_and_order($table, $where, $order_by, $order_type)
	{
		return $this->db->where($where)->order_by($order_by, $order_type)->get($table)->result_array();
	}

	public function get_with_limit($table, $order_by = '', $order_type = '', $limit, $offset)
	{
		return $this->db->from($table)->limit($limit, $offset)->order_by($order_by, $order_type)->get()->result_array();
	}

	public function get_where_with_limit($table, $col, $value, $limit, $offset, $order_by, $order_type)
	{
		if ($limit != FALSE)
		{
			$this->db->limit($limit, $offset);
		}
		return $this->db->where($col, $value)->order_by($order_by, $order_type)->get($table)->result_array();
	}

	public function get_where_with_limit_and($table, $where, $limit, $offset, $order_by, $order_type)
	{
		return $this->db->where($where)->limit($limit, $offset)->order_by($order_by, $order_type)->get($table)->result_array();
	}

	public function custom_query($mysql_query)
	{
		return $this->db->query($mysql_query)->result_array();
	}

	public function query($mysql_query)
	{
		return $this->db->query($mysql_query);
	}

	public function insert($table, $data)
	{
		return $this->db->insert($table, $data);
	}

	public function insert_id($table, $data)
	{
		return $this->db->insert($table, $data)->insert_id();
	}

	public function insert_batch($table, $data)
	{
		return $this->db->insert_batch($table, $data);
	}

	public function update($table, $data, $condition)
	{
		return $this->db->update($table, $data, $condition);
	}

	public function delete_where($tb_name, $column, $column_id)
	{
		return $this->db->where($column, $column_id)->delete($tb_name);
	}

	public function bindings($sql, $arr)
	{
		return $this->db->query($sql, $arr);
	}

	public function insert_string($table, $data)
	{
		return $this->db->insert_string($table, $data);
	}

	public function update_string($table, $data, $where)
	{
		return $this->db->update_string($table, $data, $where);
	}

	public function having($arr)
	{
		return $this->db->having($arr);
	}

	public function or_having($arr)
	{
		return $this->db->or_having($arr);
	}

	public function get_table_exists($table)
	{
		return $this->db->table_exists($table);
	}

	public function get_field_exists($field, $table)
	{
		return $this->db->field_exists($field, $table);
	}

	public function add_field($field)
	{
		return $this->dbforge->add_field($field);
	}

	public function add_key($key)
	{
		return $this->dbforge->add_key($key, TRUE);
	}

	public function create_table($table)
	{
		return $this->dbforge->create_table($table, TRUE, array('ENGINE' => 'InnoDB'));
	}

}