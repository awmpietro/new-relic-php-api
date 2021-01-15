<?php
namespace Newrelic_PHP_API;

abstract class Newrelic{
    
    protected $url; //string
    protected $format; //xml or json
    protected $content_type;
    
    public function getUrl() {
        return $this->url;
    }

    protected function getFormat() {
        return $this->format;
    }
    
    protected function getContentType() {
        return $this->content_type;
    }
    
    protected function setUrl($url) {
        $this->url = $url;
    }

    protected function setFormat($format) {
        $this->format = $format;
    }
    
    public function setContentType($contentType) {
        if($contentType == strtolower('json')){
           $this->content_type = 'Content-Type: application/json';
        }else{
            $this->content_type = $contentType = null;
        } 
    }
    
    public function __construct(){
        $this->setUrl('https://api.newrelic.com/v2/applications');
        $this->setFormat('.json');
    }

    /*
     * protected function curlInit();
     * This function makes the curl connection with the main features
     * @return: The curl object.
     */
    protected function curlInit(){
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_URL => $this->url . $this->format,
            CURLOPT_HEADER => true, // Instead of the "-i" flag
        ));
        return $curl;
    }
    
    protected function curlResponse($curl){
        $curl_response = curl_exec($curl);
        $header_size = curl_getinfo($curl, CURLINFO_HEADER_SIZE);
        $header = substr($curl_response, 0, $header_size);
        $head = explode(" ", $header);
        $status = $head[13]; // 13 = indice do status
        switch ($status) :
            case 200 :
                $body = substr($curl_response, $header_size); //Separo o body do header
                $applications = json_decode($body); //Transformo o json do servidor em array PHP
                curl_close($curl); //Fecho o service
                return $applications; //Retorno o array
            break;
            case 401 : //Erro
                $applications = array('error' => 'Invalid API key or Invalid request, API key required');
                return $applications;
            break;
            case 403 :
                $applications = array('error' => 'New Relic API access has not been enabled');
                return $applications;
            break;
            case 500 :
                $applications = array('error' => 'A server error occured, please contact New Relic support');
                return $applications;
            break;
            case 404 :
                $applications = array('error' => 'No Application found with the given ID');
                return $applications;
            break;
            case 409 :
                $applications = array('error' => 'Cannot delete an application that is still reporting data.');
                return $applications;
            break;
            default :
                $body = substr($curl_response, $header_size);
                $applications = json_decode($body);
                curl_close($curl);
                return $applications;
        endswitch;
    }
}
