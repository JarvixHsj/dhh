<?php
/**
 * Created by PhpStorm.
 * User: Jarvix
 * Date: 2016/1/19
 * Time: 19:40
 */

namespace Api\Model;
use Think\Model;

class CommentModel  extends Model
{

    protected $_vaildate = array(
        array('order_id','require','评论订单不能为空！'), //默认情况下用正则进行验证
        array('comment_content','require','评论内容不能为空！') //默认情况下用正则进行验证
    );


    public function existEmpty($order_id)
    {
        if($this->where('order_id = '.$order_id)->getField('comment_id')){
            return 1;
        }else{
            return 0;
        }
    }
}