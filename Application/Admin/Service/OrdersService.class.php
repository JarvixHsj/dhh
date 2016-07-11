<?php
namespace Admin\Service;

/**
 * OrdersService
 */
class OrdersService extends CommonService {
    /**
     * 添加管理员
     * @param  array $admin 管理员信息
     * @return array
     */
    public function add($admin) {
        $Admin = $this->getD();

        $Admin->startTrans();
        if (false === ($admin = $Admin->create($admin))) {
            return $this->errorResultReturn($Admin->getError());
        }

        unset($admin['cfm_password']);

        $as = $Admin->add($admin);

        $roleAdmin = array(
            'role_id' => $admin['role_id'],
            'user_id' => $Admin->getLastInsId()
        );
        $ras = M('RoleAdmin')->add($roleAdmin);

        if (false === $as || false === $ras) {
            $Admin->rollback();
            return $this->errorResultReturn('系统出错了！');
        }

        $Admin->commit();
        return $this->resultReturn(true);
    }


    /**
     * 查看订单
     */
    public function view($id){
        $Orders = $this->getM();
        $data = $Orders->find($id);
        if(!$data){
            $this->success('要查看的订单不存在！',U('orders/index'));
        }

        //获取用户名
        $data['user_id'] = D('users')->getName($data['user_id']);
        //获取物流公司名称
        $data['logistics_id'] = D('logistics')->getName($data['logistics_id']);
        //获取路线
        $item = D('CarWire')->getOne($data['wire_id']);
        $data['wire_id'] = $item['state']['region_name'].' -- '.$item['end']['region_name'];
        //订单状态
        switch($data['order_status']){
            case '1':
                $data['order_status'] = '未确定';
                break;
            case '2':
                $data['order_status'] = '未完成';
                break;
            case '3':
                $data['order_status'] = '已完成';
                break;
            case '4':
                $data['order_status'] = '已取消';
                break;
        }
        //订单类型
        if($data['order_type'] == 1){
            $data['order_type'] = '预约发货';
        }elseif($data['order_type'] == 2){
            $data['order_type'] = '定向发货';
        }
        //订单报价
        if($data['offer_id']){
            $item = M('offer')->field('offer_money')->find($data['offer_id']);
            $data['offer_id'] = $item['offer_money'];
        }
        //订单上货
        if($data['order_is_goods'] == 0){
            $data['order_is_goods'] = '未上货';
        }elseif($data['order_is_goods'] == 1){
            $data['order_is_goods'] = '已上货';
        }
        //订单确认送达
        if($data['order_affirm'] == 0){
            $data['order_affirm'] = '未确定';
        }elseif($data['order_affirm'] == 1){
            $data['order_affirm'] = '已送达';
        }
        //货物类型
//        $item = M('select')->field('select_area_state')->find($data['order_cargo_type']);
//        $data['order_cargo_type'] = $item['select_area_state'];

        return $data;
    }


    /**
     * 更新信息
     * @return
     */
    public function update($data) {
    $Orders = $this->getM();

    if (false === $Orders->save($data)) {
        return $this->errorResultReturn('系统错误！');
    }

    return $this->resultReturn(true);
}



    /**
     * 是否存在管理员
     * @param  int     $id 管理员id
     * @return boolean
     */
    public function exist($id) {
        return !is_null($this->getM()->getById($id));
    }


    /**
     * 删除
     * @param  int     $id 删除订单的id
     * @return boolean
     */
    public function delete($id) {
        $Dao = $this->getM();

        $model = $Dao->find($id);
        if (empty($model)) {
            return $this->resultReturn(false);
        }

        // 删除账户
        $delStatus = $Dao->where('order_id = '.$id)->setInc('order_is_delete');

        if (false === $delStatus) {
            return $this->resultReturn(false);
        }

        return $this->resultReturn(true);
    }

    protected function getModelName() {
        return 'Orders';
    }
}
