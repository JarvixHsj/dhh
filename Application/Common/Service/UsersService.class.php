<?php
/**
 * Created by PhpStorm.
 * User: SEEJOYS
 * Date: 2016/1/25
 * Time: 21:37
 */

namespace Common\Service;

class UsersService extends CommonService
{

    protected function getModelName() {
        return 'Users';
    }

    public function line()
    {
        return 'hello';
    }
}