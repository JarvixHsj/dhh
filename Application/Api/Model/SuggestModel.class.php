<?php
/**
 * Created by PhpStorm.
 * User: Jarvix
 * Date: 2016/3/17
 * Time: 11:51
 */

namespace Api\Model;


use Think\Model;
//反馈建议模型
class SuggestModel extends Model
{

    protected $_validate = array(
        array('content', 'require', '意见内容不能为空！', 1, 'regex', 3),
        array('usertype', '1,2', '无效的状态！', 1, 'in', 3)
    );

    protected $_auto = array(
        //反馈建议添加时间
        array('add_at', 'time', 1, 'function'),
    );
}