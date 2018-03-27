<?php
namespace Mchljams\Gcia;

use Httpful\Request;

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
     * Set the API key
     */
    public function setKey($key)
    {
        //
        $this->key = $key;
        //
        return null;
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
        // check to make sure input is a string
        if (is_string($this->key)) {
            // add the API key into the query string
            $params['key'] = $this->key;
            // assemble parameters
            $query = http_build_query($params, null, '&');
            $string = preg_replace('/%5B(?:[0-9]|[1-9][0-9]+)%5D=/', '=', $query);
            // assemble the request URL
            $url = $this->base.$this->version.'/' . $type . '/?' . $string;
            // return the string of the request url
            return $url;
        }
        // if its not a string thow an exception
        throw new \Exception('Your API key is required and must be a string.');
    }

    /**
     * utility method to verify what HTTP request was made 
     */
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
     * execute the curl request
     */
    private function execute()
    {
        // executes a cURL session for the request, and sets the result variable
        $result = Request::get($this->getRequestURL())->send();
        // return the json result
        return $result->raw_body;
    }

    /**
     * List of available elections to query.
     */
    public function electionQuery()
    {
        // create the HTTP request url
        $this->url = $this->buildRequestURL('elections');
        // return the json result
        return $this->execute();
    }

    /**
     * Looks up information relevant to a voter based on the voter's registered address.
     *
     * Required query parameters: address
     *
     * Optional query parameters: electionID, officialOnly
     * 
     * Options : $params items in this array will be passed as HTTP get parametrs in the request
     */
    public function voterInfoQuery($address, $electionID = null, $officialOnly = false, $params = array())
    {
        // check that address is not null, it is required
        if ($address) {
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
            // return the json result
            return $this->execute();
        }
        // throw exception when required address parameter is null
        throw new \Exception('Address is required.');
    }

    /**
     * Looks up political geography and representative information for a single address.
     * 
     * Options : $params items in this array will be passed as HTTP get parametrs in the request
     */
    public function representativeInfoByAddress($address, $params = array())
    {
        // check that address parameter is not null, it is required
        if ($address) {
            // add the address to the parameters array
            $params['address'] = $address;
            // create the HTTP request url
            $this->url = $this->buildRequestURL('representatives', $params);
            // return the json result
            return $this->execute();
        }
        // throw exception when required address parameter is null
        throw new \Exception('Address is required.');
    }

    /**
     * Looks up representative information for a single geographic division.
     * 
     * Options : $params items in this array will be passed as HTTP get parametrs in the request
     */
    public function representativeInfoByDivision($ocdID, $params = array())
    {
        // check that the ocdID parameter is not null, it is required
        if ($ocdID) {
            // The ocdID string must be url encoded so it can be concatenated onto the request url
            $ocdID = urlencode($ocdID);
            // create the HTTP request url
            $this->url = $this->buildRequestURL('representatives/' . $ocdID, $params);
            // return the json result
            return $this->execute();
        }
        // throw exception when required ocdID parameter is null
        throw new \Exception('ocdID is required.');
    }

    /**
     * Searches for political divisions by their natural name or ocdID.
     *
     * When making a query an ocdID and you want an exact match it must be passed as
     * a literal string.
     * 
     * Options : $params items in this array will be passed as HTTP get parametrs in the request
     */
    public function search($query, $params = array())
    {
        // check that the query parameter is not null, it is required
        if ($query) {
            // add the query to the parameters array
            $params['query'] = $query;
            // create the HTTP request url
            $this->url = $this->buildRequestURL('divisions', $params);
            // return the json result
            return $this->execute();
        }
        // throw an exception when the query parameter is null
        throw new \Exception('Query is required.');
    }
}
