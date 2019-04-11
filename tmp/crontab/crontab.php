<?php
include '../config/defind.php';
include '../include/MyPDO.class.php';
include '../include/Common.class.php';


class Crontab{
	public $dbConf;
	public $pdo;
	public $sev_b_receive;
	function __construct(){
		$this->dbConf = include '../config/database.php';
		$this->pdo = MyPDO::getInstance($this->dbConf);
	}
	
	/**
	* 定时执行
	*/
	function crontab(){
		//获取同步中的数据库记录，每次同步10条，可以根据实际状况想修改
		$sql = "select * from info where sync_state=1 limit 10";
		$rs = $this->pdo->query($sql);
		$datas = $rs->fetchAll(PDO::FETCH_ASSOC);
		$data['datas'] = json_encode($datas);
		
		//向B服务器提交同步的数据库数据
		$ret = Common::request_post(URL_B_RECEIVE, $data);
		if($ret){
			$arr = json_decode($ret,true);
			if(is_array($arr) && $arr['code'] == 0){
				$rda = array('code'=>0,'msg'=>"ok",'data'=>array());
			}else{
				$rda = array('code'=>1,'msg'=>"err",'data'=>array('info'=>"同步数据通知不成功"));
			}
			
		} else {
			$rda = array('code'=>2,'msg'=>"err",'data'=>array('info'=>"网络超时"));
		}
		print_r($data);
		print_r($rda);
		//$this->write_log($rda) 写入日志;
	}
	
	function write_log($msg = array()){
		//
	}
}

$ac = new Crontab();
$ac->crontab();

