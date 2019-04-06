<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/3/16
 * Time: 18:09
 */

namespace rabbit\casbin;


use Casbin\Exceptions\CasbinException;
use Casbin\Persist\Adapter as AdapterContract;
use Casbin\Persist\AdapterHelper;
use rabbit\casbin\Model\CasbinRule;

/**
 * Class Adapter
 * @package rabbit\casbin
 */
class Adapter implements AdapterContract
{
    use AdapterHelper;
    /** @var CasbinRule */
    protected $casbinRule;

    public function __construct(CasbinRule $casbinRule)
    {
        $this->casbinRule = $casbinRule;
    }

    /**
     * @param array $ptype
     * @param array $rule
     */
    public function savePolicyLine(array $ptype, array $rule)
    {
        $col['ptype'] = $ptype;
        foreach ($rule as $key => $value) {
            $col['v' . strval($key) . ''] = $value;
        }
        $ar = clone $this->casbinRule;
        $ar->setAttributes($col);
        $ar->save();
    }

    /**
     * @param \Casbin\Model\Model $model
     * @return mixed|void
     */
    public function loadPolicy($model)
    {
        $ar = clone $this->casbinRule;
        $rows = $ar->find()->all();
        foreach ($rows as $row) {
            $line = implode(', ', array_slice(array_values($row->toArray()), 1));
            $this->loadPolicyLine(trim($line), $model);
        }
    }

    /**
     * @param \Casbin\Model\Model $model
     * @return bool
     */
    public function savePolicy($model)
    {
        foreach ($model->model['p'] as $ptype => $ast) {
            foreach ($ast->policy as $rule) {
                $this->savePolicyLine($ptype, $rule);
            }
        }
        foreach ($model->model['g'] as $ptype => $ast) {
            foreach ($ast->policy as $rule) {
                $this->savePolicyLine($ptype, $rule);
            }
        }
        return true;
    }

    /**
     * @param string $sec
     * @param string $ptype
     * @param array $rule
     * @return mixed|void
     */
    public function addPolicy($sec, $ptype, $rule)
    {
        return $this->savePolicyLine($ptype, $rule);
    }

    /**
     * @param string $sec
     * @param string $ptype
     * @param array $rule
     * @return mixed
     */
    public function removePolicy($sec, $ptype, $rule)
    {
        $result = $this->casbinRule->where('ptype', $ptype);
        foreach ($rule as $key => $value) {
            $result->where('v' . strval($key), $value);
        }
        return $result->delete();
    }

    /**
     * @param string $sec
     * @param string $ptype
     * @param int $fieldIndex
     * @param mixed ...$fieldValues
     * @return mixed|void
     * @throws CasbinException
     */
    public function removeFilteredPolicy($sec, $ptype, $fieldIndex, ...$fieldValues)
    {
        throw new CasbinException('not implemented');
    }
}