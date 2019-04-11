<?php
/**
 * 服务器B,根据数据库记录把A服务器的图片，视频等同步到B服务器
 */
 
//error_reporting(0);

include '../include/MyPDO.class.php';
class SyncData{
	public $dbConf;
	public $pdo;
	public $sev_b_receive;
	public $pash_img;
	public $pash_media;
	function __construct(){
		$this->dbConf = include '../config/database.php';
		$this->pdo = MyPDO::getInstance($this->dbConf);
		$this->sev_a_filepath = "127.0.0.1/tmpb/downloadfile.php";
		$this->path_img = '/newdata/web/img/';
		$this->path_media = '/newdata/web/media/';
		$this->path_downfile = '/newdata/web/exec/wget.php';//执行wget的php文件
	}

	/* 定时处理同步任务 */
	function crontab(){
		//获取需要同步的数据库记录
		$sql = "select id,img,img_md5,media,media_md5,sync_state from info where sync_state=1 limit 10";
		$rs = $this->pdo->query($sql);
		$datas = $rs->fetchAll(PDO::FETCH_ASSOC);
		foreach($datas as $key => $val){
			$img = $this->path_img . $val['img'];
			$media = $this->path_media . $val['media'];
			$is_up_over = true;
			if(!file_exists($img) && md5_file($img) != $val['img_md5']){
				//文件不存在或者文件没有下载完成则下载文件，利用linux的wget方式断点续传
			    $is_up_over = false;
				pclose(popen("php {$this->path_downfile} {$img} 2>&1 &", 'r'));
			}
			if(!file_exists($media) && md5_file($media) != $val['media_md5']){
				//文件不存在或者文件没有下载完成则下载文件，利用linux的wget方式断点续传
			    $is_up_over = false;
				pclose(popen("php {$this->path_downfile} {$media} 2>&1 &", 'r'));
			}
			if($is_up){
				//文件下载完成 更改b服务器数据状态sync_state=2
				$sql = "update info set sync_state = 2 where id =" . $val['id'];
				$ret = $this->pdo_query($sql);
			}	
		}
		//$this->write_json_msg($rda);
	}
}

$ac = new SyncData();
$ac->crontab();