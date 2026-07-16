import os
import glob

search_text = "//cdn.datatables.net/plug-ins/1.10.25/i18n/Arabic.json"
replace_text = "https://cdn.datatables.net/plug-ins/1.11.5/i18n/ar.json"

directory = r"c:\laragon\www\oxford\resources\views\admin"
count = 0

for root, _, files in os.walk(directory):
    for file in files:
        if file.endswith(".blade.php"):
            path = os.path.join(root, file)
            with open(path, "r", encoding="utf-8") as f:
                content = f.read()
            if search_text in content:
                content = content.replace(search_text, replace_text)
                with open(path, "w", encoding="utf-8") as f:
                    f.write(content)
                print(f"Replaced in {path}")
                count += 1

print(f"Total files updated: {count}")
