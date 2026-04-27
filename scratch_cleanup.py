import os, glob, re

files = glob.glob('c:/laragon/www/oxford/resources/views/admin/**/*.blade.php', recursive=True)
files.extend(glob.glob('c:/laragon/www/oxford/resources/views/admin/*.blade.php', recursive=True))
count = 0
for path in files:
    if os.path.isfile(path):
        with open(path, 'r', encoding='utf-8') as f:
            content = f.read()
            
        def cleaner(match):
            ki1 = match.group(1) # eg. magnifier
            rest = match.group(2) # span section
            return f'<i class="ki-duotone ki-{ki1} fs-3 text-info me-2">{rest}'
            
        # Matches <i class="ki-duotone ki-magnifier ki-duotone ki-search"><span ...>
        new_content = re.sub(r'<i class="ki-duotone ki-([a-z0-9-]+) ki-duotone ki-[a-z0-9-]+">((?:<span class="path\d+"></span>)*</i>)', cleaner, content)

        if new_content != content:
            with open(path, 'w', encoding='utf-8') as f:
                f.write(new_content)
            count += 1

print(f'Fixed duplicate classes in {count} files.')
