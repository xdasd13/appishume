<!DOCTYPE html>
<html lang="en">
<head>
<link rel="stylesheet" href="<?= base_url('assets/css/errors-html-error_400.css') ?>">
    <meta charset="utf-8">
    <title><?= lang('Errors.badRequest') ?></title>

    
</head>
<body>
<div class="wrap">
    <h1>400</h1>

    <p>
        <?php if (ENVIRONMENT !== 'production') : ?>
            <?= nl2br(esc($message)) ?>
        <?php else : ?>
            <?= lang('Errors.sorryBadRequest') ?>
        <?php endif; ?>
    </p>
</div>
</body>
</html>
