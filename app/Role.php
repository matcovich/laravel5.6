<?php
/**
 * Created by PhpStorm.
 * User: Hector
 * Date: 24-12-2018
 * Time: 12:43
 */

namespace App;


class Role
{

    public static function getList()
    {
        return['admin', 'user'];
    }
}