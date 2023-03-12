<?php
include SITE_ROOT . "/app/database/db.php";

// мы в include получали постоянную ошибку и ошибка была в том что в topics.php мы подключаем database
// и в index.php получалось повторное подключение файла из за этого пришлось немного костомизировать path.php
// добавив еще одну константу SITE_ROOT = __DIR__;
// Раньше было так "../../app/database/db.php" и у нас в главной странице была ошибка, а в админке все работало

$errMsg = [];
$id = '';
$name = '';
$description = '';

$topics = selectAll('topics');


// Код для формы создания категории                                                         CREATE
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['topic-create'])) {

    $name = trim($_POST['name']);
    $description = trim($_POST['description']);

    if ($name === '' || $description === ''){
        $errMsg[] = "Не все поля заполнены!";
    } elseif (mb_strlen($name, 'UTF-8') < 2){
        $errMsg[] = "Категория должна быть более 2-х символов!";
    } else {
        $existence = selectOne('topics', ['name' => $name]);
        if ($existence['name'] === $name){
            $errMsg[] = "Такая категория уже есть в базе!";
        } else {
            $topic = [
                'name' => $name,
                'description' => $description
            ];

            $id = insert('topics', $topic);
            $topic = selectOne('topics', ['id' => $id]);
            header('location: ' . BASE_URL . 'admin/topics/index.php');
        }
    }

} else {
    $name = '';
    $description = '';
}


// Апдейт категории                                                                 UPDATE
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['id'])) {
    // Если у нас $_SERVER REQUEST_METHOD метод GET и тут у нас будет проверка глобального массива GET мы в этом суперглобальном методе GET
    // должны проверять есть ли там id и если он есть только тогда мы что-то делаем

    $id = $_GET['id'];
    $topic = selectOne('topics', ['id' => $id]);
    $id = $topic['id'];
    $name = $topic['name'];
    $description = $topic['description'];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['topic-edit'])) {

    $name = trim($_POST['name']);
    $description = trim($_POST['description']);

    if ($name === '' || $description === '') {
        $errMsg[] = "Не все поля заполнены!";
    } elseif (mb_strlen($name, 'UTF-8') < 2) {
        $errMsg[] = "Категория должна быть более 2-х символов!";
    } else {
        $topic = [
            'name' => $name,
            'description' => $description
        ];

        $id = $_POST['id'];
        $topic_id = update('topics', $id, $topic);
        header('location: ' . BASE_URL . 'admin/topics/index.php');
    }
}


// Удаление категории                                                                       DELETE
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['del_id'])) {
    // Если у нас $_SERVER REQUEST_METHOD метод GET и тут у нас будет проверка глобального массива GET мы в этом суперглобальном методе GET
    // должны проверять есть ли там del_id и если он есть только тогда мы что-то делаем

    $id = $_GET['del_id'];
    delete('topics', $id);
    header('location: ' . BASE_URL . 'admin/topics/index.php');
}