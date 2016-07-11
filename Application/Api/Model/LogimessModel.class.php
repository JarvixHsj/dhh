<?php
/**
 * Created by PhpStorm.
 * User: Jarvix
 * Date: 2016/4/13 0013
 * Time: 11:26
 */

namespace Api\Model;


use Think\Model;

/* 物流端消息模型 */

class LogimessModel extends Model
{


    public function InsertMess($userId,$content,$title)
    {
        $info['logistics_id'] = $userId;
        $info['content'] = $content;
        $info['title'] = $title;
        $info['time'] = time();
        $info['read'] = 0;

        if(false === $this->add($info)){
            return false;
        }else{
            return true;
        }
    }

}