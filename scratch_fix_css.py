import os

path = r'c:\laragon\www\oxford\resources\views\admin\layout\master.blade.php'
with open(path, 'r', encoding='utf-8') as f:
    content = f.read()

# Replace the overly aggressive font rule
to_replace = """.app-sidebar-menu * {
            font-family: 'Cairo', sans-serif !important;
        }"""
        
replacement = """.app-sidebar-menu *:not(i):not(i *) {
            font-family: 'Cairo', sans-serif !important;
        }
        .app-sidebar-menu i.ki-duotone, .app-sidebar-menu i.ki-duotone * {
            font-family: keenicons !important;
        }"""

if to_replace in content:
    content = content.replace(to_replace, replacement)
    with open(path, 'w', encoding='utf-8') as f:
        f.write(content)
    print("CSS Fixed in master.blade.php")
else:
    print("Could not find exact CSS block to replace.")
