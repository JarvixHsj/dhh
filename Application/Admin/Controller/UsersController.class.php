<?php
namespace Admin\Controller;

/**
 * UsersController
 * 客户端信息
 */
class UsersController extends CommonController {
    /**
     * 管理列表
     * @return
     */
    public function index() {

        return parent::index();
    }

    /**
     * ajax信息列表
     * @return
     */
    protected function ajaxList() {
        $result = $this->getAjaxList('Users');
        $this->gridReturn($result['rows'], $result['total']);
    }


    /**
     * 切换用户状态
     * @return
     */
    public function toggleStatus() {
        $UserService = D('Users', 'Service');
        if (!isset($_POST['id'])
            || !$UserService->existUser($_POST['id'])) {
            return $this->errorReturn('无效的操作！');
        }
        if (!$_POST['status']) {
            $UserService->setStatus($_POST['id'], 1);
        } else {
            $UserService->setStatus($_POST['id'], 0);
        }

        $info = $_POST['status'] ? '禁用成功！' : '启用成功！';
        $this->successReturn($info);
    }


    /**
     * 添加
     * @return
     */
    public function add() {
        $this->display();
    }

    /**
     * 编辑页
     * @return
     */
    public function edit() {
        if (!isset($_GET['id'])
        	  || !D('Users', 'Service')->existUser($_GET['id'])) {
            return $this->error('需要编辑的用户信息不存在！');
        }
        $row = M('Users')->find($_GET['id']);
        if (empty($row)) {
            $this->error('您需要编辑的用户不存在！');
        }

        $this->assign('row', $row);
        $this->display();
    }

    /**
     * 创建信息
     * @return
     */
    public function create() {
        if (!isset($_POST['form'])) {
            return $this->errorReturn('无效的操作！');
        }

        if($_POST['form']['user_id']){
            $result = D('Users', 'Service')->update($_POST['form']);
        }else{
            $result = D('Users', 'Service')->add($_POST['form']);
        }
        if (!$result['status']) {
            return $this->errorReturn($result['data']['error']);
        }

        return $this->successReturn('添加成功！');
    }

    /**
     * 更新信息
     * @return
     */
    public function update() {
        $UserService = D('Users', 'Service');
        if (!isset($_POST['form'])
            || !$UserService->existUser($_POST['form']['user_id'])) {
            return $this->errorReturn('无效的操作！');
        }

        $result = $UserService->update($_POST['form']);
        if (!$result['status']) {
            return $this->errorReturn($result['data']['error']);
        }

        return $this->successReturn('更新管理员信息成功！');
    }

    /**
     * 删除
     * @return
     */
    public function delete() {
        $_POST['id'] = $_POST['id']['0'];
        if (!isset($_POST['id'])) {
            $this->errorReturn('您需要删除的用户不存在！');
        }

        $result = D('Users', 'Service')->delete($_POST['id']);
        if (false === $result['status']) {
            return $this->errorReturn($result['info']);
        }

        $this->successReturn("删除用户成功！");
    }

    public function identityCheck()
    {

        if (!isset($_GET['id']) || !D('Users', 'Service')->existUser($_GET['id'])) {
            return $this->errorReturn('需要编辑的查看的公司不存在！');
        }

        $row = M('Users')->field('id_positive, is_positive, id_reverse, is_reverse, user_id, user_name, created_at')->find($_GET['id']);
        if (empty($row)) {
            $this->error('您需要编辑的用户不存在！');
        }
        $created_at = $row['created_at'] + 1296000;
        
        //判断认证状态
        if($row['is_positive'] == 1){
            $positive['message'] = '已认证';
        }elseif ($row['id_positive'] && $row['is_positive'] == 0) {
            $positive['message'] = '认证中(上传了图片)';
        }elseif ($row['is_positive'] == 2) {
            $positive['message'] = '认证失败';
        }else{
            $positive['message'] = '未上传图片';
        }
        if($row['is_reverse'] == 1){
            $reverse['message'] = '已认证';
        }elseif ($row['id_reverse'] && $row['is_reverse'] == 0) {
            $reverse['message'] = '认证中(上传了图片)';
        }elseif ($row['is_reverse'] == 2) {
            $reverse['message'] = '认证失败';
        }else{
            $reverse['message'] = '未上传图片';
        }
        if($created_at <= time()){
            $positive['message'] = '不需要认证(注册15天内)';
            $reverse['message'] = '不需要认证(注册15天内)';
        }

        $this->assign('positive', $positive);
        $this->assign('reverse', $reverse);
        $this->assign('row', $row);
        $this->display();
    }

    /**
     * @return 
     */
    public function checkPositive()
    {
        if (!isset($_POST['id']) || !D('Users', 'Service')->existUser($_POST['id'])) {
            return $this->errorReturn('需要编辑的用户不存在！');
        }
        $Model = D('Users');
        $row = $Model->field('id_positive, is_positive, user_id, user_name, created_at')->where('user_id = '. $_POST['id'])->find();
        $created_at = $row['created_at'] + 1296000;
        if($created_at <= time()){
            return $this->successReturn('注册15天内不需要认证，等过15天后再认证！');
        }

        if($row['id_positive'] == ''){
            return $this->errorReturn('该用户还未上传正面身份证照片');
        }elseif ($row['id_positive'] && $row['is_positive'] == 0) {
            $as = $Model->where('user_id = '.$_POST['id'])->setField('is_positive',1);
            if($as === false){
                return $this->errorReturn('提交审核失败，请刷新后重试！');
            }
            return $this->successReturn('提交审核成功,请刷新页面着看状态');
        }elseif ($row['is_positive'] == 1) {
            return $this->successReturn('已审核通过了，无需再次审核！');
        }else{
            return $this->errorReturn('提交审核失败，请刷新后重试！');
        }
    }


    /**
     * @return 
     */
    public function checkReverse()
    {
        if (!isset($_POST['id']) || !D('Users', 'Service')->existUser($_POST['id'])) {
            return $this->errorReturn('需要编辑的用户不存在！');
        }
        $Model = D('Users');
        $row = $Model->field('id_reverse, is_reverse, user_id, user_name, created_at')->where('user_id = '.$_POST['id'])->find();
        $created_at = $row['created_at'] + 1296000;
        if($created_at <= time()){
            return $this->successReturn('注册15天内不需要认证，等过15天后再认证！');
        }

        if($row['id_reverse'] == ''){
            return $this->errorReturn('该用户还未上传反面身份证照片');
        }elseif ($row['id_reverse'] && $row['is_reverse'] == 0) {
            $as = $Model->where('user_id = '.$_POST['id'])->setField('is_reverse',1);
            if($as === false){
                return $this->errorReturn('提交审核失败，请刷新后重试！');
            }
            return $this->successReturn('提交审核成功,请刷新页面着看状态');
        }elseif ($row['is_reverse'] == 1) {
            return $this->successReturn('已审核通过了，无需再次审核！');
        }else{
            return $this->errorReturn('提交审核失败，请刷新后重试！');
        }
    }

    //上传
    public function img_upload() {
        echo parent::uploadFile(array('jpg', 'gif', 'png', 'jpeg'), 'user');
    }



}
