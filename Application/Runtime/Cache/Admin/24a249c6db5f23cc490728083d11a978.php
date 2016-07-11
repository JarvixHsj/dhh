<?php if (!defined('THINK_PATH')) exit(); if($is_ajax != 1): ?><!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="renderer" content="webkit|ie-comp|ie-stand">
    <title><?php echo C('SITE_TITLE');?> - 后台管理系统</title>
    <!-- css -->
    <link rel="stylesheet" type="text/css" href="/myworks/dhh/www/Public/Admin/ligerUI/skins/Aqua/css/ligerui-all.css" />
<link rel="stylesheet" type="text/css" href="/myworks/dhh/www/Public/Admin/ligerUI/skins/ligerui-icons.css" />
<link rel="stylesheet" type="text/css" href="/myworks/dhh/www/Public/Admin/ligerUI/skins/Gray/css/all.css" />
<link rel="stylesheet" type="text/css" href="/myworks/dhh/www/Public/Admin/css/common.css" media="all">
    <style type="text/css">
    body{padding: 10px; zoom: 1;}
    </style>
</head>

<body>
<script type="text/javascript">var cookie_prefix = '<?php echo C('COOKIE_PREFIX');?>', cookie_domain = '', cookie_path = '/', cookie_secure = false, admin_defult_url = '<?php echo U('Public/index');?>';</script>
<script type="text/javascript" src="/myworks/dhh/www/Public/Admin/jquery/jquery-1.9.0.min.js"></script>
<script type="text/javascript" src="/myworks/dhh/www/Public/Admin/ligerUI/js/ligerui.all.js"></script>
<script type="text/javascript" src="/myworks/dhh/www/Public/Admin/ligerUI/js/plugins/ligerTab.js"></script>
<script type="text/javascript" src="/myworks/dhh/www/Public/Admin/js/jquery.cookie.js"></script>
<script type="text/javascript" src="/myworks/dhh/www/Public/Admin/js/json2.js"></script>
<script type="text/javascript" src="/myworks/dhh/www/Public/Admin/jquery-validation/jquery.validate.min.js"></script>
<script type="text/javascript" src="/myworks/dhh/www/Public/Admin/jquery-validation/jquery.metadata.js"></script>
<script type="text/javascript" src="/myworks/dhh/www/Public/Admin/jquery-validation/messages_cn.js"></script>
<script type="text/javascript" src="/myworks/dhh/www/Public/Admin/js/common.js"></script>
<!-- <script type="text/javascript" src="<?php echo U('Index/select_data');?>"></script>  --><?php endif; ?>
<div class="main_content">
    <form id="<?php echo ($js_prefix); ?>form" class="correct_form"></form>
    <?php if($xzh != 1): ?><input type="submit" value="提 交" id="<?php echo ($js_prefix); ?>submit" class="l-button l-button-submit" /><?php endif; ?>
</div>
<script type="text/javascript" src="/myworks/dhh/www/Public/Admin/js/ajaxfileupload.js"></script>
<script type="text/javascript">
;(function ($, window) {

    $.fn.<?php echo ($js_prefix); ?>form = function(options) {
        var $this = this;

        var defaults = {
            id: '<?php echo ($js_prefix); ?>',
            title: '',
            identity_key: 'id',
            identity_val: 0,
            title: '',
            url_create: '<?php echo U(CONTROLLER_NAME.'/create');?>',
            url_update: '<?php echo U(CONTROLLER_NAME.'/update');?>',
            url_parms: {},
            input_width: 300,
            label_width: 90,
            space: 40,
            label_align: 'left',
            validate: true,
            type: 'page', //dialog
            fields: {},
            data: null
        };

        options = $.extend({}, defaults, options || {}, {
            form: null,
            dialog: null
        });

        var $module_id = options.id;
        var $module_identity = "id";
        var $module_form = $("#" + $module_id + "form");
        var $module_submit = $("#" + $module_id + "submit");

        function init() {
            init_form();
            return $this;
        }

        function init_form() {
            window.parent.<?php echo ($js_prefix); ?>form = options.form = $module_form.ligerForm({
                inputWidth: options.input_width,
                labelWidth: options.label_width,
                space: options.space,
                labelCss: 'l-table-edit-th',
                labelAlign: options.label_align,
                validate : options.validate,
                fields: options.fields
            });

            if(options.data) {
                options.form.setData(options.data);
            }

            $module_submit.click(function(){
                if(options.form.valid()) {
                    submit();
                } else {
                    options.form.showInvalid();
                }
            });

            $.each(options.fields, function(i, item) {
                if (item.type == "select" && item.hasOwnProperty('comboboxName') && item.hasOwnProperty('options') && item.options.hasOwnProperty('url_upload')) {
                    var $upload_field = $.ligerui.get(item.comboboxName);
                    $upload_field.set('onBeforeOpen', select_file_upload);
                    if (options.data && options.data.hasOwnProperty(item.name)) {
                        $upload_field.setValue(options.data[item.name]);
                        $upload_field.setText(options.data[item.name]);
                    }
                } else if (item.type == "popup" && item.hasOwnProperty('comboboxName') && item.hasOwnProperty('options') && item.options.hasOwnProperty('url_upload')) {
                    var $upload_field = $.ligerui.get(item.comboboxName);
                    $upload_field.set('onButtonClick', popup_file_upload);
                    if (options.data && options.data.hasOwnProperty(item.name)) {
                        $upload_field.setValue(options.data[item.name]);
                        $upload_field.setText(options.data[item.name]);
                    }
                    if (options.data.hasOwnProperty(item.comboboxName)) {
                        $upload_field.set('data', options.data[item.comboboxName]);
                    }
                }
            });
        }

        function submit() {
            var id = 0;
            var row = options.form.getData();

            $.each(options.fields, function(i, item) {
                if ((item.type == "date" || item.type == "datetime") && row.hasOwnProperty(item.name)) {
                    var date = new Date(row[item.name]);
                    var year = date.getFullYear();
                    var month = date.getMonth() + 1;
                    if(month > 12){
                        month = 12;
                    }
                    var day = date.getDate();
                    var h = date.getHours();
                    var m = date.getMinutes();
                    var s = date.getSeconds();
                    row[item.name] = year + "-" + month + "-" + day + " " + h + ":" + m + ":" + s;
                }
            });

            if(options.identity_val > 0) {
                row.id = options.identity_val;
                id = row.id;
            } else if(options.data && options.data.hasOwnProperty('id')) {
                row.id = options.data.id;
                id = row.id;
            }

            $.ajax({
                cache: false,
                type: "POST",
                url: id > 0 ? options.url_update : options.url_create,
                data: {form: row},
                error: function(request) {
                    window.parent.$.ligerDialog.error("请求失败");
                },
                success: function(result) {
                    if(!result.status) {
                        window.parent.$.ligerDialog.warn(result.info);
                        return;
                    }

                    window.parent.$.ligerDialog.success('提交成功', '提示', function() {
                        if (options.type == 'dialog') {
                            window.parent.<?php echo ($js_prefix); ?>grid.reload();
                            window.parent.<?php echo ($js_prefix); ?>dialog.close();
                        } else if (options.type == 'page') {
                            window.parent.close_tab();
                        }
                    });
                }
            });
        }

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
                        { text: '上传', onclick: function () { select_file_upload_save(self); } },
                        { text: '取消', onclick: function () { window[self.id + '_dialog'].hide(); } }
                    ]
                });
            }
            return false;
        }

        function select_file_upload_save(target) {
            var id = target.id;
            var file = $('#' + id + '_file').val();
            var extend = file.substring(file.lastIndexOf("."), file.length);
            extend = extend.toLowerCase();
            if (extend == ".jpg" || extend == ".jpeg" || extend == ".png" || extend == ".gif" || extend == ".bmp") {
            } else {
                $.ligerDialog.warn("请上传jpg,jpep,png,gif,bmp格式的图片文件");
                return;
            }

            $.ajaxFileUpload({
                url: target.options.url_upload,
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

        function popup_file_upload() {
            var o = this;

            if(window.parent.httprequest_upload) window.parent.httprequest_upload.abort();

            window.parent.httprequest_upload = $.get(o.options.url_upload, function(result){
                if(!result) {
                    window.parent.$.ligerDialog.warn('请求失败!');
                    return;
                }

                if(result.errno && parseInt(result.errno) < 0) {
                    window.parent.$.ligerDialog.warn(result.error);
                    return;
                }

                window[o.id + '_dialog'] = window.parent.$.ligerDialog.open({
                    title: '上传文件',
                    id: 'multi_upload_dialog',
                    width: 620,
                    height: null,
                    //top: 100,
                    content: result,
                    show: false,
                    isResize: true,
                    isHidden: false,
                    load: false,
                    setData: setData,
                    getData: getData,
                    buttons: [
                        { text: '确定', onclick: function () { popup_file_upload_save(); } }
                    ]
                });
            });

            var popup_file_upload_save = function() {
                var data = o.get('imagedata');
                var value = '', text = '', saperator = '';

                if(data && typeof(data) === 'object')
                {
                    $.each(data, function(i){
                        var item = data[i];
                        if(!item) return;
                        value += saperator+item.savepath+item.savename;
                        text = value;
                        saperator = ';';
                    });
                }

                if(typeof(o.setValue) === 'function') o.setValue(value);
                if(typeof(o.setText) === 'function') o.setText(text);

                window[o.id + '_dialog'].close();
            }

            var setData = function(data) {
                o.set('imagedata', data);

                if(o.options && o.options.onSuccess && typeof(o.options.onSuccess) === 'function') o.options.onSuccess(data);
            };

            var getData = function() {
                var data = o.get('imagedata');

                if(!data && options.data.hasOwnProperty(o.id)) data = options.data[o.id];

                return data;
            };
        }

        function popup_file_upload2() {
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
                        { text: '上传', onclick: function () { select_file_upload_save(self); } },
                        { text: '取消', onclick: function () { window[self.id + '_dialog'].hide(); } }
                    ]
                });
            }
        }

        return init();
    };

})(jQuery, this);
</script>

<script>
    var ligerFormFields = [
        { display: "id", name: "order_id", newline: true, type: "text", readonly: 'true' },
        { display: "订单号", name: "order_sn", newline: true, type: "text", readonly: 'true'},
        { display: "用户名", name: "user_id", newline: true, type: "text", readonly: 'true', afterContent: '（为空表示用户没有设置）'},
        { display: "物流公司", name: "logistics_id", newline: true, type: "text", readonly: 'true', afterContent: '（空表示订单未被抢单）'},
        { display: "路线", name: "wire_id", newline: true, type: "text", readonly: 'true'},
        { display: "报价", name: "offer_id", newline: true, type: "text", readonly: 'true', afterContent: '（0表示未报价）' },
        { display: "订单类型", name: "order_type", newline: true, type: "text", readonly: 'true'},
        { display: "订单状态", name: "order_status", newline: true, type: "text", readonly: 'true'},
        { display: "是否上货", name: "order_is_goods", newline: true, type: "text", readonly: 'true'},
        { display: "起始地址", name: "order_depart_details", newline: true, type: "text", readonly: 'true' },
        { display: "目的地址", name: "order_des_details", newline: true, type: "text", readonly: 'true' },
        { display: "创建时间", name: "order_time", newline: true, type: "text", readonly: 'true' },
        { display: "货物类型", name: "order_cargo_type", newline: true, type: "text", readonly: 'true' },
        { display: "货物重量", name: "order_weight", newline: true, type: "text", readonly: 'true' },
        { display: "货物体积", name: "order_bulk", newline: true, type: "text", readonly: 'true' },
        { display: "用户电话", name: "order_user_phone", newline: true, type: "text", readonly: 'true' },
        { display: "用户叮嘱", name: "order_remark", newline: true, type: "text", readonly: 'true' },
        { display: "是否已收货", name: "order_affirm", newline: true, type: "text", readonly: 'true' },
        { display: "自动收货", name: "order_overdue", newline: true, type: "text", readonly: 'true', afterContent: '距离自动收货时间（0表示没收货）' }
    ];

    $(function() {
        $.fn.<?php echo ($js_prefix); ?>form({
            title: '',
            type : 'dialog',
            fields: ligerFormFields,
            data: <?php echo djson_encode($row);?>
    });
    });
</script>
<?php if($is_ajax != 1): ?></body>
</html><?php endif; ?>