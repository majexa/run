<?php

require_once 'Testing/Selenium.php';

class GoogleTest extends PHPUnit_Framework_TestCase {

  private $selenium;

  public function setUp() {
    $this->selenium = new Testing_Selenium("firefox", "http://litcult.ru");
    $this->selenium->start();
  }

  public function tearDown() {
    $this->selenium->stop();
  }

  public function testGoogle() {
    $this->selenium->open("/");
    $this->assertRegExp("/ЛитКульт/", $this->selenium->getTitle());
    $this->selenium->getText('//');
  }

}
