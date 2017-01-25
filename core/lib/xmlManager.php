<?php
/**
 * @brief xml manager
 * @author jribi.abdelaziz
 * @mail jribi.azouz@gmail.com
 * @date 25/12/2016
 */

/**
 * @brief Generate nav menu
 * @return string
 */
function getMenuList()
{
    $doc = simplexml_load_file(HOME_DIR . "/config/menu.xml");
    $items = $doc->item;
    $menu = "";
    $styles = array(
        'list' => 'fa-list',
        'add' => 'fa-plus'
    );
    foreach ($items as $item) {
        $subs = $item->sub;
        $actions = explode(',', $item['actions']);
        $subMenu = "";
        foreach ($subs as $sub) {
            if (in_array($sub['action'], $actions)) {
                $subMenu .= "<li><a href='dash.php?t=" . $item['name'] . "&a=" . $sub['action'] . "'><i class='fa " . $styles[$sub['action']->__toString()] . "'></i> " . $sub . " </a></li>";
            }
        }
        if (!empty($subMenu)) {
            $menu .= "<li><a><i class='fa fa-play'></i> " . $item . " <span class='fa arrow'></span></a><ul class='nav nav-second-level'>" . $subMenu . "</ul></li>";
        } elseif (in_array('list', $actions)) {
            $menu .= "<li><a><i class='fa fa-play'></i> " . $item . " </a></li>";
        }

    }
    return $menu;
}

/**
 * @brief Generate data list
 * @param $db
 * @param $table
 * @return string
 */
function getListTable($db, $table)
{
    $doc = simplexml_load_file(HOME_DIR . "/config/entities.xml");
    $tab = $doc->xpath("//table[@name='" . $table . "']")[0];
    $list = "<div class='row'><div class='col-md-12'><div class='panel panel-default'><div class='panel-heading'>" . $tab['title'] . " List</div><div class='panel-body'> <div class='table-responsive'> <table class='table table-striped table-bordered table-hover' id='dataTables-example'><thead><tr>";
    $fields = array();
    foreach ($tab->xpath('./column') as $column) {
        if ('yes' === $column['id']->__toString()) {
            $fields['isid'] = $column['name'];
        }
        if ('yes' === $column->form->list['show']->__toString()) {
            $list .= "<th> " . $column->form->list . " </th>";
            $fields[$column['name']->__toString()] = $column->type;
            if (isset($column->form->list['format'])) {
                $fields[$column['name']->__toString()] .= "," . $column->form->list['format'];
            }
        }
    }
    $list .= "<th> Action </th>";
    $list .= "</tr></thead><tbody>";
    $list .= getListDataTable($db, $table, $fields);

    return $list . "</tbody></table></div></div></div></div></div>";
}

/**
 * @brief Generate create and edit forms
 * @param $db
 * @param $table
 * @param $action
 * @param int $id
 * @return bool|string
 */
function getFormSetTable($db, $table, $action, $id = 0)
{
    $dataTable = null;
    switch ($action) {
        case 'add' :
            $title = "Create new";
            break;
        case 'edit' :
            $title = "Edit";
            $params = getIdColumn($table);
            $dataTable = execQueryWithResult($db, "select * from " . $table . " where " . $params['name'] . " = " . getValueByType($params['type'], $id))[0];
            break;
        default :
            return false;
    }
    $doc = simplexml_load_file(HOME_DIR . "/config/entities.xml");
    $tab = $doc->xpath("//table[@name='" . $table . "']")[0];
    $form = "<div class='row'><div class='col-lg-12'><div class='panel panel-default'><div class='panel-heading'>" . $title . " " . ucfirst(str_replace('_', ' ', $table)) . "</span></div><div class='panel-body'><div class='row'><div class='col-lg-12'><form role='form' method='post' action=''>";
    foreach ($tab->xpath('./column') as $column) {
        if ('auto_increment' != $column->extra->__toString() && 'yes' === $column->form->add['show']->__toString()) {
            $required = '';
            if (!empty($dataTable)) {
                $value = $dataTable[$column['name']->__toString()];
            } else {
                $value = '';
            }
            if ('no' === $column->type['null']->__toString()) {
                $required = 'required';
            }
            if (isset($column->index)) {
                $form .= getIndexForm($db, $column, $required, $value);
            } else {
                $form .= getFormFieldFormat($column, $required, $value);
            }
        }
    }
    $form .= "<button type='submit' class='btn btn-success'>Submit</button> <button type='reset' class='btn btn-warning'>Reset</button><span class='small'> (*) Required Field</span><input type='hidden' name='table' value='" . $table . "' /><input type='hidden' name='action' value='" . $action . "' /></form></div></div></div></div></div></div>";

    return $form;
}

/**
 * @brief Customize the field display
 * @param $line
 * @param $required
 * @param $value
 * @return string
 */
function getFormFieldFormat($line, $required, $value)
{
    $input = getInputFormat();
    $maxlength = "maxlength = '" . $line->type['length'] . "'";
    if (isset($input[$line->form->add['format']->__toString()])) {
        $placeholder = $input[$line->form->add['format']->__toString()];
        $inputType = "type='" . $line->form->add['format'] . "'";
    } else {
        $placeholder = $input['default'];
        $inputType = "";
    }
    if (strIsExist("enum", $line->type)) {
        $data = "<div class='form-group'><label>" . $line->form->add . " " . requiedFieldForm($required) . "</label><select class='form-control' " . $required . " ><option />";
        $content = explode('-', str_replace(array('(', ')'), '-', $line->type));
        $enum = explode(',', str_replace("'", "", $content[1]));
        foreach ($enum as $v) {
            if ($value === $v) {
                $data .= "<option value='" . $v . "' selected>" . $v . "</option>";
            } else {
                $data .= "<option value='" . $v . "'>" . $v . "</option>";
            }
        }
        return $data . "</select></div>";
    } elseif (strIsExist("date", $line->type)) {
        return "<div class='form-group'><label>" . $line->form->add . " " . requiedFieldForm($required) . "</label><input class='form-control' type='date' placeholder='" . $placeholder . "' name='" . $line['name'] . "' value='" . $value . "' " . $required . " /></div>";
    } else {
        return "<div class='form-group'><label>" . $line->form->add . " " . requiedFieldForm($required) . "</label><input class='form-control' " . $inputType . "  placeholder='" . $placeholder . "' name='" . $line['name'] . "' value='" . $value . "' " . $maxlength . " " . $required . " /></div>";
    }
}

/**
 * @brief Generate table join
 * @param $db
 * @param $line
 * @param $required
 * @param $value
 * @return string
 */
function getIndexForm($db, $line, $required, $value)
{
    $index = "<div class='form-group'><label>" . $line->form->add . " " . requiedFieldForm($required) . "</label><select class='form-control' " . $required . " ><option />";
    $sql = "select " . $line->index['key'] . ", " . $line->index . " from " . $line->index['table'];
    $data = execQueryWithResult($db, $sql);
    if (!empty($data)) {
        foreach ($data as $k => $v) {
            if ($value === $v[$line->index->__toString()]) {
                $index .= "<option value='" . $v[$line->index['key']->__toString()] . "' selected>" . $v[$line->index->__toString()] . "</option>";
            } else {
                $index .= "<option value='" . $v[$line->index['key']->__toString()] . "'>" . $v[$line->index->__toString()] . "</option>";
            }
        }
    }

    return $index . "</select></div>";
}

/**
 * @brief The function responsible for creating and updating the entity
 * @param $db
 * @param $table
 * @param $action
 * @param $data
 * @param int $id
 * @return mixed
 */
function setDataTabel($db, $table, $action, $data, $id = 0)
{
    $doc = simplexml_load_file(HOME_DIR . "/config/entities.xml");
    $columns = $doc->xpath("//table[@name='" . $table . "']/column");
    $types = array();
    foreach ($columns as $col) {
        if ('auto_increment' !== $col->extra->__toString())
            $types[$col['name']->__toString()] = $col->type->__toString();
    }
    $sql = "";
    switch ($action) {
        case "add":
            $sql .= "insert into " . $table;
            break;
        case "edit":
            $sql .= "update " . $table . " set ";
            break;
        default:
            return false;
    }
    $fields = "";
    $values = "";
    foreach ($data as $k => $v) {
        if (in_array(strtoupper($types[$k]), getAllNumericTypes())) {
            if ($v) {
                $fields .= $k . ", ";
                $values .= $v . ", ";
                unset($types[$k]);
            }
        } else {
            if ($v) {
                $values .= "'" . $v . "', ";
                $fields .= $k . ", ";
                unset($types[$k]);
            }
        }
    }
    if ('add' === $action) {
        foreach ($types as $k => $v) {
            $fields .= $k . ", ";
            $col = $doc->xpath("//table[@name='" . $table . "']/column[@name='" . $k . "']")[0];
            if ('yes' === $col->type['null']->__toString()) {
                $values .= "null, ";
            } elseif (in_array(strtoupper($v), getAllNumericTypes())) {
                $values .= "0, ";
            } elseif (strIsExist("date", $v)) {
                $values .= "now(), ";
            } else {
                $values .= "'', ";
            }
        }
        $sql .= " (" . substr(trim($fields), 0, -1) . ") values (" . substr(trim($values), 0, -1) . ")";
    } else {
        $f = explode(',', substr(trim($fields), 0, -1));
        $v = explode(',', substr(trim($values), 0, -1));
        for ($i = 0; $i < count($f); $i++) {
            $sql .= $f[$i] . " = " . $v[$i] . " ";
        }
        $key = getIdColumn($table);
        $sql .= "where " . $key['name'] . " = " . getValueByType($key['type'], $id);
    }

    return execQuery($db, $sql);
}

/**
 * @brief The function responsible for deleting the entity
 * @param $db
 * @param $table
 * @param $id
 * @return mixed
 */
function deleteDataTable($db, $table, $id)
{
    $key = getIdColumn($table);
    return execQuery($db, "delete from " . $table . " where " . $key['name'] . " = " . getValueByType($key['type'], $id));
}

/**
 * @brief The function responsible for viewing the entity
 * @param $db
 * @param $table
 * @param $id
 * @return string
 */
function viewDataTable($db, $table, $id)
{
    $doc = simplexml_load_file(HOME_DIR . "/config/entities.xml");
    $columns = $doc->xpath("//table[@name='" . $table . "']/column");
    $params = getIdColumn($table);
    $dataTable = execQueryWithResult($db, "select * from " . $table . " where " . $params['name'] . " = " . getValueByType($params['type'], $id))[0];
    $form = "<div class='row'><div class='col-lg-12'><div class='panel panel-default'><div class='panel-heading'>View " . ucfirst(str_replace('_', ' ', $table)) . "</span></div><div class='panel-body'><div class='row'><div class='col-lg-12'><form role='form' method='post' action=''>";
    foreach ($columns as $column) {
        $form .= "<div class='form-group'><label>" . $column->form->add . "</label><input class='form-control' value='" . $dataTable[$column['name']->__toString()] . "' readonly/></div>";
    }
    return $form . "</form></div></div></div></div></div></div>";
}