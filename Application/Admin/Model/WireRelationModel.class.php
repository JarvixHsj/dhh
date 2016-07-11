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

class WireRelationModel extends CommonModel
{
//    protected $_link = array(
//        'state' => array(
//            'mapping_type' => self::BELONGS_TO,
//            'class_name' => 'region',
//            'foreign_key' => 'wire_state',
//            'mapping_fields' => 'region_name'
//        ),
//        'end' => array(
//            'mapping_type' => self::BELONGS_TO,
//            'class_name' => 'region',
//            'foreign_key' => 'wire_end',
//            'mapping_fields' => 'region_name'
//        )
//        'details' => array(
//            'mapping_type' => self::BELONGS_TO,
//            'class_name' => 'wire_relation',
//            'foreign_key' => 'wire_end',
//            'mapping_fields' => 'region_name'
//        )
//    );

//    protected $_validate = array(
//        array('wire_state','require','起始地址不能为空！'),
//        array('wire_end','require','目的地址不能为空！'),
//    );
//
//
//    public function getOne($id)
//    {
//        return $this->relation(true)->where('wire_id ='.$id)->find();
//    }
}