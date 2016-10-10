<?php

namespace Web\Controller;
use W\Controller;

class HelpController extends Controller{

    public function index(){
        global $_W;
        $help_url = 'http://wxlogin.pjialin.com/App/Public/help/help.html';
        $help_conn = file_get_contents($help_url);
        include $this->display();
    }

}
