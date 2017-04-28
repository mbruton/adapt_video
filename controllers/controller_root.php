<?php

namespace adapt\adapt_video{
    
    /* Prevent direct access */
    defined('ADAPT_STARTED') or die;
    
    class controller_root extends controller{
        
        protected $_container;
        
        public function __construct(){
            $this->_container =  new html_div(array('class' => 'container'));
            
            parent::__construct();
            
            $header = new html_header();
            
            $navbar = new \bootstrap\views\view_navbar();
            $navbar->static_top = true;
            
            $navbar->brand = 'adapt video';
            $navbar->brand_url = "/";
            
            $header->add($navbar);
            
            $this->view->add($header);
            
            $this->view->add($this->_container);
        }
        
        public function add_view($view){
            $this->_container->add($view);
        }
        
        /*
         * Actions
         */
        
        
        /*
         * Views
         */
        public function view_default(){
            $row = new html_div(array('class' => 'row'));
            $this->add_view($row);
            $row->add(new html_h1('Adapt video'));
            
            $video = new html_video(array('class' => 'video-js vjs-16-9 vjs-default-skin', 'controls' => 'true', 'preload' => 'auto', 'width' => '740', 'height' => '405'));
            $row->add($video);
            //$video->add(new html_source(array('src' => '/adapt/store/public/gom6-3.540p.mp4', 'type' => 'video/mp4')));
        }
        
        public function view_test(){
            $probe = new \adapt\video\ffmpeg_wrapper\ffprobe();
            
            $this->add_view(new html_pre(print_r($probe->get_encoders(), true)));
        }
        
        public function view_file_test(){
            $storage = new \adapt\storage_database\storage_database();
            //$storage->set("foo", "Hello world");
            $storage->set_by_file("sab", "/home/matt/sab_export_20151020.csv");
            $this->add_view(new html_pre(print_r($storage->errors(), true)));
            
        }
        
        public function view_repo_test(){
            $repo_url = $this->setting('repository.url');
            $url = $repo_url[0];
            
            $repo = new \adapt\repository($url);
            
            if ($repo->has('adapt')){
                $this->add_view('Has adapt', '2.0.1');
            }else{
                $this->add_view('Couldnt find adapt');
                $this->add_view(new html_pre(print_r($repo->errors(), true)));
            }
            
            //$this->add_view($repo->get('adapt', '2.0.0'));
            
        }
    }
    
}

?>