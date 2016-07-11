<?php
/**
 * Created by PhpStorm.
 * User: Jarvix
 * Date: 2016/2/1
 * Time: 11:32
 */

namespace Api\Controller;
use Think\Controller;

/**
 * Class ElseController
 * @package Api\Controller
 * 其他公用接口
 */
class ElseController extends CommonController
{

    /**
     * 两级地区（省--市）
     */
    public function region(){
        $result = $this->returns();
        $Region = D('region');
        $data = $Region->field('region_id,region_name,region_type')->where('region_type = 1')->select();
        foreach($data as $index=>$item){
            $arr = array();
            $arr = $Region->field('region_id,region_name,region_type')->where('parent_id = '.$item['region_id'])->select();
            $data[$index]['data'] = $arr;
        }

        $result['status'] = 1;
        $result['data'] = $data;
        $this->getReturn($result);
    }


    /**
     * 获取文章
     * @param  $name  根据name值判断要的是那篇文章
     */
    public function getArticlePath(){
        $result = $this->returns();
        $key = I('post.key');
        $map['key'] = $key;
        $data = M('article')->where($map)->find();
            $id = $data['id'];
        //赋值
        $result['status'] = 1;
        $result['data']['url'] = $_SERVER['HTTP_HOST'].U('Home/Article/getArticle',"id=$id");
        $this->getReturn($result);
    }


    /**
     * 获取各个版本的版本号
     */
    public function version(){
        $result = $this->returns();
        $name = I('request.name');

        $map['name'] = $name;
        $data = D('config')->where($map)->find();
        if($data){
            $result['data'] = $data['value'];
        }else{
            $result['data'] = '';
        }
        $result['status'] = 1;
        $this->getReturn($result);
    }


    /**
     * 获取广播消息 （10条）
     */
    public function broadcast()
    {
        $result = $this->returns();
        if($data = M("Broadcast")->where('type = 1')->order('created_at desc')->limit(0,10)->select()){
            $result['data'] = $data;
        }else{
            $result['data'] = array();
            $result['message'] = '没有数据';
        }
        $result['status'] = 1;
        $this->getReturn($result);
    }


    /**
     * 设置客户端jpushid
     */
    public function setUserJpush()
    {
        $result = $this->returns();

        $id     = I('request.id');
        $jpush_id = I('request.jpush_id');

        $as = D('Users')->where('user_id = '.$id)->setField('jpush_id', $jpush_id);
        if($as === false){
            $result['status'] = 0;
            $result['message'] = '更新失败';
        }else{
            $result['status'] =  1;
            $result['message'] = '更新成功';
        }

        $this->getReturn($result);
    }


    /**
     * 设置物流断jpushid
     */
    public function setLogisticsJpush()
    {
        $result = $this->returns();

        $id     = I('request.id');
        $jpush_id = I('request.jpush_id');

        $as = D('Logistics')->where('logistics_id = '.$id)->setField('jpush_id', $jpush_id);
        if($as === false){
            $result['status'] = 0;
            $result['message'] = '更新失败';
        }else{
            $result['status'] =  1;
            $result['message'] = '更新成功';
        }

        $this->getReturn($result);
    }
}