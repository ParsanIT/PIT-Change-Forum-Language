<?php

/**
 * Plugin: PIT Change Forum Language
 * Description: Change the language of various sections that require changes to the database, without having to perform an upgrade or reinstallation process.
 * Version: 1.0
 * Author: firstboy000
 * Author Web: https://ParsanIT.ir
 * License: GPL v3
 */

if (!defined('IN_MYBB')) {
    die('Direct initialization of this file is not allowed.');
}

$plugins->add_hook('admin_load', 'pit_changeforumlang_admin_load');
$plugins->add_hook('admin_config_menu', 'pit_changeforumlang_admin_menu');
$plugins->add_hook('admin_config_action_handler', 'pit_changeforumlang_admin_action_handler');

function pit_changeforumlang_info()
{
    global $lang;
    $codename = str_replace('.php', '', basename(__FILE__));
    $lang->load($codename);

    return array(
        'name'          => $lang->pit_changeforumlang_pl_title,
        'description'   => $lang->pit_changeforumlang_pl_desc,
        'website'       => 'https://parsanit.ir/',
        'author'        => 'firstboy000',
        'authorsite'    => 'https://parsanit.ir/',
        'version'       => '1.3',
        'codename'      => $codename,
        'compatibility' => '18*',
        // "guid" 			=> "",
        'license'       => 'GPL v3',
    );
}

function pit_changeforumlang_activate()
{
    global $db;

    $table_name = TABLE_PREFIX . 'pit_changeforumlang_data';

    if (!$db->table_exists('pit_changeforumlang_data')) {
        $db->write_query("
            CREATE TABLE {$table_name} (
                id INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
                source_filename VARCHAR(255) NOT NULL DEFAULT '',
                kind VARCHAR(100) NOT NULL DEFAULT '',
                identifier VARCHAR(255) NOT NULL DEFAULT '',
                name VARCHAR(255) NOT NULL DEFAULT '',
                title VARCHAR(255) NOT NULL DEFAULT '',
                description TEXT NOT NULL,
                extra1 TEXT NOT NULL,
                extra2 TEXT NOT NULL,
                import_date INT(11) UNSIGNED NOT NULL DEFAULT '0',
                language_code VARCHAR(10) NOT NULL DEFAULT '',
                PRIMARY KEY (id),
                KEY source_filename (source_filename),
                KEY kind (kind),
                KEY identifier (identifier),
                KEY language_code (language_code)
            ) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci
        ");
    }

    return true;
}

function pit_changeforumlang_deactivate()
{
    global $db;

    $db->drop_table('pit_changeforumlang_data');

    return true;
}

function pit_changeforumlang_is_installed()
{
    global $db;
    if ($db->table_exists("pit_changeforumlang_data")) {
        return true;
    }
    return false;
}

function pit_changeforumlang_admin_load()
{
    global $mybb, $lang;

    if ($mybb->get_input('module') == 'config-pit-changeforumlang') {
        if (!isset($lang->pit_changeforumlang_pl_title)) {
            $codename = str_replace('.php', '', basename(__FILE__));
            $lang->load($codename);
        }
    }
}

function pit_changeforumlang_admin_menu(&$sub_menu)
{
    global $lang;
    if (!isset($lang->pit_changeforumlang_pl_title)) {
        $codename = str_replace('.php', '', basename(__FILE__));
        $lang->load($codename);
    }

    $last_key = 0;
    foreach ($sub_menu as $key => $item) {
        if ($key > $last_key) {
            $last_key = $key;
        }
    }
    $new_key = $last_key + 10;

    $sub_menu[$new_key] = array(
        'id'    => 'pit-changeforumlang',
        'title' => $lang->pit_changeforumlang_pl_title,
        'link'  => 'index.php?module=config-pit-changeforumlang'
    );
}

function pit_changeforumlang_admin_action_handler(&$actions)
{
    $actions['pit-changeforumlang'] = array(
        'active' => 'pit-changeforumlang',
        'file'   => 'pit_changeforumlang.php' //'/admin/modules/config/pit_changeforumlang.php'
    );
}

function pit_changeforumlang_message($message, $type = '')
{
    $class = '';
    if ($type == 'success' || $type == 'error') $class = $type;
    echo "<div class='{$class}'>
            <p><em>{$message}</em></p>
        </div>";
}

function pit_changeforumlang_starts_with($startString, $string)
{
    $len = strlen($startString);
    return (substr($string, 0, $len) === $startString);
}

function pit_changeforumlang_ends_with($endString, $string)
{
    $len = strlen($endString);
    if ($len == 0) {
        return true;
    }
    return (substr($string, -$len) === $endString);
}