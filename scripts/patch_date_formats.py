from pathlib import Path

root = Path('resources/views')
patterns = {
    "->format('M d, Y H:i')": "->translatedFormat('M d, Y H:i')",
    '->format("M d, Y H:i")': '->translatedFormat("M d, Y H:i")',
    "->format('M d, Y h:i A')": "->translatedFormat('M d, Y h:i A')",
    '->format("M d, Y h:i A")': '->translatedFormat("M d, Y h:i A")',
    "->format('M d, Y')": "->translatedFormat('M d, Y')",
    '->format("M d, Y")': '->translatedFormat("M d, Y")',
    "->format('F Y')": "->translatedFormat('F Y')",
    '->format("F Y")': '->translatedFormat("F Y")',
    "->format('F d, Y')": "->translatedFormat('F d, Y')",
    '->format("F d, Y")': '->translatedFormat("F d, Y")',
}

updated_files = []
for path in root.rglob('*.blade.php'):
    text = path.read_text(encoding='utf-8')
    new_text = text
    for old, new in patterns.items():
        new_text = new_text.replace(old, new)
    if new_text != text:
        path.write_text(new_text, encoding='utf-8')
        updated_files.append(path)

print(f'Updated {len(updated_files)} files')
for file in updated_files:
    print(file)
