<?php
include 'include/MyPDO.class.php';
$dbConf = include 'config/database.php';
$pdo = MyPDO::getInstance($dbConf);
$sql = "select * from info";
$rs = $pdo->query($sql);
$data = $rs->fetchAll();
print_r($data);
$sql = "insert into info(id,name,img,img_md5,media,media_md5,sync_state) values(1,'one','img1','aabbcc','media1','bbcc22',1)";
$rs = $pdo->query($sql);
print_r($rs);