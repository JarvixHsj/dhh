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
				<!--<a href="#" class="com-top-right fs-16">取消订单</a>		-->
			<!--</div>-->
		<!--</div>-->
		<!--<div class="headbox"></div>-->
		<div class="row PT10 PB10 bgfff">
			<div class="col-xs-3 goods-logo PR0">
				<img src="/myworks/dhh/www/Public/<?php echo ($data["user_img"]); ?>" class="img-responsive img-circle"/>
			</div>
			<div class="col-xs-9">
				<p class="MT5 MB0 fs-16">货主：<?php echo ($data["user_name"]); ?></p>
				<br/>
				<!--<a href="http://192.168.1.0" onclick="call()" class="goods-kf">|&nbsp;&nbsp;联系客服</a>-->
				<span style="color:#B5B5B5;">TA的叮嘱：<?php echo ($data["order_remark"]); ?></span>
			</div>
		</div>
		<div class="row PT10 com-ce"></div>
		<div class="row bgfff">
			<div class="col-xs-12">
				<div class="goods-ddh fs-16">
					<p class="c-6c6c6c">订单号：<?php echo ($data["order_sn"]); ?></p>
					<p class="c-6c6c6c MB0" style="font-size:14px;">上货时间：<?php echo (date('Y-m-d H点',$data["order_time"])); ?></p>
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
        <div class="row bgfff">
            <div class="goods-ddh">
                <div class="col-xs-12 PR0">
                    <span class="fl goods-ddbj">订单报价</span>
                    <span class="x-money"><?php if(!empty($$data[money])): ?>0<?php else: echo ($data["money"]); endif; ?> 元</span>
                </div>
            </div>
        </div>
		<?php if($data['comment'] != ''): ?><div class="row PT10 com-ce"></div>
			<div class="row bgfff">
				<div class="goods-ddh">
					<div class="col-xs-12 PR0">
						<span class="fl goods-ddbj">用户评价 ：</span><span class="x-money" style="float:left;"><?php echo ($data["comment"]); ?></span>
					</div>
				</div>
			</div>
		<?php else: ?>
			<div class="row href">
				<button class="goods-sd center-block MT15 MB20" >已送达</button>
			</div><?php endif; ?>

	</div>
	</body>
</html>