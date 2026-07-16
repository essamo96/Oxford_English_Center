import os

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
        
        # Replace the img tag with added style
        # we need to be careful with variations, but the grep output showed it's mostly uniform:
        old_tag1 = '<img src="{{ url(\'assets/oxford/img/OTE-Approved-Test-Centre-Logo.png\') }}" alt="OTE Approved Test Centre">'
        new_tag1 = '<img src="{{ url(\'assets/oxford/img/OTE-Approved-Test-Centre-Logo.png\') }}" alt="OTE Approved Test Centre" style="max-width: 70px; height: auto;">'
        
        old_tag2 = '<img src="{{ url(\'assets/oxford/img/OTE-Approved-Test-Centre-Logo.png\') }}" alt=""/>'
        new_tag2 = '<img src="{{ url(\'assets/oxford/img/OTE-Approved-Test-Centre-Logo.png\') }}" alt="" style="max-width: 70px; height: auto;"/>'
        
        content = content.replace(old_tag1, new_tag1)
        content = content.replace(old_tag2, new_tag2)
        
        with open(filepath, 'w', encoding='utf-8') as f:
            f.write(content)
        
print("Logo resized to 70px successfully.")
