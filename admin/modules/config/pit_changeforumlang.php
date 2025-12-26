<?php

/**
 * Plugin: PIT Change Forum Language
 * Description: Install language packges with one click and change the language of various sections that require changes to the database, without having to perform an upgrade or reinstallation process.
 * Version: 2.0
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

        if ($mybb->get_input('action') == 'install-language') {
            return pit_changeforumlang_install_language_pack();
        } else if ($mybb->get_input('action') == 'recommended') {
            return pit_changeforumlang_recommended();
        } else if ($mybb->get_input('action') == 'install-theme') {
            return pit_changeforumlang_install_theme();
        }

        return pit_changeforumlang_select_language_pack();
    }
}

class PITFileManager
{
    protected $file_path;
    protected $original_perms;

    public function __construct($file_path)
    {
        $this->file_path = $file_path;
        $this->file_exists(false);
    }

    protected function changeFilePermission($permcode = null)
    {
        $this->file_exists(); // or raise error exceptions

        if ($permcode === null) $permcode = 0644;

        $original_perms = fileperms($this->file_path);
        if (chmod($this->file_path, $permcode)) {
            $this->original_perms = $original_perms;

            return array(
                "original_perms" => $original_perms,
                "changed" => true,
            );
        }

        $this->restorePermissions();

        throw new Exception("The file permissions can't change: " . $this->file_path);
    }

    protected function file_exists($raiseException = true)
    {
        if (!file_exists($this->file_path) || !is_file($this->file_path)) {
            if ($raiseException === true) throw new Exception("The file does not exist: " . $this->file_path);
            return false;
        }

        return true;
    }

    protected function ensureWritable()
    {
        $this->file_exists(); // or raise error exceptions

        if (is_writable($this->file_path)) return true;

        $this->changeFilePermission();

        if (is_writable($this->file_path)) return true;

        throw new Exception("The file is not writable: " . $this->file_path);
    }

    protected function ensureReadable()
    {
        $this->file_exists(); // or raise error exceptions

        if (is_readable($this->file_path)) return true;

        $this->changeFilePermission(null, false);

        if (is_readable($this->file_path)) return true;

        throw new Exception("The file is not readable: " . $this->file_path);
    }

    protected function restorePermissions()
    {
        if (isset($this->original_perms)) {
            chmod($this->file_path, $this->original_perms);
        }
    }

    protected function escapeString($string)
    {
        // return substr(json_encode($string, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES), 1, -1);
        /* $replacements = [
            "\\" => "\\\\",
            "\"" => "\\\"",
        ];

        return trim(strtr($string, $replacements)); */
        return $string;
    }

    protected function findLineIndex($file_content_lines, $searchfor, $findlast_position = false)
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

    protected function appendTo($file_content, $content, $searchfor = null, $before = false, $to_another_line = false, $findlast_position = false)
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

    public function readFile()
    {
        $this->ensureReadable();

        $file_content = file_get_contents($this->file_path);

        $this->restorePermissions();

        return $file_content;
    }

    public function writePlainText($content, $searchfor = null, $before = false, $to_another_line = false, $findlast_position = false)
    {
        try {
            $this->ensureWritable();

            $file_content = file_get_contents($this->file_path);

            $content = $this->escapeString($content);
            $file_content = $this->appendTo($file_content, $content, $searchfor, $before, $to_another_line, $findlast_position);

            $result = file_put_contents($this->file_path, $file_content);

            $this->restorePermissions();

            return $result !== false;
        } catch (Exception $e) {
            error_log("FileManager Error: " . $e->getMessage());
            return false;
        }
    }

    public static function move_contents($source, $destination)
    {
        if (substr($source, -1) !== DIRECTORY_SEPARATOR) $source .= DIRECTORY_SEPARATOR;
        if (substr($destination, -1) !== DIRECTORY_SEPARATOR) $destination .= DIRECTORY_SEPARATOR;

        $files = scandir($source);
        foreach ($files as $file) {
            if ($file === '.' || $file === '..') continue;

            $src = $source . $file;
            $dest = $destination . $file;

            if (is_dir($src)) {
                if (!is_dir($dest)) mkdir($dest, 0755, true);
                self::move_contents($src, $dest);
                rmdir($src);
            } else {
                rename($src, $dest);
            }
        }
    }

    public static function get_dir_content($path, $only_dir)
    {
        clearstatcache();
        $files = scandir($path);
        $directory_lists = array();

        foreach ($files as $file) {
            if ($file === '.' || $file === '..') continue;

            if ($only_dir === true) {
                if (is_dir($path . $file)) $directory_lists[] = $file;
            } else {
                $directory_lists[] = $file;
            }
        }

        return $directory_lists;
    }
}
class LanguageFileManager extends PITFileManager
{
    protected function escapeString($string)
    {
        // return substr(json_encode($string, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES), 1, -1);
        $replacements = [
            "\\" => "\\\\",
            "\"" => "\\\"",
        ];

        return trim(strtr($string, $replacements));
    }

    protected function languageKeyExists($key, $file_content = null)
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

class GithubZipballManager
{
    public $repo_url;
    public $zipball_url;
    private $plugin_languages_dir;
    private $temp_dir;
    private $unzip_temp_dir;
    private $package_zip;

    public function __construct($zipball_url)
    {
        $this->zipball_url = $zipball_url;
        if (!$this->validate_zipball_url($zipball_url)) throw new Exception("URL is invalid: " . $this->zipball_url);

        $this->plugin_languages_dir = MYBB_ROOT . 'inc/plugins/pit_changeforumlang_languages/';
        $this->temp_dir = $this->plugin_languages_dir . 'temp/';
        $this->unzip_temp_dir = $this->temp_dir . 'unzip_temp/';
    }

    public function validate_zipball_url()
    {
        $github_domain = "https://github.com/";
        $github_zipball_domain = "https://api.github.com/repos/";

        if (substr($this->zipball_url, 0, 29) !== $github_zipball_domain) return false;
        $github_url_parts = explode('/', substr($this->zipball_url, 29));
        if (count($github_url_parts) < 2) return false;
        $github_owner = $github_url_parts[0];
        $github_repo = $github_url_parts[1];
        if (!$github_owner) return false;
        if (!$github_repo) return false;

        $this->repo_url = $github_domain . $github_owner . '/' . $github_repo . '/';

        return true;
    }

    private function prepare_directories()
    {
        if (!is_dir($this->plugin_languages_dir)) {
            @mkdir($this->plugin_languages_dir, 0755, true);
            file_put_contents($this->plugin_languages_dir . 'index.html', "<html><head><title></title></head><body>&nbsp;</body></html>");
        }
        if (!is_dir($this->temp_dir)) {
            @mkdir($this->temp_dir, 0755, true);
            file_put_contents($this->temp_dir . 'index.html', "<html><head><title></title></head><body>&nbsp;</body></html>");
        }
        if (!is_dir($this->unzip_temp_dir)) @mkdir($this->unzip_temp_dir, 0755, true);
    }

    public function get_and_extract()
    {
        $this->prepare_directories();

        $ch = curl_init($this->zipball_url);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36');

        $file_content = curl_exec($ch);

        if (curl_errno($ch)) {
            return array("error" => 'cURL error: ' . curl_error($ch));
        }

        $this->package_zip = $this->temp_dir . 'temp.zip';
        file_put_contents($this->package_zip, $file_content);

        $zip = new ZipArchive;

        if ($zip->open($this->package_zip) === true) {
            $zip->extractTo($this->unzip_temp_dir);

            $zip->close();
        } else {
            return array("error" => 'Error: Could not open the ZIP file!');
        }


        $plugin_temp_root_dir = glob($this->unzip_temp_dir . '*')[0];

        PITFileManager::move_contents($plugin_temp_root_dir, MYBB_ROOT);

        rmdir($plugin_temp_root_dir);
        rmdir($this->unzip_temp_dir);
        unlink($this->package_zip);

        return true;
    }
}


function pit_changeforumlang_select_language_pack()
{
    global $mybb, $lang, $page;

    $page->extra_header .= '<link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/lipis/flag-icons@7.3.2/css/flag-icons.min.css" />' . "\n";
    $page->extra_header .= '<script type="text/javascript" src="' . $mybb->settings['bburl'] . '/admin/jscripts/pit_changeforumlang/pit_changeforumlang.js"></script>' . "\n";
    $page->extra_header .= "<script>
        if (!lang) var lang = {};

        lang.pit_cfl = {
            'author': '{$lang->pit_changeforumlang_js_author}',
            'inc_admin': '{$lang->pit_changeforumlang_js_inc_admin}',
            'inc_setting': '{$lang->pit_changeforumlang_js_inc_setting}',
            'inc_acp_theme': '{$lang->pit_changeforumlang_js_inc_acp_theme}',
            'inc_install': '{$lang->pit_changeforumlang_js_inc_install}',
            'action_install': '{$lang->pit_changeforumlang_js_action_install}',
            'action_dl_zip': '{$lang->pit_changeforumlang_js_action_dl_zip}',
            'action_dl_mybbmod': '{$lang->pit_changeforumlang_js_action_dl_mybbmod}',
            'action_docs': '{$lang->pit_changeforumlang_js_action_docs}',
            'action_website': '{$lang->pit_changeforumlang_js_action_website}',
            'action_issues': '{$lang->pit_changeforumlang_js_action_issues}',
            'action_back': '{$lang->pit_changeforumlang_js_action_back}',
            'action_next': '{$lang->pit_changeforumlang_js_action_next}',
            'install_success': '{$lang->pit_changeforumlang_js_install_success}',
            'install_error': '{$lang->pit_changeforumlang_js_install_error}',
            'version_may_not_compatible': '{$lang->pit_changeforumlang_selected_may_not_compatible}',
            'version_fully_compatible': '{$lang->pit_changeforumlang_selected_fully_compatible}',
            'version_is_lower_version': '{$lang->pit_changeforumlang_selected_is_lower_version}',
            'version_selected_version_no_info': '{$lang->pit_changeforumlang_selected_version_no_info}',
            'q_update_bblang': '{$lang->pit_changeforumlang_update_bblang}',
            'q_update_bblang_desc': '{$lang->pit_changeforumlang_update_bblang_desc}',
            'yes_confirm': '{$lang->yes}',
	        'no_confirm': '{$lang->no}',
        };
        var pit_cfl_vars = {
            'rootpath': '{$mybb->settings['bburl']}',
            'current_version': '{$mybb->version_code}',
            'current_language': '{$mybb->settings['cplanguage']}',
            'cpstyle_images_url': '{$mybb->settings['bburl']}/admin/styles/{$mybb->settings['cpstyle']}/images',
            'spinner_image': '{$mybb->settings['bburl']}/admin/styles/{$mybb->settings['cpstyle']}/images/spinner.gif',
            'my_post_key': '{$mybb->post_code}',
        };

        window.addEventListener('DOMContentLoaded', function () {
            pit_cfl_main()
        });
    </script>";

    $page->add_breadcrumb_item($lang->pit_changeforumlang_pl_title, 'index.php?module=config-pit-changeforumlang');
    $page->output_header($lang->pit_changeforumlang_pl_title);

    $sub_tabs = array(
        'pit-changeforumlang' => array(
            'title'         => $lang->pit_changeforumlang_pl_title,
            'description'   => $lang->pit_changeforumlang_pl_desc,
            'link'          => 'index.php?module=config-pit-changeforumlang',
        ),
    );

    $page->output_nav_tabs($sub_tabs, 'pit-changeforumlang');

    echo '<div id="pit_cfl_content_header"></div>';
    echo '<div id="pit_cfl_content"></div>';
    echo '<div id="pit_cfl_content_footer"></div>';

    $page->output_footer();
}

function pit_changeforumlang_install_language_pack()
{
    global $db, $mybb, $lang, $page;

    $page->add_breadcrumb_item($lang->pit_changeforumlang_pl_title, 'index.php?module=config-pit-changeforumlang');
    $page->output_header($lang->pit_changeforumlang_pl_title);

    $sub_tabs = array(
        'pit-changeforumlang' => array(
            'title'         => $lang->pit_changeforumlang_pl_title,
            'description'   => $lang->pit_changeforumlang_pl_desc,
            'link'          => 'index.php?module=config-pit-changeforumlang',
        ),
    );

    $page->output_nav_tabs($sub_tabs, 'pit-changeforumlang');

    if ($mybb->get_input('action') != 'install-language') {
        admin_redirect("index.php?module=config-pit-changeforumlang");
        return false;
    }


    verify_post_check($mybb->get_input('my_post_key'));
    $form_data_language = $mybb->get_input('language', MyBB::INPUT_STRING);
    $form_data_index = $mybb->get_input('index', MyBB::INPUT_INT);
    $form_data_is_exist = $mybb->get_input('is_exist', MyBB::INPUT_BOOL);
    $form_data_acp_theme = $mybb->get_input('acp_theme', MyBB::INPUT_STRING);
    $form_data_mybb_mod_pid = $mybb->get_input('mybb_mod_pid', MyBB::INPUT_INT);
    $form_data_zipball_url = $mybb->get_input('zipball_url', MyBB::INPUT_STRING);
    $form_data_update_bblang = $mybb->get_input('update_bblang', MyBB::INPUT_STRING) == 'yes' ? true : false;

    // $form_data_is_exist === true, only apply language, because this should be exist by default
    // others should be download and install from github
    if (!$form_data_is_exist) {
        $zip = new GithubZipballManager($form_data_zipball_url);
        $zip->get_and_extract();
    }

    if (isset($form_data_language) && pit_changeforumlang_read_and_apply_xmls($form_data_language) !== true) {
        pit_changeforumlang_message($lang->pit_changeforumlang_error_occurred, 'error');
        return false;
    }

    $form_data_language = isset($form_data_language) ? $form_data_language : 'english';

    $condition = $form_data_update_bblang ? "OR name = 'bblanguage'" : "";
    $db->update_query('settings', array('value' => $db->escape_string($form_data_language)), "name = 'cplanguage' {$condition}");
    $lang->set_language($form_data_language, "admin");

    if (strlen($form_data_acp_theme) > 0 && $mybb->settings['cpstyle'] === 'default' && is_dir(MYBB_ROOT . '/admin/styles/' . $form_data_acp_theme . '/')) {
        $db->update_query('settings', array('value' => $db->escape_string($form_data_acp_theme)), "name = 'cpstyle'");
    }

    rebuild_settings();

    $languagepacks = $lang->get_languages();

    flash_message($lang->sprintf($lang->pit_changeforumlang_finish, htmlspecialchars_uni($languagepacks[$form_data_language])), "success");
    admin_redirect("index.php?module=config-pit-changeforumlang&action=recommended&language={$form_data_language}&index={$form_data_index}");

    $page->output_footer();
}

function pit_changeforumlang_read_and_apply_xmls($selected_language)
{
    global $db, $lang;

    $plugin_languages_dir = MYBB_ROOT . 'inc/plugins/pit_changeforumlang_languages/';
    $selected_language_dir = $plugin_languages_dir . $selected_language . '/';

    if (!is_dir($selected_language_dir)) {
        // the directory is not exists...
        return true;
    }

    $should_exists_file = array(
        "settings.xml" => array("filename" => "settings.xml", "isexist" => false, "isselected" => false),
        "tasks.xml" => array("filename" => "tasks.xml", "isexist" => false, "isselected" => false),
        "usergroups.xml" => array("filename" => "usergroups.xml", "isexist" => false, "isselected" => false),
        "adminviews.xml" => array("filename" => "adminviews.xml", "isexist" => false, "isselected" => false),
    );

    $has_no_file = true;
    foreach ($should_exists_file as $filename => $item) {
        $filepath = $selected_language_dir . $filename;
        if (is_file($filepath)) {
            $has_no_file = false;
            $should_exists_file[$filename]["isexist"] = true;
            $should_exists_file[$filename]["path"] = $filepath;
        }
    }

    if ($has_no_file) {
        // folder exists but have no required file...(xmls)
        return true;
    }

    $languagepacks = $lang->get_languages();
    if (!array_key_exists($selected_language, $languagepacks)) {
        // the zipball not contain any standard and basic language pack!
        pit_changeforumlang_message($lang->pit_changeforumlang_error_occurred, 'error');
        return false;
    }


    $db->delete_query('pit_changeforumlang_data');
    foreach ($should_exists_file as $filename => $item) {
        if ($item['isexist'] === true) {
            $data = pit_changeforumlang_xml_reader_controller($filename, $item['path']);
            if (isset($data->error)) {
                pit_changeforumlang_message("{$lang->pit_changeforumlang_issue_on_read_xml}<br><b>{$filename}</b> <p>{$data['error']}</p>", 'error');
                return false;
            }
        }
    }

    $query = $db->query("SELECT kind, identifier, COUNT(id) FROM `mybb_pit_changeforumlang_data` GROUP BY kind, identifier HAVING COUNT(id) > 1;");
    $count_result = $db->num_rows($query);
    if ($count_result > 0) {
        flash_message($lang->pit_changeforumlang_found_self_duplicated, 'error');
        admin_redirect("index.php?module=config-pit-changeforumlang");
        return false;
    }

    if ($should_exists_file['settings.xml']['isexist'] === true) {
        $query2 = $db->query("SELECT name, COUNT(sid) FROM `mybb_settings` GROUP BY name HAVING COUNT(sid) > 1;");
        $count_result = $db->num_rows($query2);
        if ($count_result > 0) {
            flash_message($lang->pit_changeforumlang_found_settings_duplicated, 'error');
            admin_redirect("index.php?module=config-pit-changeforumlang");
            return false;
        }
    }

    foreach ($should_exists_file as $filename => $item) {
        if ($item['isexist'] === true) {
            $data = pit_changeforumlang_apply_controller($filename, $selected_language);
            if (is_array($data) && isset($data->error)) {
                pit_changeforumlang_message($lang->pit_changeforumlang_error_occurred, 'error');
                return false;
            }
        }
    }

    return true;
}

function pit_changeforumlang_recommended()
{
    global $mybb, $lang, $page, $admin_session;

    $form_data_language = $mybb->get_input('language', MyBB::INPUT_STRING);
    $form_data_index = $mybb->get_input('index', MyBB::INPUT_STRING);

    $mybb_acp_theme_list = json_encode(PITFileManager::get_dir_content(MYBB_ROOT . '/admin/styles/', true));

    $page->extra_header .= '<script type="text/javascript" src="' . $mybb->settings['bburl'] . '/admin/jscripts/pit_changeforumlang/pit_changeforumlang.js"></script>' . "\n";
    $page->extra_header .= "<script>
        if (!lang) var lang = {};

        lang.pit_cfl = {
            'author': '{$lang->pit_changeforumlang_js_author}',
            'inc_admin': '{$lang->pit_changeforumlang_js_inc_admin}',
            'inc_setting': '{$lang->pit_changeforumlang_js_inc_setting}',
            'inc_acp_theme': '{$lang->pit_changeforumlang_js_inc_acp_theme}',
            'inc_install': '{$lang->pit_changeforumlang_js_inc_install}',
            'action_install': '{$lang->pit_changeforumlang_js_action_install}',
            'action_apply': '{$lang->pit_changeforumlang_js_action_apply}',
            'action_dl_zip': '{$lang->pit_changeforumlang_js_action_dl_zip}',
            'action_dl_mybbmod': '{$lang->pit_changeforumlang_js_action_dl_mybbmod}',
            'action_docs': '{$lang->pit_changeforumlang_js_action_docs}',
            'action_website': '{$lang->pit_changeforumlang_js_action_website}',
            'action_issues': '{$lang->pit_changeforumlang_js_action_issues}',
            'action_back': '{$lang->pit_changeforumlang_js_action_back}',
            'action_next': '{$lang->pit_changeforumlang_js_action_next}',
            'install_success': '{$lang->pit_changeforumlang_js_install_success}',
            'install_installed': '{$lang->pit_changeforumlang_js_install_installed}',
            'install_installed_no_act': '{$lang->pit_changeforumlang_js_install_installed_no_act}',
            'install_error': '{$lang->pit_changeforumlang_js_install_error}',
            'version_may_not_compatible': '{$lang->pit_changeforumlang_selected_may_not_compatible}',
            'version_fully_compatible': '{$lang->pit_changeforumlang_selected_fully_compatible}',
            'version_is_lower_version': '{$lang->pit_changeforumlang_selected_is_lower_version}',
            'version_selected_version_no_info': '{$lang->pit_changeforumlang_selected_version_no_info}',
            'install_rec': '{$lang->pit_changeforumlang_js_install_rec}',
            'install_rec_forcefully': '{$lang->pit_changeforumlang_js_install_rec_forcefully}',
            'install_rec_acp_theme': '{$lang->pit_changeforumlang_js_install_rec_acp_theme}',
            'install_rec_theme': '{$lang->pit_changeforumlang_js_install_rec_theme}',
            'install_rec_rtl_support': '{$lang->pit_changeforumlang_js_install_rec_rtl_support}',
            'install_rec_installed': '{$lang->pit_changeforumlang_js_install_rec_installed}',
            'install_rec_installed_is_active': '{$lang->pit_changeforumlang_js_install_rec_installed_is_active}',
            'yes_confirm': '{$lang->yes}',
	        'no_confirm': '{$lang->no}',
        };
        var pit_cfl_vars = {
            'rootpath': '{$mybb->settings['bburl']}',
            'current_version': '{$mybb->version_code}',
            'current_language': '{$mybb->settings['cplanguage']}',
            'cpstyle_images_url': '{$mybb->settings['bburl']}/admin/styles/{$mybb->settings['cpstyle']}/images',
            'spinner_image': '{$mybb->settings['bburl']}/admin/styles/{$mybb->settings['cpstyle']}/images/spinner.gif',
            'my_post_key': '{$mybb->post_code}',

            'mybb_acp_theme_list': {$mybb_acp_theme_list},
            'current_acp_theme': '{$mybb->settings['cpstyle']}',
            'language': '{$form_data_language}',
            'index': '{$form_data_index}',
        };

        window.addEventListener('DOMContentLoaded', function () {
            pit_cfl_main_recommended()
        });
    </script>";

    $page->add_breadcrumb_item($lang->pit_changeforumlang_pl_title, 'index.php?module=config-pit-changeforumlang');
    $page->output_header($lang->pit_changeforumlang_pl_title);

    $sub_tabs = array(
        'pit-changeforumlang' => array(
            'title'         => $lang->pit_changeforumlang_pl_title,
            'description'   => $lang->pit_changeforumlang_pl_desc,
            'link'          => 'index.php?module=config-pit-changeforumlang',
        ),
    );

    $page->output_nav_tabs($sub_tabs, 'pit-changeforumlang');

    if ($mybb->get_input('action') != 'recommended' || $form_data_language === '' || $form_data_index === '') {
        admin_redirect("index.php?module=config-pit-changeforumlang");
        return false;
    }

    echo '<div id="pit_cfl_content_header"></div>';
    echo '<div id="pit_cfl_content"></div>';
    echo '<div id="pit_cfl_content_footer"></div>';

    $page->output_footer();
}

function pit_changeforumlang_install_theme()
{
    global $db, $mybb, $lang, $page;

    $page->add_breadcrumb_item($lang->pit_changeforumlang_pl_title, 'index.php?module=config-pit-changeforumlang');
    $page->output_header($lang->pit_changeforumlang_pl_title);

    $sub_tabs = array(
        'pit-changeforumlang' => array(
            'title'         => $lang->pit_changeforumlang_pl_title,
            'description'   => $lang->pit_changeforumlang_pl_desc,
            'link'          => 'index.php?module=config-pit-changeforumlang',
        ),
    );

    $page->output_nav_tabs($sub_tabs, 'pit-changeforumlang');

    if ($mybb->get_input('action') != 'install-theme') {
        admin_redirect("index.php?module=config-pit-changeforumlang");
        return false;
    }


    verify_post_check($mybb->get_input('my_post_key'));
    $form_data_language = $mybb->get_input('language', MyBB::INPUT_STRING);
    $form_data_index = $mybb->get_input('index', MyBB::INPUT_INT);
    $form_data_is_acp_theme = $mybb->get_input('is_acp_theme', MyBB::INPUT_BOOL);
    $form_data_theme_name = $mybb->get_input('theme_name', MyBB::INPUT_STRING);
    $form_data_is_exist = $mybb->get_input('is_exist', MyBB::INPUT_BOOL);
    $form_data_mybb_mod_pid = $mybb->get_input('mybb_mod_pid', MyBB::INPUT_INT);
    $form_data_zipball_url = $mybb->get_input('zipball_url', MyBB::INPUT_STRING);

    // $form_data_is_exist === true, only apply language, because this should be exist by default or already installed.
    // others should be download and install from github
    if (!$form_data_is_exist) {
        $zip = new GithubZipballManager($form_data_zipball_url);
        $zip->get_and_extract();
    }

    if ($form_data_is_acp_theme) {
        if (!is_dir(MYBB_ROOT . '/admin/styles/' . $form_data_theme_name . '/')) {
            // somethings went wrong!
            flash_message($lang->pit_changeforumlang_js_install_error, "error");
            admin_redirect("index.php?module=config-pit-changeforumlang");
            return false;
        }

        $db->update_query('settings', array('value' => $db->escape_string($form_data_theme_name)), "name = 'cpstyle'");
    }

    rebuild_settings();

    flash_message($lang->pit_changeforumlang_js_install_success, "success");
    admin_redirect("index.php?module=config-pit-changeforumlang&action=recommended&language={$form_data_language}&index={$form_data_index}");

    $page->output_footer();
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
