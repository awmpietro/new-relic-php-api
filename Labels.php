<?php
namespace Newrelic_PHP_API;
require 'Newrelic.php';
use Newrelic_PHP_API\Newrelic as Newrelic;
/*
 * Class Labels
 * PHP rest client for New Relic applications interation
 * Using the apykey and the id, user can list all aplications associated with that apikey, a single applications with the id, update the available fields of an application by it´s apikey and id and delete an application
 * Refer to function comments to more information
 * @author: Arthur Mastropietro <arthur.mastropietro@credibilit.com>
 * @version: 1.0 - 2014
 */
class Labels extends Newrelic{

    private $custom_request = '';
    private $put_data = '';
    
    //Getters

     public function getCustomRequest() {
        return $this->custom_request;
    }
    
    public function getPutData(){
        return $this->put_data;
    }
 
    
    //Setters
    
    public function setPutData($put_data){
        $this->put_data = $put_data;
    }

    public function setCustomRequest($customRequest){
        $this->custom_request = strtolower($customRequest);
    }
    
    public function __construct() {
        parent::__construct();
        $this->setUrl('https://api.newrelic.com/v2/labels');
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
                case 'post' :
                    curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");
                    curl_setopt($curl, CURLOPT_POST, true);
                    curl_setopt($curl, CURLOPT_POSTFIELDS, $this->getPutData());
                break;
                case 'delete' :
                    curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "DELETE");
                break;
            }
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
        $labels = array();
        foreach($apikeys as $apikey){
            $labels[$apikey] = $this->listConnection($apikey);
        }
        return $labels;
    }
    
    /*
     * public function update();
     * This updates some attributes available for an application
     * @params: $apikey - The apikey itself, $id - The id of the application, $data - json of values to be updatad
     * @use listConnection():  to make the put request to API 
     * @return: json of the updated application
     */
    public function create($apikey, $data){
        $this->setContentType('json');
        $this->setCustomRequest('post');
        $putDataArray = array('label' => array('category' => $data['category'], 'name' => $data['name'], 'links' => array('applications' => $data['applications'])));
        $putDataJson = json_encode($putDataArray);
        $this->setPutData($putDataJson);
        $label = $this->listConnection($apikey);
        return $label;
    }
    
    /*
     * public function delete();
     * WARNING: This deletes an application. Irreversible
     * Cannot delete an application that is still reporting data
     * @params: $apikey - The apikey itself, $id - The id of the application
     * @use listConnection():  to make the delete request to API 
     * @return: 
     */
    public function delete($apikey, $label_key){
        $this->setUrl('https://api.newrelic.com/v2/labels/'.$label_key);
        $this->setCustomRequest('delete');
        $this->listConnection($apikey);
    }
}