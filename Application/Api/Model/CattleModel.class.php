<?php
/**
 * Created by PhpStorm.
 * User: Jarvix
 * Date: 2016/1/18
 * Time: 11:13
 */

namespace Api\Model;
use Think\Model;

class CattleModel extends Model
{

    protected $_vaildate = array(
        array('name','require','名称不能为空！'),
        array('phone','require','电话不能为空！'),
        array('address','require','地址不能为空！'),
        array('remark','require','备注不能为空！'),
        array('type','require','类型出错！')
    );

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


    public function getRelaAllCattle($search, $start, $end, $page = 1)
    {
        $map['c.status']  = 1;
        $map['c.name']    =  array('like', "%$search%");

            if($start && $end){

            $Region = D('region');
            $start_id = $Region->JudgeExist($start,2);
            $end_id = $Region->JudgeExist($end,1);

            $map['cw.start'] = $start_id;
            $map['cw.end'] = $end_id;
            $count      = $this->alias('c')
                ->join('__CATTLE_WIRE__ cw ON cw.cattle_id = c.id')
                ->where($map)
                ->count();// 查询满足要求的总记录数
            $Page       = new \Think\Page($count,C('PAGE_SIZE'));// 实例化分页类 传入总记录数和每页显示的记录数(25)
            $list = $this->alias('c')
                ->join('__CATTLE_WIRE__ cw ON cw.cattle_id = c.id')
                ->field('c.*')
                ->where($map)
                ->order('id desc')
                ->limit($Page->firstRow.','.$Page->listRows)
                ->select();
            $data['data'] = $list;
        }else{
            $count      = $this->alias('c')
                ->where($map)
                ->count();// 查询满足要求的总记录数
            $Page       = new \Think\Page($count,C('PAGE_SIZE'));// 实例化分页类 传入总记录数和每页显示的记录数(25)
            $list = $this->alias('c')
                ->where($map)
                ->order('id desc')
                ->limit($Page->firstRow.','.$Page->listRows)
                ->select();
            $data['data'] = $list;
        }

        $Wire = D('CattleWire');
        foreach($data['data'] as $key=>$val) {
            $data['data'][$key]['img'] = imgDomain($val['img']);
            $arr = $Wire->alias('w')
                ->join('__REGION__ r ON r.region_id = w.start')
                ->join('__REGION__ re ON re.region_id = w.end')
                ->where('w.cattle_id = '.$val['id'])
                ->field('r.region_name as start, re.region_name as end')
                ->select();
            if($arr){
                $temp = array();
                foreach($arr as $k=>$v){
                    $temp[] = $v['start']."--".$v['end'];
                }
                if($temp){
                    $data['data'][$key]['wire'] = $temp;
                }else{
                    $data['data'][$key]['wire'] = array();
                }
            }else{
                $data['data'][$key]['wire'] = array();
            }
        }
        //计算还有没有下一页
        $totalPage = ceil($count/C('PAGE_SIZE'));
        if($_GET['p'] >= $totalPage){
            $data['also'] = 0;
        }else{
            $data['also'] = 1;
        }
        return $data;
    }

}