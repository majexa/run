<?php

class PageOpCli {

  protected $page;

  function __construct($page) {
    $this->page = $page;
  }

  /**
   * @param string @
   */
  function setContent($contentObjectType) {
    $this->update('content', $contentObjectType);
  }

  static function helpOpt_contentObjectType() {
    return implode('|', ClassCore::getNames('PageContentObj'));
  }

  function addHeader() {
    $this->update('header', true);
  }

  function addToHeader($headerBlockType) {
    $this->update('header', [
      $headerBlockType
    ]);
  }

  static function helpOpt_headerBlockType() {
    return implode('|', ['asd']);
  }

  function addColBlock($colClockType) {
    $this->update('col', [
      $colClockType
    ]);
  }

  static function helpOpt_colBlockType() {
    return implode('|', ['createBtn']);
  }

  protected function update($k, $v) {
    $pageOp = Config::getVar('pageOp', true) ?: [];
    if (!isset($pageOp[$this->page])) $pageOp[$this->page] = [];
    $pageOp[$this->page][$k] = $v;
    SiteConfig::updateVar('pageOp', $pageOp);
  }

}