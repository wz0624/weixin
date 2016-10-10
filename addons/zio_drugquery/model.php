<?php
defined('IN_IA') or exit('Access Denied');
function addScanInfo($info)
{
    $scanCode = $info['code'];
    $drugId   = getDrugId($scanCode);
    $drug     = getProductByCode($drugId);
    if (empty($drugId)) {
    }
    if ($drug) {
        $sql = 'select id ' . tablename('drug_code') . ' where id=:id';
        $id  = pdo_fetchcolumn($sql, array(
            ':id' => $scanCode
        ));
        if (empty($id)) {
        }
    }
}
function toDrugModel($info)
{
    $map   = array(
        '药品通用名' => 'name',
        '药品名称' => 'name',
        '生产企业' => 'company',
        '生产日期' => 'company',
        '剂型代码' => 'company',
        '制剂规格' => 'company',
        '剂型单位' => 'batch',
        '包装规格' => 'batch',
        '包装单位' => 'batch',
        '批准文号' => 'batch',
        '生产批次' => 'batch'
    );
    $model = array();
    foreach ($info as $k => $v) {
        $v   = str_replace(array(
            '【',
            '：',
            '】'
        ), array(
            '',
            ':',
            ':'
        ), $v);
        $val = explode(':', $v);
        if (count($val) == 2) {
            if (array_key_exists($val[0], $map)) {
                $model[$map[$val[0]]] = trim($val[1]);
            }
        }
    }
    return $model;
}
function getDrugId($code)
{
    if (preg_match('/8(\d{6})(\d{9})(\d{4})/', $code, $res)) {
        return $res[1];
    } else {
        return '';
    }
}
function getProductByCode($productCode)
{
    $sql = 'select * from ' . tablename('drug_product') . ' where productcode=:code';
    return pdo()->fetch($sql, array(
        ':code' => $productCode
    ));
}