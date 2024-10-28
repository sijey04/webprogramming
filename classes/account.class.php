<?php

require_once 'database.php';

class Account{
    public $id = '';
    public $first_name = '';
    public $last_name = '';
    public $username = '';
    public $password = '';
    public $role = 'staff';
    public $is_staff = true;
    public $is_admin = false;


    protected $db;

    function __construct(){
        $this->db = new Database();
    }

    function add(){
        $sql = "INSERT INTO account (first_name, last_name, username, password, role, is_staff, is_admin) VALUES (:first_name, :last_name, :username, :password, :role, :is_staff, :is_admin);";
        $query = $this->db->connect()->prepare($sql);

        $query->bindParam(':first_name', $this->first_name);
        $query->bindParam(':last_name', $this->last_name);
        $query->bindParam(':username', $this->username);
        $hashpassword = password_hash($this->password, PASSWORD_DEFAULT);
        $query->bindParam(':password', $hashpassword);
        $query->bindParam(':role', $this->role);
        $query->bindParam(':is_staff', $this->is_staff);
        $query->bindParam(':is_admin', $this->is_admin);

        return $query->execute();
    }

    function usernameExist($username, $excludeID){
        $sql = "SELECT COUNT(*) FROM account WHERE username = :username";
        if ($excludeID){
            $sql .= " and id != :excludeID";
        }

        $query = $this->db->connect()->prepare($sql);
        $query->bindParam(':username', $username);

        if ($excludeID){
            $query->bindParam(':excludeID', $excludeID);
        }

        $count = $query->execute() ? $query->fetchColumn() : 0;

        return $count > 0;
    }

    function login($username, $password){
        $sql = "SELECT * FROM account WHERE username = :username LIMIT 1;";
        $query = $this->db->connect()->prepare($sql);

        $query->bindParam('username', $username);

        if($query->execute()){
            $data = $query->fetch();
            if($data && password_verify($password, $data['password'])){
                return true;
            }
        }

        return false;
    }

    function fetch($username){
        $sql = "SELECT * FROM account WHERE username = :username LIMIT 1;";
        $query = $this->db->connect()->prepare($sql);

        $query->bindParam('username', $username);
        $data = null;
        if($query->execute()){
            $data = $query->fetch();
        }

        return $data;
    }

    // Add this method to fetch all accounts
    function getAllAccounts($role = '', $search = '') {
        $sql = "SELECT * FROM account WHERE 1=1";
        
        if (!empty($role)) {
            $sql .= " AND role = :role";
        }
        
        if (!empty($search)) {
            $sql .= " AND (first_name LIKE :search 
                          OR last_name LIKE :search 
                          OR username LIKE :search)";
        }
        
        $sql .= " ORDER BY id ASC";
        
        $query = $this->db->connect()->prepare($sql);
        
        if (!empty($role)) {
            $query->bindParam(':role', $role);
        }
        
        if (!empty($search)) {
            $searchTerm = "%{$search}%";
            $query->bindParam(':search', $searchTerm);
        }
        
        $data = [];
        if($query->execute()){
            $data = $query->fetchAll();
        }
        
        return $data;
    }
}

// $obj = new Account();

// $obj->add();


