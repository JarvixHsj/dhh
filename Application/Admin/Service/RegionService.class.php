<?php
/**
 * Created by PhpStorm.
 * User: Jarvix
 * Date: 2016/1/28
 * Time: 16:44
 */

namespace Admin\Service;

class RegionService extends CommonService
{

    protected function getModelName() {
        return 'Region';
    }

    public function getName($id){
        $data = $this->getM()->field('region_name')->find($id);
        return $data['region_name'];
    }
}