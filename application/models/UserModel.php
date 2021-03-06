<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class UserModel extends MY_Model {

	var $table = 'users';
	var $column_order = array(null, 'FirstName', 'LastName', 'phone', 'address', 'city', 'country');
	var $column_search = array('FirstName', 'LastName', 'phone', 'address', 'city', 'country');
	var $order = array('id' => 'asc');

	public function __construct() {
		parent::__construct();
	}

	/**
	 * Generate A Query For DataTables.
	 * @return void 
	 */
	public function query(){
		$this->db->from($this->table);
	}

	/**
	 * Generate total records for DataTables
	 */
	public function total_records(){
		$this->db->from($this->table);
		return $this->db->count_all_results();
	}

	/**
	 * Get All Cutomers from Database
	 *
	 * @return array Customers
	 */
	public function all(){
		$users = $this->get_datatables();
		$data = array();
		$i = $this->input->post('start');
		foreach ($users as $user) {
			$i++;
			$row = array();
			$row[] = $i;
			$row[] = $user->FirstName;
			$row[] = $user->LastName;
			$row[] = $user->phone;
			$row[] = $user->address;
			$row[] = $user->city;
			$row[] = $user->country;
			$data[] = $row;
		}

		return array(
			"draw" => $this->input->post('draw'),
			"recordsTotal" => $this->count_all(),
			"recordsFiltered" => $this->count_filtered(),
			"data" => $data,
		);
	}

	/**
	 * User Login Function
	 * @param  string $email    Email Address
	 * @param  string $password Password
	 * @return object | boolean           
	 */
	public function loginAttempt($email, $password) {
		$user = $this->db->get_where($this->table, ['email' => $email])->row_array();
		if (count($user) > 0 && password_verify($password, $user['password']) === true) {
      $salt = str_random(30);
      $this->db->update($this->table, ['salt' => $salt], ['id' => $user['id']]);
      unset($user['password']);
      $user['salt'] = $salt;
			return $user;
		}
		return false;
	}

  public function logout($id)
  {
    $salt = str_random(5);
    return $this->db->update($this->table, ['salt' => $salt], ['id' => $id]); // change user's salt
  }
}
