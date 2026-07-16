import os

# 1. Revert meanmenu
os.system("git checkout public/assets/oxford/css/meanmenu.min.css")

# 2. Fix app.css
app_css_path = r"c:\laragon\www\oxford\public\assets\css\app.css"
with open(app_css_path, "r", encoding="utf-8") as f:
    app_css = f.read()
app_css = app_css.replace("--ox-fs-nav:   1.7rem;", "--ox-fs-nav:   1.5rem;")
with open(app_css_path, "w", encoding="utf-8") as f:
    f.write(app_css)

# 3. Fix components.css
comp_css_path = r"c:\laragon\www\oxford\public\assets\css\components.css"
with open(comp_css_path, "r", encoding="utf-8") as f:
    comp_css = f.read()

comp_css = comp_css.replace("font-size: calc(1.3em + 0.2rem);", "font-size: 1.3em;")
comp_css = comp_css.replace("font-size: calc(1.5em + 0.2rem);", "font-size: 1.5em;")
comp_css = comp_css.replace(".ox-menu .ox-caret { font-size: 16.2px;", ".ox-menu .ox-caret { font-size: 13px;")
comp_css = comp_css.replace(".ox-submenu a {\n    display: block; padding: 11px 14px;\n    font-size: 1.7rem;", ".ox-submenu a {\n    display: block; padding: 11px 14px;\n    font-size: 1.5rem;")
comp_css = comp_css.replace("cursor: pointer; font-size: 27.2px;", "cursor: pointer; font-size: 24px;")
comp_css = comp_css.replace(".ox-drawer__menu ul a { padding: 11px 6px; min-height: 44px; font-weight: 500; font-size: 1.2rem;", ".ox-drawer__menu ul a { padding: 11px 6px; min-height: 44px; font-weight: 500; font-size: 1rem;")
comp_css = comp_css.replace("display: grid; place-items: center; font-size: 21.2px;", "display: grid; place-items: center; font-size: 18px;")

with open(comp_css_path, "w", encoding="utf-8") as f:
    f.write(comp_css)

# 4. Fix style.css Header and Slider sections
style_path = r"c:\laragon\www\oxford\public\assets\oxford\style.css"
with open(style_path, "r", encoding="utf-8") as f:
    mod_style = f.read()

os.system("git checkout public/assets/oxford/style.css")

with open(style_path, "r", encoding="utf-8") as f:
    org_style = f.read()

start_marker = "[03] Header Area"
end_marker = "[05] About Area"

org_start = org_style.find(start_marker)
org_end = org_style.find(end_marker)
mod_start = mod_style.find(start_marker)
mod_end = mod_style.find(end_marker)

if org_start != -1 and org_end != -1 and mod_start != -1 and mod_end != -1:
    pristine_section = org_style[org_start:org_end]
    final_style = mod_style[:mod_start] + pristine_section + mod_style[mod_end:]
    with open(style_path, "w", encoding="utf-8") as f:
        f.write(final_style)
    print("style.css reverted header/slider successfully!")
else:
    print("Failed to find markers in style.css")
