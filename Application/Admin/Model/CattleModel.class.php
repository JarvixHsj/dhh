<?php
/**
 * Created by PhpStorm.
 * User: Jarvix
 * Date: 2016/1/28
 * Time: 15:31
 */

namespace Admin\Model;
use Api\Controller\CommonController;
use Think\Model;

class CattleModel extends CommonModel
{

    protected $_validate = array(
        array('start','require','起始地址不能为空！'),
        array('end','require','目的地址不能为空！'),
    );


}