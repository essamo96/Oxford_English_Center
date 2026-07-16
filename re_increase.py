import os
import subprocess
import re

# 1. Get modified files so we skip them (avoid double scale)
result = subprocess.run(["git", "diff", "--name-only"], capture_output=True, text=True)
modified_files = set([line.strip().replace("/", "\\") for line in result.stdout.split('\n') if line.strip()])

def process_match(match):
    val_str = match.group(1)
    unit = match.group(2)
    important = match.group(3)
    end_char = match.group(4)
    
    try:
        val = float(val_str)
    except ValueError:
        return match.group(0)

    if unit == 'px':
        new_val = round(val + 3.2, 2)
        new_val_str = str(int(new_val)) if new_val.is_integer() else str(new_val)
        return f"font-size: {new_val_str}px{important}{end_char}"
    elif unit == 'rem':
        new_val = round(val + 0.2, 3)
        new_val_str = str(int(new_val)) if new_val.is_integer() else str(new_val)
        return f"font-size: {new_val_str}rem{important}{end_char}"
    elif unit == 'em':
        return f"font-size: calc({val_str}em + 0.2rem){important}{end_char}"
    elif unit == '%':
        return f"font-size: calc({val_str}% + 0.2rem){important}{end_char}"
    else:
        return match.group(0)

pattern = re.compile(r"font-size\s*:\s*([\d\.]+)(px|rem|em|%)([^;}\"']*)([;}\"'])", re.IGNORECASE)

css_dir = r"c:\laragon\www\oxford\public\assets"

updated_count = 0
for root, dirs, files in os.walk(css_dir):
    for file in files:
        if file.endswith('.css'):
            filepath = os.path.join(root, file)
            rel_path = os.path.relpath(filepath, r"c:\laragon\www\oxford")
            
            # normalize slashes for comparison
            rel_path_fwd = rel_path.replace("\\", "/")
            if rel_path in modified_files or rel_path_fwd in modified_files:
                continue
                
            if file in ['normalize.css', 'main.css', 'meanmenu.min.css']:
                continue

            try:
                with open(filepath, 'r', encoding='utf-8') as f:
                    content = f.read()
            except:
                continue
            
            new_content = pattern.sub(process_match, content)
            
            if new_content != content:
                with open(filepath, 'w', encoding='utf-8') as f:
                    f.write(new_content)
                updated_count += 1
                print(f"Increased: {rel_path}")

print(f"Re-increased {updated_count} untouched files.")
