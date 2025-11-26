Have you ever noticed after installing a language pack that some places are still not translated? Especially important places like settings...
There are definitely developers and contributors who have translated the MyBB package separately into their own language, but translating parts like settings will be an installation and upgrade-dependent process.

Now, with this plugin, you can do this without having to upgrade or reinstall, and avoid unwanted changes to templates, etc. in the process.

### Where is this plugin useful?

1- When a client or you have already installed MyBB and you want to completely change its language.

2- When you want to completely and temporarily change the forum to another language for a trial or training version.

3- When you want to avoid language dependency to generate packages for different communities (like Iran, China, Russia, France, etc.) for each version and there is no need to fully customize MyBB with each version. With this plugin, after installation or upgrade, users only need to install it and apply the desired language. (Only once and then deactivate the plugin)

## How to create the necessary language files?

Currently, the necessary language files are named settings.xml, tasks.xml, usergroups.xml, adminviews.xml, which are available in the install directory. This is to speed up and facilitate the work of various MyBB communities around the world. If you have them ready, you can include them in the plugin and use them easily.

## How to add a language:

In the inc/plugins/pit_changeforumlang_languages ​​folder, create a folder with the code/name of your language and place the translated xml files in it.(According to the persian language template included in the package)

For correct and logical functioning, the corresponding MyBB language pack must also be present in the forum so that you can see it for selection in the plugin. (It certainly does not make sense to have the settings section in Russian in a ACP with Chinese language -- however, we may change this if users request and need it.)

---

آیا تا به حال بعد از نصب یک بسته زبانی متوجه شده‌اید که بعضی جاها هنوز ترجمه نشده‌اند؟ مخصوصاً جاهای مهم مانند تنظیمات...
قطعاً توسعه‌دهندگان و مشارکت‌کنندگانی هستند که بسته MyBB را جداگانه به زبان خودشان ترجمه کرده‌اند، اما ترجمه بخش‌هایی مانند تنظیمات، فرآیندی وابسته به نصب و ارتقا خواهد بود.

حال، با استفاده از این افزونه، می‌توانید این کار را بدون نیاز به ارتقا یا نصب مجدد انجام دهید و از تغییرات ناخواسته قالب ها و... در این فرآیند جلوگیری کنید.

### این افزونه کجا مفید است؟

1- وقتی مشتری یا شما MyBB را از قبل نصب کرده‌اید و می‌خواهید زبان آن را کاملا تغییر دهید.

2- وقتی می‌خواهید انجمن را برای نسخه آزمایشی یا آموزشی به طور کامل و موقت به زبان دیگری تغییر دهید.

3- وقتی می‌خواهید از وابستگی زبان برای تولید بسته برای جوامع مختلف (مانند ایران، چین، روسیه، فرانسه و غیره) برای هر نسخه جلوگیری کنید و نیازی به سفارشی‌سازی کامل MyBB با هر نسخه نیست. با استفاده از این افزونه، پس از نصب یا ارتقا، فقط باید کاربران آن را نصب و زبان مورد نظر را اعمال کنند.(فقط یکبار و بعد از آن پلاگین را غیرفعال کنند)

## چگونه فایل های زبانی لازم را ایجاد کنیم؟

در حال حاضر فایل‌های زبانی لازم با نام‌های settings.xml ، tasks.xml ، usergroups.xml، adminviews.xml هستند که در دایرکتوری install موجود می‌باشند. اینکار به منظور سرعت بخشیدن و راحتی جوامع مختلف MyBB در سراسر دنیا می‌باشد. اگر آنها را آماده دارید میتوانید در پلاگین بگنجانید و به راحتی استفاده کنید.

## نحوه اضافه کردن زبان:

در پوشه inc/plugins/pit_changeforumlang_languages، پوشه‌ای با کد/نام زبان خود ایجاد کنید و فایل‌های xml ترجمه شده را در آن قرار دهید.(مطابق الگو زبان persian موجود در بسته)

برای عملکرد صحیح و منطقی، بسته زبان MyBB متناظر با آن‌هم باید در انجمن موجود باشد تا بتوانید آن را برای انتخاب در افزونه مشاهده کنید. (مطمئناً منطقی نیست که بخش تنظیمات به زبان روسی در یک ACP با زبان چینی باشد -- با این حال، در صورت درخواست و نیاز کاربران، ممکن است این را تغییر دهیم.)