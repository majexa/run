<?php

class DdStructureCli {

  function create($name) {
    $manager = new DdStructuresManager();
    $manager->create([
      'title' => $name,
      'name' => $name
    ]);
  }

}