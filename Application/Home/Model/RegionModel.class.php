<?php
/**
 * Created by PhpStorm.
 * User: Jarvix
 * Date: 2016/2/1
 * Time: 16:10
 */

namespace Home\Model;
use Think\Model;

class RegionModel extends Model
{
    /**
     * 根据id查询出名称
     * @param id    地区自增id
     * @param type  地区类型
    */
    public function getName($id)
    {
        $res = $this->field('region_name')->find($id);
        if($res['region_name'] == null){    //防止app崩掉
            return '';
        }
        return $res['region_name'];
    }


    /**
     * 根据name查询出id
     * @param name    地区自增id
     * @param type  地区类型
     */
    public function getId($name,$type)
    {
        $where['region_name'] = array('like',"%$name%");
        $where['region_type'] = $type;
        $res = $this->field('region_id')->where($where)->find();
        return $res['region_id'];
    }


    /**
     * 根据id查询省
     * @param id    地区自增id
     * @param type  地区类型
     */
    public function getParentName($id)
    {
        $item = $this->field('parent_id')->find($id);
        $res = $this->field('region_name')->find($item['parent_id']);
        if($res['region_name'] == null){    //防止app崩掉
            return '';
        }
        return $res['region_name'];
    }
}