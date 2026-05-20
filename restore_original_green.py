import os

root_dir = "/home/ari/Documents/projects/pupr/e-kop-pkpp-riau/resources/views"
old_colors = ["5d7a66", "5D7A66", "68b771", "68B771", "059669"]
new_color = "043d2e" # Original Hunter Green color of the system

count = 0
for dirpath, _, filenames in os.walk(root_dir):
    for filename in filenames:
        if filename.endswith(".blade.php"):
            filepath = os.path.join(dirpath, filename)
            with open(filepath, "r", encoding="utf-8", errors="ignore") as f:
                content = f.read()
            
            modified = False
            for old_color in old_colors:
                if old_color in content:
                    content = content.replace(old_color, new_color)
                    modified = True
            
            if modified:
                with open(filepath, "w", encoding="utf-8") as f:
                    f.write(content)
                print(f"Updated: {filepath}")
                count += 1

print(f"Total files updated: {count}")
