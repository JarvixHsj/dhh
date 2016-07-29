<?php
/**
 * Created by PhpStorm.
 * User: Jarvix
 * Date: 2016/3/21
 * Time: 15:33
 */

namespace Admin\Service;


class AdService extends CommonService
{
    protected function getModelName() {
        return 'Ad';
    }

    /**
     * 添加
     * @param  array $admin 信息
     * @return array
     */
    public function add($admin) {
        $Admin = $this->getD();

        $as = $Admin->add($admin);
        if (false === $as) {
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
        $Con = $this->getD();

        if (false === $Con->save($data)) {
            return $this->errorResultReturn('系统错误！');
        }

        return $this->resultReturn(true);
    }

    /**
     * 删除
     * @param  int     $id 需要删除的id
     * @return boolean
     */
    public function delete($id) {
        $Dao = $this->getD();

        $model = $Dao->getById($id);
        if (empty($model)) {
            return $this->resultReturn(false);
        }
        $delStatus = $Dao->delete($id);
        if (false === $delStatus) {
            return $this->resultReturn(false);
        }
        return $this->resultReturn(true);
    }


    /**
     * 是否存在配置
     * @param  int     $id 配置id
     * @return boolean
     */
    public function existAd($id){
        return !is_null($this->getM()->getById($id));
    }
}