<?php

$db = new Db('root', 'root', 'localhost', 'cities2');
foreach ($db->query("
SELECT city.*, region.name AS region FROM city
LEFT JOIN region ON region.region_id=city.region_id
WHERE city.country_id=3159") as $v) {
  $tagRegion = $db->selectCell('SELECT id FROM tagcities WHERE title=?', $v['title']);
  $tagCity = $db->selectCell('SELECT id FROM tagcities WHERE title=?', $v['name']);
  prr([$tagRegion, $tagCity]);
  //print $v['id'].' '.(! ? 'empty' : 'full')."\n";
}