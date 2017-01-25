<?php
/**
 * @brief Data base manager
 * @author jribi.abdelaziz
 * @mail jribi.azouz@gmail.com
 * @date 25/12/2016
 */

/**
 * @brief Execute select query
 * @param $db
 * @param $sql
 * @return mixed
 */
function execQueryWithResult($db, $sql)
{
    $result = $db->prepare($sql);
    $result->execute();
    return $result->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * @brief Execute insert, update and delete query
 * @param $db
 * @param $sql
 * @return mixed
 */
function execQuery($db, $sql)
{
    $result = $db->prepare($sql);
    return $result->execute();
}

/**
 * @brief Get cilumns info
 * @param $db
 * @param $table
 * @return mixed
 */
function getColumnsTable($db, $table)
{
    $res = $db->prepare("SHOW columns FROM " . $table);
    $res->execute();
    return $res->fetchAll(PDO::FETCH_ASSOC);
}

/**
 * @brief Get data list of table
 * @param $db
 * @param $table
 * @param $fields
 * @return string
 */
function getListDataTable($db, $table, $fields)
{
    $id = $fields['isid'];
    unset($fields['isid']);
    $f = "";
    $t = array();
    $res = "";
    foreach ($fields as $k => $v) {
        $f .= $k . ", ";
        $t[] = $v;
    }
    $sql = "select " . $id . " as isid, " . substr(trim($f), 0, -1) . " from " . $table;
    $data = execQueryWithResult($db, $sql);
    $atad = array_reverse($data);
    $column = explode(',', substr(trim($f), 0, -1));
    if (!empty($data)) {
        foreach ($atad as $k => $v) {
            $res .= "<tr class='odd gradeX'>";
            for ($i = 0; $i < count($column); $i++) {
                $format = getFormatField($v, $column[$i], $t[$i]);
                $res .= "<td>" . $format . "</td>";
            }
            if (!notHaveAccess('show', $table)) {
                $res .= "<td><a href='dash.php?t=" . $table . "&a=view&id=" . $v['isid'] . "'><button class='btn btn-default'><i class='fa fa-eye '></i> View</button></a> ";
            }
            if (!notHaveAccess('edit', $table)) {
                $res .= "<a href='dash.php?t=" . $table . "&a=edit&id=" . $v['isid'] . "'><button class='btn btn-primary'><i class='fa fa-edit '></i> Edit</button></a> ";
            }
            if (!notHaveAccess('delete', $table)) {
                $res .= "<a href='dash.php?t=" . $table . "&a=delete&id=" . $v['isid'] . "'><button class='btn btn-danger'><i class='fa fa-trash-o'></i> Delete</button></a></td>";
            }
            $res .= "</tr>";
        }
    }

    return $res;
}

/**
 * @brief Customize the field display (data, datetime, url, image)
 * @param $line
 * @param $field
 * @param $format
 * @return bool|string
 */
function getFormatField($line, $field, $format)
{
    if (strIsExist('date', $format)) {
        $r = explode(',', $format);
        $time = strtotime($line[trim($field)]);
        if (!isset($r[1]))
            $r[1] = "d/m/Y H:i:s";
        return date($r[1], $time);
    } elseif (strIsExist('url', $format)) {
        return "<a href='" . $line[trim($field)] . "' target='_blank' >" . $line[trim($field)] . "</a>";
    } elseif (strIsExist('image', $format)) {
        return "<a href='" . $line[trim($field)] . "' target='_blank' ><img src='" . $line[trim($field)] . "' width='100' height='40' /></a>";
    } else {
        return $line[trim($field)];
    }
}