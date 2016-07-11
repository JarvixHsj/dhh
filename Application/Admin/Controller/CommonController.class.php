<?php
namespace Admin\Controller;

use Think\Controller;

/**
 * CommonController
 * 通用控制器
 */
class CommonController extends Controller {
    public $is_ajax_list = false;
    public $tpl_name = 'index';

    /**
    * 全局初始化
    * @return
    */
    public function _initialize() {
        // utf-8编码
        header('Content-Type: text/html; charset=utf-8');

        // 登录过滤
        $notLoginModules = explode(',', C('NOT_LOGIN_MODULES'));
        if (!in_array(CONTROLLER_NAME, $notLoginModules)) {
            $this->filterLogin();
        }

        // 权限过滤
        $this->filterAccess();

        /*// 菜单分配
        $noMenuModules = array('Public');
        if (!in_array(CONTROLLER_NAME, $noMenuModules)) {
            // 分配菜单
            $this->assignMenu();
            // 面包屑位置
            $this->assignBreadcrumbs();
        }*/

        $this->assignVar();
    }

    /**
     * 登录过滤
     * @return
     */
    protected function filterLogin() {
        $result = D('Admin', 'Service')->checkLogin();
        if (!$result['status']) {
            return $this->error($result['data']['error'], U('Public/index'));
        }
    }

    /**
     * 权限过滤
     * @return
     */
    protected function filterAccess() {
        if (!C('USER_AUTH_ON')) {
            return ;
        }

        if ('Index' === CONTROLLER_NAME && 'ajaxmenu' === ACTION_NAME) {
            // 获取主菜单管理
            return ;
        }

        if (\Org\Util\Rbac::AccessDecision(C('GROUP_AUTH_NAME'))) {
            return ;
        }

        if (!$_SESSION [C('USER_AUTH_KEY')]) {
            // 登录认证号不存在
            return $this->redirect(C('USER_AUTH_GATEWAY'));
        }

        if ('Index' === CONTROLLER_NAME && 'index' === ACTION_NAME) {
            // 首页无法进入，则登出帐号
            D('Admin', 'Service')->logout();
        }

        return $this->error('您没有权限执行该操作！');
    }

    /**
     * 是否已登录
     * @return boolean
     */
    protected function hasLogin() {
        $result = D('Admin', 'Service')->checkLogin();

        return $result['status'];
    }

    /**
     * 空操作
     * @return
     */
    public function _empty() {
        $this->error('您访问的页面不存在！');
    }

    /**
     * 得到数据列表
     * @param  string $modelName 模型名称
     * @param  array  $where     分页条件
     * @return array
     */
    protected function getAjaxList($modelName, $where, $order, $fields) {
        $filter = $this->handleFilter();
        $where = $where ? array_merge($filter, $where) : $filter;
        $service = D($modelName, 'Service');
        // 总数据行数
        $total = $service->getCount($where);

        $pager = $this->pagerRequest();
        // 得到分页数据
        $rows = $service->getPagination($where,
                                        $fields,
                                        $order ? $order : $pager->order_by,
                                        $pager->offset,
                                        $pager->page_size);
        $result['rows'] = $rows;
        $result['total'] = $total;
        return $result;
    }

    /**
     * 得到数据分页
     * @param  string $modelName 模型名称
     * @param  array  $where     分页条件
     * @return array
     */
    protected function getPagination($modelName, $where, $fields, $order) {
        $service = D($modelName, 'Service');
        // 总数据行数
        $total = $service->getCount($where);
        // 实例化分页
        $page = new \Org\Util\Page($total, C('PAGE_LIST_ROWS'));
        $result['show'] = $page->show();
        // 得到分页数据
        $rows = $service->getPagination($where,
                                        $fields,
                                        $order,
                                        $page->firstRow,
                                        $page->listRows);
        $result['rows'] = $rows;
        $result['total'] = $total;
        return $result;
    }

    /**
     * 分配菜单
     * @return
     */
    protected function assignMenu() {
        $menu = $this->getMenu(CONTROLLER_NAME);
        $this->assign('main_menu', $menu['main_menu']);
        $this->assign('sub_menu', $menu['sub_menu']);
    }

    /**
     * 分配面包屑
     * @return
     */
    protected function assignBreadcrumbs() {
        $breadcrumbs = $this->getBreadcrumbs();

        $this->assign('breadcrumbs', $breadcrumbs);
    }

    /**
     * 注入js变量
     * @return
     */
    protected function assignVar() {
        $this->assign('is_ajax', IS_AJAX);
        $this->assign('js_prefix', MODULE_NAME . '_' . CONTROLLER_NAME . '_');
        $this->assign('js_action_prefix', MODULE_NAME . '_' . CONTROLLER_NAME . '_' . ACTION_NAME . '_');
    }

    /**
     * 得到菜单
     * @return array
     */
    protected function getMenu($ctrlName) {
        $menu = C('MENU');
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
        }

        // 子菜单
        $subMenu = array();
        if (isset($menu[$ctrlName]['mapping'])) {
            $ctrlName = $menu[$ctrlName]['mapping'];
        }

        $actions = $access[$authGroup];
        // 主菜单如果为隐藏，则子菜单也不被显示
        foreach ($menu[$ctrlName]['sub_menu'] as $item) {
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

        unset($menu);
        return array(
            'main_menu' => $mainMenu,
            'sub_menu' => $subMenu
        );
    }

    /**
     * 得到面包屑
     * @return string
     */
    protected function getBreadcrumbs() {
        $menu = C('MENU');

        $menuItem = $menu[CONTROLLER_NAME];
        // 主菜单显示名称
        $main = $menuItem['name'];
        // 子菜单显示名称
        $sub = 'unkonwn';
        $route = CONTROLLER_NAME . '/' . ACTION_NAME;
        foreach ($menuItem['sub_menu'] as $item) {
            // 以键值匹配路由
            if (array_key_exists($route, $item['item'])) {
                $sub = $item['item'][$route];
            }
        }

        return $main . ' > ' . $sub;
}

    /**
     * { status : true, info: $info}
     * @param  string $info
     * @param  string $url
     * @return
     */
    protected function successReturn($info, $url) {
        $this->resultReturn(true, $info, $url);
    }

    /**
     * { status : false, info: $info}
     * @param  string $info
     * @param  string $url
     * @return
     */
    protected function errorReturn($info, $url) {
        $this->resultReturn(false, $info, $url);
    }

    /**
     * 返回带有status、info键值的json数据
     * @param  boolean $status
     * @param  string $info
     * @param  string $url
     * @return
     */
    protected function resultReturn($status, $info, $url) {
        $json['status'] = $status;
        $json['info'] = $info;
        $json['url'] = isset($url) ? $url : '';

        return $this->ajaxReturn($json);
    }

    /**
     * 返回带有rows、total键值的json数据
     * @param  array $rows
     * @param  int $total
     * @return
     */
    protected function gridReturn($rows, $total) {
        $json = array();
        $json['Rows'] = $rows;
        $json['Total'] = $total;

        return $this->ajaxReturn($json);
    }

    /**
     * 下载文件
     * @param  文件路径 $filePath
     * @param  文件名称 $fileName
     * @return
     */
    protected function download($filePath, $fileName) {
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; '
               . 'filename="' . $fileName . '"');
        header('Content-Length: ' . filesize($filePath));
        readfile($filePath);
    }

    /**
     * 信息列表页
     * @return
     */
    public function index() {
        $this->is_ajax_list = I('is_ajax') == 1;
        if ($this->is_ajax_list)
            return $this->ajaxList();

        $this->display($this->tpl_name);
    }

    /**
     * 解析json请求参数
     *
     * @param string $key 请求参数键名
     * @param string $default_value 请求参数默认值
     * @return string
     * @access public
     */
    protected function jsonRequest($key, $default_value = array())
    {
        $json_string = $_POST[$key];
        if($json_string)
        {
            $json_array = json_decode($json_string, true);
            foreach ($json_array as $key => $value)
            {
                if(trim($value) == '')
                    unset($json_array[$key]);
            }
        }
        else
            $json_array = $default_value;

        return $json_array;
    }

    /**
     * 处理分页请求参数
     *
     * @return class
     * @access public
     */
    protected function pagerRequest()
    {
        $page = max(intval(I("page")), 1);
        $page_size = I("pagesize")  ? intval(I("pagesize")): 10;
        $offset = ($page - 1) * $page_size;

        $sortname = strval(I("sortname"));
        $sortorder = strval(I("sortorder"));
        $order_by = $sortname && $sortorder ? $sortname . " " . $sortorder : "";

        $pager = new \stdClass();
        $pager->page = $page;
        $pager->page_size = $page_size;
        $pager->offset = $offset;
        $pager->order_by = $order_by;
        return $pager;
    }

    /**
     * 处理筛选条件
     *
     * @param arrty $config 配置参数
     * @return
     * @access public
     */
    protected function handleFilter($config = array(), $append = array())
    {
        $filter = $this->jsonRequest("filter");
        if(!$filter)
            $filter = array();

        return $filter;
    }

    protected function uploadFile($exts, $dir) {
        C('SHOW_PAGE_TRACE', '');
        $upload = new \Think\Upload();// 实例化上传类
        $upload->maxSize   = 3145728 ;// 设置附件上传大小
        $upload->exts      = $exts;// 设置附件上传类型
        $upload->rootPath  = './Public/'; // 设置附件上传根目录
        $upload->savePath  = 'Uploads/' . $dir . '/'; // 设置附件上传根目录
        // 上传文件
        $info = $upload->upload();
        if(!$info) {// 上传错误提示错误信息
            return json_encode(array(
                'status' => $upload->getError(),
            ));
        }else{// 上传成功
            $file = array_shift($info);
            return json_encode(array(
                'url'       => $file['savepath'].$file['savename'],
                'status'    => 'SUCCESS'
            ));
        }
    }



    /**
     * 极光推送（推送到所有用户端）161a3797c8072286576   120c83f76027552ea60
     */
    public function bbs_push_client_all($content,$title){
        import("Org.JPush.JPush");
        $client = new \JPush(C('CLIENT_APPKEY'), C('CLIENT_MASTER'));
        $result = $client->push()
            ->setPlatform('all')
            ->addAllAudience()
            ->setNotificationAlert($content,$title)
            ->send();
    }

    /**
     * 极光推送（推送到所有物流端）161a3797c8072286576   120c83f76027552ea60
     */
    public function bbs_push_server_all($content,$title){
        import("Org.JPush.JPush");
        $client = new \JPush(C('SERVER_APPKEY'), C('SERVER_MASTER'));
        $result = $client->push()
            ->setPlatform('all')
            ->addAllAudience()
            ->setNotificationAlert($content,$title)
            ->send();
    }
}
