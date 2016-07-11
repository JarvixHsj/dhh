<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://topthink.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// |
// +----------------------------------------------------------------------

// 应用入口文件

// 检测PHP环境
if(version_compare(PHP_VERSION,'5.3.0','<'))  die('require PHP > 5.3.0 !');

$_GET['m'] = 'Admin';

// 网站文件入口位置
define('WEB_ROOT', dirname(__FILE__) . '/');

// 绑定访问Admin模块
//define('BIND_MODULE','Admin');

// 定义应用目录
//define('APP_NAME', 'Admin');
define('APP_PATH', './Application/');
define('THINK_PATH', realpath('./ThinkPHP') . '/');

// 运行缓存目录
//define('RUNTIME_PATH', WEB_ROOT . 'Cache/Runtime/' . APP_NAME . '/');

// 开启调试
define('APP_DEBUG', true);

// 引入ThinkPHP入口文件
require THINK_PATH . 'ThinkPHP.php';
