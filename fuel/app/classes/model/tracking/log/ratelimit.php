<?php

class Model_Tracking_Log_Ratelimit extends \Orm\Model
{

    protected static $_table_name = 'tracking_log_ratelimit';

	protected static $_properties = array(
		'id',
		'service',
		'limit',
		'remaining',
		'response',
		'created_at', 
	);

	protected static $_observers = array(
		'Orm\Observer_CreatedAt' => array(
			'events' => array('before_insert'),
			'mysql_timestamp' => true,
		), 
	);


	public function parseAndSetResponseValues($data, $service) {

		$this->service = $service;   

		$headers = $this->http_parse_headers($data['headers']);

		$this->limit = $headers['X-Ratelimit-Limit']; 
		$this->remaining = $headers['X-Ratelimit-Remaining'];  
		$this->response = $data['headers']; 

		$this->save();

	}

    private function http_parse_headers( $header )
    {
        $retVal = array();
        $fields = explode("\r\n", preg_replace('/\x0D\x0A[\x09\x20]+/', ' ', $header));
        foreach( $fields as $field ) {
            if( preg_match('/([^:]+): (.+)/m', $field, $match) ) {
                $match[1] = preg_replace('/(?<=^|[\x09\x20\x2D])./e', 'strtoupper("\0")', strtolower(trim($match[1])));
                if( isset($retVal[$match[1]]) ) {
                    if (!is_array($retVal[$match[1]])) {
                        $retVal[$match[1]] = array($retVal[$match[1]]);
                    }
                    $retVal[$match[1]][] = $match[2];
                } else {
                    $retVal[$match[1]] = trim($match[2]);
                }
            }
        }
        return $retVal;
    }	
}
