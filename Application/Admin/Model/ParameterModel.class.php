<?php
/**
 * Created by PhpStorm.
 * User: Jarvix
 * Date: 2016/2/23
 * Time: 12:06
 */

namespace Admin\Model;


class ParameterModel extends CommonModel
{

    /**
     * 查询品牌或车型
     * @param $type 类型
     * @param return
     */

    public function getParame($type)
    {
        switch($type){
            //查询品牌
            case '1':
                $this->where('type = '.$type)->select();
                break;
            //查询车型
            case '2':
                $this->where('type = '.$type)->select();
                break;
        }
    }

}