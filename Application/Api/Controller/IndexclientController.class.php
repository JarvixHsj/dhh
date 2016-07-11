<?php
/**
 * Created by PhpStorm.
 * User: Jarvix
 * Date: 2016/1/13
 * Time: 11:02
 */

namespace Api\Controller;
use Think\Controller;

class IndexclientController extends ClientCommonController
{


    /**
     * 首页定位
     * @param region_name   地区名称
     */
    public function index()
    {
        $return = $this->returns(); //array('status'=> 0, 'message'=> '', 'data'=> '', 'token' => '' ,'code' => '');

        $start  = I('request.region_start');    //接受起始地区名称
        $end    = I('request.region_end');      //接受结束地区名称
        $_GET['p'] = I('request.p');
        $page_size = C('PAGE_SIZE');

        $regionModel = D('region');
        $wireModel = D('car_wire');             //实例化路线表
        if(!$region_start = $regionModel->JudgeExist($start)){
            $return['message'] = '找不到该起始城市！';
            die($this->getReturn($return));
        }

        if($end){
            if(!$region_end = $regionModel->JudgeExist($end)){
                $return['message'] = '找不到该目的地城市！';
                die($this->getReturn($return));
            }
        }

        $wire_where = array();
        $wire_where['wire_state']  =  $region_start;//起点
        if($region_end['region_id']){
            $wire_where['wire_end']    =  $region_end;   //终点
            $wireModel->where($wire_where)->setInc('hot');  //搜索热门加1
        }
        $wire_where['wire_effect'] = 1;        //并且状态是要启用的

        $count = $wireModel->where($wire_where)->count();   //统计

        $Page = new \Think\Page($count,$page_size);
        $wire_data = $wireModel->field('wire_id, wire_state, wire_end, wire_effect, gently, weighty')->where($wire_where)->limit($Page->firstRow.','.$Page->listRows)->select();
        if(empty($wire_data)){
            $return['message'] = '该城市暂时没有物流路线，请搜索其他城市。';
            die($this->getReturn($return));
        }

        //给每条路线加上地区名称
        foreach($wire_data as $key=>$val){
            $logi_data = D('car_wire')->alias('cw')
                ->join('__WIRE_RELATION__ wr ON wr.wire_id = cw.wire_id')
                ->join('__LOGISTICS__ l ON l.logistics_id = wr.logistics_id')
                ->order('l.is_recommend desc')
                ->where('cw.wire_id = '.$val['wire_id']." AND l.logistics_status = 1")
                ->field('l.*')
                ->select();

            $wire_data[$key]['wire_state'] = $this->get_region_name($val['wire_state']); //获取路线起点名称
            $wire_data[$key]['wire_end'] = $this->get_region_name($val['wire_end']);//获取路线终点名称
            if ($logi_data) {
                $wire_data[$key]['wire'] = $logi_data;
            } else {
                $wire_data[$key]['wire'] = array();
            }
        }

        //判断是否还有下一页
        if($_GET['p'] >= ceil($count/$page_size)){
            $return['also'] = 0;
        }else{
            $return['also'] = 1;
        }

        $return['status'] = 1;
        $return['data'] = $wire_data;
        $this->getReturn($return);
    }

    /**
     * 首页定位
     * @param region_name   地区名称
     */
    public function index11()
    {
        $return = $this->returns(); //array('status'=> 0, 'message'=> '', 'data'=> '', 'token' => '' ,'code' => '');

        $start  = I('request.region_start');    //接受起始地区名称
        $end    = I('request.region_end');      //接受结束地区名称
        $_GET['p'] = I('request.p');
        $page_size = C('PAGE_SIZE');

        $regionModel = D('region');
        $wireModel = D('car_wire');             //实例化路线表
        if(!$region_start = $regionModel->JudgeExist($start)){
            $return['message'] = '找不到该起始城市！';
            die($this->getReturn($return));
        }
        if($end){
            if(!$region_end = $regionModel->JudgeExist($end)){
                $return['message'] = '找不到该目的地城市！';
                die($this->getReturn($return));
            }
        }

        $wire_where = array();
        $wire_where['wire_state']  =  $region_start;//起点
        if($region_end['region_id']){
            $wire_where['wire_end']    =  $region_end;   //终点
            $wireModel->where($wire_where)->setInc('hot');  //搜索热门加1
        }
        $wire_where['wire_effect'] = 1;        //并且状态是要启用的
        $wire_data = $wireModel->where($wire_where)->find();
        if(empty($wire_data)){
            $return['message'] = '该城市暂时没有物流路线，请搜索其他城市。';
            die($this->getReturn($return));
        }

        $count = D('car_wire')->alias('cw')
            ->join('__WIRE_RELATION__ wr ON wr.wire_id = cw.wire_id')
            ->join('__LOGISTICS__ l ON l.logistics_id = wr.logistics_id')
            ->order('l.is_recommend desc')
            ->where('cw.wire_id = '.$wire_data['wire_id']." AND l.logistics_status = 1")
            ->field('l.*')
            ->count();
        $Page = new \Think\Page($count,$page_size);
        //给每条路线加上地区名称
        $logi_data = D('car_wire')->alias('cw')
            ->join('__WIRE_RELATION__ wr ON wr.wire_id = cw.wire_id')
            ->join('__LOGISTICS__ l ON l.logistics_id = wr.logistics_id')
            ->order('l.is_recommend desc')
            ->where('cw.wire_id = '.$wire_data['wire_id']." AND l.logistics_status = 1")
            ->field('l.*')
            ->limit($Page->firstRow.','.$Page->listRows)
            ->select();

        $wire_data['wire_state'] = $regionModel->getName($wire_data['wire_state']); //获取路线起点名称
        $wire_data['wire_end'] = $regionModel->getName($wire_data['wire_end']);//获取路线终点名称
        if ($logi_data) {
            $wire_data['wire'] = $logi_data;
        } else {
            $wire_data['wire'] = array();
        }
        //判断是否还有下一页
        if($_GET['p'] >= ceil($count/$page_size)){
            $return['also'] = 0;
        }else{
            $return['also'] = 1;
        }

        $return['status'] = 1;
        $return['data'] = $wire_data;
        $this->getReturn($return);
    }


    /**
     * 获取热门城市
     * @return array
     */
    public function getHotCity()
    {
        $return = $this->returns();

        //查询出所有路线的起点和终点
        $wire_data = M('car_wire')->field('wire_state, wire_end')->where('wire_effect = 1')->select();
        $item = array();
        $data = array();
        //由二维数组转为一维数组
        $item = $this->arrayChange($wire_data);
        if(count($item) > 9)    //最多显示9个，如果小于9个就直接显示， 如果大于9个，就比较那个城市重复的次数多
        {
            $item = $this->fetchRepeatMemberInArray($item); //去重
//            $item = array_unique($item); //去重
            $arr = array();
            foreach($item as $k){   //将下标按顺序重新排列
                $arr[] = $k;
            }
            for($i = 0;$i<=9-1;$i++){   //只取前面9个
                $data[] = $this->get_region_name($arr[$i]);
            }
        }else{
            $item = array_unique($item);
            foreach($item as $k=>$v){
                $data[] = $this->get_region_name($v);
            }
        }
        $return['status'] = 1;
        $return['data'] = $data;
        $this->getReturn($return);
    }


    /**
     * 模糊搜索城市
     * @param string    城市名称
     * @return array    查询出有城市名称的地区
     */
    public function searchCity()
    {
        $return = $this->returns();

        $name = I('request.search_name');   //接受搜索名称
        $map['region_name'] = array('like',"%$name%");
        $map['region_type'] = 2;
        $regionModel = M('region');
        $region_data = $regionModel->field('region_name')->where($map)->select();
        if(empty($region_data)){
            $return['message'] = '没有该城市，请重新输入！';
            die($this->getReturn($return));
        }
        $info = array();
        foreach($region_data as $k=>$val)
        {
            $info[] = $val['region_name'];
        }

        $return['status'] = 1;
        $return['data'] = $info;
        $this->getReturn($return);
    }


    /**
     * 物流详情
     * @param $id   物流id
     * @return arr  物流信息
     */
    public function logisticsDetails()
    {
        $return = $this->returns();
        $id = I('request.id');  //获取物流id
        if(empty($id)){
            $return['message'] = '该物流公司不存在！';
            die($this->getReturn($return));
        }
        $res = M('logistics')->find($id); //查询出物流信息
        if(empty($res)){
            $return['message'] = '该物流公司不存在！';
            die($this->getReturn($return));
        }

        //总路线
        $wire_num = M('wire_relation')->where('logistics_id = '.$id)->count();

        //总车辆
        $car_num = M('vehicle')->where('logistics_id = '.$id)->count();

        //总评论
        $comment_num = M('comment')->where('logistics_id = '.$id)->count();

        $return['status'] = 1;
        $res['logistics_img'] = imgDomain($res['logistics_img']);
        $return['data']['details'] = $res;
        $return['data']['wire_num'] = $wire_num;
        $return['data']['car_num'] = $car_num;
        $return['data']['comment_num'] = $comment_num;

        $this->getReturn($return);
    }


    /**
     * 路线展示
     * @param $id   物流公司id
     * @return array
     */
    public function wireShow()
    {
        $return = $this->returns();
        $logiID = I('request.id');  //物流id
        $page_size = 9;
        $_GET['p'] = I('request.p');

        $logiName = D('logistics')->field('logistics_id,logistics_name,logistics_img')->find($logiID);   //查询出物流名称 logistics_name

        //查询出物流id关联的路线id
        $count = M('wire_relation')->where('logistics_id = '.$logiID)->count();
        $Page = new \Think\Page($count,$page_size);
        $data = M('wire_relation')->where('logistics_id = '.$logiID)->limit($Page->firstRow.','.$Page->listRows)->select();
        $Model = D('car_wire');
        $arr = array();
        foreach($data as $key=>$val){
            //获取路线id的信息
            if($res = $Model->find($val['wire_id'])){
                //获取路线 起始地的名称
                $res['wire_state'] = $this->get_region_name($res['wire_state']);
                //获取路线 目的地的名称
                $res['wire_end'] = $this->get_region_name($res['wire_end']);
                $res['logistics_name'] = $logiName['logistics_name'];   //物流公司名称
                $res['logistics_id'] = $logiName['logistics_id'];   //物流公司名称
                $res['logistics_img'] = $logiName['logistics_img'];   //物流公司名称
                $res['month'] =  D('orders')->getCountWireNum($val['wire_id']); //月运单
                $res['logistics_img'] = imgDomain($res['logistics_img']);   //物流公司的图片
                $res['predict_time'] = $val['predict_time'];
                $res['price'] = $val['price'];
                $res['gently'] = $val['gently'];
                $arr[] = $res;  //赋值保存
            }
        }

        //判断是否还有下一页
        if($_GET['p'] >= ceil($count/$page_size)){
            $return['also'] = 0;
        }else{
            $return['also'] = 1;
        }

        if($arr){
            $return['data'] = $arr;
        }else{
            $return['data'] = array();
        }
        $return['status'] = 1;
        $this->getReturn($return);
    }


    /**
     * 车辆管理
     * @param id    //物流公司id
     * @return array
     */
    public function carManage()
    {
        $return = $this->returns();

        $id = I('request.id');
        $_GET['p'] = I('request.p');
        $page_size = 6;

        $count = M('vehicle')->where('logistics_id = '.$id)->count();
        $Page = new \Think\Page($count,$page_size);
        $res = M('vehicle')->field('vehicle_id , logistics_id , vehicle_series , vehicle_type , vehicle_licence , vehicle_car_weight , vehicle_weight, vehicle_car_img')->where('logistics_id = '.$id)->limit($Page->firstRow.','.$Page->listRows)->select();
        $return['status'] = 1;
        if(!$res){
            $return['message'] = '该物流公司比较懒没，还没上传车辆信息！';
            $return['data'] = array();
            $this->getReturn($return);
        }
        foreach($res as $key=>$val){
            $res[$key]['vehicle_car_img'] = imgDomain($val['vehicle_car_img']);
        }

        //判断是否还有下一页
        if($_GET['p'] >= ceil($count/$page_size)){
            $return['also'] = 0;
        }else{
            $return['also'] = 1;
        }
        $return['data'] = $res;
        $this->getReturn($return);
    }

    /**
     * 车辆详情
     * @param id    车辆id
     * @return array
     */
    public function carDetails()
    {
        $return = $this->returns();

        $id = I('request.id');

        $res = M('vehicle')->find($id);

        if(!$res){
            $return['message'] = '该车辆的信息丢失或不存在！';
            $this->getReturn($return);
        }
        $res['vehicle_year_img'] = imgDomain($res['vehicle_year_img']);
        $res['vehicle_way_img'] = imgDomain($res['vehicle_way_img']);
        $res['vehicle_car_img'] = imgDomain($res['vehicle_car_img']);
        $res['vehicle_licence_img'] = imgDomain($res['vehicle_licence_img']);
        $return['status'] = 1;
        $return['data'] = $res;
        $this->getReturn($return);
    }


    /**
     * 定向发货
     */
    public function orientShip()
    {
        $this->checkUser();
        $return = $this->returns(); //array('status'=> 0, 'message'=> '', 'data'=> '', 'token' => '' ,'code' => '');

        //查询物流和路线关系，是否存在
        if(!D('wire_relation')->JudgeExist(I('request.wire_id'),I('request.logistics_id'))){
            $return['message'] = '该物流公司没有该路线';
            $this->getReturn($return);
        }

        $data = I('request.');  //接收所有数据
        $data['user_id']    = $this->user_id;       //用户id
        $region = D('car_wire')->acquireFamily($data['wire_id']);
        $data['order_depart_province'] = $region['start_pro'];
        $data['order_depart_city'] = $region['start_city'];
        $data['order_des_province'] = $region['end_pro'];
        $data['order_des_city'] = $region['end_city'];

        $Orders = D('orders');
        if (!$Orders->create($data)){
            $return['message'] = $Orders->getError();
            $this->getReturn($return);
        }

        $yCode = array('A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J');//订单号
        $data['order_sn'] = $yCode[intval(date('Y')) - 2016] . strtoupper(dechex(date('m'))) . date('d') . substr(microtime(), 2, 5);
        $data['order_type'] = 2;  //1：预约发货，2：定向发货
        $data['order_status'] = 1;  //1 = 未确定  2 = 未完成  3 = 已完成
        $data['order_time'] = strtotime($data['order_time']);   //格式化时间
        $data['order_add_time'] = time();
        $insertID = $Orders->add($data);
        if($insertID){
            $Jpush_id = D('Logistics')->getJPush($data['logistics_id']);
            $content = "订单号为：{$data['order_sn']}，路线是{$data['order_depart_city']}--{$data['order_des_city']}";
            $title = "接到一条指定你的订单";
            D('Logimess')->InsertMess($data['logistics_id'],$content,$title);
            $this->bbs_push_server($Jpush_id,$content,$title,'grab', $insertID);

            $return['message'] = '发货成功，请耐心等待物流接收！';
            $return['status'] = 1;
            $this->getReturn($return);
        }else{
            $return['message'] = '发货失败，信息不全';
            $this->getReturn($return);
        }

    }



    /**
     * 获取货物类型
     */
    public function getCargoType()
    {
        $result = $this->returns();
        $data = D('select')->field('select_area_state')->where('type_id = 1')->select();
        $info = array();
        foreach($data as $key=>$val)
        {
            $info[] = $val['select_area_state'];
        }

        if($info == null)
        {
            $info = (object)array();
        }

        $result['data'] = $info;
        $result['status'] = 1;
        $this->getReturn($result);
    }



    /**
     * 物流详情页--收到的评论
     * @param token  客户token
     * @param id    物流公司id
     * @return array
     */
    public function commentLsit()
    {
        $result = $this->returns();
        $resylt['status'] = 1;

        $id = I('request.id');
        $_GET['p'] = I('request.p',0);
        $page_size = 2;

        $User = D('users');

        $map['logistics_id'] = $id;
        $map['comment_status'] = 1;
        $count = M('comment')->where($map)->count();
        //如果没有评论返回
        if(!$count){
            $result['message'] = '该物流公司暂时还没有评论';
            $result['data'] = array();
            $result['status'] = 1;
            $this->getReturn($result);
        }

        $Page = new \Think\Page($count,$page_size);
        $data = M('comment')->where($map)->limit($Page->firstRow.','.$Page->listRows)->select();
        $info = array();
        $arr = array();
        foreach($data as $index=>$item)
        {
            $value = $User->field('user_img,user_name')->find($item['user_id']);
            $info['comment_id'] = $item['comment_id'];
            $info['user_img'] = $value['user_img']; //用户照片
            $info['user_name'] = $value['user_name'];   //用户名
            $info['content'] = $data[$index]['comment_content'];
            $info['time'] = date('m-d',$data[$index]['comment_time']);
            $arr[] = $info;
        }

        //判断是否还有下一页
        if($_GET['p'] >= ceil($count/$page_size)){
            $result['also'] = 0;
        }else{
            $result['also'] = 1;
        }

        $result['data'] = $arr;
        $result['status'] = 1;
        $this->getReturn($result);

    }



    /**
     * 获取去掉重复数据的数组
     * @param $array   去重数组
     * @return array   重复的数组
     */
    public function  fetchRepeatMemberInArray($array) {
        // 获取去掉重复数据的数组
        $unique_arr = array_unique ($array);
        // 获取重复数据的数组
        $repeat_arr = array_diff_assoc ($array, $unique_arr);

        return array_unique($repeat_arr);
    }



}