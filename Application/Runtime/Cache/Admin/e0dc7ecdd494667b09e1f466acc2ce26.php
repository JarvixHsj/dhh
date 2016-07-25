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

<p>您正在为角色：<b><?php echo ($role["name"]); ?></b> 分配权限，项目和模块有全选和取消全选功能</p>
<br />
<form>
    <table width="100%" border="0" cellspacing="0" cellpadding="0" class="tab">
        <?php if(is_array($nodes)): $i = 0; $__LIST__ = $nodes;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$group): $mod = ($i % 2 );++$i;?><tr>
                <td style="font-size: 14px;"><label><input name="access[]" level="1" type="checkbox" obj="node_<?php echo ($group["id"]); ?>" value="<?php echo ($group["id"]); ?>:1:0"/> <b>[应用]</b> <?php echo ($group["title"]); ?></label></td>
            </tr>
            <?php if(is_array($group['modules'])): $i = 0; $__LIST__ = $group['modules'];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$module): $mod = ($i % 2 );++$i;?><tr>
                    <td style="padding-left: 30px; font-size: 14px;"><label><input name="access[]" level="2" type="checkbox" obj="node_<?php echo ($group["id"]); ?>_<?php echo ($module["id"]); ?>" value="<?php echo ($module["id"]); ?>:2:<?php echo ($module["pid"]); ?>"/> <b>[模块]</b> <?php echo ($module["title"]); ?></label></td>
                </tr>
                <tr>
                    <td style="padding-left: 50px;">
                        <?php if(is_array($module['actions'])): $i = 0; $__LIST__ = $module['actions'];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$action): $mod = ($i % 2 );++$i;?><label><input name="access[]" level="3" type="checkbox" obj="node_<?php echo ($group["id"]); ?>_<?php echo ($module["id"]); ?>_<?php echo ($action["id"]); ?>" value="<?php echo ($action["id"]); ?>:3:<?php echo ($action["pid"]); ?>"/> <?php echo ($action["title"]); ?></label> &nbsp;&nbsp;&nbsp;<?php endforeach; endif; else: echo "" ;endif; ?>
                    </td>
                </tr><?php endforeach; endif; else: echo "" ;endif; endforeach; endif; else: echo "" ;endif; ?>
    </table>
    <input type="hidden" name="id" value="<?php echo ($role["id"]); ?>"/>
</form>
<div class="commonBtnArea" >
    <button class="l-button submit">提交</button>
    <button class="l-button reset">恢复</button>
    <button class="l-button empty">清空</button>
</div>

<script type="text/javascript">
    //初始化数据
    function setAccess(){
        //清空所有已选中的
        $("input[type='checkbox']").prop("checked",false);
        var access=$.parseJSON('<?php echo ($role["access"]); ?>');
        var access_length=access.length;
        if(access_length>0){
            for(var i=0;i<access_length;i++){
                $("input[type='checkbox'][value='" + access[i].val + "']").prop("checked","checked");
            }
        }
    }

    $(function(){
        //执行初始化数据操作
        setAccess();
        //为项目时候全选本项目所有操作
        $("input[level='1']").click(function(){
            var obj=$(this).attr("obj")+"_";
            $("input[obj^='"+obj+"']").prop("checked",$(this).prop("checked"));
        });
        //为模块时候全选本模块所有操作
        $("input[level='2']").click(function(){
            var obj=$(this).attr("obj")+"_";
            $("input[obj^='"+obj+"']").prop("checked",$(this).prop("checked"));
            //分隔obj为数组
            var tem=obj.split("_");
            //将当前模块父级选中
            if($(this).prop('checked')){
                $("input[obj='node_"+tem[1]+"']").prop("checked","checked");
            }
        });
        //为操作时只要有勾选就选中所属模块和所属项目
        $("input[level='3']").click(function(){
            var tem=$(this).attr("obj").split("_");
            if($(this).prop('checked')){
                //所属项目
                $("input[obj='node_"+tem[1]+"']").prop("checked","checked");
                //所属模块
                $("input[obj='node_"+tem[1]+"_"+tem[2]+"']").prop("checked","checked");
            }
        });
        //重置初始状态，勾选错误时恢复
        $(".reset").click(function(){
            setAccess();
        });
        //清空当前已经选中的
        $(".empty").click(function(){
            $("input[type='checkbox']").prop("checked",false);
        });
        $(".submit").click(function(){
            $.ajax({
                cache: false,
                type: "POST",
                url: "<?php echo U(CONTROLLER_NAME.'/doAssignAccess');?>",
                data: $("form").serializeArray(),
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
        });
    });
</script>

<?php if($is_ajax != 1): ?></body>
</html><?php endif; ?>