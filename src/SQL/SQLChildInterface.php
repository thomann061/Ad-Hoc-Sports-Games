<?php
/**
 * Created by PhpStorm.
 * User: jthomann
 * Date: 12/7/16
 * Time: 8:10 AM
 */

namespace AdHocSportsGames\SQL;

interface SQLChildInterface {
    public function selectAll($pId);
    public function select($pId, $cId);
    public function insert($pId, $obj);
    public function fullyUpdate($pId, $cId, $obj);
    public function partiallyUpdate($pId, $cId, $obj);
    public function delete($cId, $pId);
}