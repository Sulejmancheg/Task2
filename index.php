<?php   // Создание и заполнение таблиц тестовыми данными

spl_autoload_extensions(".php");
spl_autoload_register();

$pdo = new PDO(
    "mysql:host=localhost; dbname=store; charset=utf8",
    'admin_store',
    'qwerty123456'
);

function insert($connection, $table, $fields = [], $data = [])
{
    foreach ($data as $key => $value){
        $data[$key] = implode('\', \'', $value);
    }

    $query = sprintf(
        "INSERT INTO %s (%s) VALUES ('%s')",
        $table,
        implode(', ', $fields),
        implode('\'), (\'', $data)
    );
    if ($connection->query($query)) {
        return $connection->lastInsertId();
    }
    return null;
}

function validDepth($a, $b, $count=0): bool
{
    foreach ($b as $value){
        if ($a == $value[1]){
            $count++;
            if ($count<3){
                if (validDepth($value[0], $b, $count) === true){
                    break;
                } else return false;
            } else return false;
        }
    }
    return true;
}


$tableNames = ['category', 'category_closure', 'goods', 'goods_category'];
$fieldsForTables = [
    $tableNames[0] => ['name'],
    $tableNames[1] => ['ancestor', 'descendant', 'depth'],
    $tableNames[2] => ['name', 'price'],
    $tableNames[3] => ['id_goods', 'id_category']
];
$goods = [];
$category = [];
$categoryClosure = [];
$goodsCategory = [];
$arrTemp = [];
$temp = [];


for ($i=0;$i<100;$i++){
    if ($i < 9){
        $goods []= array('Product 00'.$i +1, rand(0, 99999)/100);
    } elseif ($i >= 9 && $i < 99){
        $goods []= array('Product 0'.$i +1, rand(0, 99999)/100);
    } else {
        $goods []= array('Product '.$i +1, rand(0, 99999)/100);
    }
}

for ($i=1; $i<=31; $i++) {
    if ($i < 10){
        $category []= array('Category 0'.$i);
    } else {
        $category []= array('Category '.$i);
    }
    $categoryClosure[] = [$i, $i, 0];
    if ($i > 1 && $i <= 7) {
        $categoryClosure[] = [1, $i, 1];
    } elseif ($i > 7) {
        do {
            $randArr = array(rand(2, $i - 1), $i);
        } while (validDepth($randArr[0], $arrTemp) === false);
        $arrTemp []= $randArr;
        $randArr []= 1;
        $categoryClosure []= $randArr;
        foreach ($categoryClosure as $key => $value) {
            if ($key > 0 && $key < count($categoryClosure)) {
                if ($randArr[0] == $value[1] && $value[0] !== $value[1]) {
                    $categoryClosure[] = array($value[0], $randArr[1], $value[2] + 1);
                }
            }
        }
    }
}

for ($i=0;$i<100;$i++){
    $count = rand(1, 3);
    for ($j=0;$j<$count;$j++){
        do {
            $condition = [];
            $temp[$i][$j] = [$i + 1, rand(8, 31)];
            for ($k=0;$k<$j;$k++){
                $condition[$k] = $temp[$i][$k][1];
            }
        } while (in_array($temp[$i][$j][1], $condition));
    }
}

foreach ($temp as $key1 => $value1){
    foreach ($value1 as $key2 => $value2){
        $goodsCategory []= array_values($value2);
    }
}


$q1 = sprintf("CREATE TABLE IF NOT EXISTS %s 
                        (%s int(10) NOT NULL AUTO_INCREMENT PRIMARY KEY, 
                        %s varchar(255) NOT NULL)",
                $tableNames[0], 'id', $fieldsForTables[$tableNames[0]][0]);

$q2 = sprintf("CREATE TABLE IF NOT EXISTS %s 
                        (%s int(10) NOT NULL, 
                        %s int(10) NOT NULL, 
                        %s int(2) NOT NULL, PRIMARY KEY (%s, %s))",
                $tableNames[1], $fieldsForTables[$tableNames[1]][0], $fieldsForTables[$tableNames[1]][1], $fieldsForTables[$tableNames[1]][2],
                $fieldsForTables[$tableNames[1]][0], $fieldsForTables[$tableNames[1]][1]);

$q3 = sprintf("CREATE TABLE IF NOT EXISTS %s 
                        (%s int(10) NOT NULL AUTO_INCREMENT PRIMARY KEY, 
                        %s varchar(255) NOT NULL,  %s decimal(10, 2) NOT NULL)",
                $tableNames[2], 'id', $fieldsForTables[$tableNames[2]][0], $fieldsForTables[$tableNames[2]][1]);

$q4 = sprintf("CREATE TABLE IF NOT EXISTS %s 
                        (%s int(10) NOT NULL, 
                        %s int(10) NOT NULL, PRIMARY KEY (%s, %s))",
                $tableNames[3], $fieldsForTables[$tableNames[3]][0], $fieldsForTables[$tableNames[3]][1],
                $fieldsForTables[$tableNames[3]][0], $fieldsForTables[$tableNames[3]][1]);


$query = sprintf("DROP TABLE IF EXISTS %s", implode(', ', $tableNames));
$pdo->query($query);

$pdo->query($q1);
$pdo->query($q2);
$pdo->query($q3);
$pdo->query($q4);

insert($pdo, $tableNames[0], $fieldsForTables[$tableNames[0]], $category);
insert($pdo, $tableNames[1], $fieldsForTables[$tableNames[1]], $categoryClosure);
insert($pdo, $tableNames[2], $fieldsForTables[$tableNames[2]], $goods);
insert($pdo, $tableNames[3], $fieldsForTables[$tableNames[3]], $goodsCategory);
