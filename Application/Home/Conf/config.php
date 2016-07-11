<?php
/**
 * Created by PhpStorm.
 * User: Jarvix
 * Date: 2016/2/1
 * Time: 14:33
 */

return array(
    //模板配置
//    'DEFAULT_THEME'         =>  'default',
    'TMPL_PARSE_STRING'		=> array(
//        '__BTimg__'    => __ROOT__ . '/Public/Bootstrap/images', //图片目录
//        '__BTcss__'    => __ROOT__ . '/Public/Bootstrap/css', //CSS目录
//        '__BTjs__'     => __ROOT__ . '/Public/Bootstrap/js', //JS目录

        '__JS__'		=> __ROOT__ . '/Public/' . MODULE_NAME . '/js',
        '__CSS__' 		=> __ROOT__ . '/Public/' . MODULE_NAME . '/css',
        '__IMG__' 		=> __ROOT__ . '/Public/' . MODULE_NAME . '/img',
        '__FONTS__'     => __ROOT__ . '/Public/' . MODULE_NAME . '/fonts',
    ),

    'DB_TYPE'   => 'mysqli', // 数据库类型
    'DB_HOST'   => 'jingkaigz.com', // 服务器地址
    'DB_NAME'   => 'jingkai_dev_donghuahang', // 数据库名
    'DB_USER'   => 'dev_donghuahang', // 用户名
    'DB_PWD'    => 'donghuahang@mysql888',  // 密码
    'DB_PORT'   => '3306', // 端口
    'DB_PREFIX' => 'dhh_', // 数据库表前缀

);
