<?php
namespace Newrelic_PHP_API;
require 'Newrelic.php';
use Newrelic_PHP_API\Newrelic as Newrelic;

class Applications_instances extends Newrelic{

    //filters
    private $ids = ''; //multiple   
    private $hostname = ''; //single
    
    //Getters
    
    public function getIds() {
        return $this->ids;
    }

    public function getHostname() {
        return $this->hostname;
    }
    
    //Setters

    public function setIds($ids) {
        $this->ids = $ids;
    }
    
    public function setHostname($hostname) {
        $this->name = $hostname;
    }
    
    
    /*
     * public function listConnection();
     * Main function of this class. Make the connection with the New Relic API, set all the parameters and returns the response
     * @params: $apikey - The apikey, which is the main feature for the requests
     * @return: array of the body reponse for the request or a error message.
     */
    private function listConnection($apikey){
        $curl = $this->curlInit();

        curl_setopt($curl, CURLOPT_HTTPHEADER, array('X-Api-Key:'.$apikey));

        if(!empty($this->getHostname())){
            curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query(array('filter' => array('hostname' => $this->getHostname()))));
        }
        if(!empty($this->getIds())){
            curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query(array('filter' => array('ids' => $this->getIds()))));
        }
        
        $applications = $this->curlResponse($curl);
        return $applications;
    }
    
    public function getList($apikey, $id){
        $this->url = 'https://api.newrelic.com/v2/applications/'.$id.'/instances';
        $accounts = array();
        foreach($apikey as $key){
            $accounts[$key] = $this->listConnection($key);
        }
        return $accounts;
    }

    public function getShow($apikey, $id, $host_id){
        $this->url = 'https://api.newrelic.com/v2/applications/'.$id.'/instances/'.$host_id;
        $this->setHostname(''); $this->setIds('');
        $account = $this->listConnection($apikey);
        return $account;
    }
}