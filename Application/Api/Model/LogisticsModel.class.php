<?php
/**
 * Created by PhpStorm.
 * User: Jarvix
 * Date: 2016/1/18
 * Time: 11:22
 */

namespace Api\Model;
use Think\Model\RelationModel;

class LogisticsModel extends RelationModel
{

    protected $_validate = array(
        array('logistics_phone','','手机号已注册！',0,'unique',1),
        array('logistics_password','require','密码不能为空！'),
        array('logistics_per_name','require','法人名称不能为空！'),
//        array('logistics_name','require','公司名称已注册！',0,'unique',1),
        array('logistics_name','require','公司名称不能为空！'),
        array('logistics_tel','require','公司联系电话不能为空！'),
        array('logistics_address','require','公司地址不能为空！'),
        array('logistics_person_img','require','法人照片不能为空！'),
        array('logistics_open_img','require','营业执照不能为空！'),
        array('logistics_check_img','require','税务登记证不能为空！'),
        array('logistics_way_img','require','道路许可证不能为空！'),
        array('logistics_img','require','公司照片不能为空！'),
    );


    public function getLogiName($id)
    {
        return $this->field('logistics_name')->find($id);
    }


    public function getLogiImg($id){
        $res = $this->field('logistics_img')->find($id);
        return $res['logistics_img'];
    }


    /**
     * 统计物流公司货车数量
     * @param $id 物流公司id
     * return number
     */
    public function countVehicle($id)
    {
        return M('vehicle')->where('logistics_id = '.$id)->count();
    }


    /**
     * 统计物流公司路线数量
     * @param $id 物流公司id
     * return number
     */
    public function countWire($id)
    {
        return M('wire_relation')->where('logistics_id = '.$id)->count();
    }


    /**
     * 根据id获取jpush_id
     * @param $id 用户id
     */
    public function getJPush($id)
    {
        $res = $this->field('jpush_id')->find($id);
        return $res['jpush_id'];
    }


    /**
     * 根据id获取用户状态
     */
    public function getStatus($id){
        $res = $this->field('logistics_status')->find($id);
        return $res['logistics_status'];
    }


    /**
     * 获取一条信息（附带图片全路径）
     */
    public function getimgDomainInfo($id){
        $res = $this->find($id);
        $res['logistics_person_img'] = imgDomain($res['logistics_person_img']);
        $res['logistics_open_img'] = imgDomain($res['logistics_open_img']);
        $res['logistics_check_img'] = imgDomain($res['logistics_check_img']);
        $res['logistics_way_img'] = imgDomain($res['logistics_way_img']);
        $res['logistics_img'] = imgDomain($res['logistics_img']);
        if($res['logistics_head_img'] == ''){
            $res['logistics_head_img'] = $res['logistics_img'];
        }else{
            $res['logistics_head_img'] = imgDomain($res['logistics_head_img']);
        }

        return $res;
    }


    public function getSpecial()
    {
        $special = C('SPECIAL');
        $logistics_id = $this->where("logistics_name = '".$special."'")->getField('logistics_id');
        return $logistics_id;
    }


}