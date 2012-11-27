<?php

require_once("simpletest/autorun.php");
require_once( "../Freerider.php" );
require_once( "../FreeriderAPI.php" );

class FreeriderAPITests extends UnitTestCase
{
    // Private members
    private $freerider = null;
    private $freeriderAPI = null;

    function setUp()
    {
        // Instantiating core objects from classes

        // New Freerider-object
        $this->freerider = new Freerider("Västerås","Stockholm", date("Y-m-d", strtotime("2012-11-18")), date("Y-m-d", strtotime("2012-11-20")), "Volvo V40");

        // New wrapper objecct
        $this->freeriderAPI = new FreeriderAPI();
    }

    function tearDown()
    {
        unset($this->freerider);
        unset($this->freeriderAPI);
    }

    /*
     * Tests if Freerider constructor works properly
     */
    function testFreeriderObject()
    {
        $this->assertIsA($this->freerider, "Freerider", "Object should be type Freerider");
        $this->assertEqual($this->freerider->carModel == "Volvo V40", "Car model not valid, it is %s");
        $this->assertEqual($this->freerider->destination == "Stockholm", "Origin is not Stockholm");
        $this->assertEqual($this->freerider->origin == "Västerås", "Destination not Västerås");
        $this->assertIsA($this->freerider->startDate,"string", "Start Date not a date object");
        $this->assertIsA($this->freerider->endDate, "string", "End Date not a date object");
    }

    /*
     * Tests if the scrape helper API works
     */
    function testScrape()
    {
        // Gets content from Google
        $result = file_get_html("http://www.google.com");

        // Checks if something was scraped
        $this->assertNotNull($result, "Didnt scrape any data");
    }

    /*
     * Checks if the function that returns all events works
     */
    function testGetAll()
    {
        $result = $this->freeriderAPI->getAll();

        // Testar om en array returneras
        $this->assertIsA($result, "Array", "Return value is not an array");

        // Testar om det returneras några element
        $this->assertEqual(count($result) > 10, "No objects were returned");

        // Testar om arrayen innehåller Freeriderobjekt
        $this->assertIsA($result[0], "Freerider", "Returned array does not contain Freerider objects");

    }

    /*
     * Tests if the destination-search works
     */
    function testGetDestination()
    {
        // Gör ett anrop mot APIet med "Stockholm" som argument
        $result = $this->freeriderAPI->getDestination("Stockholm");

        // Return an array
        $this->assertEqual(is_array($result), "Did not return an array");

        // The array contains something
        $this->assertEqual(count($result) > 0, "Array didnt contain anything");

        // The array contains a Freerider object
        $this->assertIsA($result[0], "Freerider", "Array didnt contain Freerider object");

        // The array contains a Freerider object with Destination property containing "Stockholm"
        $this->assertPattern("/Stockholm/i", $result[0]->destination, "Destination is not Stockholm");
    }

    /*
     * Tests if the origin-search works
     */
    function testGetOrigin()
    {
        // Make a request to the API with "Stockholm" as search parameter
        $result = $this->freeriderAPI->getOrigin("Stockholm");

        // Return an array
        $this->assertEqual(is_array($result), "Did not return an array");

        // The array contains something
        $this->assertEqual(count($result) > 0, "Array didnt contain anything");

        // The array contains a Freerider object
        $this->assertIsA($result[0], "Freerider", "Array didnt contain Freerider object");

        // The array contains a Freerider object with Origin property containing "Stockholm"
        $this->assertPattern("/Stockholm/i", $result[0]->origin, "Origin is not Stockholm");
    }

    function tesGetOriginIncorrectSearch()
    {
        // Make a request to the API with "Stockholm" as search parameter
        $result = $this->freeriderAPI->getOrigin("ajhsdbahjdsofamfdmaoo12768");

        //Return an empty array
        $this->assertTrue(count($result) == 0, "The array is not empty");
    }
}