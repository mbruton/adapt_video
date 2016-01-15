<?php

namespace applications\adapt_video{
    
    /* Prevent direct access */
    defined('ADAPT_STARTED') or die;
    
    class controller_root extends controller{
        
        protected $_container;
        
        public function __construct(){
            parent::__construct();
            
        }
        
        /*
         * Actions
         */
        
        
        /*
         * Views
         */
        public function view_default(){
            $p = new \extensions\ffmpeg_wrapper\ffprobe();
            
            $results = $p->probe("/home/matt/Juno.2007.1080p.BluRay.x264.DTS-WiKi.mkv");
            //$results = $p->probe("/home/matt/The.Hills.Have.Eyes.2006.UNRATED.BrRip.600MB.mkv");
            
            $this->add_view(new html_h1('Results'));
            $this->add_view(new html_h2('Container'));
            
            $data = array(
                array(
                    'Format' => $results['container']['format_name'],
                    'duration' => $results['container']['duration'],
                    'size' => $results['container']['size'],
                    'bit_rate' => $results['container']['bit_rate'],
                    'streams' => $results['container']['nb_streams']
                )
            );
            
            $table = new \frameworks\adapt\view_table($data);
            
            $this->add_view($table);
            
            $this->add_view(new html_h2('Streams'));
            
            $data = array();
            
            foreach($results['streams'] as $stream){
                $frame_rate = $stream['r_frame_rate'];
                if ($frame_rate != '0/0'){
                    $parts = explode("/", $frame_rate);
                    $parts[0] = intval($parts[0]);
                    $parts[1] = intval($parts[1]);
                    
                    if ($parts[0] > 0 && $parts[1] > 0){
                        $frame_rate = $parts[0] / $parts[1];
                    }
                }else{
                    $frame_rate = '';
                }
                
                $data[] = array(
                    'Type' => $stream['codec_type'],
                    'Codec' => $stream['codec_name'],
                    'Profile' => $stream['profile'],
                    'Bit Rate' => $stream['bit_rate'],
                    'Dimentions' => $stream['width'] ? $stream['width'] . ' x ' . $stream['height'] : '',
                    'Aspect Ratio' => $stream['display_aspect_ratio'],
                    'Frame rate' => $frame_rate,
                    'Sample Rate' => $stream['sample_rate'],
                    'Channels' => $stream['channels'],
                    'Channel layout' => $stream['channel_layout'],
                    'Language' => $stream['TAG:language']
                    
                );
            }
            
            $table = new \frameworks\adapt\view_table($data);
            
            $this->add_view($table);
            
            $this->add_view(new html_pre(print_r($p->probe("/home/matt/The.Hills.Have.Eyes.2006.UNRATED.BrRip.600MB.mkv"), true)));
        }
        
        public function view_ffmpeg_config(){
            $this->add_view(new html_h1('ffmpeg config'));
            
            $p = new \extensions\ffmpeg_wrapper\ffprobe();
            $formats = $p->get_formats();
            $data = array();
            foreach($formats as $format){
                $data[] = array(
                    'Type' => implode(", ", $format['type']),
                    'Description' => $format['description'],
                    'Mixing Supported' => $format['mixing_suppored'],
                    'Demuxing Supported' => $format['demuxing_supported']
                );
            }
            
            $this->add_view(
                array(
                    new html_h2('Supported formats'),
                    new \frameworks\adapt\view_table($data),
                    new html_h2('Supported codecs'),
                    new \frameworks\adapt\view_table($p->get_codecs()),
                    new html_h2('Supported decoders'),
                    new \frameworks\adapt\view_table($p->get_decoders()),
                    new html_h2('Supported encoders'),
                    new \frameworks\adapt\view_table($p->get_encoders())
                )
            );
            
            
        }
        
    }
    
}

?>