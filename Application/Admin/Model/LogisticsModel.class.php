<?php
/**
 * Created by PhpStorm.
 * User: Jarvix
 * Date: 2016/1/26
 * Time: 10:55
 */

namespace Admin\Model;


class LogisticsModel extends CommonModel
{

    protected $_validate = array(
        array('logistics_password','require','密码不能为空！'),

        array('logistics_per_name','require','法人名称不能为空！'),
//        array('logistics_name','require','此公司名称已注册！',0,'unique',1),
        array('logistics_name','require','公司名称不能为空！'),
        array('logistics_tel','require','公司联系电话不能为空！'),
        array('logistics_address','require','公司地址不能为空！'),
    );


    /**
     * 查询物流公司名称
     */
    public function getName($id)
    {
        $data = M('logistics')->field('logistics_name')->find($id);
        return $data['logistics_name'];
    }


    /**
     * 查询权限
     * @param $id wuliuid
     * @return boolean
     */
    public function getStatus($id)
    {
        $status = $this->field('logistics_status')->find($id);
        if($status['logistics_status']) {
            return true;
        } else {
            return false;
        }
    }
}