<?php
namespace Admin\Service;

/**
 * SuggestService
 */
class SuggestService extends CommonService {


    /**
     * 是否存在
     * @param  int     $id
     * @return boolean
     */
    public function existSugg($id) {
        return !is_null($this->getM()->getById($id));
    }


    /**
     * 删除
     * @param  int     $id 需要删除的id
     * @return boolean
     */
    public function delete($id) {
        $Dao = $this->getD();

        // 删除账户
        $delStatus = $Dao->delete($id);

        if (false === $delStatus) {
            return $this->resultReturn(false);
        }

        return $this->resultReturn(true);
    }

    protected function getModelName() {
        return 'Suggest';
    }
}
