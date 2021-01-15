<?php
namespace Newrelic_PHP_API;
require 'Newrelic.php';
use Newrelic_PHP_API\Newrelic as Newrelic;
/*
 * Class Components
 * PHP rest client for New Relic applications interation
 * Using the apykey and the id, user can list all aplications associated with that apikey, a single applications with the id, update the available fields of an application by it´s apikey and id and delete an application
 * Refer to function comments to more information
 * @author: Arthur Mastropietro <arthur.mastropietro@credibilit.com>
 * @version: 1.0 - 2014
 */
class Components extends Newrelic{
    //filters
    private $name = ''; //single
    private $ids = ''; //multiple
    private $plugin_id; //single
    
    //Getters

    public function getName() {
        return $this->name;
    }
    
    public function getIds(){
        return $this->ids;
    }
    
    public function getPluginId(){
        return $this->plugin_id;
    }
    
    //Setters

    public function setName($name) {
        $this->name = strtolower($name);
    }
    
    public function setIds($ids){
        $this->ids = $ids;
    }
    
    public function setPluginId($plugin_id){
        $this->plugin_id = $plugin_id;
    }
    
    public function __construct() {
        parent::__construct();
        $this->setUrl('https://api.newrelic.com/v2/components');
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
        
        if(!empty($this->getName())){
            curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query(array('filter' => array('name' => $this->getName()))));
        }
        if(!empty($this->getIds())){
            curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query(array('filter' => array('ids' => $this->getIds()))));
        }
        
        if(!empty($this->getPluginId())){
            curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query(array('filter' => array('plugin_id' => $this->getPluginId()))));
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
        $components = array();
        foreach($apikeys as $apikey){
            $components[$apikey] = $this->listConnection($apikey);
        }
        return $components;
    }
    
    /*
     * public function getShow();
     * This endpoint returns a single Application, identified its ID
     * @params: $apikey - The apikey itself, $id - The id of the application
     * @use listConnection():  To retrieve the apikey data
     * @return: echos a json for the application
     */
    public function getShow($apikey, $id){
        $this->setUrl('https://api.newrelic.com/v2/components/'.$id);
        $this->setName(''); $this->setIds('');
        $component = $this->listConnection($apikey);
        return $component;
    }
}