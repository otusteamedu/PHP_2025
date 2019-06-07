<?php

namespace app\models\entities;


use app\models\entities\DataEntity;

class Users extends DataEntity
{
    protected $id_user;
    protected $login;
    protected $password;
    protected $hash;
    public $properties = ['id_user','login', 'password', 'hash'];

    /**
     * Users constructor.protected
     * @param $id_user
     * @param $login
     * @param $password
     */
    public function __construct($id_user=null, $login = null, $password = null, $hash = null)
    {
        $this->id_user = $id_user;
        $this->login = $login;
        $this->password = $password;
        $this->hash = $hash;
    }


    public function getId(){

        return $this->id_user;
    }
    public function getIdName(){

        return 'id_user';
    }
    public function setId($value){

        $this->id_user = $value;
    }

    /**
     * @param null $login
     */
    public function setLogin($login): void
    {
        $this->login = $login;
        if(!in_array($login, $this->changes)){

            $this->changes['login'] = $login;
        }

    }

    /**
     * @param null $password
     */
    public function setPassword($password): void
    {
        $this->password = $password;
        if(!in_array($password, $this->changes)){

            $this->changes['password'] = $password;
        }
    }

    /**
     * @param null $hash
     */
    public function setHash($hash): void
    {
        $this->hash = $hash;
        if(!in_array($hash, $this->changes)){

            $this->changes['hash'] = $hash;
        }
    }
    public function getTableName()
    {
        return 'users';
    }

}