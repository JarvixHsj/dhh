<?php
/**
 * Created by PhpStorm.
 * User: Jarvix
 * Date: 2016/2/1
 * Time: 16:40
 */

namespace Home\Model;
use Think\Model;

class SelectModel extends Model
{

    /**
     * 根据id取值
     * @param id    自增id
     */
    public function getValue($id)
    {
        $res = $this->find($id);
        if($res['is_area'] == 0){
            return $res['select_area_state'];
        }else{
            return array('state'=>$res['select_area_state'],'end'=>$res['select_area_end']);
        }
    }

}