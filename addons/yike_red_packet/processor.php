<?php
/**
 * Created by PhpStorm.
 * User: stevezheng
 * Date: 16/4/5
 * Time: 15:50
 */

class Yike_red_packetModuleProcessor extends WeModuleProcessor {
    public function respond() {
        $content = $this->message['content'];

        //这里定义此模块进行消息处理时的具体过程, 请查看微赞文档来编写你的代码
        global $_W;

        // 扩展 $_W['account'] 及 $_W['member']
        $this->extend_W();

        return $this->respText($_W['member']['nickname']);
    }
}

