import os

# 1. Update logo size in all relevant files
files_with_logo = [
    r"c:\laragon\www\oxford\resources\views\frontend\standalone_registration.blade.php",
    r"c:\laragon\www\oxford\resources\views\frontend\page\test.blade.php",
    r"c:\laragon\www\oxford\resources\views\frontend\page\format.blade.php",
    r"c:\laragon\www\oxford\resources\views\frontend\page\date.blade.php",
    r"c:\laragon\www\oxford\resources\views\frontend\general\menu.blade.php",
    r"c:\laragon\www\oxford\resources\views\frontend\general\footer.blade.php"
]

for filepath in files_with_logo:
    if os.path.exists(filepath):
        with open(filepath, 'r', encoding='utf-8') as f:
            content = f.read()
        
        # Replace the 70px style with the new 150px/70px style
        content = content.replace('style="max-width: 70px; height: auto;"', 'style="max-width: 150px; height: 70px;"')
        
        with open(filepath, 'w', encoding='utf-8') as f:
            f.write(content)

# 2. Move QR code to the right in view.blade.php
view_path = r"c:\laragon\www\oxford\resources\views\frontend\home\view.blade.php"
with open(view_path, "r", encoding="utf-8") as f:
    view_content = f.read()

view_content = view_content.replace('style="padding-left: 60px;"', 'style="padding-right: 60px;"')
view_content = view_content.replace('left: 15px; top: 50%;', 'right: 15px; top: 50%;')

with open(view_path, "w", encoding="utf-8") as f:
    f.write(view_content)

# 3. Increase icon size in components.css
comp_path = r"c:\laragon\www\oxford\public\assets\css\components.css"
with open(comp_path, "r", encoding="utf-8") as f:
    comp_css = f.read()

comp_css = comp_css.replace("width: 60px; height: 60px;", "width: 80px; height: 80px;")
comp_css = comp_css.replace("font-size: 29.2px;", "font-size: 40px;")
# just in case it was original 26px:
comp_css = comp_css.replace("font-size: 26px;", "font-size: 40px;")

with open(comp_path, "w", encoding="utf-8") as f:
    f.write(comp_css)

print("All modifications successfully applied.")
