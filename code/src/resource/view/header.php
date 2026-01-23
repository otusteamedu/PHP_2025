<!DOCTYPE html>
<html lang="en">
<head>

    <meta charset="UTF-8">
    <!-- Styles -->
    <link href="../../vendor/twbs/bootstrap/dist/css/bootstrap.css" rel="stylesheet">
    <!-- Scripts -->
    <script type="text/javascript" src="../../vendor/twbs/bootstrap/dist/js/bootstrap.js">     </script>
    <script type="text/javascript" src="../../vendor/components/jquery/jquery.js"> </script>

</head>
<body>
<div class="nav navbar-default navbar-static-top ">
    <div class="container">
        <div class="navbar-header">
        <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#app-navbar-collapse">
            <span class="sr-only">Toggle Navigation</span>

        </button>
        <a class="navbar-brand" href="/">Главная</a>
        </div>

        <div class="collapse navbar-collapse">
            <ul class="nav navbar-nav">
                <li class="dropdown"><a class="dropdown-toggle" href="http://new/busket">Товары</a></li>
                <li class="dropdown"><a class="dropdown-toggle" href="">Статьи</a></li>
                <?php if($_SESSION['user']['role'] == 'admin') :?>
                    <li class="dropdown"><a class="dropdown-toggle" href="/admin">Админка</a></li>
                <?php endif ?>
            </ul>

            <!--  ссылка на вход в систему -->
            <ul class="nav navbar-nav navbar-right">
                <!--  Авторизация пользователя, запись данных о пользователе в сессию -->
                <?php
                if(!isset($_SESSION['user'])):?>
                    <li><a href="/login"> Войти </a></li>
                <?php else : ?>
                    <li class="dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">
                            <?php echo htmlspecialchars($_SESSION['user']['name'])?> <span class="caret"></span>
                        </a>
                    </li>
                    <!-- ссылка на выход -->
                    <li>
                        <a href="http://new/login/logout"> Выйти</a>
                    </li>
                <?php endif ?>
                </ul>
        </div>


    </div>
</div>

