<?php
$sql = "
CREATE TABLE IF NOT EXISTS `ims_weliam_indiana_withdraw` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '唯一标识',
  `uniacid` int(11) NOT NULL COMMENT '公众号id',
  `openid` varchar(225) NOT NULL COMMENT '提现人',
  `createtime` varchar(45) NOT NULL COMMENT '提现时间',
  `number` int(11) NOT NULL COMMENT '金额',
  `status` int(2) NOT NULL COMMENT '提现状态（1：等待提现；2：提现成功；3提现失败）',
  `type` int(2) NOT NULL COMMENT '提现方式（1：微信；2支付宝；3京东钱包；4：百度钱包）',
  `order_no` varchar(225) NOT NULL COMMENT '提现订单号',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;
";
pdo_run($sql);

