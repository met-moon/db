# db
A database connection based on pdo

```php
<?php

// get a connection

//method 1 using set constructor
/*$config = [
    'dsn'=>'mysql:host=localhost;dbname=test',
    'username'=>'root',
    'password'=>'root'
];
$db = new \coco\db\Connection($config);*/

// or method 2 using set object attributes
$db = new \coco\db\Connection();
$db->dsn = 'mysql:host=localhost;dbname=blog';
$db->username = 'root';
$db->password = 'root';

//get pdo instance
try{
    $db->getPdo();
}catch (PDOException $e){
    echo $e->getMessage();
}


