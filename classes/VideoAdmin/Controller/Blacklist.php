<?php

namespace VideoAdmin\Controller;

class Blacklist extends ControllerAbstract {

    public function add() {
        $name = null;
        $ipList = null;
        $content = null;

        $user = \VideoAdmin\Model\User::getInstance();
        $access = $user->isAdmin();

        if (!$access) {
          $content .= $this->_showError('У вас нет прав для редактирования black-листа');
        }

        if ($_SERVER['REQUEST_METHOD'] == 'POST' && $access) {
            $model = new \VideoAdmin\Model\Blacklist();

            $data = $this->_prepareData($_POST);
            $name = $data['name'];
            $ipList = $data['iplist'];

            $error = false;

            if (empty($name)) {
              $content .= $this->_showError('Укажите название black-листа');
              $error = true;
            }

            if (!$error) {
                $success = $model->add($name, $ipList);

                if ($success) {
                    $this->_redirect();
                } else {
                    $content .= $this->_showError('Произошла ошибка при добавлении black-листа');
                }
            }
        }

        if ($access) {
          $content .= $this->_showForm($name, $ipList);
        }

        $this->_layout($content, 'Создание black-листа');
    }

    public function edit() {
        $id = (int)$_GET['id'];

        $model = new \VideoAdmin\Model\Blacklist();

        $line = $model->getItem($id);

        $name = $line['name'];
        $ipList = $line['iplist'];

        $content = null;

        $user = \VideoAdmin\Model\User::getInstance();
        $access = $user->isAdmin();

        if (!$access) {
          $content .= $this->_showError('У вас нет прав для редактирования black-листа');
        }

        if ($_SERVER['REQUEST_METHOD'] == 'POST' && $access) {

            $data = $this->_prepareData($_POST);

            $name = $data['name'];
            $ipList = $data['iplist'];

            $error = false;

            if (empty($name)) {
                $content .= $this->_showError('Укажите название black-листа');
                $error = true;
            }

            if (!$error) {
                $success = $model->edit($id, $name, $ipList);

                if ($success) {
                    $this->_redirect();
                } else {
                    $content .= $this->_showError('Произошла ошибка при добавлении black-листа');
                }
            }
        }

        if ($access) {
          $content .= $this->_showForm($name, $ipList);
        }

        $this->_layout($content, 'Редактирование black-листа');
    }

    public function delete() {
        $id = (int)$_GET['id'];

        $user = \VideoAdmin\Model\User::getInstance();

        if (!$user->isAdmin()) {
          header('HTTP/1.1 403 Forbidden', true, 403);
          return;
        }

        $model = new \VideoAdmin\Model\Blacklist();
        $success = $model->delete($id);

        if ($success) {
            $this->_redirect();
        }
    }

    public function index() {
        $model = new \VideoAdmin\Model\Blacklist();

        $items = $model->getItems();

        $content = $this->_showItems($items);

        $this->_layout($content, 'Список black-листов');
    }


    protected function _prepareData($input) {
        $name = !empty($input['name']) ? trim($input['name']) : null;
        $ipList = !empty($input['iplist']) ? trim($input['iplist']) : null;

        if ($ipList) {
          $ar = preg_split("/[\;\,\r\n]+/", $ipList);
          $ar = array_map(function($ip) {
            $ip = trim($ip);

            if (!$ip) {
              return null;
            }

            //проверка подсетки и полного ip
            if (preg_match("/^([0-9]{1,3}\.){1,3}\*/", $ip) || preg_match("/^([0-9]{1,3}\.){1,3}[0-9]{1,3}\/[0-9]+/", $ip) || false !== ip2long($ip)) {
              return $ip;
            }

            return null;
          }, $ar);

          $ar = array_filter($ar);

          $ipList = implode("\r\n", $ar);
        }

        return array(
            'name' => $name,
            'iplist' => $ipList
        );
    }

    protected function _showForm($name = null, $ipList = null) {
        ob_start();
        ?>
        <form method="POST" id="translation_form">
            <div class="form-element">
                <label for="name">Название:</label>
                <input type="text" name="name" id="name" value="<?= htmlspecialchars($name) ?>" class="form-element-input form-element-large" />
            </div>

            <div class="clearer"></div>

            <div class="form-element">
                <label for="iplist">IP-адреса (через запятую или с новой строки):</label>
                <textarea name="iplist" id="iplist" class="form-element-large" style="height: 320px"><?= htmlspecialchars($ipList) ?></textarea>
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
                    <th colspan="2">Black-листы</th>
                </tr>
                <? foreach($items as $item) : ?>
                <tr>
                    <td><a href="/blacklist/edit/<?= $item['id'] ?>"><?= htmlspecialchars($item['name']) ?></a> </td>
                    <td><button type="button" class="js-delete" data-url="/blacklist/delete/<?= $item['id'] ?>"></button></td>
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
            $url = '/blacklist';
        }

        header('Location: ' . $url);
    }

}