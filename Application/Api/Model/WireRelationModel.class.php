<?php
/**
 * Created by PhpStorm.
 * User: Jarvix
 * Date: 2016/4/20 0020
 * Time: 14:48
 */

namespace Api\Model;

use Think\Model;

class WireRelationModel extends Model
{


    /**
     * 判断路线和公司是否对应存在
     * @param $wireId   路线id
     * @param $logisticsId  物流公司id
     * @return bool
     */
    public function JudgeExist($wireId, $logisticsId)
    {
        $map['wier_id'] = $wireId;
        $map['logistics_id'] = $logisticsId;
        $as = $this->where($map)->find();
        return $as === false ? false : true;
    }

}