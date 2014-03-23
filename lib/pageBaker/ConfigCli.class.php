<?php

class ConfigCli {

  function update($name, $subKeys, $value) {
    SiteConfig::updateSubVar($name, $subKeys, $value);
  }

}