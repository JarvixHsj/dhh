<?php
/**
 * Created by PhpStorm.
 * User: SEEJOYS
 * Date: 2016/1/13
 * Time: 14:12
 */

namespace Api\Controller;
use Think\Controller;

/**
 * 物流端接口公用类
 *
 */
class LogisticsCommonController extends CommonController
{
    public $logi_id = 0;    //用户id
    public $token = '';     //用户token

    /**
     * 检查物流端是否登录
     * @param token 用户唯一口令
     */
    public function checkLogistics()
    {
        $token = I('request.token', 0);
        $mode = M('logistics');
        $res = $mode->field('logistics_id')->where("token = '$token'")->find();
        if(empty($res)){
            $return = $this->returns();
            $return['code'] = 1;
            $return['message'] = '请先登录！';
            $this->getReturn($return);
        }
        $this->logi_id = $res['logistics_id'];
        $this->token = $token;
    }



    /**
     * 检查是否有权限
     * @param $id 物流公司id
     */
    public function checkRight()
    {
//        $this->checkLogistics();

        if(!D('logistics')->getStatus($this->logi_id)){
            $return = $this->returns();
            $return['code'] = 777;
            $return['message'] = '未审核无此权限！';
            $this->getReturn($return);
        }
    }


    /**
     * 绑定极光id
     * @param $id 极光id
     */
    public function bindAppid()
    {

    }

    /**
     * 退出登录
     */
    public function exitLogistics()
    {
        $this->logi_id = 0;
        $this->token = '';
    }


}