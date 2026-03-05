<?php
namespace App\Models;

use PDO;

class Job {
    private $db;
    
    public function __construct() {
        $host = 'mysql';
        $dbname = 'async_api';
        $username = 'user';
        $password = 'password';
        
        try {
            $this->db = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
            $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (\PDOException $e) {
            die(json_encode(['error' => 'Database connection failed']));
        }
    }
    
    public function create($inputData) {
        $stmt = $this->db->prepare("INSERT INTO jobs (input_data, status) VALUES (?, 'pending')");
        $stmt->execute([$inputData]);
        return $this->db->lastInsertId();
    }
    
    public function find($id) {
        $stmt = $this->db->prepare("SELECT * FROM jobs WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    public function updateStatus($id, $status, $outputData = null) {
        if ($outputData) {
            $stmt = $this->db->prepare("UPDATE jobs SET status = ?, output_data = ? WHERE id = ?");
            $stmt->execute([$status, $outputData, $id]);
        } else {
            $stmt = $this->db->prepare("UPDATE jobs SET status = ? WHERE id = ?");
            $stmt->execute([$status, $id]);
        }
    }
}