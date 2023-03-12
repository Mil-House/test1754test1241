<?php
include SITE_ROOT . "/app/database/db.php";

$errMsg = [];

function userAuth($user){
    $_SESSION['id'] = $user['id'];              // В сесси принимаем значение id
    $_SESSION['login'] = $user['username'];     // Принимаем значение имя пользователя
    $_SESSION['admin'] = $user['admin'];        // Принимаем значение админа

    // После того как мызаписали сессию мы делаем редирект с формы регистрации на главную страницу
    // Для этого мы используем функцию header()

    // Сделаем дополнительную проверку которое будет проверять то же самое что и в нашем header.php

    if($_SESSION['admin']){
        header('location: ' . BASE_URL . "admin/posts/index.php");
    }else{
        header('location: ' . BASE_URL);
    }
}


// Показать всех пользователей из DB
$users = selectAll('users');


// ________________________ REGISTRATION ________________________
// Код формы для регистрации
// Все что мы здесь сделали это относится к методу POST | Проверяем если в этом посте есть button-reg
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['button-reg'])){   // Если тут есть button-reg значит пользователь пришел с регистрации
//    tt($_POST);
//    echo 'Я пришел с формы регистрации';
    $admin = 0;
    $login = trim($_POST['login']);       // Очищаем наши данные от лишних пробелов спомощю функции trim()
    $email = trim($_POST['email']);
    $passF = trim($_POST['pass-first']);
    $passS = trim($_POST['pass-second']);

    if ($login === '' || $email === '' || $passF === ''){
        $errMsg[] = "Не все поля заполнены!";
    } elseif (mb_strlen($login, 'UTF-8') < 2){
        $errMsg[] = "Логин должен быть более 2-х символов!";
    } elseif ($passF !== $passS){
        $errMsg[] = "Пароли в обеих полях должни соответствовать!";
    } else {
        $existence = selectOne('users', ['email' => $email]);
        if ($existence['email'] === $email){
            $errMsg[] = "Пользователь с такой почтой уже зарегистритован!";
        } else {

            $pass = password_hash($passF, PASSWORD_DEFAULT);
            $post = [
                'admin' => $admin,
                'username' => $login,
                'email' => $email,
                'password' => $pass
            ];

            $id = insert('users', $post);
            $user = selectOne('users', ['id' => $id]);
            userAuth($user); // тк код индетичен можно все заворачивать в эту функцию (написать надо самому)

//            tt($_SESSION);

//          $errMsg = "Пользователь" . " <b> " . $login . " </b> " . "успешно зарегистрирован!";
//          tt($post);
        }
    }


    //    $last_row = selectOne('users', ['id' => $id]);

} else {                // GET когда пользователь только только попадает на нашу форму
    // echo 'GET';         // Когда пользователь заполнит данные, прошел валидацию опять получит GET
    $login = '';
    $email = '';
}

//    $pass = password_hash($_POST['pass-second'], PASSWORD_DEFAULT);


// ________________________ LOG IN ________________________
// Код для формы авторизации
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['button-log'])) {
//    tt($_POST);

    $email = trim($_POST['email']);
    $pass = trim($_POST['password']);

    if ($email === '' || $pass === '') {
        $errMsg[] = "Не все поля заполнены!";
    } else {
        $existence = selectOne('users', ['email' => $email]); // Если вообще существует это данное и возвращает нам true
//        tt($existence);
        if ($existence && password_verify($pass, $existence['password'])) { // Если true и после хеширования input пароля сравнивается 2 хешированных и тоже true
            userAuth($existence); // тк код индетичен можно все заворачивать в эту функцию (написать надо самому)
        } else {
            // Ошибка авторизации
            $errMsg[] = "Почта либо пароль введены неверно!";
        }
    }
} else{
    $email =  '';
}


// _ _ _ _ _ _ _ _ __ _ __ _ _ _ __ _ __ _ _ __ _ __ _ __ _ _ __ _ _ __ _ _ __ _ __ _ _ __ _ _ __ _ __ _



// ________________________ ADMIN/USER CREATE ________________________
// Код добавления пользователя в админке
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create-user'])){

    $admin = 0;
    $login = trim($_POST['login']);       // Очищаем наши данные от лишних пробелов спомощю функции trim()
    $email = trim($_POST['email']);
    $passF = trim($_POST['pass-first']);
    $passS = trim($_POST['pass-second']);

    if ($login === '' || $email === '' || $passF === ''){
        $errMsg[] = "Не все поля заполнены!";
    } elseif (mb_strlen($login, 'UTF-8') < 2){
        $errMsg[] = "Логин должен быть более 2-х символов!";
    } elseif ($passF !== $passS){
        $errMsg[] = "Пароли в обеих полях должни соответствовать!";
    } else {
        $existence = selectOne('users', ['email' => $email]);
        if ($existence['email'] === $email){
            $errMsg[] = "Пользователь с такой почтой уже зарегистритован!";
        } else {

            $pass = password_hash($passF, PASSWORD_DEFAULT);
            if (isset($_POST['admin'])) $admin = 1;
            $user = [
                'admin' => $admin,
                'username' => $login,
                'email' => $email,
                'password' => $pass
            ];

            $id = insert('users', $user);
            $user = selectOne('users', ['id' => $id]);
            header('location: ' . BASE_URL . "admin/users/index.php");

//            tt($_SESSION);

//          $errMsg = "Пользователь" . " <b> " . $login . " </b> " . "успешно зарегистрирован!";
//          tt($post);
        }
    }

    //    $last_row = selectOne('users', ['id' => $id]);

} else {
    $login = '';
    $email = '';
}


// ________________________ UPDATE ________________________
// РЕДАКТИРОВАНИЕ ПОЛЬЗОВАТЕЛЯ ЧЕРЕЗ АДМИНКУ
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['edit_id'])) {

    $user = selectOne('users', ['id' => $_GET['edit_id']]);

    $id = $user['id'];
    $admin = $user['admin'];
    $username = $user['username'];
    $email = $user['email'];
    // можем и получать дату создания
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update-user'])) {

    $id = $_POST['id'];
    $email = trim($_POST['email']);
    $login = trim($_POST['login']);
    $passF = trim($_POST['pass-first']);
    $passS = trim($_POST['pass-second']);
    $admin = isset($_POST['admin']) ? 1 : 0;


    if ($login === '') {
        $errMsg[] = "Не все поля заполнены!";
    } elseif (mb_strlen($login, 'UTF-8') < 2) {
        $errMsg[] = "Логин должен быть более 2-ух символов!";
    } elseif ($passF !== $passS){
        $errMsg[] = "Пароли в обеих полях должны соответствовать!";
    } else {

        $pass = password_hash($passF, PASSWORD_DEFAULT);
        if (isset($_POST['admin'])) $admin = 1;
        $user = [
            'admin' => $admin,
            'username' => $login,
            'email' => $email,
            'password' => $pass
        ];

//        Проверку на уникальности название поста не делаем можно делать при желании
//        $existence = selectOne('topics', ['title' => $title]);
//        if ($existence['title'] === $title) {
//            $errMsg = "Такая категория уже есть в базе!";
//        } else {}

        $user = update('users', $id, $user);
        header('location: ' . BASE_URL . 'admin/users/index.php');
    }
} else {
    // Это для того чтобы подтягивать прежние знчение которые мы ввели
    $id = $user['id'];
    $admin = $user['admin'];
    $username = $user['username'];
    $email = $user['email'];
}



// ________________________ DELETE ________________________
// Код удаления пользователя в админке
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['delete_id'])) {
    $id = $_GET['delete_id'];
    delete('users', $id);
    header('location: ' . BASE_URL . 'admin/users/index.php');
}


