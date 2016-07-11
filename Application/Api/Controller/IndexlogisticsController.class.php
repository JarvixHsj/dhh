<?php
/**
 * Created by PhpStorm.
 * User: Jarvix
 * Date: 2016/1/19
 * Time: 20:52
 */

namespace Api\Controller;
use Think\Model;

/**
 * Class IndexlogisticsController
 * @package Api\Controller
 * 物流端首页接口
 */
class IndexlogisticsController extends LogisticsCommonController
{
    /**
     * 物流端首页
     */
    public function Index()
    {
        $result = $this->returns();
        $Select = D('select');
        $Region = D('region');
        $Users = D('users');
        $Order = D('orders');

        //接收搜索信息
        $type = I("request.type");  //货物类型
        $weight = I("request.weight");  //货物重量
        $bulk = I("request.bulk");      //货物体积
        $start = I('request.start');    //搜索城市
        $end = I('request.end');
        $_GET['p'] = I('request.p');
        $page_size = C('PAGE_SIZE');


        //货物种类
        if($type != 'all'){
            $type_data = $Select->getValue($type);
            $where = " (order_cargo_type = '".$type_data."') AND ";
        }
        //货物重量
        if($weight != 'all'){
            $wei_data = $Select->getValue($weight);
            if(is_array($wei_data)){
                $where .= " (order_weight >= {$wei_data['state']} ) AND ";
                $where .= " (order_weight <= {$wei_data['end']} ) AND ";
            }else{
                $where .= " ('order_weight >= {$wei_data} ) AND";
            }
        }
        //货物体积
        if($bulk != 'all'){
            $bulk_data = $Select->getValue($bulk);
            if(is_array($bulk_data)){
                $where .= " (order_bulk >= {$bulk_data['state']} ) AND ";
                $where .= " (order_bulk <= {$bulk_data['end']} ) AND ";
            }else{
                $where .= " (order_bulk >= {$bulk_data} ) AND ";
            }
        }
        if($start){
            $where .= " (order_depart_city = '{$start}') AND ";
        }
        if($end){
            $where .= " (order_des_city = '{$end}') AND ";
        }
        $expiration = time()-C('EXPIRATION_TIME')*60;
        $map = " {$where}   ((( order_type = 1 ) AND ( order_status = 1 )) OR ((order_type = 2 ) AND ( order_is_expiration = 0 ) AND ( order_add_time <= {$expiration} ) AND ( order_status = 1 )))";

        $count = $Order->where($map)->count();    //统计总条数，为计算下一页
        $Page       = new \Think\Page($count,$page_size);
        $lists = $Order->where($map)->field('order_id,user_id,order_user_phone,order_depart_province,order_depart_city,order_depart_details,order_des_province,order_des_city,order_des_details,order_cargo_type,order_weight,order_bulk')->order('order_add_time desc')->limit($Page->firstRow.','.$Page->listRows)->select();//显示6条信息

        foreach ($lists as $kl => $vl) {
            $lists[$kl]['order_depart'] = $vl['order_depart_province'].$vl['order_depart_city'].$vl['order_depart_details'];
            $lists[$kl]['order_des'] = $vl['order_des_province'].$vl['order_des_city'].$vl['order_des_details'];
            //查询货物类型
            $lists[$kl]['order_cargo_type'] = $vl['order_cargo_type'];
            //查询订单用户
            $lists[$kl]['user_name'] = $Users->getname($vl['user_id']);
            $lists[$kl]['user_name'] = $Users->getname($vl['user_id']);
        }

        //判断是否还有下一页
        if($_GET['p'] >= ceil($count/$page_size)){
            $result['also'] = 0;
        }else{
            $result['also'] = 1;
        }

        if($lists){
            $result['data'] = $lists;
        }else{
            $result['data'] = array();
            $result['message'] = '暂未查询到订单！';
        }
        $result['status'] = 1;
        $this->getReturn($result);
    }


    /**
     * 首页搜索信息
     *
     */
    public function searchInfo()
    {
        $result = $this->returns();

        //查询出3种搜索类型
        $typeData = M('type')->where('type_status = 1')->limit('0,3')->select();
        $Select = D('select');
        foreach ($typeData as $key => $val) {
            //循环查询每种搜索类型的20个区间
            $map['type_id'] = $val['type_id'];
            $map['select_status'] = 1;
            $selData = $Select->where($map)->limit(0, 20)->select();

            if ($selData) {
                foreach ($selData as $k => $sd) {
                    if ($sd['is_area'] == 0) {
                        $temp['value'] = $sd['select_area_state'] . $val['type_unit'];
                        $temp['ID'] = $sd['select_id'];

                        $typeData[$key]['select'][] = $temp;
                    } else {
                        if (!$sd['select_area_end']) {
                            $temp['value'] = $sd['select_area_state'] . $val['type_unit'] . '以上';
                            $temp['ID'] = $sd['select_id'];
                            $typeData[$key]['select'][] = $temp;
                        } else {
                            $temp['value'] = $sd['select_area_state'] . '-' . $sd['select_area_end'] . $val['type_unit'];
                            $temp['ID'] = $sd['select_id'];
                            $typeData[$key]['select'][] = $temp;
                        }
                    }
                }
            }
        }
        $result['data'] = $typeData;
        $result['status'] = 1;
        $this->getReturn($result);
    }


    /*
     * 抢单内页
     * @param   id  订单id
     * @return array 订单详情
     */
    public function orderInside()
    {
        $result = $this->returns();

        $id = I('request.id');
        $data = M('orders')->find($id);
        if (empty($data)) {
            $result['message'] = '该订单不存在！';
            die($this->getReturn($result));
        }

        //用户名
        $userInfo = D('users')->field('user_name,user_img')->find($data['user_id']);

        $data['user_name'] = $userInfo['user_name'];
        $data['user_img'] = imgDomain($userInfo['user_img']);
        $data['order_add_time'] = date('Y-m-d H点',$data['order_add_time']);
        $data['order_time'] = date('Y-m-d H点',$data['order_time']);
        //路线
        $Region = D('region');
//        $data['order_depart_province'] = $Region->getName($data['order_depart_province']);
        $data['start'] = $Region->getName($data['order_depart_city']);
//        $data['order_des_province'] = $Region->getName($data['order_des_province']);
        $data['end'] = $Region->getName($data['order_des_city']);

        //诚信保证金
        $cash = M('select')->where('type_id = 4')->select();
        foreach ($cash as $key => $val) {
            if ($val['is_area'] == 0) {
                $item[] = $val['select_area_state'];
            } else {
                $item[] = array('state' => $val['select_area_state'], 'end' => $val['select_area_end']);
            }
        }
        $data['cash'] = $item;

        $result['status'] = 1;
        $result['data'] = $data;

        $this->getReturn($result);
    }


    /**
     * 物流抢单报价
     * @param token 用户口令
     * @param money 报价金额
     * @param id    订单id
     */
    public function logiOffer()
    {
        $this->checkLogistics();
        $this->checkRight();
        $result = $this->returns();
        $Orders = D('orders');

        $data['offer_money'] = I('request.money');//报价金额
        $data['order_id'] = I('request.id');    //报价订单
        $data['logistics_id'] = $this->logi_id;

        //如果报价不存在
        if(!$data['offer_money']){
            $result['message'] = '报价失败，未输入报价金额';
            $this->getReturn($result);
        }

        $res = $Orders->field('order_sn,user_id,order_is_expiration,order_add_time,logistics_id,order_type')->find($data['order_id']);
        if(!$res){
            $result['message'] = '订单不存在';
            $this->getReturn($result);
        }

        //判断是否已经报价了
        $Offer = M('offer');
        $condi = $Offer->where('order_id = '.$data['order_id'].' AND logistics_id = '.$data['logistics_id'])->find();
        if($condi){
            $result['message'] = '已经报价了，不可重复报价！';
            $this->getReturn($result);
        }

        $insertId = $Offer->add($data);
        if(!$insertId){
            $result['message'] = '报价失败，数据插入数据库失败！';
            $this->getRetrun($result);
        }

        //判断特殊定向订单情况报价
        $expiration = time()-C('EXPIRATION_TIME')*60;
        if(($res['order_is_expiration'] == 0) && ($res['order_add_time'] >= $expiration) && ($res['order_type'] == 2)){
            $Orders->changeExpiration($data['order_id']);
        }

        //生成消息，推送消息
        $content = "您的订单{$res['order_sn']}收到一条报价信息";
        $title = '订单收到报价信息';
        D('message')->InsertMess($res['user_id'],$content,$title);
        //推送极光
        $jpushId = D('users')->getJPush($res['user_id']);
        $this->bbs_push_client($jpushId,$content,$title);

        $result['status'] = 1;
        $result['message'] = '报价成功';
        $this->getReturn($result);
    }


    /**
     * 公司信誉
     * @param token
     */
    public function comment()
    {
        $this->checkLogistics();
        $result = $this->returns();

        $page_size = 1;
        $_GET['p'] = I('request.p');
        $Users = D('users');
        $map['logistics_id'] = $this->logi_id;
        $map['comment_status'] = 1;

        $count = M('comment')->where($map)->count();
        $Page       = new \Think\Page($count,$page_size);

        $com = M('comment')->where($map)->limit($Page->firstRow.','.$Page->listRows)->select();
        if($com){
            $info = array();
            foreach($com as $key => $val){
                $item = array();
                $UData = $Users->field('user_img,user_name')->find($val['user_id']);
                $item['user_img'] = imgDomain($UData['user_img']);
                $item['user_name'] = $UData['user_name'];
                $item['comment_time'] = date('m-d',$val['comment_time']);
                $item['comment_content'] = $val['comment_content'];
                $info[] = $item;
            }
            $result['message'] = '有评论！';
        }else{
            $info = array();
            $result['message'] = '没有评论！';
        }

        //判断是否还有下一页
        if($_GET['p'] >= ceil($count/$page_size)){
            $result['also'] = 0;
        }else{
            $result['also'] = 1;
        }

        $result['data'] = $info;
        $result['status'] = 1;
        $this->getReturn($result);
    }

    /**
     * @param token     物流公司token
     */
    public function getOfferList()
    {

        $this->checkLogistics();
        $result = $this->returns();
        $result['status'] = 1;

        $list = D('Offer')->getOffer($this->logi_id);
        if(!empty($list)){
            $result['data'] = $list;
        }else{
            $result['data']['order_id'] = array();
        }

        $this->getReturn($result);
    }
}












