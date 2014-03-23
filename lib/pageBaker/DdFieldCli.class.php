<?php

class DdFieldCli {

  function create($structure, $name) {
    $manager = new DdFieldsManager($structure);
    $manager->create([
      'title' => $name,
      'name' => $name,
      'type' => 'text'
    ]);
  }

}