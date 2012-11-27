<?php
include_once( "helper/simple_html_dom.php" );
include_once( "Freerider.php" );

/**
 * PHP-API for fetching available Hertz Freerider travels.
 * @author Hans Bentlöv <hb222ap@student.lnu.se>
 * @todo Refactoring, renaming variables (naming convention - underscore)
 */
class FreeriderAPI
{
    // URL to Freerider list site
    private $url = "http://hertzfreerider.se/unauth/list_transport_offer.aspx";

    /**
     * Returns all available Freerider rentals
     * @return array Array containing Freerider-objects or empty array if none available
     */
    public function getAll()
    {
        $travelInfo = array();
        $html = file_get_html($this->url);
        $elements = $html->find("div[id=offers_list] tr[class=highlight]");

        foreach ($elements as $element) {
            $origin = $element->find("a")[0]->plaintext;
            $destination = $element->find("a")[1]->plaintext;
            $startDate = $element->nextSibling()->find("td span")[0]->plaintext;
            $endDate = $element->nextSibling()->find("td span")[1]->plaintext;
            $carModel = $element->nextSibling()->find("td span")[2]->plaintext;
           array_push($travelInfo, new Freerider($origin, $destination, $startDate, $endDate, $carModel));
        }
        return $travelInfo;
    }

    /**
     * Returns available Freerider rentals based on a destination search query
     * @param $destination
     * @return array Array containing Freerider-objects or empty array if none available
     */
    public function getDestination($query)
    {
        $travelInfo = array();
        $html = file_get_html($this->url);
        $elements = $html->find("div[id=offers_list] tr[class=highlight]");

        foreach ($elements as $element) {
            if(preg_match("/". $query ."/i", $element->find("a")[1]->plaintext)){
                $origin = $element->find("a")[0]->plaintext;
                $destination = $element->find("a")[1]->plaintext;
                $startDate = $element->nextSibling()->find("td span")[0]->plaintext;
                $endDate = $element->nextSibling()->find("td span")[1]->plaintext;
                $carModel = $element->nextSibling()->find("td span")[2]->plaintext;
                array_push($travelInfo, new Freerider($origin, $destination, $startDate, $endDate, $carModel));
            }
        }
        return $travelInfo;
    }

    /**
     * Returns available Freerider rentals based on a origin search query
     * @param $origin
     * @return array Array containing Freerider-objects or empty array if none available
     */
    public function getOrigin($query)
    {
        $travelInfo = array();
        $html = file_get_html($this->url);
        $elements = $html->find("div[id=offers_list] tr[class=highlight]");

        foreach ($elements as $element) {
            if(preg_match("/". $query ."/i", $element->find("a")[0]->plaintext)){
                $origin = $element->find("a")[0]->plaintext;
                $destination = $element->find("a")[1]->plaintext;
                $startDate = $element->nextSibling()->find("td span")[0]->plaintext;
                $endDate = $element->nextSibling()->find("td span")[1]->plaintext;
                $carModel = $element->nextSibling()->find("td span")[2]->plaintext;
                array_push($travelInfo, new Freerider($origin, $destination, $startDate, $endDate, $carModel));
            }
        }
        return $travelInfo;
    }
}
