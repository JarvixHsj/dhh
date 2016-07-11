<?php
namespace Admin\Service;

/**
 * PushService
 */
class PushService extends CommonService {
    /**
     * 添加
     * @param  array $admin 信息
     * @return array
     */
    public function add($data) {
        $Push = $this->getM();

        $Push->startTrans();

        //添加到推送表
        $push_id = $Push->add($data);

        //添加到用户消息表
        $UserRow = M('users')->field('user_id')->where('user_status = 1')->select();
        $UserMess = M('message');

        foreach($UserRow as $key){
            $UserInfo = array();
            $UserInfo['user_id'] = $key['user_id'];
            $UserInfo['message_content'] = $data['content'];
            $UserInfo['message_title'] = $data['title'];
            $UserInfo['message_time'] = time();
            $UserInfo['message_read'] = 0;
            $UserInfo['message_status'] = 1;
            $UserInfo['push_id'] = $push_id;
            $UserMess->add($UserInfo);
        }

        //添加到物流消息表
        $LogiRow = M("logistics")->field('logistics_id')->where('logistics_id > 1000 AND logistics_status = 1')->select();
        $LogiMess = M('logimess');
        foreach($LogiRow as $val){
            $LogiInfo = array();
            $LogiInfo['logistics_id'] = $val['logistics_id'];
            $LogiInfo['content'] = $data['content'];
            $LogiInfo['title'] = $data['title'];
            $LogiInfo['time'] = time();
            $LogiInfo['read'] = 0;
            $LogiInfo['status'] = 1;
            $LogiInfo['push_id'] = $push_id;
            $LogiMess->add($LogiInfo);
        }

        $this->bbs_push_client_all($data['content'],$data['title']);
        $this->bbs_push_server_all($data['content'],$data['title']);

        if (false === $push_id) {
            $Push->rollback();
            return $this->errorResultReturn('系统出错了！');
        }
        $Push->commit();

        return $this->resultReturn(true);
    }



    /**
     * 是否存在
     * @param  int     $id id
     * @return boolean
     */
    public function existComm($id) {
        return !is_null($this->getM()->find($id));
    }


    /**
     * 删除账户并且删除数据表
     * @param  int     $id 需要删除账户的id
     * @return boolean
     */
    public function delete($id) {
        $Dao = $this->getM();

        $model = $Dao->find($id);
        if(!$model)
            return $this->resultReturn(false);

        $delStatus = $Dao->delete($id);

        if (false === $delStatus) {
            return $this->resultReturn(false);
        }

        M('message')->where('push_id = '.$id)->delete();
        M('logimess')->where('push_id = '.$id)->delete();

        return $this->resultReturn(true);
    }

    protected function getModelName() {
        return 'Push';
    }
}
