<?php
include '../include/MyPDO.class.php';
include '../include/Common.class.php';
class Demo{
	public $dbConf;
	public $pdo;
	public $sev_b_receive;
	function __construct(){
		$this->dbConf = include '../config/database.php';
		$this->pdo = MyPDO::getInstance($this->dbConf);
	}
	
	/* 发起同步命令接口 */
	function sync(){
		//更新需要同步的数据库记录
		$sql = "update info set sync_state=1 where sync_state=0";
		$rs = $this->pdo->query($sql);
		if($rs){
			$rda = array('code'=>0,'msg'=>"ok",'data'=>array());	
		} else {
			$rda = array('code'=>1,'msg'=>"err",'data'=>array('info'=>"同步命令未发送成功"));
		}
		Common::write_json_msg($rda);
	}

}

$ac = new Demo();
if($_GET && $_GET['type'] == 'send') {
	$ac->sync();
}else{
	echo "welcome!";
}