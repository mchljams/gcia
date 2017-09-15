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
    // the string containing the API key
    private $key;
    // the api base url
    private $base = 'https://www.googleapis.com/civicinfo/';
    // the api version
    private $version = 'v2';
    // params for a url
    private $params;
    // the request url
    private $url;
    // the json result from each http request
    private $result;

    /**
     * Set the API key, instantiate the Google Civic Information API class.
     *
     * @param string $key, The Google API Key
     *
     * @throws Exception if input is not a string
     */
    public function __construct($key = null)
    {
        // check to make sure input is a string
        if (is_string($key)) {
          // set the key property
          $this->key = $key;
        }
        // if its not a string thow an exception
        throw new \Exception('Your API key is required and must be a string.');
    }

    /**
     * Concatenate the HTTP request URL that will be used to execute a
     * cURL session to the API.
     *
     * Type must be one of the following: /elections, /voterinfo,
     * /representatives, /representatives/{ocdId}, /divisions
     *
     * @param string $type, One of the API methods
     * @param array $params, any GET parameters that should be added to the request
     *
     * @return string, the HTTP request URL string
     */
    private function buildRequestURL($type = null, $params = array())
    {
        // add the API key into the query string
        $params['key'] = $this->key;
        // assemble the request URL
        $url = $this->base.$this->version.'/' . $type . '/?' . http_build_query($params);
        // return the string of the request url
        return $url;
    }

    /* Just a utility method to verify what HTTP request was made */
    public function getRequestURL()
    {
        // check that the request url property is not null
        if ($this->url) {
            // since the request url has been built return the string
            return $this->url;
        }
        // throw exception when the url property is null
        throw new \Exception('Request URL Not Set.');
    }

    /**
     *
     */
    private function execute()
    {
        // initialize a new cURL session and set the handle
        $ch = curl_init();
        // Disable SSL verification
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        // set to true (1) to return output as a string
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        // set the url that cURL will fetch
        curl_setopt($ch, CURLOPT_URL, $this->getRequestURL());
        // execute the cURL session
        $result = curl_exec($ch);
        // check if any cURL error occurred
        if (!curl_errno($ch)) {
            // since no cURL errors occured, set the results property
            $this->result = $result;
            // close the cURL session, to free the resources
            curl_close($ch);
            // return the object so methods can be chained
            return $this;
        }
        // throw exception when a cURL error happens.
        throw new \Exception('Curl error: ' . curl_error($ch));
    }

    /**
     *
     */
    public function getJSON()
    {
        // check that the result property is not null
        if ($this->result) {
            // return the result property JSON string
            return $this->result;
        }
        // thow an exception when the result property is null
        throw new \Exception('No JSON result to return.');
    }

    /**
     *
     */
    public function getOBJ()
    {
        // check that the result property is not null
        if ($this->result) {
            // return the result property JSON string as an object
            return json_decode($this->result);
        }
        // thow an exception when the result property is null
        throw new \Exception('No JSON result to return as object.');
    }

    /**
     *
     */
    public function getASSOC()
    {
        // check that the result property is not null
        if ($this->result) {
            // return the result property JSON string as an accociative array
            return json_decode($this->result, true);
        }
        // thow an exception when the result property is null
        throw new \Exception('No JSON result to return as associative array.');
    }

    /**
     * List of available elections to query.
     */
    public function electionQuery()
    {
        // create the HTTP request url
        $this->url = $this->buildRequestURL('elections');
        // executes a cURL session for the request, and sets the result property
        $this->execute();
        // return the object so methods can be chained
        return $this;
    }

    /**
     * Looks up information relevant to a voter based on the voter's registered address.
     *
     * Required query parameters: address
     *
     * Optional query parameters: electionID, officialOnly
     */
    public function voterInfoQuery($address, $electionID = null, $officialOnly = false)
    {
        // check that address is not null, it is required
        if ($address) {
            // items in this array will be passed as HTTP get parametrs in the request
            $params = array();
            // add the address to the parameters array
            $params['address'] = $address;
            // check if the optional electionID is not null
            if ($electionID != null) {
                // add the electionID to the parameters array
                $params['electionID'] = $electionID;
            }
            // check if the officialOnly parameter has been set and has a boolean type match
            if ($officialOnly === true) {
                // add the officialOnly value to the parameters array
                $params['officialOnly'] = $officialOnly;
            }
            // create the HTTP request url
            $this->url = $this->buildRequestURL('voterinfo', $params);
            // executes a cURL session for the request, and sets the result property
            $this->execute();
            // return the object so methods can be chained
            return $this;
        }
        // throw exception when required address parameter is null
        throw new \Exception('Address is required.');
    }

    /**
     * Looks up political geography and representative information for a single address.
     */
    public function representativeInfoByAddress($address)
    {
        // check that address parameter is not null, it is required
        if ($address) {
            // items in this array will be passed as HTTP get parametrs in the request
            $params = array();
            // add the address to the parameters array
            $params['address'] = $address;
            // create the HTTP request url
            $this->url = $this->buildRequestURL('representatives', $params);
            // executes a cURL session for the request, and sets the result property
            $this->execute();
            // return the object so methods can be chained
            return $this;
        }
        // throw exception when required address parameter is null
        throw new \Exception('Address is required.');
    }

    /**
     * Looks up representative information for a single geographic division.
     */
    public function representativeInfoByDivision($ocdID)
    {
        // check that the ocdID parameter is not null, it is required
        if ($ocdID) {
            // The ocdID string must be url encoded so it can be concatenated onto the request url
            $ocdID = urlencode($ocdID);
            // create the HTTP request url
            $this->url = $this->buildRequestURL('representatives/' . $ocdID);
            // executes a cURL session for the request, and sets the result property
            $this->execute();
            // return the object so methods can be chained
            return $this;
        }
        // throw exception when required ocdID parameter is null
        throw new \Exception('ocdID is required.');
    }

    /**
     * Searches for political divisions by their natural name or ocdID.
     *
     * When making a query an ocdID and you want an exact match it must be passed as
     * a literal string.
     */
    public function search($query)
    {
        // check that the query parameter is not null, it is required
        if ($query) {
            // items in this array will be passed as HTTP get parametrs in the request
            $params = array();
            // add the query to the parameters array
            $params['query'] = $query;
            // create the HTTP request url
            $this->url = $this->buildRequestURL('divisions', $params);
            // executes a cURL session for the request, and sets the result property
            $this->execute();
            // return the object so methods can be chained
            return $this;
        }
        // throw an exception when the query parameter is null
        throw new \Exception('Query is required.');
    }
}
