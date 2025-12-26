var pit_cfl_languages;
var pit_cfl_themes;
const pit_cfl_icons = {
  dl: `<svg width="20px" height="20px" viewBox="0 0 36 36" fill="deepskyblue" version="1.1"  preserveAspectRatio="xMidYMid meet" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"><path class="clr-i-outline clr-i-outline-path-1" d="M30.92,8H26.55a1,1,0,0,0,0,2H31V30H5V10H9.38a1,1,0,0,0,0-2H5.08A2,2,0,0,0,3,10V30a2,2,0,0,0,2.08,2H30.92A2,2,0,0,0,33,30V10A2,2,0,0,0,30.92,8Z"></path><path class="clr-i-outline clr-i-outline-path-2" d="M10.3,18.87l7,6.89a1,1,0,0,0,1.4,0l7-6.89a1,1,0,0,0-1.4-1.43L19,22.65V4a1,1,0,0,0-2,0V22.65l-5.3-5.21a1,1,0,0,0-1.4,1.43Z"></path><rect x="0" y="0" width="36" height="36" fill-opacity="0"/></svg>`,
  check: `<svg width="20px" height="20px" viewBox="0 0 16 16" fill="limegreen" xmlns="http://www.w3.org/2000/svg"><path d="m 3 0 c -1.644531 0 -3 1.355469 -3 3 v 10 c 0 1.644531 1.355469 3 3 3 h 10 c 1.644531 0 3 -1.355469 3 -3 v -10 c 0 -1.644531 -1.355469 -3 -3 -3 z m 0 2 h 10 c 0.421875 0 0.765625 0.234375 0.917969 0.585938 l -0.667969 0.757812 l -6.296875 7.195312 l -2.246094 -2.246093 c -0.390625 -0.390625 -1.023437 -0.390625 -1.414062 0 s -0.390625 1.027343 0 1.417969 l 3 3 c 0.410156 0.40625 1.078125 0.386718 1.460937 -0.050782 l 6.246094 -7.136718 v 7.476562 c 0 0.570312 -0.429688 1 -1 1 h -10 c -0.570312 0 -1 -0.429688 -1 -1 v -10 c 0 -0.570312 0.429688 -1 1 -1 z m 0 0"/></svg>`,
  doc: `<svg width="20px" height="20px" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" clip-rule="evenodd" d="M4 5V19C4 20.6569 5.34315 22 7 22H17C18.6569 22 20 20.6569 20 19V9C20 7.34315 18.6569 6 17 6H5C4.44772 6 4 5.55228 4 5ZM7.25 12C7.25 11.5858 7.58579 11.25 8 11.25H16C16.4142 11.25 16.75 11.5858 16.75 12C16.75 12.4142 16.4142 12.75 16 12.75H8C7.58579 12.75 7.25 12.4142 7.25 12ZM7.25 15.5C7.25 15.0858 7.58579 14.75 8 14.75H13.5C13.9142 14.75 14.25 15.0858 14.25 15.5C14.25 15.9142 13.9142 16.25 13.5 16.25H8C7.58579 16.25 7.25 15.9142 7.25 15.5Z"/><path d="M4.40879 4.0871C4.75727 4.24338 5 4.59334 5 5H17C17.3453 5 17.6804 5.04375 18 5.12602V4.30604C18 3.08894 16.922 2.15402 15.7172 2.32614L4.91959 3.86865C4.72712 3.89615 4.55271 3.97374 4.40879 4.0871Z"/></svg>`,
  github: `<svg width="20px" height="20px" viewBox="0 0 20 20" version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"><desc>Created with Sketch.</desc><defs></defs><g id="Page-1" stroke="none" stroke-width="1" fill-rule="evenodd"><g id="Dribbble-Light-Preview" transform="translate(-140.000000, -7559.000000)"><g id="icons" transform="translate(56.000000, 160.000000)"><path d="M94,7399 C99.523,7399 104,7403.59 104,7409.253 C104,7413.782 101.138,7417.624 97.167,7418.981 C96.66,7419.082 96.48,7418.762 96.48,7418.489 C96.48,7418.151 96.492,7417.047 96.492,7415.675 C96.492,7414.719 96.172,7414.095 95.813,7413.777 C98.04,7413.523 100.38,7412.656 100.38,7408.718 C100.38,7407.598 99.992,7406.684 99.35,7405.966 C99.454,7405.707 99.797,7404.664 99.252,7403.252 C99.252,7403.252 98.414,7402.977 96.505,7404.303 C95.706,7404.076 94.85,7403.962 94,7403.958 C93.15,7403.962 92.295,7404.076 91.497,7404.303 C89.586,7402.977 88.746,7403.252 88.746,7403.252 C88.203,7404.664 88.546,7405.707 88.649,7405.966 C88.01,7406.684 87.619,7407.598 87.619,7408.718 C87.619,7412.646 89.954,7413.526 92.175,7413.785 C91.889,7414.041 91.63,7414.493 91.54,7415.156 C90.97,7415.418 89.522,7415.871 88.63,7414.304 C88.63,7414.304 88.101,7413.319 87.097,7413.247 C87.097,7413.247 86.122,7413.234 87.029,7413.87 C87.029,7413.87 87.684,7414.185 88.139,7415.37 C88.139,7415.37 88.726,7417.2 91.508,7416.58 C91.513,7417.437 91.522,7418.245 91.522,7418.489 C91.522,7418.76 91.338,7419.077 90.839,7418.982 C86.865,7417.627 84,7413.783 84,7409.253 C84,7403.59 88.478,7399 94,7399" id="github-[#142]"></path></g></g></g></svg>`,
  mybb: `<svg width="20px" height="20px" viewBox="60 60 280 280" xmlns="http://www.w3.org/2000/svg"><defs><style>.cls-1{fill:none;}.cls-2{fill-rule:evenodd;}</style></defs><g id="Layer_2" data-name="Layer 2"><g id="Layer_1-2" data-name="Layer 1"><rect class="cls-1" width="400" height="400"/><path id="Combined-Shape" d="M96.61,320l13.93-45.45c-26.85-20.43-43.73-50-43.73-83C66.81,130,125.91,80,198.81,80s132,50,132,111.58c0,30.7-12.62,48.08-36.35,68.26l14.34,43.32-31.56-19.39c-20.55,11.16-52.2,19.39-78.43,19.39a151.34,151.34,0,0,1-59.71-12ZM77.45,191.42c0,1.52,0,3,.13,4.53a8.53,8.53,0,0,0,.11,1.67,27.15,27.15,0,0,0,.24,2.73c.08.9.13,1.37.21,2.14.16,1.25.35,2.49.56,3.72C80,181.1,95.92,159.4,114.78,143.29,144.12,118.18,197.43,94.7,232.15,124a77.27,77.27,0,0,1,12.14,13.22,138,138,0,0,1,40.9,21.93c11.71,9.14,24.93,27.95,30.79,42.56V191.42c0-55.82-53.39-101.08-119.26-101.08S77.45,135.6,77.45,191.42Zm150.05,73.2c-20.07,7.75-52.51,17.52-74.08,16.07l-9,4.3c5.62,1.92,15.87,4,21.71,5.18,1.87.39,9.61,1.59,11.42,1.84l5.87.54,7.37.41c2.2,0,4.21.14,6.35.14,1.43,0,3.81,0,7.17-.12,1,0,2.84-.09,5.6-.27,1.18-.1,3.49-.35,6.95-.76l5.58-.83c1.07-.17,3.32-.61,6.75-1.3l8-2q1.41-.4,10.74-3.43c3.25-1.19,19.24-7.32,20.23-7.82l-16.74-12C244.43,265.09,234.5,264.87,227.5,264.62Zm78.13-49.39c.51-22-17.07-39.22-33.94-50.21-1.27-.83-2.59-1.66-3.95-2.47a124.55,124.55,0,0,0-13-6.89c19.56,31.26,21.44,81.74-12.56,100.17-1.24.66-2.51,1.31-3.78,1.92H244c1.77,0,3.63-.09,5.31-.2,7.28-.41,10.08,2.17,16.25,6.46l2.76,1.91.64-.37-.62.39,22.41,15.64-10-24.32-.48.38.48-.39-1.06-2.57a.49.49,0,0,1,.3-.14C292.19,246.15,305.28,231.21,305.63,215.23Zm-88.06-91.59c-27.67-15.32-61.1,1-83.53,18.26-21.54,16.55-45,41.61-41.57,70.15,2.57,21.2,15.36,40.87,33.15,53.77h-.13c.24.13.39.23.37.31L113.46,299l30.39-22.92c8-6.06,12-6.2,21.74-6,41.45,1.18,88.57-15.69,89.65-61.71C255.92,179.46,245.93,139.34,217.57,123.64Z"/><path id="Path" class="cls-2" d="M147.81,142.75S174,116.18,207.51,130c36.11,14.86,39.3,68,39.3,68-38.8-86.67-99-55.25-99-55.25Z"/></g></g></svg>`,
};

async function pit_cfl_get_languages_data(language, index) {
  const rootpath = pit_cfl_vars.rootpath;
  if (!pit_cfl_languages) {
    const languages_url = rootpath + "/admin/jscripts/pit_changeforumlang/languages.json";
    pit_cfl_languages = await fetch(languages_url).then(response => response.json());
  }

  if (language) {
    if (index !== undefined) return pit_cfl_languages[language]["packages"][index];
    return pit_cfl_languages[language];
  }

  return pit_cfl_languages;
}

async function pit_cfl_get_themes_data(is_acp_theme, theme_name) {
  const rootpath = pit_cfl_vars.rootpath;
  if (!pit_cfl_themes) {
    const themes_url = rootpath + "/admin/jscripts/pit_changeforumlang/themes.json";
    pit_cfl_themes = await fetch(themes_url).then(response => response.json());
  }

  if (is_acp_theme !== undefined || theme_name !== undefined) {
    let filtered_themes = pit_cfl_themes[is_acp_theme ? "acp_theme_list" : "theme_list"];
    if (theme_name) return filtered_themes.find(theme => theme.name === theme_name);

    return filtered_themes;
  }

  return pit_cfl_themes;
}

async function pit_cfl_main() {
  const $pit_cfl = document.getElementById("pit_cfl_content");
  $pit_cfl.innerHTML = "";
  const $pit_cfl_footer = document.getElementById("pit_cfl_content_footer");
  $pit_cfl_footer.innerHTML = "";

  await pit_cfl_get_languages_data();

  let html_content = `<style>
    .languages_row { display: grid; grid-template-columns: repeat(auto-fill, minmax(180px, 1fr)); gap: 1.5rem; }
    .language-item { background: #fdfdfd; border: 1px solid #e3e3e3; border-radius: 0.5rem; margin-bottom: 15px; overflow: hidden; }
    .language-item:hover { cursor: pointer; }
    .language-item--img-container { overflow-clip-margin: content-box; overflow: clip; }
    .language-item--img-container span { width: 100% !important; aspect-ratio: 4/3; }
    .language-item--content { padding: 15px 7px; }
    .language-item--content h4 { margin: auto !important }
    .language-item--content h4 small { color: gray; font-weight: normal; }
  </style>
  <div class="languages_row">`;

  for (let language in pit_cfl_languages) {
    const language_data = pit_cfl_languages[language].data;

    html_content += `<div class="language-item" onclick="pit_cfl_show_language_packages('${language}')">
      <div class="language-item--img-container"><span class="fi fi-${language_data.icon_code}"></span></div>
      <div class="language-item--content">
        <h4>${language_data.name} <small class="float_right">(${pit_cfl_languages[language].packages.length})</small></h4>
      </div>
    </div>`;
  }

  html_content += `</div>`;

  $pit_cfl.innerHTML = html_content;
}

async function pit_cfl_show_language_packages(language) {
  const $pit_cfl = document.getElementById("pit_cfl_content");
  $pit_cfl.innerHTML = "";
  const cpstyle_images_url = pit_cfl_vars.cpstyle_images_url;

  let html_content = `<style>
    .packages_row { display: grid; grid-template-columns: repeat(auto-fill, minmax(450px, 1fr)); gap: 1.5rem; }
    .package-item { display: grid; grid-template-columns: 20% 1fr; background: #fdfdfd; border: 1px solid #e3e3e3; border-radius: 0.5rem; margin-bottom: 15px; overflow: hidden; }
    .package-item--img-container { overflow-clip-margin: content-box; overflow: clip; }
    .package-item--img-container span { width: 100% !important; aspect-ratio: 1; }
    .package-item--content { padding: 15px 7px 10px 7px; }
    .package-item--content h4 { margin: auto !important }
    .package-item--content h4 small { color: gray; font-weight: normal; }
    .package-item--content--actions a { margin: 5px; fill: #333; cursor: pointer; }
  </style>
  <div class="packages_row">`;

  const language_data = pit_cfl_languages[language].data;
  for (let index in pit_cfl_languages[language].packages) {
    const package = pit_cfl_languages[language].packages[index];
    html_content += `<div class="package-item">
      <div class="package-item--img-container"><span class="fi fi-${
        package.icon_code || language_data.icon_code
      } fis"></span></div>
      <div class="package-item--content">
        <h4><a href="https://community.mybb.com/mods.php?action=view&pid=${package.mybb_mod_pid}" target="_blank">${
      package.name
    }</a></h4>
        <p>
          <span>${lang.pit_cfl.author || "Author"}:</span> 
            <a href="${package.website}" target="_blank">${package.author}</a><br>

          <img src="${cpstyle_images_url}/icons/${package.admin ? `success.png` : `bullet_off.png`}"> 
            <span>${lang.pit_cfl.inc_admin || "The translation includes the Admin CP"}</span><br>

          <img src="${cpstyle_images_url}/icons/${
      package.includes.settings_translation ? `success.png` : `bullet_off.png`
    }">
            <span>${lang.pit_cfl.inc_setting || "The translation includes the settings"}</span><br>

          <img src="${cpstyle_images_url}/icons/${
      package.includes.install_upgrade_translation ? `success.png` : `bullet_off.png`
    }">
            <span>${lang.pit_cfl.inc_install || "The translation includes the install/upgrade"}</span><br>
              
          <img src="${cpstyle_images_url}/icons/${package.includes.acp_theme ? `success.png` : `bullet_off.png`}">
            <span>${lang.pit_cfl.inc_acp_theme || "The package includes the Admin CP theme"}</span><br>
        </p>
        <div class="package-item--content--actions">
          <a class="float_right" onclick="pit_cfl_install_language('${language}', ${index})" title="${
      lang.pit_cfl.action_install || "Install"
    }">${pit_cfl_icons.dl}</a>
          ${
            package.docs_link
              ? `<a href="${package.docs_link}" target="_blank" class="float_right" title="${
                  lang.pit_cfl.action_docs || "Documents"
                }">${pit_cfl_icons.doc}</a>`
              : ""
          }
          <a href="${package.githubrepo}" target="_blank" class="float_right" title="Github">${pit_cfl_icons.github}</a>
          <a href="https://community.mybb.com/mods.php?action=view&pid=${
            package.mybb_mod_pid
          }" target="_blank" class="float_right" title="MyBB Mod">${pit_cfl_icons.mybb}</a>
        </div>
      </div>
    </div>`;
  }

  html_content += `</div>`;

  $pit_cfl.innerHTML = html_content;

  const $pit_cfl_footer = document.getElementById("pit_cfl_content_footer");
  $pit_cfl_footer.innerHTML = `<input type="button" class="submit_button" value="${lang.pit_cfl.action_back}" onclick="pit_cfl_main()">`;
}

async function pit_cfl_install_language(language, index) {
  const package = pit_cfl_languages[language].packages[index];
  if (!package) return alert(lang.unknown_error);
  const { spinner_image, my_post_key } = pit_cfl_vars;

  let content = `<div id="pit_cfl_install_language_popup" style="width: 500px; padding: 25px 15px; text-align: center;">
    <img src="${spinner_image}" alt="loading"> ${loading_text}
  </div>`;
  $(content).modal();

  const is_default = package.is_default;

  let latest_release = package.latest_release || {};
  if (!package.latest_release && !is_default) {
    const [github_owner, github_repo] = package.githubrepo.slice(19).split("/");

    latest_release = await fetch(`https://api.github.com/repos/${github_owner}/${github_repo}/releases/latest`).then(
      response => response.json()
    );

    if (!latest_release || !latest_release.zipball_url)
      return (document.getElementById("pit_cfl_install_language_popup").innerHTML = lang.unknown_error);

    package.latest_release = latest_release;
  }

  let compatibility_msg = "";
  if (!is_default) {
    ({ compatibility_msg } = await check_compatibility(latest_release?.body));
  }

  content = `<div>
    ${compatibility_msg ? `<p>${compatibility_msg}</p>` : ""}
    <form action="index.php?module=config-pit-changeforumlang&amp;action=install-language" method="POST">
      <input type="hidden" name="my_post_key" value="${my_post_key}">
      <input type="hidden" name="language" value="${language}">
      <input type="hidden" name="index" value="${index}">
      <input type="hidden" name="is_exist" value="${is_default}">
      <input type="hidden" name="acp_theme" value="${package.includes.acp_theme}">
      <input type="hidden" name="mybb_mod_pid" value="${package.mybb_mod_pid}">
      <input type="hidden" name="zipball_url" value="${latest_release?.zipball_url || ""}">
      <div class="form_container" style="padding: 15px 7px;">
        <div style="text-align: start;">
          <label for="update_bblang">${lang.pit_cfl.q_update_bblang}</label>
          <div class="description">${lang.pit_cfl.q_update_bblang_desc}</div>
          <div class="form_row"><label for="update_bblang" class="label_radio_yes " style="float:none; display:inline-block;"><input type="radio" name="update_bblang" value="yes" class="radio_input radio_yes " id="update_bblang" checked="checked">${
            lang.pit_cfl.yes_confirm
          }</label> <label class="label_radio_no " style="float:none; display:inline-block;"><input type="radio" name="update_bblang" value="no" class="radio_input radio_no">${
    lang.pit_cfl.no_confirm
  }</label></div>
        </div>
        <input type="submit" value="${
          lang.pit_cfl.action_install
        }" class="submit_button" style="background: #1c991f; color: white; padding: 4px 25px; margin: 15px auto 0px auto">
      </div>
    </form>
  </div>`;

  if (latest_release?.zipball_url || package.mybb_mod_pid) {
    content += `<div>
    <br>
    <br>
    <br>
    <p>
      ${
        latest_release?.zipball_url
          ? `<a href="${latest_release.zipball_url}" target="_blank" onclick="$.modal.close();" style="padding-left: 10px; padding-right: 10px;">${lang.pit_cfl.action_dl_zip}</a>`
          : ""
      }
      ${latest_release?.zipball_url && package.mybb_mod_pid ? `<span> | </span>` : ""}
      ${
        package.mybb_mod_pid
          ? `<a href="https://community.mybb.com/mods.php?action=download&pid=${package.mybb_mod_pid}" target="_blank" onclick="$.modal.close();" style="padding-left: 10px; padding-right: 10px;">${lang.pit_cfl.action_dl_mybbmod}</a>`
          : ""
      }
    </p>
    </div>`;
  }
  document.getElementById("pit_cfl_install_language_popup").innerHTML = content;
}

async function pit_cfl_main_recommended() {
  const { cpstyle_images_url, mybb_acp_theme_list, current_acp_theme, language, index } = pit_cfl_vars;

  const $pit_cfl = document.getElementById("pit_cfl_content");
  $pit_cfl.innerHTML = "";

  const language_package = await pit_cfl_get_languages_data(language, index);

  let html_content = `<style>
    .packages_row { display: grid; grid-template-columns: repeat(auto-fill, minmax(450px, 1fr)); gap: 1.5rem; }
    .package-item { display: grid; grid-template-columns: 20% 1fr; background: #fdfdfd; border: 1px solid #e3e3e3; border-radius: 0.5rem; margin-bottom: 15px; overflow: hidden; }
    .package-item.non-preview { display: grid; grid-template-columns: 1fr; background: #fdfdfd; border: 1px solid #e3e3e3; border-radius: 0.5rem; margin-bottom: 15px; overflow: hidden; }
    .package-item--img-container { overflow-clip-margin: content-box; overflow: clip; }
    .package-item--img-container span { width: 100% !important; aspect-ratio: 1; }
    .package-item--content { padding: 15px 7px 10px 7px; }
    .package-item--content h4 { margin: auto !important }
    .package-item--content h4 small { color: gray; font-weight: normal; }
    .package-item--content--actions a { margin: 5px; fill: #333; cursor: pointer; }
  </style>
  <h2>${lang.pit_cfl.install_rec}</h2>`;

  if (language_package.recommended?.acp_theme?.name) {
    const package = await pit_cfl_get_themes_data(true, language_package.recommended.acp_theme.name);
    const is_exist = mybb_acp_theme_list.includes(package.name);
    const is_active = current_acp_theme === package.name;

    html_content += `<div class="packages_row">
      <div class="package-item non-preview" style="${is_active ? `background: #dcffdc;` : ""}">
        <div class="package-item--content">
          <h4 style="text-align: center;">
            <a href="https://community.mybb.com/mods.php?action=view&pid=${package.mybb_mod_pid}"
                target="_blank">${package.title} <sup style="color: ${is_active ? "green" : "red"};">${
      is_active
        ? lang.pit_cfl.install_rec_installed_is_active || "Activated"
        : language_package.recommended.acp_theme.forcefully
        ? lang.pit_cfl.install_rec_forcefully || "forcefully"
        : ""
    }</sup></a>
          </h4>
          <p style="text-align: center; font-weight: bold;">${
            lang.pit_cfl.install_rec_acp_theme || "Admin CP Theme"
          }</p>
          <p>
            <span>${lang.pit_cfl.author || "Author"}:</span> 
              <a href="${package.website}" target="_blank">${package.author}</a><br>

            <img src="${cpstyle_images_url}/icons/${package.rtl ? `success.png` : `bullet_off.png`}"> 
              <span>${lang.pit_cfl.install_rec_rtl_support || "Support RTL"}</span><br>
          </p>
          <div class="package-item--content--actions">
            ${
              is_active
                ? ""
                : is_exist
                ? `<a class="float_right" onclick="pit_cfl_apply_theme(true, '${package.name}')" title="${
                    lang.pit_cfl.action_apply || "Apply"
                  }">${pit_cfl_icons.check}</a>`
                : `<a class="float_right" onclick="pit_cfl_install_theme(true, '${package.name}')" title="${
                    lang.pit_cfl.action_install || "Install"
                  }">${pit_cfl_icons.dl}</a>`
            }
            ${
              package.docs_link
                ? `<a href="${package.docs_link}" target="_blank" class="float_right" title="${
                    lang.pit_cfl.action_docs || "Documents"
                  }">
                  ${pit_cfl_icons.doc}</a>`
                : ""
            }
            <a href="${package.githubrepo}" target="_blank" class="float_right" title="Github">${
      pit_cfl_icons.github
    }</a>
            <a href="https://community.mybb.com/mods.php?action=view&pid=${
              package.mybb_mod_pid
            }" target="_blank" class="float_right" title="MyBB Mod">${pit_cfl_icons.mybb}</a>
          </div>
        </div>
      </div>
    </div>`;
  }

  html_content += ``;

  $pit_cfl.innerHTML = html_content;
}

async function pit_cfl_install_theme(is_acp_theme, theme_name) {
  const { spinner_image, my_post_key, mybb_acp_theme_list, language, index } = pit_cfl_vars;

  const package = await pit_cfl_get_themes_data(is_acp_theme, theme_name);
  if (!package) return alert(lang.unknown_error);

  let content = `<div id="pit_cfl_install_theme_popup" style="width: 500px; padding: 25px 15px; text-align: center;">
    <img src="${spinner_image}" alt="loading"> ${loading_text}
  </div>`;
  $(content).modal();

  const is_default = package.is_default;
  const is_exist = is_default || mybb_acp_theme_list.includes(theme_name);

  let latest_release = package.latest_release || {};
  if (!package.latest_release && !is_default) {
    const [github_owner, github_repo] = package.githubrepo.slice(19).split("/");

    latest_release = await fetch(`https://api.github.com/repos/${github_owner}/${github_repo}/releases/latest`).then(
      response => response.json()
    );

    if (!latest_release || !latest_release.zipball_url)
      return (document.getElementById("pit_cfl_install_theme_popup").innerHTML = lang.unknown_error);

    package.latest_release = latest_release;
  }

  let compatibility_msg = "";
  if (!is_default) {
    ({ compatibility_msg } = await check_compatibility(latest_release?.body));
  }

  content = `<div>
    ${compatibility_msg ? `<p>${compatibility_msg}</p>` : ""}
    <form action="index.php?module=config-pit-changeforumlang&amp;action=install-theme" method="POST">
      <input type="hidden" name="my_post_key" value="${my_post_key}">
      <input type="hidden" name="language" value="${language}">
      <input type="hidden" name="index" value="${index}">
      <input type="hidden" name="is_acp_theme" value="${is_acp_theme}">
      <input type="hidden" name="theme_name" value="${theme_name}">
      <input type="hidden" name="is_exist" value="${is_exist}">
      <input type="hidden" name="mybb_mod_pid" value="${package.mybb_mod_pid}">
      <input type="hidden" name="zipball_url" value="${latest_release?.zipball_url || ""}">
      <div class="form_container" style="padding: 15px 7px;">
        <input type="submit" value="${
          lang.pit_cfl.action_install
        }" class="submit_button" style="background: #1c991f; color: white; padding: 4px 25px; margin: 15px auto 0px auto">
      </div>
    </form>
  </div>`;

  if (latest_release?.zipball_url || package.mybb_mod_pid) {
    content += `<div>
    <br>
    <br>
    <br>
    <p>
      ${
        latest_release?.zipball_url
          ? `<a href="${latest_release.zipball_url}" target="_blank" onclick="$.modal.close();" style="padding-left: 10px; padding-right: 10px;">${lang.pit_cfl.action_dl_zip}</a>`
          : ""
      }
      ${latest_release?.zipball_url && package.mybb_mod_pid ? `<span> | </span>` : ""}
      ${
        package.mybb_mod_pid
          ? `<a href="https://community.mybb.com/mods.php?action=download&pid=${package.mybb_mod_pid}" target="_blank" onclick="$.modal.close();" style="padding-left: 10px; padding-right: 10px;">${lang.pit_cfl.action_dl_mybbmod}</a>`
          : ""
      }
    </p>
    </div>`;
  }
  document.getElementById("pit_cfl_install_theme_popup").innerHTML = content;
}

async function pit_cfl_apply_theme(is_acp_theme, theme_name) {
  const { spinner_image, my_post_key, mybb_acp_theme_list, current_acp_theme, language, index } = pit_cfl_vars;

  const is_exist = mybb_acp_theme_list.includes(theme_name);
  if (!is_exist) return alert(lang.unknown_error);

  const package = await pit_cfl_get_themes_data(is_acp_theme, theme_name);
  if (!package) return alert(lang.unknown_error);

  if (current_acp_theme === theme_name) {
    return alert(lang.pit_cfl.install_installed_no_act);
  }

  const myForm = document.createElement("form");
  myForm.setAttribute("method", "POST");
  myForm.setAttribute("action", "index.php?module=config-pit-changeforumlang&action=install-theme");

  myForm.innerHTML = `<input type="hidden" name="my_post_key" value="${my_post_key}">
    <input type="hidden" name="language" value="${language}">
    <input type="hidden" name="index" value="${index}">
    <input type="hidden" name="is_acp_theme" value="${is_acp_theme}">
    <input type="hidden" name="theme_name" value="${theme_name}">
    <input type="hidden" name="is_exist" value="${is_exist}">`;

  document.body.appendChild(myForm);
  myForm.submit();
  document.body.removeChild(myForm);
}

async function check_compatibility(text, compatible_version) {
  const { current_version, cpstyle_images_url } = pit_cfl_vars;

  let compatibility_msg = `<img src="${cpstyle_images_url}/icons/warning.png"> ${lang.pit_cfl.version_selected_version_no_info}`;

  if (text && !compatible_version) {
    const result = text.match(/(Compatible|Compatibility): (\d{1}[\.]?\d{1,2}[\.]?(\d{0,2}|[\*]?))/gi);

    if (result && result.length) {
      compatible_version = result[0]
        .match(/(\d{1}[\.]?\d{1,2}[\.]?(\d{0,2}|[\*]?))/g)[0]
        .split(".")
        .join("");
    }
  }

  if (compatible_version) {
    if (compatible_version === current_version) {
      compatibility_msg = `<img src="${cpstyle_images_url}/icons/success.png"> ${lang.pit_cfl.version_fully_compatible}`;
    } else {
      for (let i = 0; i < compatible_version.length; i++) {
        if (compatible_version[i] === "*") {
          compatibility_msg = `<img src="${cpstyle_images_url}/icons/success.png"> ${lang.pit_cfl.version_fully_compatible}`;
          break;
        } else if (compatible_version[i] < current_version[i]) {
          compatibility_msg = `<img src="${cpstyle_images_url}/icons/warning.png"> ${lang.pit_cfl.version_is_lower_version}`;
          break;
        } else if (compatible_version[i] > current_version[i]) {
          compatibility_msg = `<img src="${cpstyle_images_url}/icons/warning.png"> ${lang.pit_cfl.version_may_not_compatible}`;
          break;
        }
      }
    }
  }

  return { compatibility_msg, compatible_version };
}
