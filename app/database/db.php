<?php

session_start();
require('connect.php');

// Напишем интересную функцию tt (test)
function tt($value){
    echo '<pre>';
    print_r($value);
    echo '</pre>';
    exit();
}


global $pdo;
// Проверка выполнения запроса к БД
function dbCheckError($query){
    $errInfo = $query -> errorInfo();       // выполнение
    if ($errInfo[0] !== PDO::ERR_NONE) {    // возврат и проверка на ошибки
        echo $errInfo[2];
        exit();
    }
    return true;    // мголи и не добавить
}


// Запросна получение данных с одной таблицы                    getAll
function selectAll($table, $params = []){
    global $pdo;
    $sql = "SELECT * FROM $table";

    if(!empty($params)){
        $i = 0;
        foreach ($params as $key => $value){
            if (!is_numeric($value)){               // Если $value не является числом
                $value = "'".$value."'";
            }
            if ($i === 0){
                $sql = $sql . " WHERE $key = $value";        // запрос
            } else {
                $sql = $sql . " AND $key = $value";          // запрос
            }
            $i++;
        }
    }
//    tt($sql); для того чтобы проверить правильно ли мы ввели запрос
//    exit();

    $query = $pdo->prepare($sql);        // подготовка
    $query->execute();                   // подготовка
    dbCheckError($query);                // выполнение, возврат и проверка на ошибки
    return $query->fetchAll();           // если ошибок нет через return возвращаем само значение
}


// Запросна получение одной строки с выбранной таблицы           getOne
function selectOne($table, $params = []){
    global $pdo;
    $sql = "SELECT * FROM $table";

    if(!empty($params)){
        $i = 0;
        foreach ($params as $key => $value){  // foreach разбираем как ключ значение
            if (!is_numeric($value)){   // Если $value не является числом
                $value = "'".$value."'";
            }
            if ($i === 0){
                $sql = $sql . " WHERE $key = $value";        // запрос
            } else {
                $sql = $sql . " AND $key = $value";          // запрос
            }
            $i++;
        }
    }
//    $sql = $sql . " LIMIT 1";  Тут это бесполезно тк когда мыиспользуем fetch() будет выводится одна строка

//    tt($sql);
//    exit();

    $query = $pdo->prepare($sql);      // подготовка
    $query->execute();                 // подготовка
    dbCheckError($query);              // выполнение, возврат и проверка на ошибки
    return $query->fetch();            // если ошибок нет через return возвращаем само значение
}


// Запись в таблицы БД                                      CREATE
function insert($table, $params){
    global $pdo;
    // INSERT INTO `users`(admin, username, email, password) VALUES ('0', 'Some21', 'some@test.com', '214A12z');

    $i = 0;
    $coll = '';                             // Первая строка будет колонка
    $mask = '';                             // Вторая будет значение этой колонки
    foreach ($params as $key => $value) {
        if ($i === 0) {
            $coll = $coll . "$key";
            $mask = $mask . "'" . "$value" . "'";
        } else {
            $coll = $coll . ", $key";
            $mask = $mask . ", '" . "$value" . "'";
        }
        $i++;
    }

    $sql = "INSERT INTO $table ($coll) VALUES ($mask)";

//    tt($sql);
//    exit();
    $query = $pdo->prepare($sql);
    $query->execute($params);
    dbCheckError($query);

    return $pdo->lastInsertId();    // Чтобы вернуть значение id (для id = insert(...) в /controllers)
}

$arrData = [
    'admin' => '0',
    'username' => 'user2f',
    'email' => 'r1w2@test.ru',
    'password' => '12g1!A1a2s'
];

//insert('users', $arrData);


// Обновление данных строки в таблице                       UPDATE
function update($table, $id, $params){
    global $pdo;;

    $i = 0;
    $str = '';    // Это будет строка запросов в ктором мы будем собирать sql запрос
    foreach ($params as $key => $value) {
        if ($i === 0) {
            $str = $str . $key . " = '" . $value . "'";
        } else {
            $str = $str . ", " . $key . " = '" . $value . "'";
        }
        $i++;
    }

    // UPDATE `users` SET username = 'test' WHERE `id` = 14
    $sql = "UPDATE $table SET $str WHERE id = $id";

//    tt($sql);
//    exit();
    $query = $pdo->prepare($sql);
    $query->execute($params);
    dbCheckError($query);
}

//$param = [
//    'admin' => '0',
//    'password' => '1Bg1#A2s',
//    'email' => 'test211s@gmail.com'
//];

//update('users', 7, $param);


// Удаление данных строки в таблице                          DELETE
function delete($table, $id){
    global $pdo;
    // DELETE FROM `users` WHERE id = 8
    $sql = "DELETE FROM $table WHERE id =". $id;
    $query = $pdo->prepare($sql);
    $query->execute();
    dbCheckError($query);
}

//delete('users', 8);


//$params = [
//    'admin' => 0,
//    'username' => 'Some21'
//];

//$sql = "SELECT * FROM users"; mysql запрос для получении всех users

// В $pdo находим методо prepare и забросим нашу переменную $sql Мы сделали подготовку после подготовки мы отпраляем этот запрос
// $query = $pdo->prepare($sql);
// Теперь обращаемся к обьекту $query и вызываем у него метод execute()
// $query->execute();
// Создадим переменную $errInfo и будем улавливать ошибки в нем если таковые будут
// $errInfo = $query -> errorInfo();

// Если ошибка будет найдено то мы выводим эту ошибку и прерываем выполнение кода (строгое неравенство !==)
// if ($errInfo[0] !== PDO::ERR_NONE){
//    echo $errInfo[2];
//    exit();
// }

// $data будет нашей информацией (бывает 2 типа получения данных)
// 1) когда мы знаем что будет возвращена одна строка из базы данных мы используем fetch()
// 2) если мы хотим выбрать всыу выборку SELECT * FROM users те мы хотим всю таблицу получить тогда fetch() не подойдет а fetchAll()
// $data = $query->fetch();

//tt(selectAll('users', $params)); Выполняем метод selectAll() (с параметрами $params для mysql запроса)
//tt(selectOne('users')); Выполняем метод selectOne()




// Попрактикуемся в join _ ах (Для sql специальный язык) | Обеденяем две таблицы | Lesson #33

// Выборка записей (posts) с автором в админку
function selectAllFromPostsWithUsers($table1, $table2){
    global $pdo;
    $sql = "
    SELECT 
    t1.id,
    t1.title,
    t1.img,
    t1.content,
    t1.status,
    t1.id_topic,
    t1.created_date,
    t2.username
    FROM $table1 AS t1 JOIN $table2 AS t2 ON t1.id_user = t2.id
    ";
    $query = $pdo->prepare($sql);
    $query->execute();
    dbCheckError($query);
    return $query->fetchAll();
}



// Функция для вывода Автора на главную страницу | p (post), u (user)
function selectAllFromPostsWithUsersOnIndex($table1, $table2, $limit, $offset){  // пробрасываем тут наш $limit и $offset
    global $pdo;                                                             // просто добавим запрос еще для нашего $limit и $offset
    $sql = "SELECT p.*, u.username FROM $table1 AS p JOIN $table2 AS u ON p.id_user = u.id WHERE p.status=1 LIMIT $limit OFFSET $offset";
    $query = $pdo->prepare($sql);
    $query->execute();
    dbCheckError($query);
    return $query->fetchAll();
}


//____________________ 4CAROUSEL ____________________
// Функция для вывода Top Поста на главную страницу
function selectTopTopicFromPostsOnIndex($table1){
    global $pdo;
    $sql = "SELECT * FROM $table1 WHERE id_topic = 16"; // получить все с таблицы $table1
    $query = $pdo->prepare($sql);
    $query->execute();
    dbCheckError($query);
    return $query->fetchAll();
}


//____________________ 4SEARCH ____________________
// Поиск по заголовкам и содержимого (примитивный) | p (post), u (user)
function searchInTitleAndContent($text, $table1, $table2){
    $text = trim(strip_tags(stripcslashes(htmlspecialchars($text))));
    global $pdo;
    $sql = "
    SELECT 
    p.*, u.username 
    FROM $table1 AS p 
    JOIN $table2 AS u 
    ON p.id_user = u.id 
    WHERE p.status=1
    AND p.title LIKE '%$text%' OR p.content LIKE '%$text%'";
    $query = $pdo->prepare($sql);
    $query->execute();
    dbCheckError($query);
    return $query->fetchAll();
}

// $text чтобы сделать очистку (примитивную) при помощи имеющихся функций в PHP
// Перед выборкой запросов к нам вылетает $text которую нам надо подчистить
// чтобы не отправили иньекции через блок поиска





//____________________ 4SINGLE PAGE ____________________
// Выборка записи (post) с автором на single page | p (post), u (user)
function selectPostFromPostsWithUsersOnSingle($table1, $table2, $id){
    global $pdo;
    $sql = "SELECT p.*, u.username FROM $table1 AS p JOIN $table2 AS u ON p.id_user = u.id WHERE p.id=$id";
    $query = $pdo->prepare($sql);
    $query->execute();
    dbCheckError($query);
    return $query->fetch(); // Убираем All чтобы не получить многомерный массив
}




//____________________ 4PAGINATION ____________________
function countRow($table){
    global $pdo;
    $sql = "SELECT COUNT(*) FROM $table WHERE status = 1";
    $query = $pdo->prepare($sql);
    $query->execute();
    dbCheckError($query);
    return $query->fetchColumn();
}

// WHERE status = 1
// Проверка опубликованы ли посты или нет






