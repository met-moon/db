# db
A database connection based on pdo

```php
<?php

// get a connection

$config = [
    'dsn'=>'mysql:host=localhost;dbname=test',
    'username'=>'root',
    'password'=>'root'
];
$db = new Moon\Db\Connection($config);

//get pdo instance
try{
    $pdo = $db->getPdo();
}catch (PDOException $e){
    echo $e->getMessage();
}


