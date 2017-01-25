<?php
/**
 * @brief generate xml fil config
 * @author jribi.abdelaziz
 * @mail jribi.azouz@gmail.com
 * @date 25/12/2016
 */

/**
 * @brief Generate the menu configuration file (config/menu.xml)
 * @param array $data
 * @return string
 */
function generateXmlMenuConfig($data = array())
{
    $xml = "<?xml version='1.0' encoding='UTF-8'?>\n";
    $xml = "<menu>\n";
    $xml .= "<!-- <item name = 'table_name' actions='list,add,remove' ><value>Menu title</value><sub action = 'action_name' >Sub menu</sub></item> -->\n";
    if (is_array($data) && !empty($data)) {
        foreach ($data as $item) {
            $title = ucfirst(str_replace('_', ' ', $item));
            $xml .= "<item name ='" . $item . "' actions='list,add,edit,delete,show' >" . $title . "\n";
            $xml .= "<sub action = 'list' >List " . $title . "</sub>\n";
            $xml .= "<sub action = 'add' >Create new " . $title . "</sub>\n</item>\n";
        }
    }
    return $xml . "</menu>";
}

/**
 * @brief Generate the database schema file (config/entities.xml)
 * @param $db
 * @param $tables
 * @return string
 */
function generateXmlDbConfig($db, $tables)
{
    $xml = "<entities>\n";
    foreach ($tables as $table) {
        $data = getColumnsTable($db, $table);
        $xml .= "\n<!-- Schema Database Table : >> " . strtoupper($table) . " << -->\n";
        $xml .= "<table name='" . $table . "' title='" . ucfirst(str_replace('_', ' ', $table)) . "' >\n";
        if (!empty($data)) {
            foreach ($data as $k => $v) {
                if ('PRI' === $v['Key']) {
                    $isId = 'yes';
                } else {
                    $isId = 'no';
                }
                $xml .= "<column name='" . $v['Field'] . "' id='" . $isId . "' >\n";
                $type = getTypeColumn($v['Type']);
                $xml .= "<type length='" . $type['length'] . "' null='" . strtolower($v['Null']) . "' >" . $type['type'] . "</type>\n";
                if (!empty($v['Key']) || !empty($v['Default']) || !empty($v['Extra'])) {
                    $xml .= "<extra";
                    if (!empty($v['Key'])) $xml .= " key='" . strtolower($v['Key']) . "'";
                    if (!empty($v['Default'])) $xml .= " default='" . $v['Default'] . "'";
                    if (!empty($v['Extra'])) {
                        $xml .= " >" . $v['Extra'] . "</extra>\n";
                    } else {
                        $xml .= " />\n";
                    }
                }
                $xml .= "<form>\n";
                if (!empty($v['Extra']) && 'auto_increment' === $v['Extra']) {
                    $xml .= "<add show='no'";
                } else {
                    $xml .= "<add show='yes'";
                }
                if ('no' === strtolower($v['Null'])) {
                    $xml .= " required='yes'";
                } else {
                    $xml .= " required='no'";
                }
                if (strpos(' ' . $type['type'], 'date')) {
                    $xml .= " format='d/m/Y H:i:s'";
                } elseif (strpos(' ' . $type['type'], 'int')) {
                    $xml .= " format='number'";
                } else {
                    $xml .= " format='default'";
                }
                $xml .= " >" . ucfirst(str_replace('_', ' ', $v['Field'])) . "</add>\n";
                $xml .= "<list show='yes'";
                if (strpos(' ' . $type['type'], 'date')) {
                    $xml .= " format='d/m/Y'";
                }
                $xml .= " >" . ucfirst(str_replace('_', ' ', $v['Field'])) . "</list>\n";
                $xml .= "</form>\n</column>\n";
            }
        }
        $xml .= "</table>\n";
    }

    return $xml . "</entities>";
}

/**
 * @brief Get type info
 * @param $type
 * @return array
 */
function getTypeColumn($type)
{
    $typeParam = array();
    if (strIsExist("date", $type)) {
        $typeParam['type'] = $type;
        $typeParam['length'] = -1;
    } elseif (strIsExist("enum", $type)) {
        $typeParam['type'] = $type;
        $typeParam['length'] = -1;
    } elseif (strIsExist("time", $type)) {
        $typeParam['type'] = $type;
        $typeParam['length'] = -1;
    } else {
        $content = explode('-', str_replace(array('(', ')'), '-', $type));
        $typeParam['type'] = $content[0];
        $typeParam['length'] = $content[1];
    }

    return $typeParam;
}

