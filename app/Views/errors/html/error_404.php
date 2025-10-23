<!DOCTYPE html>
<html lang="en">
<head>
<link rel="stylesheet" href="<?= base_url('assets/css/errors-html-error_404.css') ?>">
    <meta charset="utf-8">
    <title><?= lang('Errors.pageNotFound') ?></title>

    
</head>
<body>
    <div class="wrap">
        <h1>404</h1>

        <p>
            <?php if (ENVIRONMENT !== 'production') : ?>
                <?= nl2br(esc($message)) ?>
            <?php else : ?>
                <?= lang('Errors.sorryCannotFind') ?>
            <?php endif; ?>
        </p>
    </div>
</body>
</html>
