<?php
/**
 * Created by PhpStorm.
 * User: Jarvix
 * Date: 2016/5/9 0009
 * Time: 18:05
 */

namespace Api\Model;


use Think\Model;

class BroadcastModel extends Model
{

    /**
     * 创建消息
     * @param $type     1=系统消息   （其他待定）
     * @param $content
     */
    public function createSystem($type = 1, $content)
    {
        $map['type']    =    $type;
        $map['content'] =    $content;
        $map['created_at'] = time();
        if(false === $this->add($map)){
            return false;
        }else{
            return true;
        }
    }
}