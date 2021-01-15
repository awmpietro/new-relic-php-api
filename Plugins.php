<?php
namespace Newrelic_PHP_API;
require 'Newrelic.php';
use Newrelic_PHP_API\Newrelic as Newrelic;
/*
 * Class Users
 * PHP rest client for New Relic applications interation
 * Using the apykey and the id, user can list all aplications associated with that apikey, a single applications with the id, update the available fields of an application by it´s apikey and id and delete an application
 * Refer to function comments to more information
 * @author: Arthur Mastropietro <arthur.mastropietro@credibilit.com>
 * @version: 1.0 - 2014
 */
class Plugins extends Newrelic{
    //filters
    private $guid = ''; //single
    private $ids = ''; //multiple
    
    //Getters

    public function getGuid() {
        return $this->guid;
    }
    
    public function getIds(){
        return $this->ids;
    }
    
    //Setters

    public function setGuid($guid) {
        $this->guid = strtolower($guid);
    }
    
    public function setIds($ids){
        $this->ids = $ids;
    }
    
    public function __construct() {
        parent::__construct();
        $this->setUrl('https://api.newrelic.com/v2/plugins');
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
        
        if(!empty($this->getGuid())){
            curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query(array('filter' => array('guid' => $this->getGuid()))));
        }
        if(!empty($this->getIds())){
            curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query(array('filter' => array('ids' => $this->getIds()))));
        }
        $applications = $this->curlResponse($curl);
        return $applications;
    }
    
    /*
     * public function getList();
     * This endpoint returns a single Application, identified its ID
     * @params: $apikeys - Simple Array of apikeys (Just values)
     * @use listConnection():  To retrieve the apikeys data
     * @return: echo a nested json of all Applications for the apikeys in the params. The apikye will be the key of each nested json 
     */
    public function getList($apikeys){
        $plugins = array();
        foreach($apikeys as $apikey){
            $plugins[$apikey] = $this->listConnection($apikey);
        }
        return $plugins;
    }
    
    /*
     * public function getShow();
     * This endpoint returns a single Application, identified its ID
     * @params: $apikey - The apikey itself, $id - The id of the application
     * @use listConnection():  To retrieve the apikey data
     * @return: echos a json for the application
     */
    public function getShow($apikey, $id){
        $this->setUrl('https://api.newrelic.com/v2/plugins/'.$id);
        $this->setGuid(''); $this->setIds('');
        $user = $this->listConnection($apikey);
        return $user;
    }
}