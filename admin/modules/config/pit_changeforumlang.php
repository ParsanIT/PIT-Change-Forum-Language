<?php

/**
 * Plugin: PIT Change Forum Language
 * Description: Change the language of various sections that require changes to the database, without having to perform an upgrade or reinstallation process.
 * Version: 1.0
 * Author: firstboy000
 * Author Web: https://ParsanIT.ir
 * License: GPL v3
 */

if (!defined('IN_MYBB') || !defined('IN_ADMINCP')) die('Direct initialization of this file is not allowed.');

function pit_changeforumlang_admin_module()
{
    global $mybb, $lang;

    if ($mybb->get_input('module') == 'config-pit-changeforumlang') {
        if (!isset($lang->pit_changeforumlang_pl_title)) {
            $codename = str_replace('.php', '', basename(__FILE__));
            $lang->load($codename);
        }

        if ($mybb->get_input('action') == 'save') {
            return pit_changeforumlang_save_settings();
        } else if ($mybb->get_input('action') == 'preconfirm') {
            return pit_changeforumlang_preconfirm();
        }

        return pit_changeforumlang_show_form();
    }
}

function pit_changeforumlang_show_form()
{
    global $lang, $page;

    $page->add_breadcrumb_item($lang->pit_changeforumlang_pl_title, 'index.php?module=config-pit-changeforumlang');
    $page->output_header($lang->pit_changeforumlang_pl_title);

    $sub_tabs['pit-changeforumlang'] = array(
        'title'         => $lang->pit_changeforumlang_pl_title,
        'description'   => $lang->pit_changeforumlang_pl_desc,
        'link'          => 'index.php?module=config-pit-changeforumlang',
    );
    $page->output_nav_tabs($sub_tabs, 'pit-changeforumlang');

    $languages = array();

    $plugin_dir = MYBB_ROOT . 'inc/plugins/';
    $language_dir = $plugin_dir . 'pit_changeforumlang_languages/';
    if (!is_dir($language_dir)) {
        @mkdir($language_dir, 0755, true);
    }

    $languagepacks = $lang->get_languages();

    if (is_dir($language_dir)) {
        $folders = scandir($language_dir);
        foreach ($folders as $folder) {
            if ($folder != '.' && $folder != '..' && is_dir($language_dir . $folder)) {
                if (array_key_exists($folder, $languagepacks)) {
                    $languages[$folder] = $languagepacks[$folder];
                }
            }
        }
    }

    $form = new Form("index.php?module=config-pit-changeforumlang&amp;action=preconfirm", "get");
    echo $form->generate_hidden_field('module', 'config-pit-changeforumlang');
    echo $form->generate_hidden_field('action', 'preconfirm');

    $form_container = new FormContainer($lang->pit_changeforumlang_header);

    $form_container->output_row($lang->pit_changeforumlang_help, $lang->pit_changeforumlang_help_desc);
    $form_container->output_row(
        $lang->pit_changeforumlang_select_lang,
        $lang->pit_changeforumlang_select_lang_desc,
        $form->generate_select_box('selected_language', $languages, '', array('id' => 'selected_language')),
        'selected_language'
    );

    $form_container->end();

    $buttons[] = $form->generate_submit_button($lang->pit_changeforumlang_check_requirement);
    $form->output_submit_wrapper($buttons);
    $form->end();

    $page->output_footer();
}

function pit_changeforumlang_preconfirm()
{
    global $db, $mybb, $lang, $page;

    $page->add_breadcrumb_item($lang->pit_changeforumlang_pl_title, 'index.php?module=config-pit-changeforumlang');
    $page->output_header($lang->pit_changeforumlang_pl_title);

    $sub_tabs['pit-changeforumlang'] = array(
        'title'         => $lang->pit_changeforumlang_pl_title,
        'description'   => $lang->pit_changeforumlang_pl_desc,
        'link'          => 'index.php?module=config-pit-changeforumlang',
    );
    $page->output_nav_tabs($sub_tabs, 'pit-changeforumlang');

    if ($mybb->get_input('action') != 'preconfirm') {
        admin_redirect("index.php?module=config-pit-changeforumlang");
        return false;
    }

    $should_exists_file = array(
        "settings.xml" => array("filename" => "settings.xml", "isexist" => false),
        "tasks.xml" => array("filename" => "tasks.xml", "isexist" => false),
        "usergroups.xml" => array("filename" => "usergroups.xml", "isexist" => false),
        "adminviews.xml" => array("filename" => "adminviews.xml", "isexist" => false),
    );

    $selected_language = $mybb->get_input('selected_language', MyBB::INPUT_STRING);
    $selected_language_dir = MYBB_ROOT . 'inc/plugins/pit_changeforumlang_languages/' . $selected_language . '/';

    if (!is_dir($selected_language_dir)) {
        flash_message($lang->pit_changeforumlang_selected_lang_does_not_exist . htmlspecialchars($selected_language), "error");
        admin_redirect("index.php?module=config-pit-changeforumlang");
        return false;
    }

    // $some_file_is_missing = false;
    foreach ($should_exists_file as $filename => $item) {
        if (is_file($selected_language_dir . $filename)) {
            $should_exists_file[$filename]["isexist"] = true;
            $should_exists_file[$filename]["path"] = $selected_language_dir . $filename;
        } else {
            // $some_file_is_missing = true;
        }
    }

    echo "<p>{$lang->pit_changeforumlang_check_file_status}...</p>
            <ul>";

    foreach ($should_exists_file as $filename => $item) {
        $icon_url = $mybb->settings['bburl'] . '/admin/styles/default/images/icons/';
        if ($item["isexist"]) $icon_url .= 'success.png';
        else {
            $icon_url .= 'error.png';
            // $some_file_is_missing = true;
        }
        echo "<li style=\"list-style: none;\"><img src=\"{$icon_url}\"> {$filename}</li>";
    }
    echo '</ul>';

    /* if ($some_file_is_missing == true) {
        pit_changeforumlang_message($lang->pit_changeforumlang_some_file_does_not_exist, 'error');
        $page->output_footer();
        return false;
    } */


    $db->delete_query('pit_changeforumlang_data');
    foreach ($should_exists_file as $filename => $item) {
        if ($item['isexist'] == true) {
            $data = pit_changeforumlang_xml_reader_controller($filename, $item['path']);
            if (isset($data->error)) {
                pit_changeforumlang_message("{$lang->pit_changeforumlang_issue_on_read_xml}<br><b>{$filename}</b> <p>{$data['error']}</p>", 'error');
                return false;
            }
        }
    }



    pit_changeforumlang_message($lang->pit_changeforumlang_ready_to_apply, 'success');

    $form = new Form("index.php?module=config-pit-changeforumlang&amp;action=save", "post");
    echo $form->generate_hidden_field('selected_language', $selected_language);
    update_admin_session('should_exists_file', $should_exists_file);

    $buttons[] = $form->generate_submit_button($lang->pit_changeforumlang_apply);
    $form->output_submit_wrapper($buttons);
    $form->end();

    $page->output_footer();
}

function pit_changeforumlang_save_settings()
{
    global $db, $mybb, $lang, $admin_session;

    if ($mybb->request_method != "post") {
        admin_redirect("index.php?module=config-pit-changeforumlang");
        return false;
    }

    verify_post_check($mybb->get_input('my_post_key'));
    $selected_language = $mybb->get_input('selected_language', MyBB::INPUT_STRING);
    $should_exists_file = '';
    if (isset($admin_session['data']['should_exists_file']) && $admin_session['data']['should_exists_file']) {
        $should_exists_file = $admin_session['data']['should_exists_file'];
        update_admin_session('should_exists_file', ''); // better to use new general function for unset
    }

    if (!isset($should_exists_file) || !is_array($should_exists_file)) {
        flash_message($lang->pit_changeforumlang_error_occurred, 'error');
        admin_redirect("index.php?module=config-pit-changeforumlang");
        return false;
    }

    $query = $db->query("SELECT kind, identifier, COUNT(id) FROM `mybb_pit_changeforumlang_data` GROUP BY kind, identifier HAVING COUNT(id) > 1;");
    $count_result = $db->num_rows($query);
    if ($count_result > 0) {
        flash_message($lang->pit_changeforumlang_found_self_duplicated, 'error');
        admin_redirect("index.php?module=config-pit-changeforumlang");
        return false;
    }

    if ($should_exists_file['settings.xml']['isexist']) {
        $query2 = $db->query("SELECT name, COUNT(sid) FROM `mybb_settings` GROUP BY name HAVING COUNT(sid) > 1;");
        $count_result = $db->num_rows($query2);
        if ($count_result > 0) {
            flash_message($lang->pit_changeforumlang_found_settings_duplicated, 'error');
            admin_redirect("index.php?module=config-pit-changeforumlang");
            return false;
        }
    }

    foreach ($should_exists_file as $filename => $item) {
        if ($item['isexist'] == true) {
            $data = pit_changeforumlang_apply_controller($filename);
            if (isset($data->error)) {
                pit_changeforumlang_message($lang->pit_changeforumlang_error_occurred, 'error');
                return false;
            }
        }
    }

    $selected_language = isset($selected_language) ? $selected_language : 'english';

    $db->update_query('settings', array('value' => $db->escape_string($selected_language)), "name = 'bblanguage' OR name = 'cplanguage'");
    $lang->set_language($selected_language, "user");
    $lang->set_language($selected_language, "admin");
    rebuild_settings();
    $languagepacks = $lang->get_languages();

    flash_message($lang->sprintf($lang->pit_changeforumlang_finish, htmlspecialchars($languagepacks[$selected_language])), "success");
    admin_redirect("index.php?module=config-pit-changeforumlang");
}

function pit_changeforumlang_xml_reader_controller($filename, $file_path)
{
    if ($filename == 'settings.xml') {
        return pit_changeforumlang_settings_xml_reader($filename, $file_path);
    } else if ($filename == 'tasks.xml') {
        return pit_changeforumlang_tasks_xml_reader($filename, $file_path);
    } else if ($filename == 'usergroups.xml') {
        return pit_changeforumlang_usergroups_xml_reader($filename, $file_path);
    } else if ($filename == 'adminviews.xml') {
        return pit_changeforumlang_adminviews_xml_reader($filename, $file_path);
    }
    return array('error' => true);
}
function pit_changeforumlang_settings_xml_reader($filename, $file_path)
{
    global $db;

    if (!file_exists($file_path)) {
        return array('error' => 'File not found');
    }

    $dom = new DOMDocument();
    $load_result = $dom->load($file_path);
    if (!$load_result) {
        return array('error' => 'Invalid XML format');
    }

    if (!$dom->getElementsByTagName('settings')) {
        return array('error' => 'No root element found in XML');
    }

    $data = array();

    $root = $dom->getElementsByTagName('settings')->item(0);
    $data['version'] = $root->getAttribute('version');
    $data['settings'] = array();

    $settinggroup_doms = $root->getElementsByTagName('settinggroup');
    foreach ($settinggroup_doms as $settinggroup_dom) {
        $identifier = $settinggroup_dom->getAttribute('name');
        $name = $identifier;
        $title = $settinggroup_dom->getAttribute('title');
        $description = $settinggroup_dom->getAttribute('description');

        $settinggroup_data = array(
            'identifier' => $identifier ? $identifier : '',
            'name' => $name ? $name : '',
            'title' => $title ? $title : '',
            'description' => $description ? $description : '',
            'settings_data' => array(),
        );

        $setting_doms = $settinggroup_dom->getElementsByTagName('setting');
        $settings_data = array();
        foreach ($setting_doms as $setting_dom) {
            $setting_identifier = $setting_dom->getAttribute('name');
            $setting_name = $setting_identifier;

            $setting_title = $setting_dom->getElementsByTagName('title')->item(0);
            $setting_description = $setting_dom->getElementsByTagName('description')->item(0);
            $setting_optionscode = $setting_dom->getElementsByTagName('optionscode')->item(0);
            $setting_settingvalue = $setting_dom->getElementsByTagName('settingvalue')->item(0);
            // $description = $setting_dom->item(0);
            $setting_data = array(
                'identifier' => $setting_identifier ? $setting_identifier : '',
                'name' => $setting_name ? $setting_name : '',
                'title' => $setting_title ? $setting_title->nodeValue : '',
                'description' => $setting_description ? $setting_description->nodeValue : '',
                'optionscode' => $setting_optionscode ? $setting_optionscode->nodeValue : '',
                'settingvalue' => $setting_settingvalue ? $setting_settingvalue->nodeValue : '',
            );
            $settings_data[] = $setting_data;
            $db->insert_query('pit_changeforumlang_data', array(
                'source_filename' => $db->escape_string($filename),
                'kind' => 'setting',
                'identifier' => $db->escape_string($setting_data['identifier']),
                'name' => $db->escape_string($setting_data['name']),
                'title' => $db->escape_string($setting_data['title']),
                'description' => $db->escape_string($setting_data['description']),
                'extra1' => $db->escape_string($setting_data['optionscode']),
                'extra2' => $db->escape_string($setting_data['settingvalue']),
                'language_code' => '',
            ));
        }
        $settinggroup_data['settings_data'] = $settings_data;

        $data['settings'][] = $settinggroup_data;
        $db->insert_query('pit_changeforumlang_data', array(
            'source_filename' => $db->escape_string($filename),
            'kind' => 'settinggroup',
            'identifier' => $db->escape_string($settinggroup_data['identifier']),
            'name' => $db->escape_string($settinggroup_data['name']),
            'title' => $db->escape_string($settinggroup_data['title']),
            'description' => $db->escape_string($settinggroup_data['description']),
            'language_code' => '',
        ));
    }

    return $data;
}
function pit_changeforumlang_tasks_xml_reader($filename, $file_path)
{
    global $db;
    if (!file_exists($file_path)) {
        return array('error' => 'File not found');
    }

    $dom = new DOMDocument();
    $load_result = $dom->load($file_path);
    if (!$load_result) {
        return array('error' => 'Invalid XML format');
    }

    if (!$dom->getElementsByTagName('tasks')) {
        return array('error' => 'No root element found in XML');
    }

    $data = array();

    $root = $dom->getElementsByTagName('tasks')->item(0);
    $data['version'] = $root->getAttribute('version');
    $data['exported'] = $root->getAttribute('exported');
    $data['tasks'] = array();

    $tasks = $root->getElementsByTagName('task');
    foreach ($tasks as $task) {
        $title = $task->getElementsByTagName('title')->item(0);
        $description = $task->getElementsByTagName('description')->item(0);
        $file = $task->getElementsByTagName('file')->item(0);
        $identifier = $file;

        $task_data = array(
            'identifier' => $identifier ? $identifier->nodeValue : '',
            'title' => $title ? $title->nodeValue : '',
            'description' => $description ? $description->nodeValue : '',
            'file' => $file ? $file->nodeValue : '',
        );

        $data['tasks'][] = $task_data;
        $db->insert_query('pit_changeforumlang_data', array(
            'source_filename' => $db->escape_string($filename),
            'kind' => 'task',
            'identifier' => $db->escape_string($task_data['identifier']),
            'name' => $db->escape_string($task_data['file']),
            'title' => $db->escape_string($task_data['title']),
            'description' => $db->escape_string($task_data['description']),
            'language_code' => '',
        ));
    }

    return $data;
}
function pit_changeforumlang_usergroups_xml_reader($filename, $file_path)
{
    global $db;
    if (!file_exists($file_path)) {
        return array('error' => 'File not found');
    }

    $dom = new DOMDocument();
    $load_result = $dom->load($file_path);
    if (!$load_result) {
        return array('error' => 'Invalid XML format');
    }

    if (!$dom->getElementsByTagName('usergroups')) {
        return array('error' => 'No root element found in XML');
    }

    $data = array();

    $root = $dom->getElementsByTagName('usergroups')->item(0);
    $data['version'] = $root->getAttribute('version');
    $data['exported'] = $root->getAttribute('exported');
    $data['usergroups'] = array();

    $usergroups_dom = $root->getElementsByTagName('usergroup');
    foreach ($usergroups_dom as $usergroup_dom) {
        $identifier = $usergroup_dom->getElementsByTagName('gid')->item(0);
        $gid = $identifier;
        $title = $usergroup_dom->getElementsByTagName('title')->item(0);
        $description = $usergroup_dom->getElementsByTagName('description')->item(0);
        $usertitle = $usergroup_dom->getElementsByTagName('usertitle')->item(0);

        $usergroup_data = array(
            'identifier' => $identifier ? $identifier->nodeValue : '',
            'gid' => $gid ? $gid->nodeValue : '',
            'title' => $title ? $title->nodeValue : '',
            'description' => $description ? $description->nodeValue : '',
            'usertitle' => $usertitle ? $usertitle->nodeValue : '',
        );

        $data['usergroups'][] = $usergroup_data;
        $db->insert_query('pit_changeforumlang_data', array(
            'source_filename' => $db->escape_string($filename),
            'kind' => 'usergroup',
            'identifier' => $db->escape_string($usergroup_data['identifier']),
            'name' => $db->escape_string($usergroup_data['gid']),
            'title' => $db->escape_string($usergroup_data['title']),
            'description' => $db->escape_string($usergroup_data['description']),
            'extra1' => $db->escape_string($usergroup_data['usertitle']),
            'language_code' => '',
        ));
    }

    return $data;
}
function pit_changeforumlang_adminviews_xml_reader($filename, $file_path)
{
    global $db;
    if (!file_exists($file_path)) {
        return array('error' => 'File not found');
    }

    $dom = new DOMDocument();
    $load_result = $dom->load($file_path);
    if (!$load_result) {
        return array('error' => 'Invalid XML format');
    }

    if (!$dom->getElementsByTagName('adminviews')) {
        return array('error' => 'No root element found in XML');
    }

    $data = array();

    $root = $dom->getElementsByTagName('adminviews')->item(0);
    $data['version'] = $root->getAttribute('version');
    $data['exported'] = $root->getAttribute('exported');
    $data['adminviews'] = array();

    $adminviews = $root->getElementsByTagName('view');
    foreach ($adminviews as $adminview) {
        $vid = $adminview->getAttribute('vid');
        $identifier = $vid;
        $title = $adminview->getElementsByTagName('title')->item(0);

        $adminview_data = array(
            'identifier' => $identifier ? $identifier : '',
            'vid' => $vid ? $vid : '',
            'title' => $title ? $title->nodeValue : '',
        );

        $data['adminviews'][] = $adminview_data;
        $db->insert_query('pit_changeforumlang_data', array(
            'source_filename' => $db->escape_string($filename),
            'kind' => 'adminview',
            'identifier' => $db->escape_string($adminview_data['identifier']),
            'name' => $db->escape_string($adminview_data['vid']),
            'title' => $db->escape_string($adminview_data['title']),
            'language_code' => '',
        ));
    }

    return $data;
}

function pit_changeforumlang_apply_controller($filename)
{
    if ($filename == 'settings.xml') {
        return pit_changeforumlang_settings_apply($filename);
    } else if ($filename == 'tasks.xml') {
        return pit_changeforumlang_tasks_apply($filename);
    } else if ($filename == 'usergroups.xml') {
        return pit_changeforumlang_usergroups_apply($filename);
    } else if ($filename == 'adminviews.xml') {
        return pit_changeforumlang_adminviews_apply($filename);
    }
    return array('error' => true);
}
function pit_changeforumlang_settings_apply()
{
    global $db;
    $TABLE_PREFIX = TABLE_PREFIX;

    $query = $db->write_query("SELECT sg.gid, p.id, p.source_filename, p.kind, p.identifier, p.name, p.title, p.description FROM {$TABLE_PREFIX}settinggroups sg
                            LEFT JOIN {$TABLE_PREFIX}pit_changeforumlang_data p 
                                ON p.kind = 'settinggroup'
                                AND sg.name = p.identifier
                            WHERE p.source_filename = 'settings.xml'
                            AND p.kind = 'settinggroup'
                            AND p.id IS NOT NULL");

    while ($result = $db->fetch_array($query)) {
        $db->update_query(
            "settinggroups",
            array(
                "title" => $db->escape_string($result['title']),
                "description" => $db->escape_string($result['description']),
            ),
            "gid = {$result['gid']}"
        );
    }

    $query = $db->write_query("SELECT s.sid, s.optionscode, p.id, p.source_filename, p.kind, p.identifier, p.name, p.title, p.description, p.extra1 FROM {$TABLE_PREFIX}settings s
                            LEFT JOIN {$TABLE_PREFIX}pit_changeforumlang_data p 
                                ON p.kind = 'setting'
                                AND s.name = p.identifier
                            WHERE p.source_filename = 'settings.xml'
                            AND p.kind = 'setting'
                            AND p.id IS NOT NULL");

    while ($result = $db->fetch_array($query)) {
        $can_update_optionscode = false;
        if (pit_changeforumlang_starts_with('select', $result['optionscode']) && pit_changeforumlang_starts_with('select', $result['extra1'])) $can_update_optionscode = true;
        else if (pit_changeforumlang_starts_with('radio', $result['optionscode']) && pit_changeforumlang_starts_with('radio', $result['extra1'])) $can_update_optionscode = true;


        $db->update_query(
            "settings",
            array(
                "title" => $db->escape_string($result['title']),
                "description" => $db->escape_string($result['description']),
                "optionscode" => $db->escape_string($can_update_optionscode ? $result['extra1'] : $result['optionscode']),
            ),
            "sid = {$result['sid']}"
        );
    }

    return true;
}
function pit_changeforumlang_tasks_apply()
{
    global $db;
    $TABLE_PREFIX = TABLE_PREFIX;

    $query = $db->write_query("SELECT t.tid, p.id, p.source_filename, p.kind, p.identifier, p.name, p.title, p.description FROM {$TABLE_PREFIX}tasks t
                            LEFT JOIN {$TABLE_PREFIX}pit_changeforumlang_data p 
                                ON p.kind = 'task'
                                AND t.file = p.identifier
                            WHERE p.source_filename = 'tasks.xml'
                            AND p.kind = 'task'
                            AND p.id IS NOT NULL");

    while ($result = $db->fetch_array($query)) {
        $db->update_query(
            "tasks",
            array(
                "title" => $db->escape_string($result['title']),
                "description" => $db->escape_string($result['description']),
            ),
            "tid = {$result['tid']}"
        );
    }

    return true;
}
function pit_changeforumlang_usergroups_apply()
{
    global $db;
    $TABLE_PREFIX = TABLE_PREFIX;

    $query = $db->write_query("SELECT ug.gid, p.id, p.source_filename, p.kind, p.identifier, p.name, p.title, p.description, p.extra1 FROM {$TABLE_PREFIX}usergroups ug
                            LEFT JOIN {$TABLE_PREFIX}pit_changeforumlang_data p 
                                ON p.kind = 'usergroup'
                                AND ug.gid = p.identifier
                            WHERE p.source_filename = 'usergroups.xml'
                            AND p.kind = 'usergroup'
                            AND p.id IS NOT NULL");

    while ($result = $db->fetch_array($query)) {
        $db->update_query(
            "usergroups",
            array(
                "title" => $db->escape_string($result['title']),
                "description" => $db->escape_string($result['description']),
                "usertitle" => $db->escape_string($result['extra1']),
            ),
            "gid = {$result['gid']}"
        );
    }

    return true;
}
function pit_changeforumlang_adminviews_apply()
{
    global $db;
    $TABLE_PREFIX = TABLE_PREFIX;

    $query = $db->write_query("SELECT av.vid, p.id, p.source_filename, p.kind, p.identifier, p.name, p.title FROM {$TABLE_PREFIX}adminviews av
                            LEFT JOIN {$TABLE_PREFIX}pit_changeforumlang_data p 
                                ON p.kind = 'adminview'
                                AND av.vid = p.identifier
                            WHERE p.source_filename = 'adminviews.xml'
                            AND p.kind = 'adminview'
                            AND p.id IS NOT NULL");

    while ($result = $db->fetch_array($query)) {
        $db->update_query(
            "adminviews",
            array(
                "title" => $db->escape_string($result['title']),
            ),
            "vid = {$result['vid']}"
        );
    }

    return true;
}

pit_changeforumlang_admin_module();
