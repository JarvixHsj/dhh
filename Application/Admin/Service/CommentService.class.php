<?php
namespace Admin\Service;

/**
 * CommentService
 */
class CommentService extends CommonService {
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
    public function update($data) {
    $Comm = $this->getM();

    if (false === ($data = $Comm->create($data))) {
        return $this->errorResultReturn($Comm->getError());
    }

    if (false === $Comm->save($data)) {
        return $this->errorResultReturn('系统错误！');
    }

    return $this->resultReturn(true);
}


    /**
     * 是否存在管理员
     * @param  int     $id 管理员id
     * @return boolean
     */
    public function existComm($id) {
        return !is_null($this->getM()->find($id));
    }


    /**
     * 删除账户并且删除数据表
     * @param  int     $id 需要删除账户的id
     * @return boolean
     */
    public function delete($id) {
        $Dao = $this->getM();


        $model = $Dao->find($id);
        if(!$model)
            return $this->resultReturn(false);

        $delStatus = $Dao->delete($id);

        if (false === $delStatus) {
            return $this->resultReturn(false);
        }

        return $this->resultReturn(true);
    }

    protected function getModelName() {
        return 'Comment';
    }
}
