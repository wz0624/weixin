<?php
defined('IN_IA') or exit('Access Denied');
class Cyl_wxweizhangModuleProcessor extends WeModuleProcessor
{
    private $tb_article = 'cyl_wxwenzhang_article';
    public function respond()
    {
        global $_W, $_GPC;
        if (!$this->inContext) {
            $news = '请输入关键词搜索';
            $this->beginContext();
            return $this->respText($news);
        } else {
            $content = $this->message['content'];
            $list    = pdo_fetchall("SELECT id,title,thumb,pic,createtime,click,pcate,description FROM " . tablename($this->tb_article) . " WHERE uniacid = '{$_W['uniacid']}' AND title LIKE '%{$content}%' ORDER BY id DESC");
            $news    = array();
            foreach ($list as $key => $item) {
                if ($key <= 9) {
                    $news[] = array(
                        'title' => $item['title'],
                        'description' => $item['description'],
                        'url' => $this->createMobileUrl('detail', array(
                            'id' => $item['id'],
                            'op' => 'detail'
                        )),
                        'picurl' => $item['thumb']
                    );
                }
            }
            $this->endContext();
        }
        return $this->respNews($news);
    }
}