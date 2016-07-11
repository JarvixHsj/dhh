<?php
/**
 * Created by PhpStorm.
 * User: Jarvix
 * Date: 2016/1/20
 * Time: 17:34
 */

namespace Api\Model;
use Think\Model;

class TypeModel extends Model
{

    /**
     * 根据id获取type_name
     * @param id    类型id
     */
    public function getName($id)
    {
        $res = $this->field('type_name')->find($id);
        if($res['type_name'] == null){
            return '';
        }
        return $res['type_name'];
    }


}