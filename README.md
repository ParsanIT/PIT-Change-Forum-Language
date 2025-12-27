Read me in [![EN](https://img.shields.io/badge/EN-green?style=flat)](#)
[![FA](https://img.shields.io/badge/FA-blue?style=flat)](https://github.com/ParsanIT/PIT-Change-Forum-Language/blob/main/README.fa.md)

<h1>🧩 PIT Change Forum Language plugin for MyBB</h1>

<p>A plugin for managing and installing MyBB language packs that allows <b>full and automatic translation of all forum sections</b> without the need for reinstallation or upgrade.</p>

<h2>Introduction</h2>

<p>In the current MyBB architecture, The text in some sections only applies when performing a <b>installation or upgrade</b> of the forum.<br><br>
Because of this, parts of MyBB may remain in English even after installing a language pack. (Especially in language packs that did not follow the full and standard approach.)</p>

<p>The <b>PIT Change Forum Language</b> plugin has completely removed this limitation.<br>
With this plugin, all parts of MyBB — even those that were previously only translated during the installation or upgrade process — are fully translated <b>without the need for a reinstallation or upgrade</b>.</p>

<p>The plugin also recommends and installs the <b>appropriate skin</b> if needed to ensure complete harmony between the language and the user interface.</p>

<h2>Key Features</h2>

<ul>
  <li>🌐 Download and install language packs online</li>
  <li>🔄 <b>Full translation coverage</b> for all parts of the forum, without the need for an installation or upgrade process</li>
  <li>🧠 Suggest or install a suitable theme with the language pack installed.</li>
  <li>🖱 Setup and operation with just <b>click</b></li>
  <li>🤖 No need to upload language pack files or do manual settings</li>
</ul>

<h2>Installation steps</h2>

<ol>
  <li>Upload the plugin file to your MyBB root directory.</li>
  <li>From the MyBB admin panel, go to the <b>Plugins</b> section and activate the plugin.</li>
  <li>Go to the <b>Configuration &gt; Change PIT Community Language</b>.</li>
  <li>Select your desired language and complete the installation by clicking.</li>
</ol>

<h2>Advantages</h2>

<ul>
  <li>Complete and flawless translation of all MyBB components</li>
  <li>No need to reinstall or upgrade the forum to apply translations</li>
  <li>Fast, convenient, and uncomplicated management experience</li>
  <li>Simple and clean user interface.</li>
</ul>

<h2>Add language</h2>

<p>This depends on the activity of your community developers, they must prepare and provide us with their language packs according to the principles.<br>
We will include it in the plugin as soon as possible and provide it to you in the form of an update.</p>

<h3>How to add by developers and translators:</h3>

<ol>
  <li>Prepare the initial language pack by adhering to a principled structure.</li>
  <li>Add the required xml files in the specified structure in the path <code>/inc/plugins/pit_changeforumlang_languages</code><br>
<pre>
language_common_name/
    |
    |--- adminviews.xml
    |--- settings.xml
    |--- tasks.xml
    |--- usergroups.xml<br>
language_common_name.php
</pre>
  </li>
  <li>Create a GitHub repository for the relevant language pack as Public.</li>
  <li>Create a Release for the first time or every update.
    <ul>
      <li>To identify compatibility before installation, enter <code>Compatibility: xxxx</code> in its description. (Not mandatory)</li>
    </ul>
  </li>
  <li>Submit a request for inclusion in the project by preparing the following JSON and submitting it to the issues section of this project.</li>
</ol>

<pre>
{
  "english": {
    "data": {
      "__comment_rtl": "Sets if the language is RTL (Right to Left) (1: yes, 0: no)",
      "__comment_htmllang": "Sets the lang in the &lt;html&gt; on all pages",
      "__comment_charset": "Sets the character set, blank uses the default.",
      "__comment_icon_code": "use https://flagicons.lipis.dev/",
      "__comment_common_name": "equal with folder name",
      "name": "English",
      "rtl": 0,
      "htmllang": "en",
      "charset": "UTF-8",
      "icon_code": "gb",
      "common_name": "english"
    },
    "packages": [
      {
        "__comment_seperator_0": "PIT Change forum languages data(difference with language pack base data)",

        "__comment_is_default": "is default language pack in that exist in mybb package... other language pack can't be default...",
        "__comment_mybb_mod_pid": "can find from url of that like https://community.mybb.com/mods.php?action=view&pid=1675",
        "__comment_mybb_mod_codename": "can find from details of that project(in edit page)",
        "__comment_mybb_userid": "can find from url of user profile like https://community.mybb.com/user-79079.html",
        "is_default": true,
        "mybb_mod_pid": 0,
        "mybb_mod_codename": "english",
        "mybb_userid": 1,
        "mybb_username": "Chris Boulton",

        "__comment_githubrepo": "github repo base link (for browse) like: https://github.com/ParsanIT/MyBB-Persian-Language-Pack",
        "__comment_githubrepo_release_zipball": "github repo suggestion release asset like: https://github.com/ParsanIT/MyBB-Persian-Language-Pack/archive/refs/tags/1839.zip",
        "__comment_githubrepo_release_version": "github repo suggestion release 1.8.39",

        "__comment_includes": {
          "settings_translation": "Inserted setting language strings into standard method(inc/languages/{common_name}/admin/settings.php), true or false",
          "install_upgrade_translation": "Install and upgrade is translated in this languagepack?  true or false",
          "acp_theme": "if package contain acp theme, input name of that(same as that folder) here"
        },
        "includes": {
          "settings_translation": true,
          "install_upgrade_translation": false,
          "acp_theme": ""
        },
        "__comment_recommended": {
          "acp_theme": "if a theme has spcefic option for your language such as RTL, input required data of that here such as below"
        },
        "recommended": {
          "acp_theme": {
            "forcefully": true,
            "name": "default"
          }
        },

        "__comment_seperator_1": "Language pack data",

        "__comment_name": "The friendly name of the language",
        "__comment_author": "The author of the language",
        "__comment_website": "The language authors website",
        "__comment_docs_link": "The documentation site link",
        "__comment_common_issues_link": "The documentation FAQs site link",
        "__comment_support_link": "The support website link",
        "__comment_version": "Compatible version of MyBB",
        "__comment_admin": "Sets if the translation includes the Admin CP (1: yes, 0: no)",

        "name": "English (American)",
        "author": "MyBB",
        "website": "https://mybb.com/",
        "docs_link": "",
        "common_issues_link": "",
        "support_link": "",
        "version": "1839",
        "admin": 1,
        "icon_codus"
      }
    ]
  }
};
</pre>
