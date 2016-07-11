<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: 麦当苗儿 <zuojiazi@vip.qq.com> <http://www.zjzit.cn>
// +----------------------------------------------------------------------

/**
 * 系统配文件
 * 所有系统级别的配置
 */
return array(
    /* 模块相关配置 */
    'AUTOLOAD_NAMESPACE' => array('Addons' => ONETHINK_ADDON_PATH), //扩展模块列表
    'DEFAULT_MODULE'     => 'Api',
    'MODULE_DENY_LIST'   => array('Common', 'User'),
    //'MODULE_ALLOW_LIST'  => array('Home','Admin'),

    /* 系统数据加密设置 */
    'DATA_AUTH_KEY' => 'z@;j+e-faDg3RdkE_G4iPb~VcH2|N,^1u$nyCwM/', //默认数据加密KEY

    /* 调试配置  默认开启*/
    'SHOW_PAGE_TRACE' => false,

    /* Cookie设置 */
    'COOKIE_EXPIRE'         =>  0,       // Cookie有效期
    'COOKIE_DOMAIN'         =>  '',      // Cookie有效域名
    'COOKIE_PATH'           =>  '/',     // Cookie路径
    'COOKIE_PREFIX'         =>  'KlscvV_', // Cookie前缀 避免冲突
    'COOKIE_SECURE'         =>  false,   // Cookie安全传输
    'COOKIE_HTTPONLY'       =>  '',      // Cookie httponly设置

    /* 用户相关设置 */
    'USER_MAX_CACHE'     => 1000, //最大缓存用户数
    'USER_ADMINISTRATOR' => 1, //管理员用户ID

    /* URL配置 */
    'URL_CASE_INSENSITIVE' => true, //默认false 表示URL区分大小写 true则表示不区分大小写
    'URL_MODEL'            => 3, //URL模式
    'VAR_URL_PARAMS'       => '', // PATHINFO URL参数变量
    'URL_PATHINFO_DEPR'    => '/', //PATHINFO URL分割符

    /* 全局过滤配置 */
    'DEFAULT_FILTER' => '', //全局过滤函数

    /* 数据库配置 */
//    'DB_TYPE'   => 'mysqli', // 数据库类型
//    'DB_HOST'   => 'jingkaigz.com', // 服务器地址
//    'DB_NAME'   => 'jingkai_dev_donghuahang', // 数据库名
//    'DB_USER'   => 'dev_donghuahang', // 用户名
//    'DB_PWD'    => 'donghuahang@mysql888',  // 密码
//    'DB_PORT'   => '3306', // 端口
//    'DB_PREFIX' => 'dhh_', // 数据库表前缀
//    'DB_FIELDS_CACHE'           =>  FALSE,

    'DB_TYPE'   => 'mysqli', // 数据库类型
    'DB_HOST'   => '127.0.0.1', // 服务器地址
    'DB_NAME'   => 'dhh', // 数据库名
    'DB_USER'   => 'root', // 用户名
    'DB_PWD'    => 'root',  // 密码
    'DB_PORT'   => '3306', // 端口
    'DB_PREFIX' => 'dhh_', // 数据库表前缀
    'DB_FIELDS_CACHE'           =>  FALSE,

    /* 短信配置 */
    'HY_ACCOUNT'			=>  'cf_jingkai_dhx',
    'HY_PASSWORD'			=>  'cf_jingkai_dhx',

    /* 极光推送AppKey（东华行用户端） */
    'CLIENT_APPKEY' =>  'f35071767b22a45934b22c8a',
    'CLIENT_MASTER' =>  '2a5ecc8d540bcb032a5bf4bd',

    /* 极光推送AppKey（东华行物流端） */
    'SERVER_APPKEY' =>  '70b29e3ddbb46e1e46b8a23e',
    'SERVER_MASTER' =>  '7d5453750ab8ea716b4c0f83',

    /* 极光环境设置  true是开发环境，false是测试环境 */
    'JPUSH_SET' => false,

    /* 文档模型配置 (文档模型核心配置，请勿更改) */
    'DOCUMENT_MODEL_TYPE' => array(2 => '主题', 1 => '目录', 3 => '段落'),

    /* 页面全局设置 */
    'PAGE_SIZE' => 10,
    'SPECIAL'   => '东华行物流有限公司',
);
