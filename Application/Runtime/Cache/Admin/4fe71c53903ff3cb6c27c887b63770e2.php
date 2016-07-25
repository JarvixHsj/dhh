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
    <style type="text/css">
    body{padding: 10px; zoom: 1;}
    .l-text-invalid {float: left;}
    .l-exclamation {float: left; margin-top: 3px; margin-left: 6px;}
    .l-button-submit{width:80px; float:left; margin-left:10px; padding-bottom:2px;}
    .l-verify-tip{ left:230px; top:120px;}
    #errorLabelContainer{ padding:10px; width:300px; border:1px solid #FF4466; display:none; background:#FFEEEE; color:Red;}
    </style>
</head>

<body>
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
</if>
<script type="text/javascript" src="/github/dhh/Public/Admin/js/ajaxfileupload.js"></script>
<script type="text/javascript">
    function select_file_upload() {
        var self = $(this)[0];
        if (window[self.id + '_dialog']) {
            window[self.id + '_dialog'].show();
        } else {
            var wrap = '<div id="' + self.id + '_file_div" style="display:none;">';
            wrap += '<table style="height:100%;width:100%">';
            wrap += '<tr><td style="width:20%">选择文件:</td>';
            wrap += '<td><input type="file"  style="width:100%" id="' + self.id + '_file" name="' + self.id + '_file" /></td></tr>';
            wrap += '</table>';
            wrap += '</div>';
            $("body").append(wrap);

            window[self.id + '_dialog'] = $.ligerDialog.open({
                target: $('#' + self.id + '_file_div'),
                title: '上传文件',
                width: 360,
                height: 120,
                top: 170,
                left: 280,
                buttons: [
                    { text: '上传', onclick: function () { select_file_upload_save(self.id); } },
                    { text: '取消', onclick: function () { window[self.id + '_dialog'].hide(); } }
                ]
            });
        }
        return false;
    }

    function select_file_upload_save(id) {
        var file = $('#' + id + '_file').val();
        var extend = file.substring(file.lastIndexOf("."), file.length);
        extend = extend.toLowerCase();
        if (extend == ".jpg" || extend == ".jpeg" || extend == ".png" || extend == ".gif" || extend == ".bmp") {
        } else {
            $.ligerDialog.warn("请上传jpg,jpep,png,gif,bmp格式的图片文件");
            return;
        }

        $.ajaxFileUpload({
            url: $("#" + id).data("url-upload"),
            secureuri: false,
            fileElementId: id + "_file",
            dataType: "json",
            success: function (data, status) {
                if (data.status == "SUCCESS") {
                    var $id = $.ligerui.get(id);
                    $id.setValue(data.url);
                    $id.setText(data.url);
                    window[id + '_dialog'].hide();
                } else {
                    $.ligerDialog.warn(data.status);
                }
            },
            error: function (data, status, e) {
                $.ligerDialog.warn(data);
            }
        });
    }
</script>

<link rel="stylesheet" href="/github/dhh/Public/Admin/css/scale.css">
<script src="/github/dhh/Public/Admin/js/scale.js"></script>
<section class="imgzoom_pack">
    <div class="imgzoom_x">X</div>
    <div class="imgzoom_img"><img src="" style="max-width: 100%;height: auto;vertical-align: middle" /></div>
</section>
<ul class="contentArea" style="margin-left:20px;">
    <p><B>身份证正面图片：</B><B style="color:red"><?php echo ($positive["message"]); ?></B></p>
    <li style="width:350px;height:350px;background-size: 100%;float:left;margin-right: 20px;display: block;line-height:350px;">
        <?php if($row['id_positive'] != ''): ?><img src="/github/dhh/Public/<?php echo ($row['id_positive']); ?>" alt="法人头像" title="法人头像"  style="max-width: 100%;height: auto;vertical-align: middle">
        <?php else: ?>
            <img src="/github/dhh/Public/nopic.jpg" alt="法人头像" title="法人头像未完善"  style="max-width: 100%;height: auto;vertical-align: middle"><?php endif; ?>
         <div>
            <?php if($row['is_positive'] != 1): ?><button class="l-button positive">提交</button><?php endif; ?>
        </div>
        <!-- <button>审核正面身份证通过</button> -->
        <!-- <button>审核正面身份证通过</button> -->
        
    </li>
    <p><B>身份证反面图片：</B><B style="color:red"><?php echo ($reverse["message"]); ?></B></p>
    <li style="width:350px;height:350px;background-size: 100%;float:left;margin-right: 20px;display: block;line-height:350px">
        <?php if($row['id_reverse'] != ''): ?><img src="/github/dhh/Public/<?php echo ($row['id_reverse']); ?>" alt="头像（展示在物流端个人信息里面）" title="头像（展示在物流端个人信息里面）" style="max-width: 100%;height: auto;vertical-align: middle">
        <?php else: ?>
            <img src="/github/dhh/Public/nopic.jpg" alt="法人头像" title="头像（展示在物流端个人信息里面）未完善"  style="max-width: 100%;height: auto;vertical-align: middle"><?php endif; ?>
        <div>
            <?php if($row['is_reverse'] != 1): ?><button class="l-button reverse">提交</button><?php endif; ?>
        </div>
    <!-- <button>审核反面身份证通过</button> -->
    <!-- <button>审核反面身份证通过</button> -->
    </li>
    <input type="hidden" id="checkid" name="logistics_id" value="<?php echo ($row["logistics_id"]); ?>">
<script type="text/javascript">
    document.addEventListener("DOMContentLoaded", function(event){
        ImagesZoom.init({
            "elem": ".contentArea"
        });
    }, false);

    $(".positive").click(function(){
        var id = $('#checkid').val();
        $.ajax({
            cache: false,
            type: "POST",
            url: "<?php echo U(CONTROLLER_NAME.'/checkPositive');?>",
            data: "id="+id,
            async: false,
            error: function(request) {
                $.ligerDialog.error("请求错误");
            },
            success: function(json) {
                if(!json){
                    $.ligerDialog.warn('请求失败');
                    return;
                }
                if(!json.status){
                    $.ligerDialog.warn(json.info);
                    return;
                }

                $.ligerDialog.success(json.info, '提示', function() {
                    // window.parent.close_tab();
                });
            }
        });
    });

    $(".reverse").click(function(){
        var id = $('#checkid').val();
        $.ajax({
            cache: false,
            type: "POST",
            url: "<?php echo U(CONTROLLER_NAME.'/checkReverse');?>",
            data: "id="+id,
            async: false,
            error: function(request) {
                $.ligerDialog.error("请求错误");
            },
            success: function(json) {
                if(!json){
                    $.ligerDialog.warn('请求失败');
                    return;
                }
                if(!json.status){
                    $.ligerDialog.warn(json.info);
                    return;
                }

                $.ligerDialog.success(json.info, '提示', function() {
                    // window.parent.close_tab();
                });
            }
        });
    });
</script>

<?php if($is_ajax != 1): ?></body>
</html><?php endif; ?>