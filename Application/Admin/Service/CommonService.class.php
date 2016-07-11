<?php
namespace Admin\Service;

/**
 * CommonService
 */
abstract class CommonService {
    /**
     * 得到数据行数
     * @param  array $where
     * @return int
     */
    public function getCount(array $where) {
        return $this->getM()->where($where)->count();
    }

    /**
     * 得到分页数据
     * @param  array $where    分页条件
     * @param  int   $firstRow 起始行
     * @param  int   $listRows 行数
     * @return array
     */
    public function getPagination($where, $fields, $order, $firstRow, $listRows) {
        // 是否关联模型
        $M = $this->isRelation() ? $this->getD()->relation(true)
                                 : $this->getM();
        // 需要查找的字段
        if (isset($fields)) {
            $M = $M->field($fields);
        }

        // 条件查找
        if (isset($where)) {
            $M = $M->where($where);
        }

        // 数据排序
        if (isset($order)) {
            $M = $M->order($order);
        }

        // 查询限制
        if (isset($firstRow) && isset($listRows)) {
            $M = $M->limit($firstRow . ',' . $listRows);
        } else if (isset($listRows) && isset($firstRow)) {
            $M = $M->limit($listRows);
        }

//        $M->select();
//        var_dump($M->getLastSql());die;
        return $M->select();
    }

    /**
     * 返回结果值
     * @param  int   $status
     * @param  fixed $data
     * @return array
     */
    protected function resultReturn($status, $data) {
        return array('status' => $status,
                     'data' => $data);
    }

    /**
     * 返回错误的结果值
     * @param  string $error 错误信息
     * @return array         带'error'键值的数组
     */
    protected function errorResultReturn($error) {
        return $this->resultReturn(false, array('error' => $error));
    }

    /**
     * 得到M
     * @return Model
     */
    protected function getM() {
        return M($this->getModelName());
    }

    /**
     * 得到D
     * @return Model
     */
    protected function getD() {
        return D($this->getModelName());
    }

    /**
     * 是否关联查询
     * @return boolean
     */
    protected function isRelation() {
        return true;
    }


    /**
     * 极光推送（推送到所有用户端）161a3797c8072286576   120c83f76027552ea60
     */
    public function bbs_push_client_all($content,$title){
        import("Org.JPush.JPush");
        $client = new \JPush(C('CLIENT_APPKEY'), C('CLIENT_MASTER'));
//        $result = $client->push()
//            ->setPlatform('all')
//            ->addAllAudience()
//            ->setNotificationAlert($content,$title)
//            ->send();

        $result = $client->push()
            ->setPlatform('ios', 'android')
            ->addAlias('alias1')
            ->addTag(array('tag1', 'tag2'))
            ->setNotificationAlert($title)
            ->addAndroidNotification($content, $title, 1, array("key1"=>"value1", "key2"=>"value2"))
            ->addIosNotification($content, 'sound.mp3', '+1', true, 'iOS category', array("key1"=>"value1", "key2"=>"value2"))
            ->setMessage($content, $title)
            ->setOptions(100000, 3600, null, true)
            ->send();
    }

    /**
     * 极光推送（推送到所有物流端）161a3797c8072286576   120c83f76027552ea60
     */
    public function bbs_push_server_all($content,$title){
        import("Org.JPush.JPush");
        $client = new \JPush(C('SERVER_APPKEY'), C('SERVER_MASTER'));
//        $result = $client->push()
//            ->setPlatform('all')
//            ->addAllAudience()
//            ->setNotificationAlert($content,$title)
//            ->send();

        $result = $client->push()
            ->setPlatform('ios', 'android')
            ->addAlias('alias1')
            ->addTag(array('tag1', 'tag2'))
            ->setNotificationAlert($title)
            ->addAndroidNotification($content, $title, 1, array("key1"=>"value1", "key2"=>"value2"))
            ->addIosNotification($content, 'sound.mp3', '+1', true, 'iOS category', array("key1"=>"value1", "key2"=>"value2"))
            ->setMessage($content, $title)
            ->setOptions(100000, 3600, null, true)
            ->send();
    }


    /**
     * 得到模型的名称
     * @return string
     */
    protected abstract function getModelName();
}
