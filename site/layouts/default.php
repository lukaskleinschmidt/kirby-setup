<?php
/**
 * @var \App\Core\App $kirby
 * @var \App\Core\Site $site
 * @var \App\Core\Page $page
 * @var \App\Core\Pages $pages
 */
?>
<!DOCTYPE html>
<html lang="<?= $kirby->languageCode() ?>">
<head>

  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <?= vite([
    'resources/scripts/app.js',
    'resources/styles/app.css',
    'resources/scripts/templates/{{ page.intendedTemplate }}.js',
    'resources/styles/templates/{{ page.intendedTemplate }}.css',
  ]) ?>

</head>
<body>

  <?= $slot ?>

</body>
</html>
