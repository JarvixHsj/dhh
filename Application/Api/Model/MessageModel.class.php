<?php
/**
 * Created by PhpStorm.
 * User: Jarvix
 * Date: 2016/4/13 0013
 * Time: 11:26
 */

namespace Api\Model;


use Think\Model;

/* 用户端消息模型 */

class MessageModel extends Model
{


    public function InsertMess($userId,$content,$title)
    {
        $info['user_id'] = $userId;
        $info['message_content'] = $content;
        $info['message_title'] = $title;
        $info['message_time'] = time();
        $info['message_read'] = 0;
        $this->add($info);
    }

}