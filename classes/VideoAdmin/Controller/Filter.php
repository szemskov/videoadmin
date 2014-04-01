<?php

namespace VideoAdmin\Controller;

class Filter extends ControllerAbstract {

    public function add() {
        $name = null;
        $keywords = null;
        $content = null;

        $user = \VideoAdmin\Model\User::getInstance();
        $access = $user->isAdmin();

        if (!$access) {
          $content .= $this->_showError('У вас нет прав для редактирования фильтров');
        }

        if ($_SERVER['REQUEST_METHOD'] == 'POST' && $access) {
            $model = new \VideoAdmin\Model\Filter();

            $data = $this->_prepareData($_POST);

            $name = $data['name'];
            $keywords = $data['keywords'];

            $error = false;

            if (empty($name)) {
                $content .= $this->_showError('Укажите название линии');
                $error = true;
            }

            if (!$error) {
                $success = $model->add($name, $keywords);

                if ($success) {
                    $this->_redirect();
                } else {
                    $content .= $this->_showError('Произошла ошибка при добавлении фильтра');
                }
            }
        }

        if ($access) {
          $content .= $this->_showForm($name, $keywords);
        }

        $this->_layout($content, 'Создание фильтра');
    }

    public function edit() {
        $id = (int)$_GET['id'];

        $model = new \VideoAdmin\Model\Filter();

        $line = $model->getItem($id);

        $name = $line['name'];
        $keywords = $line['keywords'];

        $content = null;

        $user = \VideoAdmin\Model\User::getInstance();
        $access = $user->isAdmin();

        if (!$access) {
          $content .= $this->_showError('У вас нет прав для редактирования фильтров');
        }

        if ($_SERVER['REQUEST_METHOD'] == 'POST' && $access) {

            $data = $this->_prepareData($_POST);

            $name = $data['name'];
            $keywords = $data['keywords'];

            $error = false;

            if (empty($name)) {
                $content .= $this->_showError('Укажите название фильтра');
                $error = true;
            }

            if (!$error) {
                $success = $model->edit($id, $name, $keywords);

                if ($success) {
                    $this->_redirect();
                } else {
                    $content .= $this->_showError('Произошла ошибка при добавлении фильтра');
                }
            }
        }

        if ($access) {
          $content .= $this->_showForm($name, $keywords);
        }

        $this->_layout($content, 'Редактирование фильтра');
    }

    public function delete() {
        $id = (int)$_GET['id'];

        $user = \VideoAdmin\Model\User::getInstance();

        if (!$user->isAdmin()) {
          header('HTTP/1.1 403 Forbidden', true, 403);
          return;
        }

        $model = new \VideoAdmin\Model\Filter();
        $success = $model->delete($id);

        if ($success) {
            $this->_redirect();
        }
    }

    public function index() {
        $model = new \VideoAdmin\Model\Filter();

        $page = !empty($_GET['page']) ? (int)$_GET['page'] : 0;

        if ($page <= 0) {
            $page = 1;
        }

        //$limit = 10;
        //$offset = $limit * ($page - 1);

        $items = $model->getItems();

        $content = $this->_showItems($items);
        //$pager = $this->_showPager($limit, $page, $model->countTotal(), '/line/page');

        $this->_layout($content, 'Список фильтров');
    }

    public function select() {
        $model = new \VideoAdmin\Model\Filter();
        $items = $model->getItems('name', 'asc');

        $data = array();

        foreach ($items as $item) {
          $data[] = $item->toArray();
        }

        header('Content-Type: application/json');

        echo json_encode($data);
        exit;
    }

    protected function _prepareData($input) {
        $name = !empty($input['name']) ? trim($input['name']) : null;
        $keywords = !empty($input['keywords']) ? trim($input['keywords']) : null;

        return array(
            'name' => $name,
            'keywords' => $keywords
        );
    }

    protected function _showForm($name = null, $keywords = null) {
        ob_start();
        ?>
        <form method="POST" id="translation_form">
            <div class="form-element">
                <label for="name">Название:</label>
                <input type="text" name="name" id="name" value="<?= htmlspecialchars($name) ?>" class="form-element-input form-element-large" />
            </div>

            <div class="clearer"></div>

            <div class="form-element">
                <label for="keywords">Ключевые слова (через точку с запятой):</label>
                <textarea name="keywords" id="keywords" class="form-element-large" style="height: 140px"><?= htmlspecialchars($keywords) ?></textarea>
            </div>

            <div class="clearer"></div>

            <input type="submit" value="Сохранить" />
        </form>
        <?
        $content = ob_get_clean();

        return $content;
    }

    protected function _showItems(array $items) {
        ob_start();
        ?>
        <div class="items">
            <table cellspacing="0">
                <tr>
                    <th colspan="2">Название фильтра</th>
                </tr>
                <? foreach($items as $item) : ?>
                <tr>
                    <td><a href="/filter/edit/<?= $item['id'] ?>"><?= htmlspecialchars($item['name']) ?></a> </td>
                    <td><button type="button" class="js-delete" data-url="/filter/delete/<?= $item['id'] ?>"></button></td>
                </tr>
                <? endforeach; ?>
            </table>


        </div>
        <script>
            $(document).ready(function() {
                $(".js-delete").button({
                    icons: {
                        primary: "ui-icon-trash"
                    },
                    text: false
                }).click(function() {
                    if (confirm('Удалить выбранную запись?')) {
                        location.href = $(this).attr('data-url');
                    }
                });
            });
        </script>
    <?
        $content = ob_get_clean();

        return $content;
    }

    protected function _redirect($url = null) {
        if (!$url) {
            $url = '/filter';
        }

        header('Location: ' . $url);
    }

}