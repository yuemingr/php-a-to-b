<?php
include '../config/defind.php';
include '../include/MyPDO.class.php';
include '../include/Common.class.php';

class ActionA{
	public $dbConf;
	public $pdo;
	public $sev_b_receive;
	function __construct(){
		$this->dbConf = include '../config/database.php';
		$this->pdo = MyPDO::getInstance($this->dbConf);
	}
	
	/* 获取状态 */
	function getSyncState(){
		if(isset($_POST['ids'])){
			$dtxt = urldecode($_POST['datas']);
		}
		//示例数据
		$dtxt = '[{"id":"1"},{"id":"2"},{"id":"3"},{"id":"6"}]';
		$data = json_decode($dtxt,true);
		
		$ids = '';
		foreach($data as $key => $val){
			$ids .= $val['id'] . ",";
		}
		$ids = substr($ids,0,-1);
		
		//$sql = "update info set sync_state=1 where sync_state=0";
		//$rs = $this->pdo->query($sql);
		$sql = "select id,sync_state from info where id in(" . $ids . ")";
		$rs = $this->pdo->query($sql);
		$datas = $rs->fetchAll(PDO::FETCH_ASSOC);
		
		if(is_array($datas)){
			$rda = array('code'=>0,'msg'=>"ok",'data'=>$datas);	
		} else {
			$rda = array('code'=>1,'msg'=>"err",'data'=>array('info'=>"同步命令未发送成功"));
		}
		Common::write_json_msg($rda);
	}
	/* 更新sync_state为同步完成状态 */
	function upSyncStateSucc(){
		if(isset($_POST['datas'])){
			$dtxt = urldecode($_POST['datas']);
		}
		//示例数据
		$dtxt = '[{"id":"1"},{"id":"2"},{"id":"3"},{"id":"6"}]';
		$data = json_decode($dtxt,true);
		
		$ids = '';
		foreach($data as $key => $val){
			$ids .= $val['id'] . ",";
		}
		$ids = substr($ids,0,-1);
		
		//$sql = "update info set sync_state=1 where sync_state=0";
		//$rs = $this->pdo->query($sql);
		$sql = "update info set sync_state=" . SYNC_SUCC . " where id in(" . $ids . ")";
		$rs = $this->pdo->query($sql);
		
		if($rs){
			$rda = array('code'=>0,'msg'=>"ok",'data'=>array());	
		} else {
			$rda = array('code'=>1,'msg'=>"err",'data'=>array());
		}
		Common::write_json_msg($rda);
	}
}

$ac = new ActionA();
if($_GET && $_GET['type'] == 'getSyncState') {
	$ac->getSyncState();
}elseif($_GET && $_GET['type'] == 'upSyncStateSucc'){
	$ac->upSyncStateSucc();
}else{
	echo "welcome!";
}