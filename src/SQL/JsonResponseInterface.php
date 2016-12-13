<?php
/**
 * Created by PhpStorm.
 * User: jthomann
 * Date: 12/13/16
 * Time: 10:07 AM
 */

namespace AdHocSportsGames\SQL;

/**
 * Interface JsonResponseInterface
 * @package AdHocSportsGames\SQL
 */
interface JsonResponseInterface {
    public function returnResponse($message, $data, $status, $code);
}