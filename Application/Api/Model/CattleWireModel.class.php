<?php
/**
 * Created by PhpStorm.
 * User: Jarvix
 * Date: 2016/5/11
 * Time: 21:13
 */

namespace Api\Model;
use Think\Model;

class CattleWireModel extends Model
{

    public function createRela($cid, $start, $end)
    {
        $Region = D('region');
        $startId = $Region->JudgeExist($start, 2);
        $endId = $Region->JudgeExist($end, 1);

        $data['cattle_id']  = $cid;
        $data['start']      = $startId;
        $data['end']        = $endId;
        if(false === $this->add($data)){
            return false;
        }else{
            return true;
        }
    }


}