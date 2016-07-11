<?php
include('_pre.php');

/**
 * 后台公共文件
 * 主要定义后台公共函数库
 */

/**
 * 检测验证码
 * @param  integer $id 验证码ID
 * @return boolean     检测结果
 * @author 麦当苗儿 <zuojiazi@vip.qq.com>
 */
function check_verify($code, $id = 1){
    $verify = new \Think\Verify();
    return $verify->check($code, $id);
}

function djson_encode($data) {
    return json_encode($data);
}

function handle_where($arr) {
	$where = array();
	foreach ($arr["rules"] as $value) {
		switch ($value["op"]) {
			case "like":
				$where[$value["field"]]=array(act($value['op']),"%".$value["value"]."%");
				break;
			case "startwith":
				$where[$value["field"]]=array(act($value['op']),$value["value"]."%");
				break;
			case "endwith":
				$where[$value["field"]]=array(act($value['op']),"%".$value["value"]);
				break;
			default:
				$where[$value["field"]]=array(act($value['op']),$value["value"]);
				break;
		}
		// $where[$value["field"]]=array(act($value['op']),$value["value"]);
		$where["_logic"]=$arr["op"];

	}
	return $where;
}

function tpl_str_replace($string, $arg1, $arg2) {
	return str_replace($arg1, $arg2, $string);
}