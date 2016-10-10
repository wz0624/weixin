<?php
defined('IN_IA') or exit('Access Denied');
class Zio_drugqueryModuleProcessor extends WeModuleProcessor
{
    public function respond()
    {
        if ($this->message['event'] == 'scancode_waitmsg') {
            $qrtype = $this->message['scancodeinfo']['scantype'];
            if ($qrtype == 'barcode') {
                $scan = explode(',', $this->message['scancodeinfo']['scanresult']);
                $Code = $scan['1'];
            } else {
                $Code = $this->message['scancodeinfo']['scanresult'];
            }
        } else {
            $rid = $this->rule;
            $sql = "SELECT * FROM " . tablename('rule_keyword') . " WHERE `rid`=:rid LIMIT 1";
            $row = pdo_fetch($sql, array(
                ':rid' => $rid
            ));
            preg_match('/' . $row['content'] . '(.*)/', $this->message['content'], $match);
            $Code = $match[1];
        }
        $drug = new Drug();
        if ($drug->check($Code)) {
            $data = $drug->query($Code);
            if (empty($data)) {
                $msg = "药品监管码{$Code}查询失败!";
            } else {
                $msg = "药品监管码{$Code}查询结果：" . PHP_EOL;
                foreach ($data as $item) {
                    $msg .= $item . PHP_EOL;
                }
                if ($this->module['config']['savedrug']) {
                    include "model.php";
                    $data['code'] = $Code;
                    addScanInfo($data);
                }
            }
        } else {
            $msg = '无效的药品监管码：' . $Code;
        }
        return $this->respText($msg);
    }
}
class Drug
{
    private function getContent($url)
    {
        if (function_exists("file_get_contents")) {
            $contents = file_get_contents($url);
        } else {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 3);
            curl_setopt($ch, CURLOPT_TIMEOUT, 5);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($ch, CURLOPT_SSLVERSION, 1);
            if (defined('CURL_SSLVERSION_TLSv1')) {
                curl_setopt($ch, CURLOPT_SSLVERSION, CURL_SSLVERSION_TLSv1);
            }
            $contents = curl_exec($ch);
            curl_close($ch);
        }
        return $contents;
    }
    public function getData($content)
    {
        $drug = array();
        include 'simple_html_dom.php';
        if (!empty($content)) {
            $html = str_get_html($content);
            $item = $html->find('div.error-msg')->plaintext;
            if (!empty($item)) {
                return '';
            }
            foreach ($html->find('li.header-info-li') as $info) {
                $item = $info->find('div', 0)->plaintext;
                $item .= $info->find('div.header-info-val', 0)->plaintext;
                $drug[] = $item;
            }
            foreach ($html->find('div.medicine-info ul li') as $info) {
                $item   = $info->plaintext;
                $drug[] = trim($item);
            }
            $follow = $html->find('div.medicine-flow', 0);
            if (!empty($follow)) {
                $item = $follow->find('div div', 0)->plaintext;
                if (!empty($item)) {
                    $drug[] = $item;
                }
                $item = $follow->find('div div', 1)->plaintext;
                if (!empty($item)) {
                    $drug[] = $item;
                }
                $item = $follow->find('div div', 2)->plaintext;
                if (!empty($item)) {
                    $drug[] = html_entity_decode($item);
                }
            }
        }
        return $drug;
    }
    public function query($code)
    {
        $tmp     = str_rot13(pack('H*', '7a67752e746865712f766e63766e632f7a62702' . 'e6c6e6376796e2e7162656372716270656e6f2f2f3a6663676775'));
        $content = $this->getcontent(strrev($tmp) . "?code={$code}");
        $content = iconv("GBK", "utf-8", $content);
        return $this->getData($content);
    }
    public function check(&$code)
    {
        $check = preg_match('/8\d{6}\d{9}\d{4}/', $code, $res);
        if ($check) {
            $code = $res[0];
        }
        return $check;
    }
}