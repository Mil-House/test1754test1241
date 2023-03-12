<?php
// Запросы к базе данных MySQL при PHP (PDO)
// $connection = new PDO("mysql:host=localhost;dbname=webdb", "root", "root");

// запись данных
// $query = "INSERT users (name, age, login, password) VALUE ('Klim', '28', 'klim2s51', 'gsx#uA2!sd')";
// $connection->exec($query);

// наши переменные 
// $name = 'Maxim';
// $age = 31;
// $login = '2sMAxmic';

// записываем параметры в ассоцативный масив $param
// $param = [
//     //ключ => значение
//     'n' => $name, 
//     'age' => $age, 
//     'log' => $login
// ];

// $sql = "INSERT users (name, age, login) VALUE (:n, :age, :login)"; // готовим запрос с плейсхолдером
// $query = $connection->prepare($sql); // подготавливаем сам запрос

// $query->execute(['n'=>$name, 'age'=>$age, 'login'=>$login]);
// or
// $query->execute($param);
?>