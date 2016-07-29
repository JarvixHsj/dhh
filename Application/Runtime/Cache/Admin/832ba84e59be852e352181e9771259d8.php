<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="renderer" content="webkit|ie-comp|ie-stand">
    <title><?php echo C('SITE_TITLE');?> - 后台管理系统</title>
    <!-- css -->
    <link rel="stylesheet" type="text/css" href="/jarvix/dhh/dhh/Public/Admin/ligerUI/skins/Aqua/css/ligerui-all.css" />
<link rel="stylesheet" type="text/css" href="/jarvix/dhh/dhh/Public/Admin/ligerUI/skins/ligerui-icons.css" />
<link rel="stylesheet" type="text/css" href="/jarvix/dhh/dhh/Public/Admin/ligerUI/skins/Gray/css/all.css" />
<link rel="stylesheet" type="text/css" href="/jarvix/dhh/dhh/Public/Admin/css/common.css" media="all">
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
<script type="text/javascript" src="/jarvix/dhh/dhh/Public/Admin/jquery/jquery-1.9.0.min.js"></script>
<script type="text/javascript" src="/jarvix/dhh/dhh/Public/Admin/ligerUI/js/ligerui.all.js"></script>
<script type="text/javascript" src="/jarvix/dhh/dhh/Public/Admin/ligerUI/js/plugins/ligerTab.js"></script>
<script type="text/javascript" src="/jarvix/dhh/dhh/Public/Admin/js/jquery.cookie.js"></script>
<script type="text/javascript" src="/jarvix/dhh/dhh/Public/Admin/js/json2.js"></script>
<script type="text/javascript" src="/jarvix/dhh/dhh/Public/Admin/jquery-validation/jquery.validate.min.js"></script>
<script type="text/javascript" src="/jarvix/dhh/dhh/Public/Admin/jquery-validation/jquery.metadata.js"></script>
<script type="text/javascript" src="/jarvix/dhh/dhh/Public/Admin/jquery-validation/messages_cn.js"></script>
<script type="text/javascript" src="/jarvix/dhh/dhh/Public/Admin/js/common.js"></script>
<!-- <script type="text/javascript" src="<?php echo U('Index/select_data');?>"></script>  -->
</if>
<script type="text/javascript" src="/jarvix/dhh/dhh/Public/Admin/js/ajaxfileupload.js"></script>
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

<div class="contentArea">
    <form id="<?php echo ($js_prefix); ?>form" method="post">
        <table width="100%" border="0" cellspacing="0" cellpadding="0" class="l-table-edit">
            <tr>
                <th class="l-table-edit-th" width="80">登录邮箱：</th>
                <td class="l-table-edit-td"><input id="email" name="email" type="text" ltype="text" readonly value="<?php echo ($row['email']); ?>" /></td>
            </tr>
            <tr>
                <th class="l-table-edit-th" width="80">新密码：</th>
                <td class="l-table-edit-td"><input id="password" name="password" type="password" validate="{required:true,minlength:5,maxlength:30}" /></td>
            </tr>
            <tr>
                <th class="l-table-edit-th" width="80">确认密码：</th>
                <td class="l-table-edit-td"><input id="cfm_password" name="cfm_password" type="password" validate="{required:true,minlength:5,maxlength:30}" /></td>
            </tr>
        </table>
    </form>
    <div class="commonBtnArea" >
        <input type="hidden" id="id" name="id" value="<?php echo ($row['id']); ?>" />
        <button id="<?php echo ($js_prefix); ?>submit" class="l-button l-button-submit">提 交</button>
    </div>
</div>

<script type="text/javascript">
$(function ()
{
    $.metadata.setType("attr", "validate");
    var v = $("#<?php echo ($js_prefix); ?>form").validate({
        debug: false,
        errorPlacement: function (lable, element) {
            if (element.hasClass("l-textarea"))
            {
                element.addClass("l-textarea-invalid");
            }
            else if (element.hasClass("l-text-field"))
            {
                element.parent().addClass("l-text-invalid");
            }
            $(element).removeAttr("title").ligerHideTip();
            $(element).attr("title", lable.html()).ligerTip();
        },
        success: function (lable) {
            var element = $("#" + lable.attr("for"));
            if (element.hasClass("l-textarea"))
            {
                element.removeClass("l-textarea-invalid");
            }
            else if (element.hasClass("l-text-field"))
            {
                element.parent().removeClass("l-text-invalid");
            }
            $(element).removeAttr("title").ligerHideTip();
        },
        submitHandler: function () {
            var form = <?php echo ($js_prefix); ?>form.getData();
            form.id = $("#id").val();
            $.ajax({
                cache: false,
                type: "POST",
                url: "<?php echo U(CONTROLLER_NAME.'/updatePassword');?>",
                data: {form: form},
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
                        window.parent.close_tab();
                    });
                }
            });
        }
    });

    var <?php echo ($js_prefix); ?>form = $("#<?php echo ($js_prefix); ?>form").ligerForm();
    $("#<?php echo ($js_prefix); ?>submit").click(function(){
        if(<?php echo ($js_prefix); ?>form.valid()) {
            $("#<?php echo ($js_prefix); ?>form").submit();
        }
    });
});
</script>

<?php if($is_ajax != 1): ?></body>
</html><?php endif; ?>