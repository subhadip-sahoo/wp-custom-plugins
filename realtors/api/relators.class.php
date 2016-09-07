<?php

class Realtors {
    
    protected $request;
    protected $query_str;
    protected $api_url = 'http://members.lasvegasrealtor.com/search/v1/realtors';
    
    public function __construct($request) {
        $this->request = $request;
    }

    public function get_request() {
        return $this->request;
    }
    
    public function prepare() {
        if(empty($this->request))
            return;
        
        foreach($this->request as $key => $value){ 
            if(empty($value))
                continue;
            
            $this->query_str .= $key . '='.$value.'&'; 
        }
        $this->query_str = rtrim($this->query_str, '&');
        return $this->query_str;
    }
    
    public function call() {
        $this->prepare();
        if($this->query_str === '')
            return require_once REALTORS_TEAMPLATE_DIR . '/invalid-request.php';
        
        $response = file_get_contents($this->api_url . '?' . $this->query_str);
        $results = json_decode($response);
        if($results->status === 'invalid_param')
            require_once REALTORS_TEAMPLATE_DIR . '/invalid-request.php';
        else if($results->status === 'no_result_found')
            require_once REALTORS_TEAMPLATE_DIR . '/no-results.php';
        else
            require_once REALTORS_TEAMPLATE_DIR . '/search-results.php';
    }
}