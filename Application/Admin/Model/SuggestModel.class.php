<?php
namespace Admin\Model;

/**
 * Suggest
 * 建议模型
 */
class SuggestModel extends CommonModel {

    // realtions
//    protected $_link = array(
//        // 一个管理员属于一个角色
//        'role' => array(
//            'mapping_type' => self::BELONGS_TO,
//            'class_name' => 'Role',
//            'foreign_key' => 'role_id',
//            'mapping_fields' => 'name'
//        )
//    );

//    protected $_validate = array(
//        // 用户名不能为空
//        array('name', 'require', '用户名不能为空！', 1, 'regex', 3),
//        // 用户名长度不能大于64个字符
//        array('name', '0,64', '用户名长度不能超过64个字符！', 1, 'length', 3),
//        // 用户名唯一性
//        array('name', '', '用户名已经存在，请更换一个！', 1, 'unique', 1),
//
//        // 登录密码不能为空
//        array('password', 'require', '登录密码不能为空！', 1, 'regex', 1),
//        // 确认密码不一致
//        array('password', 'cfm_password', '确认密码不一致！', 2, 'confirm', 3),
//
//        // 状态
//        array('is_active', '0,1', '无效的状态！', 1, 'in', 3),
//
//        // 角色不能为空
//        array('role_id', 'require', '所属角色不能为空！', 1, 'regex', 3),
//    );
//
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


}
