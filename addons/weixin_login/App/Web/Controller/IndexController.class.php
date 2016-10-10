<?php

namespace Web\Controller;
use W\Controller;

class IndexController extends Controller{
    protected $_dev = array(
        'add' => 'http://wxlogin.pjialin.com/Dev/add',
        'index' => 'http://wxlogin.pjialin.com/Dev/index',
        'update' => 'http://wxlogin.pjialin.com/Dev/update',
        'remove' => 'http://wxlogin.pjialin.com/Dev/remove',
        'disabled' => 'http://wxlogin.pjialin.com/Dev/disabled',
    );
    public function __construct()
    {
        parent::__construct();
        $this->appid = $this->W['account']['key'];
    }

    public function index(){
        global $_W;
        $data = array(
            'appid' => $this->appid,
        );
        $lists = curl($this->_dev['index'],$data);
        $lists = json_decode($lists,true);
        if($lists['Code']%2==0)
            $_lists = $lists['Data']['list'];
        include $this->display();
    }

    public function add(){
        global $_W;
        if(IS_POST){
            $data = array(
                'appid' => $this->appid,
            );
            $data = array_merge($_POST,$data);
            echo $lists = curl($this->_dev['add'],$data);
            die;
        }
        include $this->display();
    }
    public function edit(){
        global $_W;
        $id = I('get.id');
        if($id){
            if(IS_POST){
                $data = array(
                    'appid' => $this->appid,
                    'id' => $id,
                );
                $data = array_merge($_POST,$data);
                echo $lists = curl($this->_dev['update'],$data);
                die;
            }
            $data = array(
                'appid' => $this->appid,
                'id' => $id,
            );
            $lists = curl($this->_dev['index'],$data);
            $lists = json_decode($lists,true);
            if($lists['Code']%2==0)
                $info = $lists['Data']['list'];
            include $this->display();
        }
    }
    public function del(){
        global $_W;
        $id = I('get.id');
        if($id){
            $data = array(
                'appid' => $this->appid,
                'id' => $id,
            );
            echo $lists = curl($this->_dev['remove'],$data);
            die;
        }
    }
    public function disabled(){
        global $_W;
        $id = I('get.id');
        if($id){
            $data = array(
                'appid' => $this->appid,
                'id' => $id,
            );
            echo $lists = curl($this->_dev['disabled'],$data);
            die;
        }
    }
}
