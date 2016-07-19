<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
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
    body,html{height:100%;}
    body{ padding:0px; margin:0;   overflow:hidden; background-color: #f2f2f2;}
    /* 头部 */
    #header{height:47px; position:relative; margin-bottom: 3px; background-color:#379be9; border-bottom:3px solid #1a88de; overflow:hidden;}

    /* logo */
    #logo{padding: 0 20px; float: left; font-size:20px; font-weight:bold; color:#fff; line-height:47px; text-align:center;}

    /* 退出系统 */
    .quit{position:absolute; text-align: right; right:25px; top:16px; color: #ffffff; font-weight:bold; }
    .quit a{ color:#fff; text-decoration:none;}

    /* 顶部菜单 */
    #topnav{float: left;}
    #topnav a{ display:inline-block; float:left; height:47px; line-height:47px; text-align:center; color:#fff; font-weight:bold; font-size:14px; text-decoration:none;}
    #topnav a span{padding:0 10px;/* border-left:1px solid #64b4f2; border-right:1px solid #1a88de;*/}
    #topnav a:hover, #topnav a.active{background-color:#2292e9; color:#fff;}
    #topnav a.selected{background-color:#1a88de; color:#fff;}
    /* End 顶部菜单 */

    /* 左侧边栏菜单 */
    #sidebar{overflow:auto;}
    /* End 左侧边栏菜单 */

    /* 页面主体 */
    #main{padding:0px; background-color: #ffffff;}

    /* 页脚 */
    #footer{height:30px; line-height: 30px; overflow: hidden; text-align: center; font-family: "verdana", "arial"; color:#999999;}

    /*左边导航样式*/
    .nlist li a{ display:block; line-height:22px; height:22px; padding-left:22px; background:url(/github/dhh/Public/Admin/images/nav_icon.gif) no-repeat 5px top; border:1px solid #fff; margin:3px 2px 0 2px; color:#333; text-decoration:none; }
    .nlist li a:hover{ border:1px solid #eae8e8; background:url(/github/dhh/Public/Admin/images/nav_icon.gif) no-repeat 5px -22px #f5f5f5; }
    .nlist li.title{ margin-top:5px; line-height:25px; font-weight:600; color:#090; padding-left:25px; background:url(/github/dhh/Public/Admin/images/nav_icon.gif) no-repeat 7px -50px;}
    .nlist li ul li{ margin-left:-13px; font-weight:normal; }
    </style>
</head>

<body>
<!-- header -->
<div id="header" class="cl">
    <!-- logo -->
    <div id="logo"><?php echo C('SITE_TITLE');?> - 后台管理系统</div>
    <!-- End logo -->

    <!-- topnav -->
    <div id="topnav" class="cl">
        <?php if(is_array($main_menu)): $i = 0; $__LIST__ = $main_menu;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$menu_item): $mod = ($i % 2 );++$i;?><a id="menu_<?php echo ($menu_item['controller']); ?>" href="javascript:" data-key="<?php echo ($menu_item['controller']); ?>" main_nav_id="<?php echo ($i); ?>" url="<?php echo U($menu_item['target']);?>"><span><?php echo ($menu_item['name']); ?></span></a><?php endforeach; endif; else: echo "" ;endif; ?>
    </div>
    <!-- End topnav -->

    <!-- quit -->
    <div class="quit">您好，<?php echo ($_SESSION['current_account']['name']); ?> ！ <a href="<?php echo U('Public/logout');?>">安全退出</a></div>
    <!-- End quit -->
</div>
<!-- End header -->

<div id="layout">
    <!-- sidebar -->
    <div position="left" id="sidebar" class="cl" title="管理菜单">
        <ul id="nav_tree_Index" class="nlist js-nlist">
            <?php if(is_array($sub_menu)): $i = 0; $__LIST__ = $sub_menu;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$menu_item): $mod = ($i % 2 );++$i;?><li><a class="l-link" href="javascript:add_tab('<?php echo ($key); ?>','<?php echo ($menu_item); ?>','<?php echo U($key);?>')"><?php echo ($menu_item); ?></a></li><?php endforeach; endif; else: echo "" ;endif; ?>
        </ul>
    </div>
    <!-- End sidebar -->

    <!-- main -->
    <div position="center" id="main" class="cl">
        <div tabid="navigator" title="后台主页">
            <iframe frameborder="0" name="navigator" id="navigator" src="<?php echo U('Index/webcome');?>" scrolling="auto"></iframe>
        </div>
    </div>
    <!-- End main -->
</div>
<!-- js -->
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
<script type="text/javascript">
var tree_data = {};
var refresh = null;
var main_layout = null;
var main_layout_height = null;
var main_tab = null;
var main_tree = null;
var tabItems = [];
var tablimits = 20;
var default_isLeftCollapse = true;
var visit_data = {};

$(function ()
{
    if(get_visit_data('default_isLeftCollapse') == false) default_isLeftCollapse = false;

    main_layout = $("#layout").ligerLayout({
        leftWidth: 180,
        height: '100%',
        heightDiff: -3,
        space: 3,
        //isLeftCollapse: default_isLeftCollapse,
        allowLeftCollapse: true,
        allowLeftResize: true,
        onEndResize: function(parm,e)
        {
            draw_collapse_left();
        }
    });

    main_layout_height = $(".l-layout-center").height();

    main_tab = $("#main").ligerTab({
        height: '100%',
        dblClickToClose: true,
        changeHeightOnResize: true,
        dragToMove: false,
        showSwitchInTab: false,
        showSwitch: false,
        onAfterAddTabItem: function (tabdata)
        {
            tabItems.push(tabdata);
            saveTabStatus();
        },
        onAfterRemoveTabItem: function (tabid)
        {
            for (var i = 0; i < tabItems.length; i++)
            {
                var o = tabItems[i];
                if (o.tabid == tabid)
                {
                    tabItems.splice(i, 1);
                    saveTabStatus();
                    break;
                }
            }
        },
        onAfterSelectTabItem: function(tabid)
        {
            if(refresh) set_visit_data('liger-home-tab-id-selected', tabid);
        }
    });

    $(window).resize(function(){
        layout_resize();
    });

    layout_resize();
    init_nav();
    init_pages();

    refresh = true;
});

/* 保存已打开的tab */
function saveTabStatus()
{
    set_visit_data('liger-home-tab', tabItems);
}

/* 页面初始化 */
function init_pages()
{
    var tabitems = get_visit_data('liger-home-tab');
    if (tabitems)
    {
        for (var i = 0; tabitems && tabitems[i];i++)
        {
            add_tab(tabitems[i].tabid, tabitems[i].text, tabitems[i].url);
        }
    }

    var tabidSelected = get_visit_data("liger-home-tab-id-selected");
    if(tabidSelected)
    {
        if(!main_tab) main_tab = $("#main").ligerGetTabManager();
        main_tab.selectTabItem(tabidSelected);
    }
}

/**
 * 初始化导航菜单
 */
function init_nav()
{
    //默认选中的主菜单
    var default_nav_id = get_visit_data('default_nav_id');
    var default_navigator_title = get_visit_data('default_navigator_title');
    var default_nav_menu = default_nav_id ? $("#menu_" + default_nav_id) : null;

    if( default_nav_id && default_nav_menu.length > 0 )
    {
        default_nav_menu.addClass("selected");
    }
    else
    {
        default_nav_id = null;
        $("#topnav a").eq(0).addClass("selected");
    }

    toggle_nav_tree(default_nav_id);

    //主菜单点击动作
    $("#topnav a").click(function(){
        var self = $(this);
        var key = self.data("key");

        if(!self.hasClass("selected")){
            $("#topnav a").removeClass("selected");
            self.addClass("selected");
            toggle_nav_tree(key);
        }
    });
}

/* 导航菜单显示切换 */
function toggle_nav_tree(selected_nav_id)
{
    if (!selected_nav_id) return;

    if (selected_nav_id == 'ModelMenu') {
        if ($('#accordion_ModelMenu').length > 0) {
            $("#sidebar .js-nlist").hide();
            $("#accordion_ModelMenu").show();
            return;
        }
    } else {
        var nav_tree = $("#nav_tree_" + selected_nav_id);
        if (nav_tree.length > 0) {
            $("#accordion_ModelMenu").hide();
            $("#sidebar .js-nlist").hide();
            nav_tree.show();
            return;
        }
    }

    var sidebar = $("#sidebar");
    commonAjaxPost("<?php echo U('Index/ajaxMenu');?>", {"controller_name": selected_nav_id}, function(json) {
        if (selected_nav_id == 'ModelMenu') {
            sidebar.find('.js-nlist').hide();
            var tree = [];
            tree.push('<div id="accordion_ModelMenu">');
            $.each(json.info, function(ii, menu) {
                tree.push('<div title="' + menu.name + '">');
                tree.push('<ul id="nav_tree_' + selected_nav_id + '" class="nlist">');
                $.each(menu.sub_menu, function(zz, item) {
                    tree.push('<li><a class="l-link" href="javascript:add_tab(\'' + item.key + '\',\'' + item.name + '\',\'' + item.url + '\')">' + item.name + '</a></li>');
                });
                tree.push('</ul>');
                tree.push('</div>');
            });
            tree.push('</div>');
            sidebar.append(tree.join(''));

            $("#accordion_ModelMenu").ligerAccordion(
            {
                height: main_layout_height - 26, speed: null
            });
        } else {
            var tree = [];
            tree.push('<ul id="nav_tree_' + selected_nav_id + '" class="nlist js-nlist">');
            $.each(json.info, function(ii, item) {
                tree.push('<li><a class="l-link" href="javascript:add_tab(\'' + item.key + '\',\'' + item.name + '\',\'' + item.url + '\')">' + item.name + '</a></li>');
            });
            tree.push('</ul>');
            $('#accordion_ModelMenu').hide();
            sidebar.find('.js-nlist').hide();
            sidebar.append(tree.join(''));
        }
    });

    selected_nav_id = save_selected_nav_id(selected_nav_id);

    /*if(!main_tree) main_tree = $("#nav_tree").ligerGetTreeManager();

    $("#nav_tree").hide();

    if(selected_nav_id && tree_data[selected_nav_id])
    {
        main_tree.clear();
        main_tree.setData(tree_data[selected_nav_id]);
        $("#nav_tree").show();
    }*/
}

/* 保存选中的主菜单 */
function save_selected_nav_id(selected_nav_id)
{
    set_visit_data('default_nav_id', selected_nav_id);

    return selected_nav_id;
}

/* 新增tab页 */
function add_tab(tabid, text, url)
{
    main_tab.addTabItem({
        tabid: tabid.replace('/', '_'),
        text: text,
        url: url
    });
}

function close_tab(tabid)
{
    if(tabid)
        main_tab.removeTabItem(tabid);
    else
        main_tab.removeSelectedTabItem();
}

/* 重置与布局相关的高度 */
function layout_resize()
{
    var height = $(".l-layout-left").height();
    var header_height = $(".l-layout-left .l-layout-header").height();
    $("#sidebar").height(height-header_height);
}

/* 跳转至导航图 */
function navigator_jump(url, title)
{
    var title_prefix = '', header = '';
    if(!url) return;
    if(!title) title = '';

    header = title_prefix;
    if(title) header = title+'-'+header;

    $("#navigator").attr("src", url);
    if(!main_tab) main_tab = $("#main").ligerGetTabManager();
    main_tab.setHeader('navigator', header);
    main_tab.selectTabItem('navigator');

    set_visit_data('default_navigator_url', url);
    set_visit_data('default_navigator_title', title);
}

function set_visit_data(name, value)
{
    if(!name) return false;

    visit_data[name] = value;

    set_cookie('visit_data', JSON2.stringify(visit_data), 0);
}

function get_visit_data(name)
{
    var tmp = get_cookie('visit_data');

    if(tmp)
    {
        visit_data = JSON2.parse(tmp);
    }

    if(visit_data && visit_data.hasOwnProperty(name)) return visit_data[name];
}
</script>
</body>
</html>