<?php
/**
 * Created by PhpStorm.
 * User: Jarvix
 * Date: 2016/3/21
 * Time: 15:33
 */

namespace Admin\Service;


class ConfigService extends CommonService
{
    protected function getModelName() {
        return 'Config';
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
     * 是否存在配置
     * @param  int     $id 配置id
     * @return boolean
     */
    public function existConfig($id){
        return !is_null($this->getM()->getById($id));
    }
}