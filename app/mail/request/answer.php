<?php

/** @var \app\models\Request $request */

?>

<h1> Добрый день, <?= $request->name ?>!</h1>
<h2> Ваш запрос № <?= $request->id ?> был успешно обработан и помечен как 'Решено'.</h2>
<p>Комментарий: <br><?= $request->comment ?></p>
<p>Спасибо за обращение!</p>