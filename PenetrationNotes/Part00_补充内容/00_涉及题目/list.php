<?php
header('content-type:text/html;charset=utf-8');
require_once '../config.php';
//解密过程
function decode($data){
	$td = mcrypt_module_open(MCRYPT_RIJNDAEL_128,'',MCRYPT_MODE_CBC,'');
	mcrypt_generic_init($td,'ydhaqPQnexoaDuW3','2018201920202021');
	$data = mdecrypt_generic($td,base64_decode(base64_decode($data)));
	mcrypt_generic_deinit($td);
	mcrypt_module_close($td);
	if(substr(trim($data),-6)!=='_mozhe'){
		echo '<script>window.location.href="/index.php";</script>';
	}else{
		return substr(trim($data),0,strlen(trim($data))-6);
	}
}
$id=decode($_GET['id']);
$sql="select id,title,content,time from notice where id=$id";
$info=$link->query($sql);
$arr=$info->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>X公司HR系统V1.0</title>
<style>.body{width:600px;height:500px;margin:0 auto}.title{color:red;height:60px;line-height:60px;font-size:30px;font-weight:700;margin-top:75pt;border-bottom:2px solid red;text-align:center}.content,.title{margin:0 auto;width:600px;display:block}.content{height:30px;line-height:30px;font-size:18px;margin-top:40px;text-align:left;color:#828282}</style>
</head>
<body>
<div class="body">
<div class="title"><?php echo $arr['title']?></div>
<div class="content"><?php echo $arr['content']?></div>
</body>
</html>