<?php
namespace Newrelic_PHP_API;
require 'Newrelic.php';
use Newrelic_PHP_API\Newrelic as Newrelic;
/*
 * Class Servers
 * PHP rest client for New Relic applications interation
 * Using the apykey and the id, user can list all aplications associated with that apikey, a single applications with the id, update the available fields of an application by it´s apikey and id and delete an application
 * Refer to function comments to more information
 * @author: Arthur Mastropietro <arthur.mastropietro@credibilit.com>
 * @version: 1.0 - 2014
 */
class Servers extends Newrelic{
    //filters
    private $name = ''; //single
    private $ids = ''; //multiple   
    private $labels = '';
    private $custom_request = '';
    private $put_data = '';
    
    //Getters

    public function getName() {
        return $this->name;
    }

    public function getIds() {
        return $this->ids;
    }

    public function getLabels() {
        return $this->labels;
    }
    
     public function getCustomRequest() {
        return $this->custom_request;
    }
    
    public function getPutData(){
        return $this->put_data;
    }
    
    //Setters

    public function setFormat($format) {
        $this->format = strtolower($format);
    }

    public function setName($name) {
        $this->name = $name;
    }

    public function setIds($ids) {
        $this->ids = $ids;
    }

    public function setLabels($labels) {
        $this->labels = strtolower(labels);
    }
    
    public function setPutData($put_data){
        $this->put_data = $put_data;
    }

    public function setCustomRequest($customRequest){
        $this->custom_request = strtolower($customRequest);
    }
    
    public function __construct() {
        parent::__construct();
        $this->setUrl('https://api.newrelic.com/v2/servers');
    }


    /*
     * public function listConnection();
     * Main function of this class. Make the connection with the New Relic API, set all the parameters and returns the response
     * @params: $apikey - The apikey, which is the main feature for the requests
     * @return: array of the body reponse for the request or a error message.
     */
    private function listConnection($apikey){
        $curl = $this->curlInit();
        if(!empty($this->getContentType())){
            curl_setopt($curl, CURLOPT_HTTPHEADER, array($this->getContentType(), 'X-Api-Key:'.$apikey));
        }else{
            curl_setopt($curl, CURLOPT_HTTPHEADER, array('X-Api-Key:'.$apikey));
        }
        if(!empty($this->getCustomRequest())){
            switch ($this->getCustomRequest()){
                case 'put' :
                    curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "PUT");
                    curl_setopt($curl, CURLOPT_POSTFIELDS, $this->getPutData());
                break;
                case 'delete' :
                    curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "DELETE");
                break;
            }
        }
        if(!empty($this->getName())){
            curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query(array('filter' => array('name' => $this->getName()))));
        }
        if(!empty($this->getIds())){
            curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query(array('filter' => array('ids' => $this->getIds()))));
        }
        if(!empty($this->getLabels())){
            curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query(array('filter' => array('label' => $this->getLabels()))));
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
        $servers = array();
        foreach($apikeys as $apikey){
            $servers[$apikey] = $this->listConnection($apikey);
        }
        return $servers;
    }
    
    /*
     * public function getShow();
     * This endpoint returns a single Application, identified its ID
     * @params: $apikey - The apikey itself, $id - The id of the application
     * @use listConnection():  To retrieve the apikey data
     * @return: echos a json for the application
     */
    public function getShow($apikey, $id){
        $this->setUrl('https://api.newrelic.com/v2/applications/'.$id);
        $this->setName(''); $this->setIds(''); $this->setLabels('');
        $server = $this->listConnection($apikey);
        return $server;
    }
    
    /*
     * public function update();
     * This updates some attributes available for an application
     * @params: $apikey - The apikey itself, $id - The id of the application, $data - json of values to be updatad
     * @use listConnection():  to make the put request to API 
     * @return: json of the updated application
     */
    public function update($apikey, $id, $name){
        $this->setUrl('https://api.newrelic.com/v2/servers/'.$id);
        $this->setName(''); $this->setIds(''); $this->setLanguage('');
        $this->setContentType('json');
        $this->setCustomRequest('put');
        $putDataArray = array('server' => array('name' => $name ));
        $putDataJson = json_encode($putDataArray);
        $this->setPutData($putDataJson);
        $server = $this->listConnection($apikey);
        return $server;
    }
    
    /*
     * public function delete();
     * WARNING: This deletes an application. Irreversible
     * Cannot delete an application that is still reporting data
     * @params: $apikey - The apikey itself, $id - The id of the application
     * @use listConnection():  to make the delete request to API 
     * @return: 
     */
    public function delete($apikey, $id){
        $this->setUrl('https://api.newrelic.com/v2/servers/'.$id);
        $this->setName(''); $this->setIds(''); $this->setLanguage('');
        $this->setCustomRequest('delete');
        $this->listConnection($apikey);
    }
}