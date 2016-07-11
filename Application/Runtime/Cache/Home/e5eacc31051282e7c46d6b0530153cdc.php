<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
    <title>东华行-我的订单-货物跟踪(未完成)</title>
    <link rel="stylesheet" href="/myworks/dhh/www/Public/Home/css/bootstrap.min.css" />
    <link rel="stylesheet" href="/myworks/dhh/www/Public/Home/css/global.css" />
    <link rel="stylesheet" href="/myworks/dhh/www/Public/Home/css/common.css" />
    <script type="text/javascript" src="/myworks/dhh/www/Public/Home/js/jquery_1.11.3.min.js" ></script>
    <script type="text/javascript" src="/myworks/dhh/www/Public/Home/js/bootstrap.min.js" ></script>
    <script type="text/javascript" src="/myworks/dhh/www/Public/Home/js/global.js" ></script>
	<style>
		.href a:hover{
			color:white;
		}
	</style>
</head>
<body class="bgf3f3f3">
	<div class="container">
		<!--<div class="row">-->
			<!--<div class="col-xs-12 header text-center c-ffffff fs-18 bgff7c2c goods-head">-->
				<!--<a href="#" class="com-top-left"><span class="glyphicon glyphicon-menu-left"></span></a>-->
				<!--货物跟踪-->
				<!--<a href="#" class="com-top-right fs-16">取消订单</a>-->
			<!--</div>-->
		<!--</div>-->
		<!--<div class="headbox"></div>-->
		<div class="row PT10 PB10 bgfff">
			<div class="col-xs-3 goods-logo PR0">
				<img src="/myworks/dhh/www/Public/<?php echo ($data["logistics_img"]); ?>" class="img-responsive img-circle"/>
			</div>
			<div class="col-xs-9">
				<p class="MT5 MB0 fs-16"><?php echo ($data["logistics_name"]); ?></p>
				<a href="http://192.168.1.0" class="goods-kf" onclick="call()">|&nbsp;&nbsp;联系客服</a>
				<!--<a href="http://123" class="goods-kf">|&nbsp;&nbsp;联系客服</a>-->
			</div>
		</div>
		<div class="row PT10 com-ce"></div>
		<div class="row bgfff">
			<div class="col-xs-12">
				<div class="goods-ddh fs-16">
					<span class="fl c-6c6c6c">订单号：<?php echo ($data["order_sn"]); ?></span>
					<span class="fr c-f6623a"><?php echo ($data["money"]); ?></span>
				</div>
				<ul class="dhh-carli MT5" style="border: none;">
					<li class="car-lx">路线：<?php echo ($data["order_depart_city"]); ?>--<?php echo ($data["order_des_city"]); ?></li>
					<li class="car-zz">载重：<?php echo ($data["order_weight"]); ?></li>
					<li class="car-hx">种类：<?php echo ($data["order_cargo_type"]); ?></li>
					<li class="car-tj">体积：<?php echo ($data["order_bulk"]); ?></li>
				</ul>
			</div>
		</div>
		<div class="row PT10 com-ce"></div>		
		<div class="row bgfff">
			<div class="col-xs-12 PR0">
				<div class="goods-ddh fs-16">
					<span class="fl c-6c6c6c">详细地址</span>
				</div>
				<div class="goods-ddh">
					<span class="fl goods-qd">起点</span>
					<span class="fl c-b3b3b3"><?php echo ($data["order_depart_province"]); ?>省<?php echo ($data["order_depart_city"]); ?>市<?php echo ($data["order_depart_details"]); ?></span>
				</div>
				<div class="goods-ddh" style="border: none;">
					<span class="fl goods-zd">终点</span>
					<span class="fl c-b3b3b3"><?php echo ($data["order_des_province"]); ?>省<?php echo ($data["order_des_city"]); ?>市<?php echo ($data["order_des_details"]); ?></span>
				</div>
			</div>
		</div>
		<div class="row PT10 com-ce"></div>		
		<div class="row bgfff" style="border-bottom: 1px solid #e9e9e9;">
			<div class="col-xs-12 PR0">
				<div class="goods-ddh fs-16">
					<span class="fl c-6c6c6c">物流信息</span>				
				</div>
				<?php if(null == $data[track]): ?><p class="c-f6623a MT10 MB10">暂无物流信息</p><?php endif; ?>
				<table class="goods-wllist">
					<?php if(is_array($data["track"])): foreach($data["track"] as $k=>$vo): ?><tr>
						<td class="col-xs-1 goods-tdl PT15" valign="top">
							<img <?php if($k == $data[trackcount]): ?>src="/myworks/dhh/www/Public/Home/img/icon14.png"<?php else: ?>src="/myworks/dhh/www/Public/Home/img/icon13.png"<?php endif; ?>  width="30%" height="30%" class="img-responsive center-block"/></td>
						<td class="col-xs-11 goods-tdr PT15">
							<p class="MB0"><?php echo ($vo["track_name"]); echo ($vo["track_content"]); ?></p>
							<p><?php echo ($vo["track_time"]); ?></p>
						</td>
					</tr><?php endforeach; endif; ?>
				</table>
			</div>
		</div>
		<!--如果物流端没有确认送达，则客户端不显示确认送达按钮-->
		<?php if($data['order_affirm'] == 1): ?><div class="row href">
					<a href="http://192.168.1.1" class="goods-sd center-block MT15 MB20" onclick="clickbuttom()">确认送达</a>
			</div><?php endif; ?>

	</div>
	</body>

</html>