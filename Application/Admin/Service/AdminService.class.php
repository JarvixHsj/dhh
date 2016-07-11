<?php
namespace Admin\Service;

/**
 * AdminService
 */
class AdminService extends CommonService {
    /**
     * 添加管理员
     * @param  array $admin 管理员信息
     * @return array
     */
    public function add($admin) {
        $Admin = $this->getD();

        $Admin->startTrans();
        if (false === ($admin = $Admin->create($admin))) {
            return $this->errorResultReturn($Admin->getError());
        }

        unset($admin['cfm_password']);

        $as = $Admin->add($admin);

        $roleAdmin = array(
            'role_id' => $admin['role_id'],
            'user_id' => $Admin->getLastInsId()
        );
        $ras = M('RoleAdmin')->add($roleAdmin);

        if (false === $as || false === $ras) {
            $Admin->rollback();
            return $this->errorResultReturn('系统出错了！');
        }

        $Admin->commit();
        return $this->resultReturn(true);
    }

    /**
     * 更新管理员信息
     * @return
     */
    public function update($admin) {
    $Admin = $this->getD();

    if (false === ($admin = $Admin->create($admin))) {
        return $this->errorResultReturn($Admin->getError());
    }

    if (empty($admin['password'])) {
        unset($admin['password']);
    }

    unset($admin['cfm_password']);

    if (false === $Admin->save($admin)) {
        return $this->errorResultReturn('系统错误！');
    }

    M('RoleAdmin')->where("user_id={$admin['id']}")
        ->save(array('role_id' => $admin['role_id']));

    return $this->resultReturn(true);
}

    /**
     * 管理员登录认证
     * @param  array $admin 管理员信息
     * @return array
     */
    public function login($admin) {
        $Admin = $this->getM();

        // 用户是否存在
        if (!$this->existAccount($admin['name'])) {
            return $this->errorResultReturn('用户不存在！');
        }

        $account = $Admin->getByName($admin['name']);
        // 密码验证
        if ($account['password'] != $this->encrypt($admin['password'])) {
            return $this->errorResultReturn('密码不正确！');
        }

        // 是否启用
        if (!$this->isActive($admin['name'])) {
            return $this->errorResultReturn('账户已被禁用！');
        }

        $loginMarked = C('LOGIN_MARKED');
        $shell = $this->genShell($account['id'], $account['password']);

        // 生成登录session
        $_SESSION[$loginMarked] = $shell;

        // 生成登录cookie
        $shell .= '_' . time();
        setcookie($loginMarked, $shell, 0, '/');
        $_SESSION['current_account'] = $account;

        // 权限认证
        if (C('USER_AUTH_ON')) {
            $_SESSION[C('USER_AUTH_KEY')] = $account['id'];
            if ($account['is_super']) {
                // 超级管理员无需认证
                $_SESSION[C('ADMIN_AUTH_KEY')] = true;
            }

            // 缓存访问权限
            \Org\Util\Rbac::saveAccessList();
        }

        // 更新最后登录时间
        $Admin->where("id={$account['id']}")
              ->save(array('login_count' => array('exp','login_count+1'), 'last_login_at' => time(), 'last_login_ip' => get_client_ip()));

        return $this->resultReturn(true);
    }

    /**
     * 管理员登出
     * @return
     */
    public function logout() {
        $this->unsetLoginMarked();

        if (C('USER_AUTH_ON')) {
            unset($_SESSION[C('USER_AUTH_KEY')]);
            unset($_SESSION[C('ADMIN_AUTH_KEY')]);
        }

        session_destroy();
    }

    /**
     * 检查登录状态
     * @return array
     */
    public function checkLogin() {
        $loginMarked = C('LOGIN_MARKED');

        // 是否已登录
        if (!isset($_COOKIE[$loginMarked])) {
            return $this->errorResultReturn('尚未登录，请先进行登录！');
        }

        // 是否登录超时
        $cookie = explode('_', $_COOKIE[$loginMarked]);
        $timeout = C('LOGIN_TIMEOUT');
        if (time() > (end($cookie) + $timeout)) {
            $this->unsetLoginMarked();

            return $this->errorResultReturn('登录超时，请重新登录！');
        }

        // 是否帐号异常
        if ($cookie[0] != $_SESSION[$loginMarked]) {
            $this->unsetLoginMarked();

            return $this->errorResultReturn('账户异常，请重新登录！');
        }

        // 重新设置过期时间
        setcookie($loginMarked, $cookie[0] . '_' . time(), 0, '/');
        return $this->resultReturn(true);
    }

    /**
     * 加密数据
     * @param  string $data 需要加密的数据
     * @return string
     */
    public function encrypt($data) {
        return md5(C('AUTH_MASK') . md5($data));
    }

    /**
     * 生成登录shell
     * @param  int    $id       shell的id
     * @param  string $password shell的密码
     * @return string
     */
    private function genShell($id, $password) {
        return md5($password . C('AUTH_TOKEN')) . $id;
    }

    /**
     * 销毁登录标记
     * @return
     */
    private function unsetLoginMarked() {
        $loginMarked = C('LOGIN_MARKED');
        setcookie("{$loginMarked}", null, -3600, '/');
        unset($_SESSION[$loginMarked], $_COOKIE[$loginMarked]);

        return ;
    }

    /**
     * 是否存在帐号
     * @param  string  $name name
     * @return boolean
     */
    public function existAccount($name) {
        if ($this->getM()->where("name='{$name}'")->count() > 0) {
            return true;
        }

        return false;
    }

    /**
     * 是否存在管理员
     * @param  int     $id 管理员id
     * @return boolean
     */
    public function existAdmin($id) {
        return !is_null($this->getM()->getById($id));
    }

    /**
     * 是否存在初始化的管理员
     * @return boolean
     */
    public function existInitAdmin() {
        $where = array(
            'id' => 1,
            'is_super' => 1
        );

        return !is_null($this->getM()->where($where)->find());
    }

    /**
     * 是否已经发送重置密码邮件
     * @param  int     $id   管理员id
     * @param  string  $hash 邮件hash值
     * @return boolean
     */
    public function hasSendMail($id, $hash) {
        $where = array(
            'id' => $id,
            'mail_hash' => $hash
        );

        return !is_null($this->getM()->where($where)->find());
    }

    /**
     * 账户是否启用
     * @param  string  $name name
     * @return boolean
     */
    public function isActive($name) {
        $where = array(
            'name' => $name,
            'is_active' => 1
        );

        if ($this->getM()->where($where)->count() > 0) {
            return true;
        }

        return false;
    }

    /**
     * 删除账户并且删除数据表
     * @param  int     $id 需要删除账户的id
     * @return boolean
     */
    public function delete($ids) {
        $Dao = $this->getD();

        $Dao->startTrans();

        $ids = is_array($ids) ? $ids : array($ids);
        foreach ($ids as $id) {
            $model = $Dao->getById($id);
            if (empty($model)) {
                return $this->resultReturn(false);
            }

            // 删除账户
            $delStatus = $Dao->delete($id);
            // 删除角色关联
            $dropRole = M('RoleAdmin')->where("user_id={$id}")->delete();

            if (false === $dropRole || false === $delStatus) {
                $Modle->rollback();
                return $this->resultReturn(false);
            }
        }

        $Dao->commit();
        return $this->resultReturn(true);
    }

    protected function getModelName() {
        return 'Admin';
    }
}
