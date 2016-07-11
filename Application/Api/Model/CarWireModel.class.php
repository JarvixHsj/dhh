<?php
/**
 * Created by PhpStorm.
 * User: Jarvix
 * Date: 2016/1/18
 * Time: 11:13
 */

namespace Api\Model;
use Think\Model;

class CarWireModel extends Model
{

    protected $_validate = array(
        array('wire','require','路线不能为空！'),
        array('predict_time','require','预计时间不能为空！'),
        array('price','require','预计费用不能为空！')
    );
    /**
     * 获取路线信息
     * @param $id   路线id
     * @return array
     */
    public function getOneInfo($id)
    {
        return $this->find($id);
    }


    /**
     * @param $id   路线id
     */
    public function acquireFamily($id)
    {
        $city =  $this->alias('cw')
            ->join('__REGION__ Rs ON Rs.region_id = cw.wire_state')
            ->join('__REGION__ Re ON Re.region_id = cw.wire_end')
            ->field('Rs.region_id as start_id,Rs.region_name as start_city_name, Re.region_id as end_id,Re.region_name as end_city_name')
            ->where('cw.wire_id = '.$id)
            ->find();
        $province = $this->acquireParent($city['start_id'],$city['end_id']);
        $res['start_pro'] = $province['start']['region_name'];  //省份
        $res['start_city'] = $city['start_city_name'];          //城市
        $res['end_pro'] = $province['end']['region_name'];      //省份
        $res['end_city'] = $city['end_city_name'];              //城市
        return $res;

    }

    /**
     * @param $startId  城市id
     * @param $endId    城市id
     * @return mixed
     */
    public function acquireParent($startId,$endId)
    {
        $start = M('region')->alias('r')
            ->join('__REGION__ rs ON rs.region_id = r.parent_id')
            ->where('r.region_id = '.$startId)
            ->field('rs.region_name')
            ->find();
        $end = M('region')->alias('r')
            ->join('__REGION__ re ON re.region_id = r.parent_id')
            ->where('r.region_id = '.$endId)
            ->field('re.region_name')
            ->find();
        $province['start'] = $start;
        $province['end'] = $end;
        return $province;
    }


}