<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="renderer" content="webkit|ie-comp|ie-stand">
    <title><?php echo C('SITE_TITLE');?> - 后台管理系统</title>
    <!-- css -->
    <link rel="stylesheet" type="text/css" href="/github/dhh/Public/Admin/ligerUI/skins/Aqua/css/ligerui-all.css" />
<link rel="stylesheet" type="text/css" href="/github/dhh/Public/Admin/ligerUI/skins/ligerui-icons.css" />
<link rel="stylesheet" type="text/css" href="/github/dhh/Public/Admin/ligerUI/skins/Gray/css/all.css" />
<link rel="stylesheet" type="text/css" href="/github/dhh/Public/Admin/css/common.css" media="all">
</head>

<body>
    <div class="wrap">
        <!-- header -->
        

        <!-- main -->
        <div class="mainBody">
            <!-- left -->
            

            <!-- right -->
            <div id="Right">
                
<table width="100%" border="0" cellspacing="0" cellpadding="0" class="tab">
    <tr>
        <td width="120" align="right">操作系统：</td>
        <td><?php echo ($info['system']); ?></td>
        <td width="120" align="right">主机名IP端口：</td>
        <td><?php echo ($info['hostport']); ?></td>
    </tr>
    <tr>
        <td width="120" align="right">服务器：</td>
        <td><?php echo ($info['server']); ?></td>
        <td width="120" align="right">PHP运行方式：</td>
        <td><?php echo ($info['php_env']); ?></td>
    </tr>
    <tr>
        <td width="120" align="right">程序目录：</td>
        <td><?php echo ($info['app_dir']); ?></td>
        <td width="120" align="right">MYSQL版本：</td>
        <td><?php echo ($info['mysql']); ?></td>
    </tr>
    <tr>
        <td width="120" align="right">GD库版本：</td>
        <td><?php echo ($info['gd']); ?></td>
        <td width="120" align="right">上传附件限制：</td>
        <td><?php echo ($info['upload_size']); ?></td>
    </tr>
    <tr>
        <td width="120" align="right">执行时间限制：</td>
        <td><?php echo ($info['exec_time']); ?></td>
        <td width="120" align="right">剩余空间：</td>
        <td><?php echo ($info['disk_free']); ?></td>
    </tr>
    <tr>
        <td width="120" align="right">服务器时间：</td>
        <td><?php echo ($info['server_time']); ?></td>
        <td width="120" align="right">北京时间：</td>
        <td><?php echo ($info['beijing_time']); ?></td>
    </tr>
    <tr>
        <td width="120" align="right">allow_url_fopen</td>
        <td><?php echo ($info['fopen']); ?></td>
        <td width="120" align="right">register_globals：</td>
        <td><?php echo ($info['reg_gbl']); ?></td>
    </tr>
    <tr>
        <td width="120" align="right">magic_quotes_gpc：</td>
        <td><?php echo ($info['quotes_gpc']); ?></td>
        <td width="120" align="right">magic_quotes_runtime：</td>
        <td><?php echo ($info['quotes_runtime']); ?></td>
    </tr>
</table>

            </div>
        </div>
        <div class="clear"></div>

        <!-- footer -->
        
    </div>

<script type="text/javascript">var cookie_prefix = '<?php echo C('COOKIE_PREFIX');?>', cookie_domain = '', cookie_path = '/', cookie_secure = false, admin_defult_url = '<?php echo U('Public/index');?>';</script>
<script type="text/javascript" src="/github/dhh/Public/Admin/jquery/jquery-1.9.0.min.js"></script>
<script type="text/javascript" src="/github/dhh/Public/Admin/ligerUI/js/ligerui.all.js"></script>
<script type="text/javascript" src="/github/dhh/Public/Admin/ligerUI/js/plugins/ligerTab.js"></script>
<script type="text/javascript" src="/github/dhh/Public/Admin/js/jquery.cookie.js"></script>
<script type="text/javascript" src="/github/dhh/Public/Admin/js/json2.js"></script>
<script type="text/javascript" src="/github/dhh/Public/Admin/jquery-validation/jquery.validate.min.js"></script>
<script type="text/javascript" src="/github/dhh/Public/Admin/jquery-validation/jquery.metadata.js"></script>
<script type="text/javascript" src="/github/dhh/Public/Admin/jquery-validation/messages_cn.js"></script>
<script type="text/javascript" src="/github/dhh/Public/Admin/js/common.js"></script>
<!-- <script type="text/javascript" src="<?php echo U('Index/select_data');?>"></script>  -->
</body>
</html>