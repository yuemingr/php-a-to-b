<?php
/**
 * 服务器B
 */
 
error_reporting(0);

include '../include/MyPDO.class.php';
include '../include/Common.class.php';
class ActionB{
	public $dbConf;
	public $pdo;
	function __construct(){
		$this->dbConf = include '../config/database.php';
		$this->pdo = MyPDO::getInstance($this->dbConf);
	}
	
	/* 接收同步命令接口 */
	function receive(){
		//只处理新增数据同步，
		//数据修改的情况的同步需要另外添加逻辑处理
		//获取同步的数据库记录
		if(isset($_POST['datas'])){
			$dtxt = urldecode($_POST['datas']);
		}
		//示例数据
		$dtxt = ' [{"id":"27","name":"one","img":"img1","img_md5":"aabbcc","media":"media1","media_md5":"bbcc22","sync_state":"0"},{"id":"2","name":"one","img":"img1","img_md5":"aabbcc","media":"media1","media_md5":"bbcc22","sync_state":"0"}]';
		$data = json_decode($dtxt);
		$dtxt = '';
		foreach($data as $key => $val){
			if($key == 0){
				$dtxt .= "(";	
			}else{
				$dtxt .= ",(";
			}
			$dtxt .= $val->id . '1,';
			$dtxt .= "'$val->name'" . ',';
			$dtxt .= "'$val->img'" . ',';
			$dtxt .= "'$val->img_md5'" . ',';
			$dtxt .= "'$val->media'" . ',';
			$dtxt .= "'$val->media_md5'" . ',';
			$dtxt .= $val->sync_state;
			$dtxt .= ")";
		}
		$sql = "insert into info(id,name,img,img_md5,media,media_md5,sync_state) values " . $dtxt;
		try {
			$rs = $this->pdo->query($sql);
			if($rs){
				$rda = array('code'=>0,"msg"=>"ok","data"=>array());
			}else{
				$rda = array('code'=>1,"msg"=>"err","data"=>array("info"=>"数据同步不成功功,可能存在id冲突"));
			}
		} catch(Exception $e) {
			$rda = array('code'=>2,"msg"=>"err","data"=>array());
		}
		Common::write_json_msg($rda);
	}
	
}

$ac = new ActionB();
$ac->receive();

