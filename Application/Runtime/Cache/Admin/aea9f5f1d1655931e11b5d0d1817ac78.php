<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="renderer" content="webkit|ie-comp|ie-stand">
    <title><?php echo C('SITE_TITLE');?> - 后台管理系统</title>
    <!-- css -->
    <link rel="stylesheet" type="text/css" href="/myworks/dhh/www/Public/Admin/ligerUI/skins/Aqua/css/ligerui-all.css" />
<link rel="stylesheet" type="text/css" href="/myworks/dhh/www/Public/Admin/ligerUI/skins/ligerui-icons.css" />
<link rel="stylesheet" type="text/css" href="/myworks/dhh/www/Public/Admin/ligerUI/skins/Gray/css/all.css" />
<link rel="stylesheet" type="text/css" href="/myworks/dhh/www/Public/Admin/css/common.css" media="all">
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
<!-- <script type="text/javascript" src="<?php echo U('Index/select_data');?>"></script>  -->

<script type="text/javascript">
var proDatas = [];
var proDataRow = {};
proDataRow = {};
<?php if(is_array($pro)): foreach($pro as $key=>$vo): ?>proDataRow = {};
proDataRow.id = <?php echo ($vo["region_id"]); ?>;
proDataRow.text = '<?php echo ($vo["region_name"]); ?>';
proDatas.push(proDataRow);<?php endforeach; endif; ?>

var cityDatas = [];
var cityDataRow = {};
cityDataRow = {};
<?php if(is_array($city)): foreach($city as $key=>$vo): ?>cityDataRow = {};
cityDataRow.id = <?php echo ($vo["region_id"]); ?>;
cityDataRow.text = '<?php echo ($vo["region_name"]); ?>';
cityDataRow.pid = '<?php echo ($vo["parent_id"]); ?>';
cityDatas.push(cityDataRow);<?php endforeach; endif; ?>



    $(function ()
    {
        var proData = proDatas;
        var cityData = cityDatas;

        $("#start_city").ligerComboBox({ data: null, isMultiSelect: false, isShowCheckBox: false });

        $("#start_pro").ligerComboBox({
            data: proData, isMultiSelect: false,
            onSelected: function (newvalue)
            {
                var newData = new Array();
                for (i = 0; i < cityData.length; i++)
                {
                    if (cityData[i].pid == newvalue)
                    {
                        newData.push(cityData[i]);
                    }
                }
                liger.get("start_city").setData(newData);
            }
        });

        $("#end_city").ligerComboBox({ data: null, isMultiSelect: false, isShowCheckBox: false });

        $("#end_pro").ligerComboBox({
            data: proData, isMultiSelect: false,
            onSelected: function (newvalue)
            {
                var newData = new Array();
                for (i = 0; i < cityData.length; i++)
                {
                    if (cityData[i].pid == newvalue)
                    {
                        newData.push(cityData[i]);
                    }
                }
                liger.get("end_city").setData(newData);
            }
        });

    });


</script>
<body style="padding:10px">
<form action="<?php echo U(CONTROLLER_NAME.'/create');?>" method="post">
    <br />
    物流公司：
    <!--<select name="logistics_id" >-->
        <!--<?php if(is_array($logi)): $i = 0; $__LIST__ = $logi;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?>-->
        <!--<option value="<?php echo ($vo["logistics_id"]); ?>"><?php echo ($vo["logistics_name"]); ?></option>-->
        <!--<?php endforeach; endif; else: echo "" ;endif; ?>-->
    <!--</select>-->
    <input type="text" id="logi_name" value="">
    <span id="gou" style="widthL20xp;height:20px;"></span>
    <input type="hidden" name="logistics_id" id="logiid">

    <br/>
    <br/>
    <table>
        <tr>
            <td>起&nbsp;始&nbsp;地：&nbsp;</td>
            <td>省：<input type="text" name="strt_pro" id="start_pro" /></td>
            <td>市：<input type="text" name="strt_city" id="start_city" /></td>
        </tr>
        <tr>
            <td>目&nbsp;的&nbsp;地：&nbsp;</td>
            <td>省：<input type="text" name="end_pro" id="end_pro" /></td>
            <td>市：<input type="text" name="end_city" id="end_city" /></td>
        </tr>
    </table>
    <br/>
    轻货价格：<input type="text" placeholder="例如：200-300元" name="gently">
    <br/>
    <br/>
    重货价格：<input type="text" placeholder="例如：200-300元" name="price">
    <br/>
    <br/>
    花费时间：<input type="text" name="predict_time" placeholder="例如：1天  或者  2-3天">
    <br/>
    <br/>

<input type="submit" name="提交">
</form>
<div style="display:none;">
</div>
</body>
<script>
    $('#logi_name').focus(function(){
        $('#gou').empty();
    })

    $('#logi_name').blur(function(){
        var name = $(this).val();
        if(name == ''){
            alert('不能为空');
            return false;
        }
        $.ajax({
            type: "POST",
            url: "<?php echo U('WireRelation/searchName');?>",
            data:'name='+name,
            dataType: "json",
            success:function(msg){
                if(msg.status == 0){
                    alert(msg.message);
                }else{
                    $('#gou').append('<img src="/myworks/dhh/www/Public/Admin/images/ico_gou.png">');
                    $('#logiid').val(msg.data);
                }

            }
        });
    })
</script>

<div class="main_content">
    <div id="<?php echo ($js_prefix); ?>tablelist"></div>
</div>
<script type="text/javascript">
;(function ($, window) {

    $.fn.<?php echo ($js_prefix); ?>list = function(options) {
        var $this = this;

        var defaults = {
            id: '<?php echo ($js_prefix); ?>',
            title: null,
            url_load: '<?php echo U(CONTROLLER_NAME.'/index');?>',
            url_add: '<?php echo U(CONTROLLER_NAME.'/add');?>',
            url_edit: '<?php echo U(CONTROLLER_NAME.'/edit');?>',
            url_del: '<?php echo U(CONTROLLER_NAME.'/delete');?>',
            url_view: '<?php echo U(CONTROLLER_NAME.'/view');?>',
            url_addpath: '<?php echo U(CONTROLLER_NAME.'/addpath');?>',
            url_parms: {},
            use_pager: true,
            pagesize: 30,
            tree: null,
            rownumbers: true,
            checkbox: true,
            toolbar: true,
            toolbar_items: [],
            toolbar_items_add: [],
            toolbar_has_add: true,
            toolbar_has_del: true,
            toolbar_has_edit:true,
            toolbar_has_view: false,
            toolbar_has_audit: false,
            toolbar_has_addpath: false,
            grid_columns: [],
            grid_onLoaded: null,
            identity_key: 'id',
            identity_val: 0,
            form_type: 'page', //dialog
            form_dialog_width: 500,
            form_search_fields: null,
            form_search_dialog_width: 350,
            form_search_data_default: null,
            form_search_data: null  //保存查询表单的值
        };

        options = $.extend({}, defaults, options || {}, {
            grid: null,
            dialog: null
        });

        var $module_id = options.id;
        var $module_identity = "id";
        var $tablelist = $("#" + $module_id + "tablelist");

        var xmlhttprequest_add = null;
        var xmlhttprequest_edit = null;
        var xmlhttprequest_view = null;
        var xmlhttprequest_addpath = null;

        function init() {
            init_grid();
            return $this;
        }

        function get_toolbar_items() {
            if (!options.toolbar)
                return null;

            if (options.toolbar_items.length > 0)
                return { items: options.toolbar_items };

            var default_toolbar_items = []

            if (options.form_search_fields) {
                default_toolbar_items.push({ text: '搜索', click: search, icon: 'search' });
                default_toolbar_items.push({ line: true });
            }

            default_toolbar_items.push({ text: '刷新', click: refresh, icon: 'refresh' });

            if (options.toolbar_has_add) {
                default_toolbar_items.push({ line: true });
                default_toolbar_items.push({ text: '新增', click: add, icon: 'add' });
            }

            if(options.toolbar_has_edit){
                default_toolbar_items.push({ line: true });
                default_toolbar_items.push({ text: '修改', click: edit, icon: 'modify' });
            }
            if (options.toolbar_has_del) {
                default_toolbar_items.push({ line: true });
                default_toolbar_items.push({ text: '删除', click: del, icon: 'delete' });
            }

            if (options.toolbar_has_view) {
                default_toolbar_items.push({ line: true });
                default_toolbar_items.push({ text: '查看', click: view, icon: 'ok' });
            }

            if (options.toolbar_has_audit) {
                default_toolbar_items.push({ line: true });
                default_toolbar_items.push({ text: '审核', click: audit, icon: 'right' });
            }

            if (options.toolbar_has_addpath) {
                default_toolbar_items.push({ line: true });
                default_toolbar_items.push({ text: '添加路线', click: addpath, icon: 'up' });
            }

            if (options.toolbar_items_add.length > 0)
                default_toolbar_items = $.merge(default_toolbar_items, options.toolbar_items_add);

            return { items: default_toolbar_items };
        }

        function init_grid() {

            window.parent.<?php echo ($js_prefix); ?>grid = options.grid = $tablelist.ligerGrid({
                //title: options.title,
                height: '100%',
                heightDiff: -6,
                columns: options.grid_columns,
                whenRClickToSelect: true,
                allowUnSelectRow: true,
                url: options.url_load,
                parms: $.extend({'is_ajax': 1}, options.url_parms),
                usePager: options.use_pager,
                pageSize: options.pagesize,
                rownumbers: options.rownumbers,
                checkbox: options.checkbox,
                tree: options.tree,
                toolbar: get_toolbar_items()
            });
        }

        //初化搜索表单
        function init_search_form() {
            if( window.parent.$("#<?php echo ($js_prefix); ?>search_dialog_cont").length > 0 ) return false;

            var wrap = '<div id="<?php echo ($js_prefix); ?>search_dialog_cont">';
            wrap += '<div class="main_content">';
            wrap += '<form id="<?php echo ($js_prefix); ?>form_search"></form>';
            wrap += '</div>';
            wrap += '</div>';

            if( window.parent.$("#<?php echo ($js_prefix); ?>search_dialog_cont").length > 0 ) window.parent.$("#<?php echo ($js_prefix); ?>search_dialog_cont").remove();

            window.parent.$("body").append(wrap);

            options.search_form = window.parent.$("#<?php echo ($js_prefix); ?>form_search").ligerForm({
                inputWidth: 180,
                labelWidth: 90,
                space: 40,
                labelAlign: 'right',
                validate : true,
                readonly: false,
                labelCss: 'l-table-edit-th',
                fields: options.form_search_fields
            });

            if (options.form_search_data) {
                options.search_form.setData(options.form_search_data);
            }

            if(!options.form_search_data_default) {
                options.form_search_data_default = options.search_form.getData();
            }
        }

        //查询
        function search() {
            if (!options.form_search_fields)
                return false;

            init_search_form();
            options.dialog = window.parent.$.ligerDialog.open({
                title: '搜索' + options.title,
                id: '<?php echo ($js_prefix); ?>search_dialog',
                width: options.form_search_dialog_width,
                height: null,
                target: window.parent.$("#<?php echo ($js_prefix); ?>search_dialog_cont"),
                buttons: [
                    {text: '提交', onclick: function (item, dialog) { doSearch(item, dialog); }},
                    {text: '重置', onclick: function (item, dialog) { doReset(item, dialog); }},
                    {text: '取消', onclick: function (item, dialog) { dialog.close(); }}
                ]
            });

            var doSearch = function(item, dialog) {
                if(!options.search_form.valid()) {
                    options.search_form.showInvalid();
                    return false;
                }

                /*$.each(window.parent.$("#<?php echo ($js_prefix); ?>form_search").serializeArray(), function(i,item){
                    if(!item.name || item.name == 'undefined') return;
                    data[item.name] = item.value;
                });*/

                options.form_search_data = options.search_form.getData();

                var parms = window.parent.$("#<?php echo ($js_prefix); ?>form_search").serializeArray();
                parms.push({'name': 'filter', 'value': JSON.stringify(options.form_search_data)});
                //options.grid.setParm('filter', JSON.stringify(options.form_search_data));
                options.grid.setOptions({parms: parms});
                options.grid.reload();
                dialog.close();
            };

            var doReset = function(item, dialog) {
                options.search_form.setData(options.form_search_data_default);
            }
        }

        function add() {
            if (options.form_type == 'dialog') {
                add_dialog();
            } else if (options.form_type == 'page') {
                add_page();
            }
        }

        function edit(id) {
            if (options.form_type == 'dialog') {
                edit_dialog(id);
            } else if (options.form_type == 'page') {
                edit_page(id);
            }
        }

        function view(id) {
            if (options.form_type == 'dialog') {
                view_dialog(id);
            } else if (options.form_type == 'page') {
                view_page(id);
            }
        }

        function addpath() {
            if (options.form_type == 'dialog') {
                add_dialog();
            } else if (options.form_type == 'page') {
                add_page();
            }
        }


        function add_page() {
            window.parent.add_tab("<?php echo ($js_prefix); ?>add", "新增" + options.title, options.url_add);
        }

        function edit_page(id) {
            if(id == null || typeof(id) != 'number') {
                if(options.grid.getSelectedRow() == null) {
                    window.parent.$.ligerDialog.warn('请选择行');
                    return;
                }
                id = options.grid.getSelectedRow()[options.identity_key];
            }

            var url = options.url_edit;
            if (url.indexOf("?") != -1)
                url += "&";
            else
                url += "?";
            url += "id=" + id;
            window.parent.add_tab("<?php echo ($js_prefix); ?>edit", "修改" + options.title, url);
        }

        function add_dialog() {
            if(xmlhttprequest_add) xmlhttprequest_add.abort();
            $.ajaxSetup({ cache: false });
            xmlhttprequest_add = $.get(options.url_add, function(data) {
                if(!data){
                    window.parent.$.ligerDialog.warn('请求失败');
                    return;
                } else if(data.hasOwnProperty('status') && !data.status) {
                    window.parent.$.ligerDialog.warn(data.info);
                    return;
                }

                window.parent.<?php echo ($js_prefix); ?>dialog = options.dialog = window.parent.$.ligerDialog.open({
                    width: options.form_dialog_width,
                    height: null,
                    top: 100,
                    content: data,
                    show:false,
                    isResize: true,
                    isHidden: false,
                    load: false,
                    id: '<?php echo ($js_prefix); ?>add_dialog',
                    title: '新增' + options.title
                });
            });
        }

        function edit_dialog(id) {
            if(id == null || typeof(id) != 'number') {
                if(options.grid.getSelectedRow() == null) {
                    window.parent.$.ligerDialog.warn('请选择行');
                    return;
                }
                var id = options.grid.getSelectedRow()[options.identity_key];
            }

            if(xmlhttprequest_edit) xmlhttprequest_edit.abort();
            $.ajaxSetup({ cache: false });
            xmlhttprequest_edit = $.get(options.url_edit, {id: id}, function(data) {
                if(!data) {
                    window.parent.$.ligerDialog.warn('请求失败');
                    return;
                } else if(data.hasOwnProperty('status') && !data.status) {
                    window.parent.$.ligerDialog.warn(data.info);
                    return;
                }

                window.parent.<?php echo ($js_prefix); ?>dialog = options.dialog = window.parent.$.ligerDialog.open({
                    width: options.form_dialog_width,
                    height: null,
                    top: 100,
                    content: data,
                    show:false,
                    isResize: true,
                    isHidden: false,
                    load: false,
                    id: '<?php echo ($js_prefix); ?>edit_dialog',
                    title: '修改' + options.title
                });
            });
        }

        function view_dialog(id) {
            if(id == null || typeof(id) != 'number') {
                if(options.grid.getSelectedRow() == null) {
                    window.parent.$.ligerDialog.warn('请选择行');
                    return;
                }
                var id = options.grid.getSelectedRow()[options.identity_key];
            }

            if(xmlhttprequest_view) xmlhttprequest_view.abort();
            $.ajaxSetup({ cache: false });
            xmlhttprequest_view = $.get(options.url_view, {id: id}, function(data) {
                console.log(data);
                if(!data){
                    window.parent.$.ligerDialog.warn('请求失败');
                    return;
                } else if(data.hasOwnProperty('status') && !data.status) {
                    window.parent.$.ligerDialog.warn(data.info);
                    return;
                }

                window.parent.<?php echo ($js_prefix); ?>dialog = options.dialog = window.parent.$.ligerDialog.open({
                    width: options.form_dialog_width,
                    height: null,
                    top: 100,
                    content: data,
                    show: false,
                    isResize: true,
                    isHidden: false,
                    load: false,
                    id: '<?php echo ($js_prefix); ?>view_dialog',
                    title: '查看' + options.title
                });
            });
        }

        function view_page(id) {
            if(id == null || typeof(id) != 'number') {
                if(options.grid.getSelectedRow() == null) {
                    window.parent.$.ligerDialog.warn('请选择行');
                    return;
                }
                id = options.grid.getSelectedRow()[options.identity_key];
            }

            var url = options.url_view;
            if (url.indexOf("?") != -1)
                url += "&";
            else
                url += "?";
            url += "id=" + id;
            window.parent.add_tab("<?php echo ($js_prefix); ?>view", "查看" + options.title, url);
        }

        function del() {
            if(options.grid.getSelectedRow() == null) {
                window.parent.$.ligerDialog.warn('请选择行');
                return;
            }

            window.parent.$.ligerDialog.confirm('确定删除?', function (yes) {
                if(!yes) return ;

                var ids = [];
                var rows = options.grid.getSelectedRows();
                $(rows).each(function() {
                    ids.push(this[options.identity_key]);
                });
                $.post(options.url_del, { id: ids },
                    function(json) {
                        if(!json.status) {
                            window.parent.$.ligerDialog.warn(json.info);
                            return;
                        }

                        $(rows).each(function() {
                            options.grid.deleteRow(this);
                        });
                    }
                );
            });
        }

        function refresh() {
            options.grid.reload();
        }


        function addpath_page() {
            if(id == null || typeof(id) != 'number') {
                if(options.grid.getSelectedRow() == null) {
                    window.parent.$.ligerDialog.warn('请选择行');
                    return;
                }
                id = options.grid.getSelectedRow()[options.identity_key];
            }

            var url = options.url_edit;
            if (url.indexOf("?") != -1)
                url += "&";
            else
                url += "?";
            url += "id=" + id;
            window.parent.add_tab("<?php echo ($js_prefix); ?>addpath", "添加" + options.title, options.url_addpath);
        }

        function addpath_dialog() {
            if(id == null || typeof(id) != 'number') {
                if(options.grid.getSelectedRow() == null) {
                    window.parent.$.ligerDialog.warn('请选择行');
                    return;
                }
                var id = options.grid.getSelectedRow()[options.identity_key];
            }

            if(xmlhttprequest_addpath) xmlhttprequest_addpath.abort();
            $.ajaxSetup({ cache: false });
            xmlhttprequest_addpath = $.get(options.url_addpath, function(data) {
                if(!data){
                    window.parent.$.ligerDialog.warn('请求失败');
                    return;
                } else if(data.hasOwnProperty('status') && !data.status) {
                    window.parent.$.ligerDialog.warn(data.info);
                    return;
                }

                window.parent.<?php echo ($js_prefix); ?>dialog = options.dialog = window.parent.$.ligerDialog.open({
                    width: options.form_dialog_width,
                    height: null,
                    top: 100,
                    content: data,
                    show:false,
                    isResize: true,
                    isHidden: false,
                    load: false,
                    id: '<?php echo ($js_prefix); ?>addpath_dialog',
                    title: '添加' + options.title
                });
            });
        }

        return init();
    };

})(jQuery, this);
</script>
</body>
</html>