<?php
    session_start();
    if (isset($_SESSION['user']['name'])) // Проверка повторного входа
    {
        return 'Вы уже вошли как '.htmlspecialchars($_SESSION['user']['name'], ENT_QUOTES, 'UTF-8');
        exit();
    }
    if (!empty($_REQUEST['user_name'] or !empty($_REQUEST['pass']))) // если из формы пришли не пустые поля
    {
        $user =  new \App\Controllers\loginController();
        $user = $user->auth($_REQUEST['user_name'],$_REQUEST['pass']);
        if(isset($user['message'])){
            echo $user['message'];
            exit();
        }
        else{
            $_SESSION['user'] = $user;
            Header('Location: http://new/');// редирект
        }
        exit();
    }
    else
    {
        $ErrorMessage = 'Необходимо авторизоваться';
    }
