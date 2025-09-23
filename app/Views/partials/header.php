<!DOCTYPE html>
<html>
  <head>
    <link href="https://fonts.googleapis.com/css2?family=Roboto&display=swap" rel="stylesheet">

    <?php foreach ($attributes as $attribute): ?>
    <link rel="stylesheet" href="/style/<?= $attribute?>.css">
    <?php endforeach; ?>
