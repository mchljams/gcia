<?php
namespace Mchljams\Gcia;

/**
 * This class is a PHP wrapper for the Google Civic Information API (Gcia).
 *
 * @copyright  2017 Michael James
 * @license    https://opensource.org/licenses/MIT   MIT
 * @link       https://github.com/mchljams/spun
 * @since      Class available since Release 0.0.1
 */
class Gcia
{
    // the api base url
    private $base = 'https://www.googleapis.com/civicinfo/';
    // the string containing spintax
    private $key;
    // the api version
    private $version = 'v2';
    // params for a url
    private $params;
    // the request url
    private $url;
    //
    private $result;

    /**
     * Set the API key, initialize the library
     *
     * @param string   $key  The Google API Key
     *
     * @throws Exception if input is not a string
     */
    public function __construct($key = null)
    {
        // check to make sure input is a string
        if (!is_string($key)) {
            // if its not a string thow an exception
            throw new \Exception('Your API key is required and must be a string.');
        }
        // set the string
        $this->key = $key;
    }

    /*
      type must be one of the following:

      /elections
      /voterinfo
      /representatives
      /representatives/{ocdId}
      /divisions
    */
    private function buildRequestURL($type = null, $params = array()) {
      // add the API key into the query string
      $params['key'] = $this->key;

      // assemble the request URL
      $url = $this->base.$this->version.'/' . $type . '/?' . http_build_query($params);

      return $url;
    }

    /* Just a utility method to verify what HTTP request was made */
    public function getRequestURL() {
      //
      if($this->url){

        return $this->url;
      }
      throw new \Exception('Request URL Not Set.');
    }

    private function execute() {

      $ch = curl_init();
      // Disable SSL verification
      curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
      curl_setopt($ch, CURLOPT_URL, $this->getRequestURL());

      $result = curl_exec($ch);
      // Check if any error occurred
      if(!curl_errno($ch))
      {
          $this->result = $result;
          curl_close($ch);
          return $this;
      }
      throw new \Exception('Curl error: ' . curl_error($ch));

    }

    public function getJSON() {
      if($this->result) {
        return $this->result;
      }
      throw new \Exception('No JSON result to return.');
    }

    public function getOBJ() {
      if($this->result) {
        return json_decode($this->result);
      }
      throw new \Exception('No JSON result to return as object.');
    }

    public function getASSOC() {
      if($this->result) {
        return json_decode($this->result, true);
      }
      throw new \Exception('No JSON result to return as associative array.');
    }

    /**
     * List of available elections to query.
     */
    public function electionQuery() {
      //
      $this->url = $this->buildRequestURL('elections');
      //
      $this->execute();
      //
      return $this;
    }

    /**
     * Looks up information relevant to a voter based on the voter's registered address.
     *
     * Required query parameters: address
     *
     * Optional query parameters: electionID, officialOnly
     */
    public function voterInfoQuery($address, $electionID = null, $officialOnly = false) {
      //
      if($address){
        //
        $params = array();
        //
        $params['address'] = $address;
        //
        if($electionID != null) {
          $params['electionID'] = $electionID;
        }
        //
        if($officialOnly === true) {
          $params['officialOnly'] = $officialOnly;
        }
        //
        $this->url = $this->buildRequestURL('voterinfo',$params);
        //
        $this->execute();
        //
        return $this;
      }
      throw new \Exception('Address is required.');
    }

    /**
     * Looks up political geography and representative information for a single address.
     */
    public function representativeInfoByAddress($address) {
      //
      if($address){
        //
        $params = array(
          'address' => $address
        );
        //
        $this->url = $this->buildRequestURL('representatives',$params);
        //
        $this->execute();
        //
        return $this;
      }
      throw new \Exception('Address is required.');
    }

    /**
     * Looks up representative information for a single geographic division.
     */
    public function representativeInfoByDivision($ocdID) {
      //
      if($ocdID) {
        // The ocdID string must be url encoded so it can be concatenated onto the request url
        $ocdID = urlencode($ocdID);
        //
        $this->url = $this->buildRequestURL('representatives/' . $ocdID);
        //
        $this->execute();
        //
        return $this;
      }
      throw new \Exception('ocdID is required.');
    }

    /**
     * Searches for political divisions by their natural name or ocdID. When
     * making a query an ocdID and you want an exact match it must be passed as
     * a literal string.
     */
    public function search($query) {
      //
      if($query) {
        //
        $params = array(
          'query' => $query
        );
        //
        $this->url = $this->buildRequestURL('divisions',$params);
        //
        $this->execute();
        //
        return $this;
      }
      throw new \Exception('Query is required.');
    }
}
