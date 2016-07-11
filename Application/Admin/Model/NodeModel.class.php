<?php
namespace Admin\Model;

/**
 * Node
 * 节点模型
 */
class NodeModel extends CommonModel {
	//表单的数据验证
	protected $_validate = array (
	);

   //数据的自动填充
   protected $_auto = array (
   		array('created_at', 'time', 1, 'function'), // 对create_at字段在新增的时候写入当前时间戳
        array('updated_at', 'time', 2, 'function'), // 对update_at字段在更新的时候写入当前时间戳
   );
}