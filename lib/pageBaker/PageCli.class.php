<?php

class PageCli {

  function create($name) {
    $pages = Config::getVar('pages', true) ?: [];
    if (in_array($name, $pages)) throw new Exception("'$name' already exists");
    $pages[] = $name;
    SiteConfig::updateVar('pages', $pages);
  }

}