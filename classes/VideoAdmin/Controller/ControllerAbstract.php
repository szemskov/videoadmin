<?php

namespace VideoAdmin\Controller;

class ControllerAbstract {

    public function __construct() {
        header('Content-Type: text/html; charset=UTF-8', true);
    }

    protected function _showMessage($msg) {
        ob_start();
        ?>
        <p><b><?= $msg ?></b></p>
        <?
        return ob_get_clean();
    }

    protected function _showError($error) {
        ob_start();
        ?>
        <p class="error"><?= $error ?></p>
        <?
        return ob_get_clean();
    }

    protected function _showPager($perPage, $page, $total, $baseUrl, array $filter = null) {
        ob_start();

        if ($total > $perPage) {

            $pages = ceil($total / $perPage);

            if ($filter) {
              $filter = http_build_query($filter);
            }

            ?>
        <div class="paginator" id="paginator"></div>
        <script>
            $('#paginator').bootpag({
                total: <?= $pages ?>,
                href: "<?= $baseUrl ?>/{{number}}<?= ($filter ? '?' . $filter : '') ?>",
                page: <?= $page ?>
            });
        </script>
        <?
        }

        $content = ob_get_clean();
        return $content;
    }

    protected function _layout($content = null, $title = null) {

        $url = $_SERVER['REQUEST_URI'];

        if ($url == '/') {
            $url = \VideoAdmin\Menu::getDefaultUrl();
        }

        ?>
        <!doctype html>
        <html>
        <head>
            <script type="text/javascript" src="/js/jquery-1.9.min.js"></script>
            <script type="text/javascript" src="/js/jquery-ui.js"></script>
            <!--<script src="//netdna.bootstrapcdn.com/twitter-bootstrap/2.1.1/js/bootstrap.min.js"></script>-->
            <script src="/js/jquery.bootpag.js"></script>
            <script src="/js/jquery.jgrowl.js"></script>
            <link rel="stylesheet" href="/css/jquery.jgrowl.css" />
            <link rel="stylesheet" href="/css/jquery-ui.css" />
            <link rel="stylesheet" href="/css/bootstrap-combined.min.css" />

            <link rel="stylesheet" href="/css/admin.css" />
        </head>
        </html>

        <body>
        <div class="menu">
            <ul>
                <? foreach (\VideoAdmin\Menu::getItems() as $item) : ?>
                <? if ($url == $item['url']) : ?>
                    <li><?= $item['title'] ?></li>
                    <? else : ?>
                    <li><a href="<?= $item['url'] ?>"><?= $item['title'] ?></a></li>
                    <? endif; ?>
                <? endforeach; ?>
            </ul>
            <div class="clearer"></div>
        </div>
        <div class="title">
            <h1><?= $title ?></h1>
        </div>
        <div class="content">
            <?= $content ?>
        </div>
        </body>
        <?
    }

}