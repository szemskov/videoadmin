<?php

namespace VideoAdmin\Controller;

class Line extends ControllerAbstract {

    public function add() {
        $name = null;
        $stream = null;
        $content = null;

        $user = \VideoAdmin\Model\User::getInstance();
        $access = $user->isAdmin();

        if (!$access) {
          $content .= $this->_showError('У вас нет прав для редактирования линий');
        }

        if ($_SERVER['REQUEST_METHOD'] == 'POST' && $access) {
            $model = new \VideoAdmin\Model\Line();

            $data = $this->_prepareData($_POST);

            $name = $data['name'];
            $stream = $data['stream'];

            $error = false;

            if (empty($name)) {
                $content .= $this->_showError('Укажите название линии');
                $error = true;
            }

            if (!$error) {
                $success = $model->add($name, $stream);

                if ($success) {
                    $this->_redirect();
                } else {
                    $content .= $this->_showError('Произошла ошибка при добавлении линии');
                }
            }
        }

        if ($access) {
          $content .= $this->_showForm($name, $stream);
        }

        $this->_layout($content, 'Создание линии');
    }

    public function edit() {
        $id = (int)$_GET['id'];

        $model = new \VideoAdmin\Model\Line();

        $line = $model->getItem($id);

        $name = $line['name'];
        $stream = $line['stream'];

        $content = null;

        $user = \VideoAdmin\Model\User::getInstance();
        $access = $user->isAdmin();

        if (!$access) {
          $content .= $this->_showError('У вас нет прав для редактирования линий');
        }

        if ($_SERVER['REQUEST_METHOD'] == 'POST' && $access) {

            $data = $this->_prepareData($_POST);

            $name = $data['name'];
            $stream = $data['stream'];

            $error = false;

            if (empty($name)) {
                $content .= $this->_showError('Укажите название линии');
                $error = true;
            }

            if (!$error) {
                $success = $model->edit($id, $name, $stream);

                if ($success) {
                    $this->_redirect();
                } else {
                    $content .= $this->_showError('Произошла ошибка при добавлении линии');
                }
            }
        }

        if ($access) {
          $content .= $this->_showForm($name, $stream);
        }

        $this->_layout($content, 'Редактирование линии');
    }

    public function delete() {
        $id = (int)$_GET['id'];

        $user = \VideoAdmin\Model\User::getInstance();

        if (!$user->isAdmin()) {
          header('HTTP/1.1 403 Forbidden', true, 403);
          return;
        }

        $model = new \VideoAdmin\Model\Line();
        $success = $model->delete($id);

        if ($success) {
            $this->_redirect();
        }
    }

    public function index() {
        $model = new \VideoAdmin\Model\Line();

        $page = !empty($_GET['page']) ? (int)$_GET['page'] : 0;

        if ($page <= 0) {
            $page = 1;
        }

        $limit = 10;
        $offset = $limit * ($page - 1);

        $items = $model->getItems(null, null, $limit, $offset);

        $content = $this->_showItems($items);
        $pager = $this->_showPager($limit, $page, $model->countTotal(), '/line/page');

        $this->_layout($content . $pager, 'Список линий');
    }

    protected function _prepareData($input) {
        $name = !empty($input['name']) ? trim($input['name']) : null;
        $stream = !empty($input['stream']) ? trim($input['stream']) : null;

        return array(
            'name' => $name,
            'stream' => $stream
        );
    }

    protected function _showForm($name = null, $stream = null) {
        ob_start();
        ?>
        <form method="POST" id="translation_form">
            <div class="form-element">
                <label for="name">Название:</label>
                <input type="text" name="name" id="name" value="<?= htmlspecialchars($name) ?>" class="form-element-input form-element-large" />
            </div>

            <div class="clearer"></div>

            <div class="form-element">
                <label for="stream">Имя потока для мониторинга:</label>
                <input type="text" name="stream" id="stream" value="<?= htmlspecialchars($stream) ?>" class="form-element-input form-element-large" />
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
                    <th>Название линии</th>
                    <th>Дата создания</th>
                </tr>
                <? foreach($items as $item) : ?>
                <tr>
                    <td><a href="/line/edit/<?= $item['id'] ?>"><?= htmlspecialchars($item['name']) ?></a> </td>
                    <td><?= date('Y-m-d H:i', strtotime($item['created'])) ?></td>
                    <td><button type="button" class="js-delete" data-url="/line/delete/<?= $item['id'] ?>"></button></td>
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
            $url = '/line';
        }

        header('Location: ' . $url);
    }

}