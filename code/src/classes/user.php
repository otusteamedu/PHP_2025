<?php

namespace classes;
use App\config\db_connect;
use PDO;
class user
{
    private $db;
    private $user;

    public function  __construct()
    {
        $this->db = db_connect::getInstance();
    }

    public function findBy(array $condition)
    {
        $query = 'SELECT users.name,users.surname,post.title, users.role
                  FROM users
                  INNER JOIN  post USING (id_post)
                  ';
        if(!empty($condition))
        {
            $query .= ' WHERE ';

            $whereCondition = [];
            foreach ($condition as $key => $val){
                $whereCondition[] =  "$key = '$val'";
            }
            $query .= implode(' AND ',$whereCondition);

            $st = $this->db->prepare($query);

            $result = $st->execute($condition);
            $data = $result ? $st->fetch(PDO::FETCH_ASSOC) : null;
            if($result && $data){
                $this->user = $data;
            }
            return null;

        }
    }

    public function get(){
        return $this->user;
    }

    public function isAdmin()
    {
        if($this->user['role'] == 'admin'){
            return true;
        }else false;
    }

}