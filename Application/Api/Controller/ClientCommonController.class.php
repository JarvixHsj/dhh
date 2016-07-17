<?php
/**
 * Created by PhpStorm.
 * User: SEEJOYS
 * Date: 2016/1/13
 * Time: 14:09
 */

namespace Api\Controller;
use Think\Controller;
/**
 * 客户端接口公用类
 *
 */
class ClientCommonController extends CommonController
{

    public $user_id = 0;    //用户id
    public $token = '';     //用户token
    /**
     * 检查用户是否登录
     *
     */
    protected function checkUser()
    {
        $token = I('request.token', 0);
        $return = $this->returns();
        $mode = M('users');
        $res = $mode->field('user_id ,session_time')->where("token = '$token'")->find();
        if(empty($res)){
            $return['code'] = 1;
            $return['message'] = '请先登录！';
            $this->getReturn($return);
        }

        //判断会话过期时间
        if($res['session_time'] < time()){
            $return['message'] = '会话已过期，请重新登录';
            $this->getReturn($return);
        }

        $this->user_id = $res['user_id'];
        $this->token = $token;

    }


    /**
     * 刷新token会话时间
     */
    protected function refresh($id)
    {
        $map['user_id'] = $id;
        $map['session_time'] = time()+604800;
        M('users')->save($map);
    }

    /**
     * @param $id       地区表id
     * @return string   地区名称
     */
    protected function get_region_name($id)
    {
        $res =  M('region')->field('region_name')->find($id);
        return $res['region_name'];
    }


    /**
     * 根据路线id查询出关联此路线的物流公司id
     * @param $id       路线id
     * @return array
     */
    protected function getRelationInfo($id)
    {
        return M('wire_relation')->where('wire_id = '.$id)->select();
    }


    /**
     * 获取物流公司信息
     * @param $id       物流公司id
     * @return array    一维数组
     */
    protected function getLogisticsInfo($id)
    {
        if ($id) {
            return M('logistics')->find($id);
        }
    }


    /**
     * 退出登录（客户端）
     */
    protected function exitUser()
    {
        $this->user_id = 0;
        $this->token = '';
    }



    /**
     * 图片上传，支持多图片上传
     * @return array    返回格式： 如果上传文件是：'$_FILES['name']'   则  'name' => '文件路径';
     */
    public function uploadImg($filename = 'else'){
        $upload = new \Think\Upload();// 实例化上传类
        $upload->maxSize   =     3145728 ;// 设置附件上传大小
        $upload->exts      =     array('jpg', 'gif', 'png', 'jpeg');// 设置附件上传类型
        $upload->rootPath  =     './Public/'; // 设置附件上传根目录
        $upload->savePath  =     'Uploads/'.$filename.'/'; // 设置附件上传（子）目录
        // 上传文件
        $info   =   $upload->upload();

        $path = array();
        $return = array('status' => 0, 'message' => '');
        if(!$info) {// 上传错误提示错误信息
            $return['message'] = $upload->getError();
            return $return;
        }else{// 上传成功 获取上传文件信息
            foreach($info as $key=>$file){
//                return $file['savepath'].$file['savename'];
                $path[$key] =  $file['savepath'].$file['savename'];
            }
            $return['status'] = 1;
            $return['path'] = $path;
            return $return;
        }
    }

}