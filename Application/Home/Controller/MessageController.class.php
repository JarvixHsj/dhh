<?php
/**
 * Created by PhpStorm.
 * User: Jarvix
 * Date: 2016/3/21
 * Time: 19:37
 */

namespace Home\Controller;


class MessageController extends CommonController
{

    /**
     * 客户端消息详情
     */
    public function clientMess()
    {
        $id = I("request.id");

        $data = M('message')->find($id);

        $this->assign('data',$data);
        $this->display('message');
    }



    /**
     * 客户端消息详情
     */
    public function logisticsMess()
    {
        $id = I("request.id");

        $data = M('logimess')->find($id);

        $this->assign('data',$data);
        $this->display('message_logi');
    }
}