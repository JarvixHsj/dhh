<?php
namespace Admin\Service;

/**
 * UsersService
 */
class UsersService extends CommonService {


    /**
     * 判断用户是否存在
     * @param  int     $id
     * @return boolean
     */
    public function existUser($id) {
        $node = $this->getM()->find($id);
        return !empty($node);
    }

    /**
     * 设置用户公司状态
     * @param  int   $id     用户id
     * @param  int   $status 用户状态
     * @return mixed
     */
    public function setStatus($id, $status) {
        $result = $this->getM()
            ->where("user_id={$id}")
            ->save(array('user_status' => $status));

        return $result;
    }

    /**
     * 添加管理员
     * @param  array $admin 管理员信息
     * @return array
     */
    public function add($data) {
        $User = $this->getD();

        //生成token
        $data['token'] = D('Users','Service')->get_user_token();

        if (false === ($User->create($data))) {
            return $this->errorResultReturn($User->getError());
        }

        $as = $User->add($data);

        if (false === $as) {
            return $this->errorResultReturn('系统出错了！');
        }

        return $this->resultReturn(true);
    }

    /**
     * 更新管理员信息
     * @return
     */
    public function update($data) {
    $User = $this->getM();

    if (!$this->existUser($data['user_id'])) {
            return $this->errorReturn('无效的操作！');
    }

    if (false === $User->save($data)) {
        return $this->errorResultReturn('系统错误！');
    }

    return $this->resultReturn(true);
}

    /**
     * 生成用户加密唯一口令
     * @return string   返回32位加密
     */
    function get_user_token()
    {
        /* 选择一个随机的方案 */
        mt_srand((double) microtime() * 1000000);

        return md5(date('Ymd') . str_pad(mt_rand(1, 99999), 5, '0', STR_PAD_LEFT));
    }

    /**
     * 删除账户并且删除数据表
     * @param  int     $id 需要删除账户的id
     * @return boolean
     */
    public function delete($id) {
        $Dao = $this->getM();

        $Dao->startTrans(); //开启事务

        $model = $Dao->find($id);
        if (empty($model)) {
            return $this->resultReturn(false);
        }

        // 删除账户
        $delStatus = $Dao->delete($id);

        if (false === $delStatus) {
            return $this->resultReturn(false);
        }


        $map['user_id'] = $id;
        $messAS = M('message')->where($map)->delete(); //删除用户消息
        $orderAS = M('orders')->where($map)->setField('order_is_delete',1);      //删除假用户订单

        if($delStatus === false || $messAS === false || $orderAS === false){
            $Dao->rollback();
            return $this->errorResultReturn('系统出错了！');
        }
        $Dao->commit();

        return $this->resultReturn(true);
    }

    protected function getModelName() {
        return 'Users';
    }
}
