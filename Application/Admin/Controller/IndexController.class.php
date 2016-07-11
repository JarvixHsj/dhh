<?php
namespace Admin\Controller;

/**
 * IndexController
 * 系统信息管理
 */
class IndexController extends CommonController {
    /**
     * 后台主页
     * @return
     */
    public function index(){
        // 菜单分配
        $noMenuModules = array('Public');
        if (!in_array(CONTROLLER_NAME, $noMenuModules)) {
            // 分配菜单
            $this->assignMenu();
            // 面包屑位置
            //$this->assignBreadcrumbs();
        }

        $this->display();
    }

    /**
     * 网站，服务器基本信息
     * @return
     */
    public function webcome(){
        $gd = '不支持';
        if (function_exists('gd_info')) {
            $gd = gd_info();
            $gd = $gd['GD Version'];
        }

        $hostport = $_SERVER['SERVER_NAME']
                    ."($_SERVER[SERVER_ADDR]:$_SERVER[SERVER_PORT])";
        $mysql = function_exists('mysql_close') ? mysql_get_client_info()
                                                : '不支持';
        $info = array(
            'system' => get_system(),
            'hostport' => $hostport,
            'server' => $_SERVER['SERVER_SOFTWARE'],
            'php_env' => php_sapi_name(),
            'app_dir' => WEB_ROOT,
            'mysql' => $mysql,
            'gd' => $gd,
            'upload_size' => ini_get('upload_max_filesize'),
            'exec_time' => ini_get('max_execution_time') . '秒',
            'disk_free' => round((@disk_free_space(".")/(1024 * 1024)),2).'M',
            'server_time' => date("Y-n-j H:i:s"),
            'beijing_time' => gmdate("Y-n-j H:i:s", time() + 8 * 3600),
            'reg_gbl' => get_cfg_var("register_globals") == '1'? 'ON' : 'OFF',
            'quotes_gpc' => (1 === get_magic_quotes_gpc()) ? 'YES' : 'NO',
            'quotes_runtime' => (1===get_magic_quotes_runtime()) ?'YES' : 'NO',
            'fopen' => ini_get('allow_url_fopen') ? '支持' : '不支持'
        );

        $this->assign('info', $info);
        $this->display();
    }

    /**
     * 编辑个人密码
     * @return
     */
    public function editPassword() {
        $admin = $_SESSION['current_account'];

        $this->assign('row', $admin);
        $this->display('edit_password');
    }

    /**
     * 更新个人密码
     * @return
     */
    public function updatePassword() {
        if (!isset($_POST['form'])) {
            return $this->errorReturn('无效的操作！');
        }

        $admin = $_SESSION['current_account'];
        $admin['password'] = $_POST['form']['password'];
        $admin['cfm_password'] = $_POST['form']['cfm_password'];

        $result = D('Admin', 'Service')->update($admin);
        if (!$result['status']) {
            return $this->errorReturn($result['data']['error']);
        }

        return $this->successReturn('修改密码成功！');
    }

    /**
     * 编辑站点信息
     * @return
     */
    public function siteEdit() {
        $title = C('SITE_TITLE');
        $keyword = C('SITE_KEYWORD');
        $description = C('SITE_DESCRIPTION');

        $this->assign('title', $title);
        $this->assign('keyword', $keyword);
        $this->assign('description', $description);
        $this->display('site_edit');
    }

    /**
     * 更新站点信息
     * @return
     */
    public function siteUpdate() {
        if (!isset($_POST['form'])) {
            return $this->errorReturn('无效的操作！');
        }

        $confName = 'system_config';

        $conf = fast_cache($confName, '', C('COMMON_CONF_PATH'));
        $conf['SITE_TITLE'] = $_POST['form']['title'];
        $conf['SITE_KEYWORD'] = $_POST['form']['keyword'];
        $conf['SITE_DESCRIPTION'] = $_POST['form']['description'];

        if (false === fast_cache($confName, $conf, C('COMMON_CONF_PATH'))) {
            return $this->errorReturn('站点信息更新失败！');
        }

        return $this->successReturn('站点信息更新成功！');
    }

    public function ajaxMenu() {
        if (!isset($_POST['controller_name'])) {
            return $this->errorReturn('无效的操作！');
        }

        $sub_menu = array();

        if ($_POST['controller_name'] == 'ModelMenu') {
            $sub_menu = $this->getModelMenu();
        } else {
            // 菜单分配
            $noMenuModules = array('Public');
            if (!in_array($_POST['controller_name'], $noMenuModules)) {
                // 分配菜单
                $menu = $this->getMenu($_POST['controller_name']);
                foreach ($menu['sub_menu'] as $key => $value) {
                    $sub_menu[] = array(
                        'url' => U($key),
                        'key' => $key,
                        'name' => $value
                    );
                }
            }
        }

        $this->resultReturn(1, $sub_menu);
    }

    private function getModelMenu()
    {
        $all_menu = C('MENU');
        $menu = $all_menu['ModelMenu']["sub_menu"];
        // 主菜单
        $mainMenu = array();
        // 已被映射过的键值
        $mapped = array();

        // 访问权限
        $access = $_SESSION['_ACCESS_LIST'];
        if (empty($access)) {
            $authId = $_SESSION[C('USER_AUTH_KEY')];
            $authGroup = \Org\Util\Rbac::getAccessList($authId);
        }
        $authGroup = strtoupper(C('GROUP_AUTH_NAME'));
        // 处理主菜单
        foreach ($menu as $key => $menuItem) {
            // 不显示无权限访问的主菜单
            if (!$_SESSION[C('ADMIN_AUTH_KEY')]
                && $key != 'ModelMenu'
                && !array_key_exists(strtoupper($key), $access[$authGroup])) {

                continue ;
            }

            $controller = $key;

            // 主菜单是否存在映射
            if (isset($menuItem['mapping'])) {
                // 映射名
                $mapping = $menuItem['mapping'];
                // 新的菜单键值
                if (!empty($mapped[$mapping])) {
                    $key = "{$mapped[$mapping]}-{$key}";
                    $mapping = $mapped[$mapping];
                } else {
                    $key = "{$mapping}-{$key}";
                }

                // 需要映射的键值已存在，则删除
                if (isset($mainMenu[$mapping])) {
                    $mainMenu[$key]['controller'] = $controller;
                    $mainMenu[$key]['name'] = $mainMenu[$mapping]['name'];
                    $mainMenu[$key]['target'] = $mainMenu[$mapping]['target'];
                    unset($mainMenu[$mapping]);
                    $mapped[$mapping] = $key;
                }

                continue ;
            }

            $mainMenu[$key]['controller'] = $controller;
            $mainMenu[$key]['name'] = $menuItem['name'];
            $mainMenu[$key]['target'] = $menuItem['target'];

            //如果默认的target用户无权访问，则显示sub_menu中的用户有权访问的第一个页面
            $actions = $access[$authGroup][strtoupper($key)];
            $action = explode('/', strtoupper($mainMenu[$key]['target']));
            while (!$_SESSION[C('ADMIN_AUTH_KEY')] && !array_key_exists($action[1], $actions)) {
                $nextSubMenu = next($menu[$key]['sub_menu']);
                if (empty($nextSubMenu)) break;
                $mainMenu[$key]['controller'] = $controller;
                $mainMenu[$key]['target'] = key(current($nextSubMenu));
                $action = explode('/', strtoupper($mainMenu[$key]['target']));
            }

            $actions = $access[$authGroup];

            // 子菜单
            $subMenu = array();

            // 主菜单如果为隐藏，则子菜单也不被显示
            foreach ($menuItem['sub_menu'] as $item) {
                // 子菜单是否需要显示
                if (isset($item['hidden']) && true === $item['hidden']) {
                    continue ;
                }

                $route = array_shift(array_keys($item['item']));
                $action = explode('/', strtoupper($route));
                // 不显示无权限访问的子菜单
                if (!$_SESSION[C('ADMIN_AUTH_KEY')]
                    && (!array_key_exists($action[0], $actions)
                        || !array_key_exists($action[1], $actions[$action[0]]))) {
                    continue ;
                }

                // 子菜单是否有配置
                if (!isset($item['item']) || empty($item['item'])) {
                    continue ;
                }

                $routes = array_keys($item['item']);
                $itemNames = array_values($item['item']);
                $subMenu[$routes[0]] = $itemNames[0];
            }

            $mainMenu[$key]['sub_menu'] = $subMenu;
        }

        //var_dump($mainMenu);exit;

        $sub_menu = array();

        foreach ($mainMenu as $key => $value) {
            $_sub_menu = array(
                'url' => U($value['target']),
                'key' => $key,
                'name' => $value['name']
            );

            foreach ($value['sub_menu'] as $sub_item_key => $sub_item_value) {
                $_sub_menu['sub_menu'][] = array(
                    'url' => U($sub_item_key),
                    'key' => str_replace('/', '_', $sub_item_key),
                    'name' => $sub_item_value
                );
            }
            $sub_menu[] = $_sub_menu;
        }

        unset($menu);
        return $sub_menu;
    }

    public function ajaxMenu2() {
        if (!isset($_POST['controller_name'])) {
            return $this->errorReturn('无效的操作！');
        }

        $sub_menu = array();

        if ($_POST['controller_name'] == 'ModelMenu') {
            $modelLogic = D('Model', 'Logic');
//            $menu = $modelLogic->getMenu();
            $menu = $this->getMenu($_POST['controller_name']);
            var_dump($menu);die;

            foreach ($menu as $key => $value) {
                $_sub_menu = array(
                        'url' => U($value['target']),
                        'key' => $key,
                        'name' => $value['name']
                    );

                foreach ($value['sub_menu'] as $sub_item) {
                    foreach ($sub_item['item'] as $sub_item_key => $sub_item_value) {
                        $_sub_menu['sub_menu'][] = array(
                            'url' => U($sub_item_key),
                            'key' => str_replace('/', '_', $sub_item_key),
                            'name' => $sub_item_value
                        );
                    }
                }
                $sub_menu[] = $_sub_menu;
            }
        } else {
            // 菜单分配
            $noMenuModules = array('Public');
            if (!in_array($_POST['controller_name'], $noMenuModules)) {
                // 分配菜单
                $menu = $this->getMenu($_POST['controller_name']);
                foreach ($menu['sub_menu'] as $key => $value) {
                    $sub_menu[] = array(
                            'url' => U($key),
                            'key' => $key,
                            'name' => $value
                        );
                }
            }
        }

        $this->resultReturn(1, $sub_menu);
    }
}
