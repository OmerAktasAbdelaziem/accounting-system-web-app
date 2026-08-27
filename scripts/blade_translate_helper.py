#!/usr/bin/env python3
import argparse
import os
import re
import sys
from difflib import unified_diff
from pathlib import Path

SCRIPT_BLOCK = re.compile(r"(<script\b[^>]*>).*?(</script>)", re.IGNORECASE | re.DOTALL)
STYLE_BLOCK = re.compile(r"(<style\b[^>]*>).*?(</style>)", re.IGNORECASE | re.DOTALL)
TEXT_NODE = re.compile(r">(\s*)([^<]+?)(\s*)<", re.DOTALL)

IGNORE_PATTERNS = [r"\{\{", r"\}@", r"@lang", r"__\(", r"trans\(", r"Lang::", r"\$", r"\{\%", r"@if", r"@foreach", r"@endif", r"@endforeach"]


def is_translatable_text(text: str) -> bool:
    trimmed = text.strip()
    if not trimmed:
        return False
    if not re.search(r"[A-Za-z]", trimmed):
        return False
    if re.search(r"^[0-9\s\W]+$", trimmed):
        return False
    for pattern in IGNORE_PATTERNS:
        if re.search(pattern, trimmed):
            return False
    return True


def escape_for_single_quotes(text: str) -> str:
    return text.replace("'", "\\'")


def wrap_text(match: re.Match) -> str:
    leading = match.group(1)
    content = match.group(2)
    trailing = match.group(3)
    if not is_translatable_text(content):
        return match.group(0)

    escaped = escape_for_single_quotes(content.strip())
    return f">{leading}{{{{ __('{escaped}') }}}}{trailing}<"


def mask_blocks(text: str) -> tuple[str, list[tuple[int, str]]]:
    placeholders = []

    def replacer(match: re.Match, block_type: str) -> str:
        index = len(placeholders)
        placeholder = f"__BLADE_TRANSLATE_PLACEHOLDER_{block_type}_{index}__"
        placeholders.append((index, match.group(0)))
        return placeholder

    text = SCRIPT_BLOCK.sub(lambda m: replacer(m, 'SCRIPT'), text)
    text = STYLE_BLOCK.sub(lambda m: replacer(m, 'STYLE'), text)
    return text, placeholders


def restore_blocks(text: str, placeholders: list[tuple[int, str]]) -> str:
    for index, original in placeholders:
        placeholder = f"__BLADE_TRANSLATE_PLACEHOLDER_SCRIPT_{index}__"
        text = text.replace(placeholder, original)
        placeholder = f"__BLADE_TRANSLATE_PLACEHOLDER_STYLE_{index}__"
        text = text.replace(placeholder, original)
    return text


def process_file(path: Path, apply: bool, show_diff: bool) -> tuple[int, str | None]:
    original_text = path.read_text(encoding='utf-8')
    masked_text, placeholders = mask_blocks(original_text)
    replaced_text = TEXT_NODE.sub(wrap_text, masked_text)
    fixed_text = restore_blocks(replaced_text, placeholders)

    if fixed_text == original_text:
        return 0, None

    if show_diff:
        diff = '\n'.join(
            unified_diff(
                original_text.splitlines(),
                fixed_text.splitlines(),
                fromfile=str(path),
                tofile=str(path) + ' (updated)',
                lineterm='',
            )
        )
    else:
        diff = None

    if apply:
        path.write_text(fixed_text, encoding='utf-8')
    return 1, diff


def scan_directory(root: Path, apply: bool, show_diff: bool, patterns: list[str]) -> tuple[int, int, int]:
    changed_files = 0
    scanned_files = 0
    total_replacements = 0

    for entry in root.rglob('*.blade.php'):
        if patterns and not any(re.search(pattern, str(entry)) for pattern in patterns):
            continue

        scanned_files += 1
        changed, diff = process_file(entry, apply, show_diff)
        if changed:
            changed_files += 1
            total_replacements += 1
            print(f"[MATCH] {entry}")
            if diff:
                print(diff)
            if apply:
                print(f"  => updated {entry}")

    return scanned_files, changed_files, total_replacements


def main() -> int:
    parser = argparse.ArgumentParser(
        description="Scan Blade files and wrap plain English text nodes in __('...') translation calls."
    )
    parser.add_argument(
        '--root',
        default='resources/views',
        help='Root directory for Blade files (default: resources/views)',
    )
    parser.add_argument(
        '--apply',
        action='store_true',
        help='Write replacements back to files. Without this flag, the script only previews changes.',
    )
    parser.add_argument(
        '--diff',
        action='store_true',
        help='Show unified diff for each changed file in preview mode.',
    )
    parser.add_argument(
        '--include',
        action='append',
        default=[],
        help='Optional regex to only process files whose path matches this pattern.',
    )
    args = parser.parse_args()

    root = Path(args.root)
    if not root.exists():
        print(f'ERROR: root path not found: {root}', file=sys.stderr)
        return 2

    scanned_files, changed_files, total_replacements = scan_directory(root, args.apply, args.diff, args.include)
    print('')
    print(f'Scanned files: {scanned_files}')
    print(f'Files changed: {changed_files}')
    print(f'Replacement candidates: {total_replacements}')
    print('Mode: ' + ('APPLY' if args.apply else 'PREVIEW'))

    return 0 if changed_files >= 0 else 1


if __name__ == '__main__':
    raise SystemExit(main())
