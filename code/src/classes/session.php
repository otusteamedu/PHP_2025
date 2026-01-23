<?php
namespace Classes;


class session
{
    protected $user;

    public function __construct()
    {
        // Если сесии не существует, то создаем ее
        if(session_id() == ''){
            session_start();
        }

        $id_user = isset($_SESSION['id_user']) ? $_SESSION['id_user'] : null;
        if($id_user)
            {
                $this->user = User::findBy(array('id'=>$id_user));
            }
    }
// флаг залогинен ли текущий пользователь
    public function isLoggedIn()
    {
        return isset($_SESSION['username']);
    }

//  имя текущего пользователя если он залогинен
    public function getName()
    {
        return $this->isLoggedIn() ? $_SESSION['username']: '';
    }

//  возвращает true если пользователь имеет право админа
    public function isAdmin(){
        return ($this->user) ? (bool)$this->user->isAdmin : FALSE;
    }

    public function login($login, $pass)
    {
        $this->user = UserModel::findBy(array('login' => $login, 'authkey' => $pass));
        return (bool)$this->user;
    }
    /**
     * Выход из системы
     */
    public function logout()
    {
        session_destroy();
        $this->user = null;
    }

    public function __destruct()
    {
        if ($this->user) {
            $_SESSION['userId'] = $this->user->id;
            $_SESSION['username'] = $this->user->name;
        }
    }







}