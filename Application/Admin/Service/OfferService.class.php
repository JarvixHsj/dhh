<?php
namespace Admin\Service;

/**
 * OfferService
 */
class OfferService extends CommonService {
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
    $Offer = $this->getM();

    if (false === $Offer->save($data)) {
        return $this->errorResultReturn('系统错误！');
    }

    return $this->resultReturn(true);
    }


    /**
     * 删除账户并且删除数据表
     * @param  int     $id 需要删除账户的id
     * @return boolean
     */
    public function delete($ids) {
        $Dao = $this->getM();


        $model = $Dao->find($id);
        if (empty($model)) {
            return false;
        }

        // 删除账户
        $delStatus = $Dao->delete($id);

        if (false === $delStatus) {
            return false;
        }

        return true;
    }


    /**
     * 查看报价是否存在
     * @param id 报价id
     */
    public function existOffer(){
        return !is_null($this->getM()->find($id));
    }

    protected function getModelName() {
        return 'Offer';
    }
}
