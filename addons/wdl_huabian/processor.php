<?php
/**
 * 花边新闻模块定义
 *
 * @author 微赞科技
 * @url http://www.012wz.com/
 */
defined('IN_IA') or exit('Access Denied');

class Wdl_huabianModuleProcessor extends WeModuleProcessor
{

    public function respond()
    {
        $content = $this->message['content'];
        // 这里定义此模块进行消息处理时的具体过程, 请查看微赞文档来编写你的代码
        
        $api = 'http://www.weiduola.cn/api/huabian.php?mod=get';
        $data = file_get_contents($api);
        $data = json_decode($data);
        $news = array();
        foreach ($data as $item) {
            $news[] = array(
                'title' => $item->title,
                'description' => $item->description,
                'url' => $item->url,
                'picurl' => $item->picurl
            );
        }
        
        return $this->respNews($news);
    }
}