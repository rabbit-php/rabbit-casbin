<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/3/16
 * Time: 17:59
 */

namespace rabbit\casbin\Model;


use rabbit\activerecord\ActiveRecord;
use rabbit\db\ConnectionInterface;
use rabbit\db\Manager;

/**
 * Class CasbinRule
 * @package rabbit\casbin\Model
 */
class CasbinRule extends ActiveRecord
{
    /**
     * @return string Active Record
     */
    public static function tableName()
    {
        return getDI('casbin')->getConfig()['database']['casbin_rules_table'];
    }

    /**
     * @return ConnectionInterface
     */
    public static function getDb(): ConnectionInterface
    {
        $dbKey = getDI('casbin')->getConfig()['database']['connection'] ?: 'db';
        /** @var Manager $db */
        $db = getDI('db');
        return $db->getConnection($dbKey);
    }

    public function rules()
    {
        return [
            [['ptype', 'v0'], 'required'],
            [['ptype', 'v0', 'v1', 'v2', 'v3', 'v4', 'v5'], 'safe'],
        ];
    }
}