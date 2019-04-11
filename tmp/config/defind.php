<?php
define("SYNC_EMPTY",0); //未同步
define("SYNC_SUBMIT",1); //已经提交同步
define("SYNC_FILE",2); //文件数据已经同步完成
define("SYNC_SUCC",3); //同步已完成

define("URL_A_GETDATASTATE","http://192.168.3.204/test.php"); 
define("URL_A_UPDATASTATESUCC","http://192.168.3.204/test.php");

define("URL_B_RECEIVE","http://192.168.3.204/tmpb/api/receive.php");
