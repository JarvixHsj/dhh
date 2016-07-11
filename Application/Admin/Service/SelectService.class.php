<?php
namespace Admin\Service;

/**
 * SelectService
 */
class SelectService extends CommonService {
    /**
     * 添加筛选类型
     * @param  array $admin 信息
     * @return array
     */
    public function add($data) {
        $Select = $this->getD();

        $as = $Select->add($data);

        if (false === $as) {
            return $this->errorResultReturn('系统出错了！');
        }

        return $this->resultReturn(true);
    }

    /**
     * 更新管理员信息
     * @return
     */
    public function update($admin) {
        $Admin = $this->getM();

        if (false === $Admin->save($admin)) {
            return $this->errorResultReturn('系统错误！');
        }

        return $this->resultReturn(true);
    }


    /**
     * 是否存在
     * @param  int     $id id
     * @return boolean
     */
    public function existAdmin($id) {
        return !is_null($this->getM()->find($id));
    }


    /**
     * 删除账户并且删除数据表
     * @param  int     $id 需要删除账户的id
     * @return boolean
     */
    public function delete($id) {
        $Dao = $this->getM();

        $delStatus = $Dao->delete($id);

        if (false === $delStatus) {
            return $this->resultReturn(false);
        }
        return $this->resultReturn(true);
    }

    protected function getModelName() {
        return 'Select';
    }
}
