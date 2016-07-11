<?php
$menuConfig = include('menu_config.php');
$systemConfig = include(APP_PATH . 'Common/Conf/system_config.php');
$securityConfig = include('security_config.php');
$mailConfig = include('mail_config.php');

$appConfig =  array(
    // 调试页
    // 'SHOW_PAGE_TRACE' =>true,

    // 默认模块和Action
    'MODULE_ALLOW_LIST' => array('Admin'),
    'DEFAULT_MODULE' => 'Admin',

    'URL_HTML_SUFFIX' => '',

    'SUPER_ADMIN_NAME' => 'admin',

    // 默认控制器
    'DEFAULT_CONTROLLER' => 'Public',

    // 分页列表数
    'PAGE_LIST_ROWS' => 10,

    // 开启布局
    'LAYOUT_ON' => false,
    'LAYOUT_NAME' => 'Common/layout',

    // error，success跳转页面
    'TMPL_ACTION_ERROR' => 'Common:dispatch_jump',
    'TMPL_ACTION_SUCCESS' => 'Common:dispatch_jump',

    // 菜单项配置
    'MENU' => $menuConfig,
    // 'BACKUP' => $backupConfig,
    'MAIL' => $mailConfig,

    // 系统保留表名
    'SYSTEM_TBL_NAME' => 'model,models,filed,fileds,admin,admins',
    // 系统保留菜单名
    'SYSTEM_MENU_NAME' => '首页,模型,数据',

    // 文件上传根目录
    'UPLOAD_ROOT' =>  'Uploads/',
    // 系统公用配置目录
    'COMMON_CONF_PATH' => WEB_ROOT . 'Application/Common/Conf/',

    /* 模板相关配置 */
    'TMPL_PARSE_STRING' => array(
        '__STATIC__' => __ROOT__ . '/Public/static',
        '__ADDONS__' => __ROOT__ . '/Public/' . MODULE_NAME . '/Addons',
        '__IMG__'    => __ROOT__ . '/Public/' . MODULE_NAME . '/images',
        '__CSS__'    => __ROOT__ . '/Public/' . MODULE_NAME . '/css',
        '__JS__'     => __ROOT__ . '/Public/' . MODULE_NAME . '/js',
        '__PUBLIC__'     => __ROOT__ . '/Public',
    )
);

return array_merge($appConfig, $systemConfig, $securityConfig, $mailConfig);