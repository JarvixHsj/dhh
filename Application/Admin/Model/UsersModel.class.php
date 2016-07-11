<?php
namespace Admin\Model;

/**
 * Users
 * 客户端用户管理模型
 */
class UsersModel extends CommonModel {

    protected $_validate = array(
        array('user_name','require','用户名不能为空'),
        array('token','require','用户唯一口令不能为空！')
    );

//    protected $_auto = array(
//        // password
//        array('password', 'encryptPassword', 3, 'callback'),
//        // remark
//        array('remark', 'htmlspecialchars', 3, 'function'),
//        // 创建时间
//        array('created_at', 'time', 1, 'function'),
//        // 更新时间
//        array('updated_at', 'time', 3, 'function'),
//        // 最后登录时间
//        array('last_login_at', 'time', 1, 'function')
//    );


    /**
     * 查询用户名称
     */
    public function getName($id)
    {
        $data = M('users')->field('user_name')->find($id);
        return $data['user_name'];
    }



}
