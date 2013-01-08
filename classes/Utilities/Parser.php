<?php

namespace Utilities;

/*
 * @Parser
 * Utility Class to perform various conversion tasks
 * 
 * convertToDOMDocument method taken from David Walker's Xerxes Parser Class
 * 
 * https://github.com/dswalker/xerxes/blob/master/library/Xerxes/Utility/Parser.php
 *  
 */

Class Parser {
  
  /**
   * Convert an XML-based variable to DOMDocument
   * 
   * @param string|SimpleXMLElement|DOMNode $xml
   * @return DOMDocument
   */

  public static function convertToDOMDocument($xml)
  {
    // already a document

    if ( $xml instanceof \DOMDocument )
    {
      return $xml;
    }

    // convert simplexml to string, which will 
    // get covered by string below

    if ( $xml instanceof \SimpleXMLElement )
    {
      $xml = $xml->asXML();
    }

    // convertable type

    if ( is_string($xml) )
    {
      $document = new \DOMDocument();
      $document->loadXML($xml);

      return $document;
    }
    elseif ( $xml instanceof \DOMNode )
    {
      // we'll convert this node to a DOMDocument

      // first import it into an intermediate doc, 
      // so we can also import namespace definitions as well as nodes

      $intermediate = new \DOMDocument();
      $intermediate->loadXML("<wrapper />");

      $import = $intermediate->importNode($xml, true);
      $our_node = $intermediate->documentElement->appendChild($import);

      // now get just our xml, minus the wrapper

      $document = new \DOMDocument();
      $document->loadXML($intermediate->saveXML($our_node));

      return $document;
    }
    else
    {
      throw new \InvalidArgumentException("param 1 must be of type string, SimpleXMLElement, DOMNode, or DOMDocument");
    }
  }
  
}
