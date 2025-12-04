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

class LanguageFileManager
{
    private $file_path;
    private $original_perms;

    public function __construct($file_path)
    {
        $this->file_path = $file_path;
    }

    private function ensureWritable()
    {
        if (!file_exists($this->file_path) || !is_file($this->file_path)) {
            throw new Exception("The file does not exist: " . $this->file_path);
        }

        if (is_writable($this->file_path)) {
            return true;
        }

        $original_perms = fileperms($this->file_path);
        if (chmod($this->file_path, 0644) && is_writable($this->file_path)) {
            $this->original_perms = $original_perms;
            return true;
        }

        throw new Exception("The file is not writable: " . $this->file_path);
    }

    private function restorePermissions()
    {
        if (isset($this->original_perms)) {
            chmod($this->file_path, $this->original_perms);
        }
    }

    private function escapeString($string)
    {
        // return substr(json_encode($string, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES), 1, -1);
        $replacements = [
            "\\" => "\\\\",
            "\"" => "\\\"",
        ];

        return trim(strtr($string, $replacements));
    }

    private function findLineIndex($file_content_lines, $searchfor, $findlast_position = false)
    {
        if (!is_array($file_content_lines) && is_string($file_content_lines)) {
            $file_content_lines = explode("\n", $file_content_lines);
        }

        $last_position = false;
        foreach ($file_content_lines as $index => $line) {
            if (strrpos($line, $searchfor) !== false) {
                if (!$findlast_position) {
                    return $index; // first position
                }
                $last_position = $index;
            }
        }

        return $last_position;
    }

    private function appendTo($file_content, $content, $searchfor = null, $before = false, $to_another_line = false, $findlast_position = false)
    {
        if ($searchfor === null || $searchfor === '') {
            $searchfor = '?>';
            $before = true;
            $findlast_position = true;
        }

        if ($to_another_line) {
            $file_content_lines = explode("\n", $file_content);
            $line_index = $this->findLineIndex($file_content_lines, $searchfor, $findlast_position);
            if ($line_index === false) {
                array_push($file_content_lines, $content);
            } else {
                $insert_at = $line_index + ($before ? 0 : 1);
                array_splice($file_content_lines, $insert_at, 0, $content);
            }
            $file_content = implode("\n", $file_content_lines);
            return $file_content;
        }

        $strindex = strrpos($file_content, $searchfor);
        if ($strindex !== false) {
            if (!$before) $strindex += strlen($searchfor);
            return substr($file_content, 0, $strindex) .
                $content .
                substr($file_content, $strindex);
        } else {
            return $file_content . $content;
        }
    }

    public function writePlainText($content, $searchfor = null)
    {
        try {
            $this->ensureWritable();

            $file_content = file_get_contents($this->file_path);

            $content = $this->escapeString($content);
            $file_content = $this->appendTo($file_content, $content, $searchfor);

            $result = file_put_contents($this->file_path, $file_content);

            $this->restorePermissions();

            return $result !== false;
        } catch (Exception $e) {
            error_log("LanguageFileManager Error: " . $e->getMessage());
            return false;
        }
    }

    private function languageKeyExists($key, $file_content = null)
    {
        if ($file_content === null) {
            $file_content = file_get_contents($this->file_path);
        }

        $patterns = [
            '/\\$l\[\'' . preg_quote($key, '/') . '\'\]/',
            '/\\$l\["' . preg_quote($key, '/') . '"\]/',
        ];

        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $file_content)) {
                return true;
            }
        }

        return false;
    }

    public function writeLanguageKey($key, $value)
    {
        try {
            $this->ensureWritable();

            $file_content = file_get_contents($this->file_path);

            if ($this->languageKeyExists($key, $file_content)) {
                return false;
            }

            $content = "\$l[\"{$key}\"] = \"" . $this->escapeString($value) . "\";";

            $file_content = $this->appendTo($file_content, $content, null, false, true);

            $result = file_put_contents($this->file_path, $file_content);

            $this->restorePermissions();

            return $result !== false;
        } catch (Exception $e) {
            error_log("LanguageFileManager Error: " . $e->getMessage());
            return false;
        }
    }

    public function writeLanguageKeys($array_of_language_keys)
    {
        try {
            $this->ensureWritable();

            $file_content = file_get_contents($this->file_path);

            foreach ($array_of_language_keys as $gid => $items) {
                foreach ($items as $key => $value) {
                    $value = $this->escapeString($value);

                    if (pit_changeforumlang_starts_with('___comment_header_', $key)) {
                        $value_len = strlen($value);
                        $content = "\n\n\n/*" . str_repeat('*', $value_len + 12) . "\n *\t" . $value . "\n *" . str_repeat('*', $value_len + 12) . "\n */\n";
                        $file_content = $this->appendTo($file_content, $content);
                    } else if (pit_changeforumlang_ends_with('_zins_newline_after', $key)) {
                        $content = "\n";
                        $file_content = $this->appendTo($file_content, $content);
                    } else {
                        if ($this->languageKeyExists($key, $file_content)) continue;

                        $content = "\$l[\"{$key}\"] = \"" . $value . "\";";

                        $file_content = $this->appendTo($file_content, $content, null, false, true);
                    }
                }
            }

            $result = file_put_contents($this->file_path, $file_content);

            $this->restorePermissions();

            return $result !== false;
        } catch (Exception $e) {
            error_log("LanguageFileManager Error: " . $e->getMessage());
            return false;
        }
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

    $plugin_languages_dir = MYBB_ROOT . 'inc/plugins/pit_changeforumlang_languages/';
    if (!is_dir($plugin_languages_dir)) {
        @mkdir($plugin_languages_dir, 0755, true);
    }

    $languagepacks = $lang->get_languages();

    if (is_dir($plugin_languages_dir)) {
        $folders = scandir($plugin_languages_dir);
        foreach ($folders as $folder) {
            if ($folder != '.' && $folder != '..' && is_dir($plugin_languages_dir . $folder)) {
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
    $form_container->output_row(
        $lang->pit_changeforumlang_update_bblang,
        $lang->pit_changeforumlang_update_bblang_desc,
        $form->generate_yes_no_radio('update_bblang', $languages, '', array('id' => 'update_bblang')),
        'update_bblang'
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
        "settings.xml" => array("filename" => "settings.xml", "isexist" => false, "isselected" => false),
        "tasks.xml" => array("filename" => "tasks.xml", "isexist" => false, "isselected" => false),
        "usergroups.xml" => array("filename" => "usergroups.xml", "isexist" => false, "isselected" => false),
        "adminviews.xml" => array("filename" => "adminviews.xml", "isexist" => false, "isselected" => false),
    );

    $selected_language = $mybb->get_input('selected_language', MyBB::INPUT_STRING);
    $update_bblang = $mybb->get_input('update_bblang', MyBB::INPUT_STRING) == 'yes' ? true : false;
    $plugin_languages_dir = MYBB_ROOT . 'inc/plugins/pit_changeforumlang_languages/';
    $selected_language_dir = $plugin_languages_dir . $selected_language . '/';

    if (!is_dir($selected_language_dir)) {
        flash_message($lang->pit_changeforumlang_selected_lang_does_not_exist . htmlspecialchars_uni($selected_language), "error");
        admin_redirect("index.php?module=config-pit-changeforumlang");
        return false;
    }

    $has_no_file = true;
    // $some_file_is_missing = false;
    foreach ($should_exists_file as $filename => $item) {
        if (is_file($selected_language_dir . $filename)) {
            $has_no_file = false;
            $should_exists_file[$filename]["isexist"] = true;
            $should_exists_file[$filename]["path"] = $selected_language_dir . $filename;
        } else {
            // $some_file_is_missing = true;
        }
    }

    if ($has_no_file) {
        flash_message($lang->pit_changeforumlang_has_no_file . htmlspecialchars_uni($selected_language), "error");
        admin_redirect("index.php?module=config-pit-changeforumlang");
        return false;
    }

    $compatibility_msg = $lang->pit_changeforumlang_selected_may_not_compatible;
    $langinfo_file_path = $plugin_languages_dir . $selected_language . '.php';
    if (is_file($langinfo_file_path)) {
        require $langinfo_file_path;
        if ($mybb->version_code == $langinfo['version']) {
            $compatibility_msg = $lang->pit_changeforumlang_selected_fully_compatible;
        } else if ($mybb->version_code > $langinfo['version']) {
            $compatibility_msg = $lang->pit_changeforumlang_selected_is_lower_version;
        }
    }

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
    echo $form->generate_hidden_field('update_bblang', $update_bblang);

    echo "<p>{$compatibility_msg}</p>
        <p>{$lang->pit_changeforumlang_check_file_status}...</p>
        <ul>";

    foreach ($should_exists_file as $filename => $item) {
        $icon_url = $mybb->settings['bburl'] . '/admin/styles/default/images/icons/';
        if ($item["isexist"]) $icon_url .= 'success.png';
        else $icon_url .= 'error.png';

        $checked = $item["isexist"] == true ? 'checked' : '';
        $disabled = $item["isexist"] == true ? '' : 'disabled';
        echo "<li style=\"list-style: none;\">
                <label for=\"languagefiles_{$filename}\">
                    <input type=\"checkbox\" name=\"languagefiles[]\" id=\"languagefiles_{$filename}\" value=\"{$filename}\" {$checked} {$disabled}>
                    {$filename} <img src=\"{$icon_url}\">
                </label>
            </li>";
    }
    echo '</ul>';

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
    $update_bblang = $mybb->get_input('update_bblang', MyBB::INPUT_BOOL);
    $selectedlanguagefiles = $mybb->get_input('languagefiles', MyBB::INPUT_ARRAY);
    $languagepacks = $lang->get_languages();

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

    foreach ($should_exists_file as $filename => $item) {
        if ($item['isexist']) {
            $isselected = in_array($filename, $selectedlanguagefiles);
            $should_exists_file[$filename]['isselected'] = $isselected;
        }
    }

    $query = $db->query("SELECT kind, identifier, COUNT(id) FROM `mybb_pit_changeforumlang_data` GROUP BY kind, identifier HAVING COUNT(id) > 1;");
    $count_result = $db->num_rows($query);
    if ($count_result > 0) {
        flash_message($lang->pit_changeforumlang_found_self_duplicated, 'error');
        admin_redirect("index.php?module=config-pit-changeforumlang");
        return false;
    }

    if ($should_exists_file['settings.xml']['isselected']) {
        $query2 = $db->query("SELECT name, COUNT(sid) FROM `mybb_settings` GROUP BY name HAVING COUNT(sid) > 1;");
        $count_result = $db->num_rows($query2);
        if ($count_result > 0) {
            flash_message($lang->pit_changeforumlang_found_settings_duplicated, 'error');
            admin_redirect("index.php?module=config-pit-changeforumlang");
            return false;
        }
    }

    foreach ($should_exists_file as $filename => $item) {
        if ($item['isselected'] == true) {
            $data = pit_changeforumlang_apply_controller($filename, $selected_language);
            if (is_array($data) && isset($data->error)) {
                pit_changeforumlang_message($lang->pit_changeforumlang_error_occurred, 'error');
                return false;
            }
        }
    }

    $selected_language = isset($selected_language) ? $selected_language : 'english';

    $condition = $update_bblang ? "OR name = 'bblanguage'" : "";
    $db->update_query('settings', array('value' => $db->escape_string($selected_language)), "name = 'cplanguage' {$condition}");
    $lang->set_language($selected_language, "admin");
    if ($update_bblang) $lang->set_language($selected_language, "user");

    rebuild_settings();

    flash_message($lang->sprintf($lang->pit_changeforumlang_finish, htmlspecialchars_uni($languagepacks[$selected_language])), "success");
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

    if (!is_file($file_path)) {
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
                'title' => $db->escape_string(trim($setting_data['title'])),
                'description' => $db->escape_string(trim($setting_data['description'])),
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
            'title' => $db->escape_string(trim($settinggroup_data['title'])),
            'description' => $db->escape_string(trim($settinggroup_data['description'])),
            'language_code' => '',
        ));
    }

    return $data;
}
function pit_changeforumlang_tasks_xml_reader($filename, $file_path)
{
    global $db;
    if (!is_file($file_path)) {
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
            'title' => $db->escape_string(trim($task_data['title'])),
            'description' => $db->escape_string(trim($task_data['description'])),
            'language_code' => '',
        ));
    }

    return $data;
}
function pit_changeforumlang_usergroups_xml_reader($filename, $file_path)
{
    global $db;
    if (!is_file($file_path)) {
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
            'title' => $db->escape_string(trim($usergroup_data['title'])),
            'description' => $db->escape_string(trim($usergroup_data['description'])),
            'extra1' => $db->escape_string(trim($usergroup_data['usertitle'])),
            'language_code' => '',
        ));
    }

    return $data;
}
function pit_changeforumlang_adminviews_xml_reader($filename, $file_path)
{
    global $db;
    if (!is_file($file_path)) {
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
            'title' => $db->escape_string(trim($adminview_data['title'])),
            'language_code' => '',
        ));
    }

    return $data;
}

function pit_changeforumlang_apply_controller($filename, $selected_language)
{
    if ($filename == 'settings.xml') {
        return pit_changeforumlang_settings_apply($selected_language);
    } else if ($filename == 'tasks.xml') {
        return pit_changeforumlang_tasks_apply($selected_language);
    } else if ($filename == 'usergroups.xml') {
        return pit_changeforumlang_usergroups_apply($selected_language);
    } else if ($filename == 'adminviews.xml') {
        return pit_changeforumlang_adminviews_apply($selected_language);
    }
    return array('error' => true);
}
function pit_changeforumlang_settings_apply($selected_language)
{
    global $db;
    $TABLE_PREFIX = TABLE_PREFIX;

    $lang_manager = new LanguageFileManager(
        MYBB_ROOT . 'inc/languages/' . $selected_language . '/admin/config_settings.lang.php'
    );
    $array_of_language_keys = array();

    $query = $db->write_query("SELECT sg.gid, p.id, p.source_filename, p.kind, p.identifier, p.name, p.title, p.description FROM {$TABLE_PREFIX}settinggroups sg
                            LEFT JOIN {$TABLE_PREFIX}pit_changeforumlang_data p 
                                ON p.kind = 'settinggroup'
                                AND sg.name = p.identifier
                            WHERE p.source_filename = 'settings.xml'
                            AND p.kind = 'settinggroup'
                            AND p.id IS NOT NULL");

    while ($result = $db->fetch_array($query)) {
        if ($selected_language === 'english') {
            $db->update_query(
                "settinggroups",
                array(
                    "title" => $db->escape_string($result['title']),
                    "description" => $db->escape_string($result['description']),
                ),
                "gid = {$result['gid']}"
            );
        } else {
            // $lang_manager->writeLanguageKey('setting_group_' . $result['name'], $result['title']);
            // $lang_manager->writeLanguageKey('setting_group_' . $result['name'] . '_desc', $result['description']);
            $array_of_language_keys[$result['gid']] = array();
            $array_of_language_keys[$result['gid']]['___comment_header_setting_group_' . $result['name']] = $result['name'] . ' settings';
            $array_of_language_keys[$result['gid']]['setting_group_' . $result['name']] = $result['title'];
            $array_of_language_keys[$result['gid']]['setting_group_' . $result['name'] . '_desc'] = $result['description'];
            $array_of_language_keys[$result['gid']]['setting_group_' . $result['name'] . '_zins_newline_after'] = "\n";
        }
    }

    $query = $db->write_query("SELECT s.sid, s.gid, s.optionscode, p.id, p.source_filename, p.kind, p.identifier, p.name, p.title, p.description, p.extra1 FROM {$TABLE_PREFIX}settings s
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

        if ($selected_language === 'english') {
            $db->update_query(
                "settings",
                array(
                    "title" => $db->escape_string($result['title']),
                    "description" => $db->escape_string($result['description']),
                    "optionscode" => $db->escape_string($can_update_optionscode ? $result['extra1'] : $result['optionscode']),
                ),
                "sid = {$result['sid']}"
            );
        } else {
            $array_of_language_keys[$result['gid']]['setting_' . $result['name']] = $result['title'];
            $array_of_language_keys[$result['gid']]['setting_' . $result['name'] . '_desc'] = $result['description'];
            if ($can_update_optionscode) {
                $optionscode_array = explode("\n", $result['extra1']);
                array_shift($optionscode_array); // remove first element (select or radio)
                foreach ($optionscode_array as $option) {
                    $option_equel_index = strpos($option, "=");
                    $option_value = trim(substr($option, 0, $option_equel_index));
                    $option_title = trim(substr($option, $option_equel_index + 1));
                    $array_of_language_keys[$result['gid']]['setting_' . $result['name'] . '_' . $option_value] = $option_title;
                }
            }
            $array_of_language_keys[$result['gid']]['setting_' . $result['name'] . '_zins_newline_after'] = "\n";
        }
    }

    if ($selected_language !== 'english') {
        $write_to_file_result = $lang_manager->writeLanguageKeys($array_of_language_keys);
        if ($write_to_file_result === false) return array('error' => true);
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
