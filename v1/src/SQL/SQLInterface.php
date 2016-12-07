<?php
/**
 * Created by PhpStorm.
 * User: jthomann
 * Date: 12/7/16
 * Time: 8:10 AM
 */

namespace AdHocSportsGames\SQL;

interface SQLInterface {
    public function selectAll();
    public function select($id);
    public function insert($obj);
    public function fullyUpdate($obj, $id);
    public function partiallyUpdate($obj, $id);
    public function delete($id);
}