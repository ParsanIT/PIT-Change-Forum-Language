[![en](https://img.shields.io/badge/readme-en-blue?style=flat)](https://github.com/ParsanIT/PIT-Change-Forum-Language/blob/main/README.md)

<h1 dir="rtl">🧩 افزونه PIT Change Forum Language برای MyBB</h1>

<p dir="rtl">افزونه‌ای برای مدیریت و نصب بسته‌های زبانی MyBB که امکان <b>ترجمه کامل و خودکار تمامی بخش‌های انجمن</b> را بدون نیاز به نصب مجدد یا ارتقا فراهم می‌کند.</p>

<h2 dir="rtl">معرفی</h2>

<p dir="rtl">در ساختار فعلی MyBB، متن برخی از بخش‌ها تنها هنگام انجام <b>نصب یا ارتقای انجمن</b> اعمال می‌شوند.<br>
به همین دلیل، بخش‌هایی از MyBB ممکن است حتی پس از نصب بسته زبانی، همچنان به زبان انگلیسی باقی بمانند. (بخصوص در بسته‌های زبانی که روش کامل و استاندارد را در پیش نگرفتند.)</p>

<p dir="rtl">افزونه <b>PIT Change Forum Language</b> این محدودیت را به‌طور کامل رفع کرده است.<br>
با استفاده از این افزونه، تمام بخش‌های MyBB — حتی آن‌هایی که قبلاً فقط در فرایند نصب یا ارتقا ترجمه می‌شدند — <b>بدون نیاز به عملیات نصب یا ارتقای مجدد</b> به‌صورت کامل ترجمه می‌شوند.</p>

<p dir="rtl">همچنین این پلاگین در صورت نیاز <b>پوسته‌ی متناسب</b> را پیشنهاد و نصب می‌کند تا هماهنگی کامل بین زبان و رابط کاربری برقرار شود.</p>

<h2 dir="rtl">ویژگی‌های کلیدی</h2>

<ul dir="rtl">
  <li>🔄 دانلود و نصب آنلاین بسته‌های زبانی</li>
  <li>🧠 <b>پوشش کامل ترجمه</b> برای تمام بخش‌های انجمن، بدون نیاز به اجرای فرآیند نصب یا ارتقا</li>
  <li>🧭 پیشنهاد یا نصب پوسته‌ی مناسب با بسته زبانی نصب شده.</li>
  <li>⚙️ راه‌اندازی و عملکرد تنها با <b>کلیک کردن</b></li>
  <li>🌐 بدون نیاز به آپلود فایل‌ها بسته زبانی یا انجام تنظیمات دستی</li>
</ul>

<h2 dir="rtl">مراحل نصب</h2>

<ol dir="rtl">
  <li>فایل پلاگین را در مسیر ریشه MyBB خود آپلود کنید.</li>
  <li>از پنل مدیریت MyBB، به بخش <b>پلاگین‌ها</b> رفته و پلاگین را فعال کنید.</li>
  <li>به بخش <b>پیکربندی &gt; تغییر زبان انجمن PIT</b> بروید.</li>
  <li>زبان مورد نظر خود را انتخاب کرده و عملیات نصب را با کلیک کردن انجام دهید.</li>
</ol>

<h2 dir="rtl">مزایا</h2>

<ul dir="rtl">
  <li>ترجمه کامل و بی‌نقص تمامی اجزای MyBB</li>
  <li>بی‌نیازی از نصب مجدد یا ارتقای انجمن برای اعمال ترجمه‌ها</li>
  <li>تجربه‌ی مدیریتی سریع، راحت و بدون پیچیدگی</li>
  <li>رابط کاربری ساده و تمیز.</li>
</ul>

<h2 dir="rtl">افزودن زبان</h2>

<p dir="rtl">این کار وابسته به فعالیت توسعه دهندگان جامعه شما دارد، آنها می‌بایست طبق اصول بسته‌های زبانی خود را آماده و به ما ارائه کنند.<br>
ما هم در اولین فرصت آنرا در پلاگین میگنجانیم و در قالب بروزرسانی در اختیار شما قرار خواهیم داد.</p>

<h3 dir="rtl">نحوه افزودن توسط توسعه دهندگان و مترجمان:</h3>

<ol dir="rtl">
  <li>آماده سازی بسته زبانی اولیه با رعایت ساختار اصولی</li>
  <li>افزودن فایل‌های xml مورد نیاز در ساختار مشخص در مسیر <code dir="ltr">/inc/plugins/pit_changeforumlang_languages</code><br>
<pre dir="ltr" align="left">
language_common_name/
    |
    |--- adminviews.xml
    |--- settings.xml
    |--- tasks.xml
    |--- usergroups.xml<br>
language_common_name.php
</pre>
</li>
  <li>یک مخزن گیت‌هاب برای بسته زبان مربوطه به صورت عمومی (Public) ایجاد کنید.</li>
  <li>برای اولین بار یا هر به‌روزرسانی، یک Release ایجاد کنید.</li>
    <ul dir="rtl">
      <li>برای شناسایی سازگاری قبل از نصب، عبارت <code>Compatibility: xxxx</code> را در توضیحات آن وارد کنید.(اجباری نیست)</li>
    </ul>
  <li>با آماده کردن JSON زیر و ارسال آن به بخش مسائل این پروژه، درخواستی برای گنجاندن در پروژه ارسال کنید.</li>
</ol>

<pre dir="ltr" align="left">
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
        "icon_code": "us"
      }
    ]
  }
};
</pre>
