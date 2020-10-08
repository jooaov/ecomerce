<?php
namespace Hcode;

use Rain\Tpl;

class Page {
    
    private $tpl;
    private $options = [];
    //dados default
    private $defaults = [
        "header"=>true,
        "footer"=>true,
        "data"=>[]
    ];
    public function __construct($opts = array(),$opts_dir = "/views/")
    {
        //se existir opts subustitui o default
        $this ->options = array_merge($this->defaults,$opts);
        $config = array(
            "tpl_dir"       => $_SERVER["DOCUMENT_ROOT"].$opts_dir,
            "cache_dir"     => $_SERVER["DOCUMENT_ROOT"]."/view-cache/",
            "debug"         => false // set to false to improve the speed
           );
        Tpl::configure( $config );

        $this->tpl=new Tpl;
        $this->setData($this->options["data"]);
        if($this ->options["header"] == true)
            $this->tpl->draw("header");

    }

    private function setData($data = array()){
        foreach ($data as $key => $value) {
            $this->tpl->assign($key,$value);
        }
    }

    public function setTpl($nome,$data=array(),$returnHTML = false){
        $this->setData($data);
        $this->tpl->draw($nome,$returnHTML);
    }

    public function __destruct()
    {
        if($this ->options["footer"] == true)
        $this->tpl->draw("footer");
    }
}

?>