<?php
/**
 * 服务器B
 */
 
//error_reporting(0);
include '../config/defind.php';
include '../include/MyPDO.class.php';
include '../include/Common.class.php';
class SyncState{
	public $dbConf;
	public $pdo;

	function __construct(){
		$this->dbConf = include '../config/database.php';
		$this->pdo = MyPDO::getInstance($this->dbConf);
	}

	/* 定时处理 同步A,B服务器两边的数据状态 sync_state */
	function syncState(){
		//获取需要同步状态的数据库记录，每次同步100条
		$sql = "select id from info where sync_state=" . SYNC_FILE . " limit 100";
		$rs = $this->pdo->query($sql);
		$datas = $rs->fetchAll(PDO::FETCH_ASSOC);
		//获取对应A服务器的数据状态
		
		$data['datas'] = json_encode($datas);
		$ret = Common::request_post(URL_A_GETDATASTATE, $data);
		
		$state_from_a = json_decode($ret, true);
		if(!is_array($state_from_a) || $state_from_a['code'] != 0){
			//出现错误，写入日志退出程序
			return;
		}

		//根据sync_state是否为3，得到两个id串
		$succids = '';
		$arr_to_succ = '';
		foreach($state_from_a['data'] as $key => $val){
			if($val['sync_state'] == SYNC_SUCC){
				$succids .= $val['id'] . ",";
			}else{
				$arr_to_succ[] = $val;
			}
		}
		
		//更新B服务器的sync_state为同步完成状态
		if($succids != ""){
			$succids = substr($succids,0,-1);
			$sql = "update info set sync_state=" . SYNC_SUCC . " where id in($succids)";
			$ret = $this->pdo->query($sql);
		}
		
		//更新A服务中状态为同步完成状态
		if(is_array($arr_to_succ)){
			$data['datas'] = json_encode($arr_to_succ);
			Common::request_post(URL_A_UPDATASTATESUCC,$data);
		}
	}
}

$ac = new SyncState();
$ac->syncState();