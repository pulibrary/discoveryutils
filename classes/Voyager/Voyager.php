<?php

namespace Voyager;
use Guzzle\Service\Client as Client;

/*
 * Voyager connectivity class
 *
 * Utility to extract html data from Voyager OPAC via html
 * or xml via Voyager Web Services (vxws)
 *
 *
 */


class Voyager
{

    function __construct($voyager_connection_details = array(), Client $client = null) {
        $this->host = $voyager_connection_details['base.url'];
        $this->html_base = $voyager_connection_details['html.base'];
        if ( $client != null )
        {
            $this->http_client = $client;
        }
        else
        {
            $this->http_client = new \Guzzle\Http\Client($this->host);
        }
    }

    /*
     * Example URL
     * http://catalog.princeton.edu/cgi-bin/Pwebrecon.cgi?holdingsinfo?&BBID=7446612
     *
     * returns an array containing various holdings param values of intertest
     */
    function getHoldings($bibliographic_id, $source = "html") {
        // get via
        if($source == "html") {
            $holdings_base = $this->html_base . "?holdingsinfo?&BBID=" ;
        }
        $response = $this->http_client->get($holdings_base . $bibliographic_id)->send();

        return (string)$response->getBody();

    }


}



