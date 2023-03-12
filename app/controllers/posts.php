<?php // Этот файл будет отвечать за контроль поста

include SITE_ROOT . "/app/database/db.php";
if (!$_SESSION){
    header('location: ' . BASE_URL . 'log.php');
}

// По умолчанию наши переменные пустые
$errMsg = [];
$id = '';
$title = '';
$content = '';
$img = '';
$topic = '';

$topics = selectAll('topics'); // Нам оно доступно тк в файле posts/create.php мы подключили ../../app/controllers/posts.php";
$posts = selectAll('posts');
$postsAdm = selectAllFromPostsWithUsers('posts', 'users');

// Код для формы создания записи (поста)                                                   CREATE
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_post'])) {

    // Изображение сейчас прилетает как название, чтобы оно прилетало к нам как файл нам нужно пойти в create.php
    // в форме нужно добавить enctype="multipart/from-data" для того чтобы загружать файлы в нашу временную память
    // И напишем проверку

//    tt($_FILES);

    // У нас есть массив, мы также можем в этом массиве обратится к ключу img и это будет еще один массив
    // а внутри его обратится к названию [name] картинки, и если все это возвращает true мы понимаем что картинка у нас подгрзилась
    // и мы можем дальше с ним что то сделать. Мы создадим $imgName которую мы будем просто присваивать имя нашей картинки
    // Второе нам нужно временный файл, он нам пригодится потому что с него мы будем копировать саму картинку к нам на сервер
    if (!empty($_FILES['img']['name'])) {
        $imgName = time() . "_" . $_FILES['img']['name']; // для того чтобы имена картинок не совподали для хранения нужно что то сделать например time(), random()...
        $fileTmpName = $_FILES['img']['tmp_name']; // обращаемся к ключу tmp_name
        $fileType = $_FILES['img']['type']; // для проверки типа файла на фото
        $destination = ROOT_PATH . "\assets\img\posts\\" . $imgName; // создадим путь где мы будем нашу картинку сохранять | \\ экранирующий слеш

        // Проверки (скажем это фото или нет?)
        if (strpos($fileType, 'image') === false) {
            // Функция array_push(); состоит из 2ух частей 1) мы указиваем атрибут самого массива в котором будем добавлять сообщения
            // и 2) мы указываем что конкретное мы туда добавляем в нашем случае текст ошибки
            // Есть более чистый вариант это array[] = "";
            $errMsg[] = "Подгружаемый файл не является изображением!";
        } else {
            // Теперь нам осталось после подготовительных процесов сделать загрузку на сервер
            // Чтобы мы могли потом понимать успешно или не успешно произошла загрузка мы создадим переменную $result которое будет принимать булевое значение
            // Функция move_uploaded_file() принимает 2 параметра 1)временный файл и 2)куда мы хотим этот файл разместить
            $result = move_uploaded_file($fileTmpName, $destination);

            if ($result){
                $_POST['img'] = $imgName;
            } else {
                $errMsg[] = "Ошибка загрузки изображения на сервер";
            }
        }
//        tt($_FILES['img']);

    } else {
        $errMsg[] = "Ошибка получения картинки!";
    }


    $title = trim($_POST['title']);
    $content = trim($_POST['content']);
    $topic = trim($_POST['topic']);     // здесь мы будем ее получать, те сразу с нашего поста тянем и пишем 'topic' как мы в файле create.php задали в name="topic"
    $publish = isset($_POST['publish']) ? 1 : 0; // Проверяем чекбок $publish если там присутствует галочка 1 в противном случие 0

    if ($title === '' || $content === '' || $topic === '') {
        $errMsg[] = "Не все поля заполнены!";
    } elseif (mb_strlen($title, 'UTF-8') < 7) {
        $errMsg[] = "Название статьи должно быть более 7-ми символов!";
    } else {
//        $existence = selectOne('topics', ['title' => $title]);        Проверку на уникальности название поста не делаем можно делать при желании
//        if ($existence['title'] === $title) {
//            $errMsg = "Такая категория уже есть в базе!";
//        } else {}
        $post = [                  // Далле мы формируем массив данных которую мы будем отправлять уже в DB
            'id_user' => $_SESSION['id'],   // Здесь 'id_user' будет равен суперглобальному массиву $_SESSION['у которого обращаемся к ключу id']
            'title' => $title,
            'content' => $content,
            'img' => $_POST['img'],
            'status' => $publish,
            'id_topic' => $topic
        ];

        $post = insert('posts', $post);
        $post = selectOne('posts', ['id' => $id]);
        header('location: ' . BASE_URL . 'admin/posts/index.php');
    }
} else {
    $id = '';
    $title = '';
    $content = '';
    $publish = '';
    $topic = '';
}






// АПДЕЙТ СТАТЬИ                                                          UPDATE
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['id'])) {
    // Если у нас $_SERVER REQUEST_METHOD метод GET и тут у нас будет проверка глобального массива GET мы в этом суперглобальном методе GET
    // должны проверять есть ли там id и если он есть только тогда мы что-то делаем

    $post = selectOne('posts', ['id' => $_GET['id']]);

    $id = $post['id'];
    $title = $post['title'];
    $content = $post['content'];
    $topic = $post['id_topic'];
    $publish = $post['status'];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_post'])) {

    $id = $_POST['id'];
    $title = trim($_POST['title']);
    $content = trim($_POST['content']);
    $topic = trim($_POST['topic']);
    $publish = isset($_POST['publish']) ? 1 : 0;

    // Добавление и проверка фото
    if (!empty($_FILES['img']['name'])) {
        $imgName = time() . "_" . $_FILES['img']['name'];
        $fileTmpName = $_FILES['img']['tmp_name'];
        $fileType = $_FILES['img']['type'];
        $destination = ROOT_PATH . "\assets\img\posts\\" . $imgName;

        // Проверки (скажем это фото или нет?)
        if (strpos($fileType, 'image') === false) {
            $errMsg[] = "Подгружаемый файл не является изображением!";
        } else {
            $result = move_uploaded_file($fileTmpName, $destination);

            if ($result){
                $_POST['img'] = $imgName;
            } else {
                $errMsg[] = "Ошибка загрузки изображения на сервер";
            }
        }
    } else {
        $errMsg[] = "Ошибка получения картинки!";
    }


    if ($title === '' || $content === '' || $topic === '') {
        $errMsg[] = "Не все поля заполнены!";
    } elseif (mb_strlen($title, 'UTF-8') < 7) {
        $errMsg[] = "Название статьи должно быть более 7-ми символов!";
    } else {

//        $existence = selectOne('topics', ['title' => $title]);        Проверку на уникальности название поста не делаем можно делать при желании
//        if ($existence['title'] === $title) {
//            $errMsg = "Такая категория уже есть в базе!";
//        } else {}

        $post = [
            'id_user' => $_SESSION['id'],
            'title' => $title,
            'content' => $content,
            'img' => $_POST['img'],
            'status' => $publish,
            'id_topic' => $topic
        ];

        $post = update('posts', $id, $post); // id это то же самое что $post['id']
        header('location: ' . BASE_URL . 'admin/posts/index.php');
    }
} else {            // Это для того чтобы подтягивать прежние знчение которые мы ввели
    $title = $_POST['title'];
    $content = $_POST['content'];
    $publish = isset($_POST['publish']) ? 1 : 0; // Проверяем чекбокс publish пуст или нет
    $topic = $_POST['id_topic'];
}


// Publish and Un Publish
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['pub_id'])) {
    $id = $_GET['pub_id'];
    $publish = $_GET['publish'];    // его статус 'publish'

    $postId = update('posts', $id, ['status' => $publish]);

    header('location: ' . BASE_URL . 'admin/posts/index.php');
    exit();
}


// Удаление статьи                                                                       DELETE
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['delete_id'])) {
    // Если у нас $_SERVER REQUEST_METHOD метод GET и тут у нас будет проверка глобального массива GET мы в этом суперглобальном методе GET
    // должны проверять есть ли там delete_id и если он есть только тогда мы что-то делаем

    $id = $_GET['delete_id'];
    delete('posts', $id);
    header('location: ' . BASE_URL . 'admin/posts/index.php');
}