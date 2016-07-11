<?php
/**
 * Created by PhpStorm.
 * User: Jarvix
 * Date: 2016/1/17
 * Time: 17:51
 */

namespace Api\Model;
use Think\Model;


class UsersModel extends Model
{

    protected $_validate = array(
        array('user_phone','','手机号已注册！',0,'unique',1),
        array('user_password','require','密码不能为空！'),
        array('token','require','用户唯一口令不能为空！')
 );


    /**
     * 根据id查询姓名
     * @param id    自增id
     */
    public function getName($id)
    {
        $res = $this->field('user_name')->find($id);
        return $res['user_name'];
    }


    /**
     * 根据id获取jpush_id
     * @param $id 用户id
     */
    public function getJPush($id)
    {
        $res = $this->field('jpush_id')->find($id);
        return $res['jpush_id'];
    }
}