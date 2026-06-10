#!/usr/bin/env python3
"""Загрузка документов из локальной папки в поисковый индекс Yandex AI Studio.

Создаёт единый индекс MkdBot-Docs (API ограничивает max_chunk_size_tokens: 100–2600).
Если индекс уже существует (ID из terraform.tfvars) — пересоздаёт
его с актуальными файлами. Новый ID записывается обратно в terraform.tfvars.

Авторизация — OAuth-токен из конфига yc CLI (~/.config/yandex-cloud/config.yaml)
или переменная YC_OAUTH_TOKEN. folder_id — из terraform.tfvars или YANDEX_FOLDER_ID.

Использование:
  python3 upload-to-search-index.py              # Обновить/создать единый индекс
  python3 upload-to-search-index.py --docs-dir DIR  # Другой каталог с документами
"""

import argparse
import os
import re
import sys
import time

from yandex_ai_studio_sdk import AIStudio
from yandex_ai_studio_sdk._files.file import File
from yandex_ai_studio_sdk._search_indexes.chunking_strategy import (
    StaticIndexChunkingStrategy,
)
from yandex_ai_studio_sdk._search_indexes.index_type import VectorSearchIndexType


DEFAULT_CHUNKING = {"max_chunk_size_tokens": 800, "chunk_overlap_tokens": 200}

UNIFIED_INDEX_NAME = "MkdBot-Docs"


def _read_var_from_tfvars(tfvars_path: str, var_name: str) -> str | None:
    """Читает значение переменной из terraform.tfvars по имени."""
    if not os.path.isfile(tfvars_path):
        return None
    with open(tfvars_path, encoding="utf-8") as f:
        for line in f:
            match = re.match(rf'^\s*{var_name}\s*=\s*"([^"]+)"', line)
            if match:
                return match.group(1).strip()
    return None


def update_vector_store_ids_in_tfvars(tfvars_path: str, new_id: str) -> None:
    """Обновляет vector_store_ids в terraform.tfvars."""
    if not os.path.isfile(tfvars_path):
        with open(tfvars_path, "a", encoding="utf-8") as f:
            f.write(f'\nvector_store_ids = "{new_id}"\n')
        print(f"  vector_store_ids записан в {tfvars_path}")
        return

    lines: list[str] = []
    found = False
    with open(tfvars_path, encoding="utf-8") as f:
        for line in f:
            if re.match(r'^\s*vector_store_ids\s*=\s*"', line):
                lines.append(f'vector_store_ids = "{new_id}"\n')
                found = True
            else:
                lines.append(line)

    if not found:
        lines.append(f'\nvector_store_ids = "{new_id}"\n')

    with open(tfvars_path, "w", encoding="utf-8") as f:
        f.writelines(lines)
    print(f"  vector_store_ids обновлён в {tfvars_path}")


def read_oauth_from_yc_config() -> str | None:
    """Читает OAuth-токен из конфига yc CLI."""
    config_path = os.path.expanduser("~/.config/yandex-cloud/config.yaml")
    if not os.path.isfile(config_path):
        return None
    with open(config_path, encoding="utf-8") as f:
        for line in f:
            match = re.match(r'^\s+token:\s+(.+)$', line)
            if match:
                return match.group(1).strip()
    return None


def get_auth() -> str:
    """Получает OAuth-токен: YC_OAUTH_TOKEN > конфиг yc CLI."""
    token = os.environ.get("YC_OAUTH_TOKEN")
    if token:
        return token

    token = read_oauth_from_yc_config()
    if token:
        print("OAuth-токен из конфига yc CLI")
        return token

    print("Ошибка: не найден OAuth-токен для авторизации", file=sys.stderr)
    print("Задайте один из вариантов:", file=sys.stderr)
    print("  export YC_OAUTH_TOKEN=<oauth-токен>", file=sys.stderr)
    print("Или войдите через: yc init", file=sys.stderr)
    sys.exit(1)


def get_folder_id(tfvars_path: str) -> str:
    """Получает folder_id из окружения или terraform.tfvars."""
    folder_id = os.environ.get("YANDEX_FOLDER_ID")
    if folder_id:
        return folder_id

    folder_id = _read_var_from_tfvars(tfvars_path, "folder_id")
    if folder_id:
        print(f"folder_id из terraform.tfvars: {folder_id}")
        return folder_id

    print("Ошибка: не задан YANDEX_FOLDER_ID и не найден в terraform.tfvars", file=sys.stderr)
    print("Экспортируйте переменную:", file=sys.stderr)
    print("  export YANDEX_FOLDER_ID=<id-папки>", file=sys.stderr)
    sys.exit(1)


def collect_files(docs_dir: str) -> list[dict]:
    """Собирает файлы для загрузки, пропуская скрытые."""
    files = []
    for dirpath, _, filenames in os.walk(docs_dir):
        for filename in filenames:
            if filename.startswith("."):
                continue
            file_path = os.path.join(dirpath, filename)
            relative_path = os.path.relpath(file_path, docs_dir)
            parts = relative_path.split(os.sep)
            section = parts[0] if len(parts) > 1 else "other"
            files.append({
                "file_path": file_path,
                "relative_path": relative_path,
                "section": section,
                "file_name": filename,
            })
    return files


def upload_files(
    sdk: AIStudio,
    files: list[dict],
) -> tuple[list[File], int, int, list[str]]:
    """Загружает файлы в Files API. Возвращает (File-список, успех, ошибки, список_ошибок)."""
    uploaded: list[File] = []
    success = 0
    failed = 0
    errors: list[str] = []

    for i, file_info in enumerate(files, 1):
        file_path = file_info["file_path"]
        relative_path = file_info["relative_path"]
        section = file_info["section"]
        file_name = file_info["file_name"]

        labels = {
            "source_path": f"local/{relative_path}",
            "section": section,
            "file_name": file_name,
            "source_type": "local",
        }

        print(f"  [{i}/{len(files)}] {relative_path} (секция: {section})")

        t0 = time.monotonic()
        try:
            uploaded_file = sdk.files.upload(
                path=file_path,
                name=file_name,
                labels=labels,
            )
            dt = time.monotonic() - t0
            print(f"    Файл загружен, id={uploaded_file.id} ({dt:.1f} с)")
            uploaded.append(uploaded_file)
            success += 1
        except Exception as exc:
            error_msg = f"Ошибка при загрузке {relative_path}: {exc}"
            print(f"    {error_msg}", file=sys.stderr)
            errors.append(error_msg)
            failed += 1

    return uploaded, success, failed, errors


def create_search_index(
    sdk: AIStudio,
    name: str,
    files: list[File],
) -> str:
    """Создаёт поисковый индекс с файлами. Возвращает ID."""
    chunking = StaticIndexChunkingStrategy(
        max_chunk_size_tokens=DEFAULT_CHUNKING["max_chunk_size_tokens"],
        chunk_overlap_tokens=DEFAULT_CHUNKING["chunk_overlap_tokens"],
    )
    print(f"  Чанкинг: max={DEFAULT_CHUNKING['max_chunk_size_tokens']}, overlap={DEFAULT_CHUNKING['chunk_overlap_tokens']}")

    index_type = VectorSearchIndexType(chunking_strategy=chunking)
    operation = sdk.search_indexes.create_deferred(
        files=files,
        index_type=index_type,
        name=name,
    )
    print(f"  Ожидание создания индекса (может занять несколько минут)...")
    t0 = time.monotonic()
    search_index = operation.wait(poll_timeout=900)
    dt = time.monotonic() - t0
    print(f"  Индекс создан, id={search_index.id} ({dt:.1f} с)")
    return search_index.id


def create_or_recreate_index(
    sdk: AIStudio,
    files: list[dict],
    tfvars_path: str,
    existing_index_id: str | None = None,
) -> tuple[int, int, list[str], dict[str, str]]:
    """Создаёт единый индекс. Если existing_index_id задан — пересоздаёт (сначала создаёт новый, потом удаляет старый)."""
    index_name = UNIFIED_INDEX_NAME

    if existing_index_id:
        try:
            old_index = sdk.search_indexes.get(search_index_id=existing_index_id)
            index_name = getattr(old_index, 'name', UNIFIED_INDEX_NAME) or UNIFIED_INDEX_NAME
        except Exception as exc:
            print(f"  Предупреждение: не удалось получить старый индекс: {exc}", file=sys.stderr)

    uploaded, upload_success, upload_failed, upload_errors = upload_files(sdk, files)
    if not uploaded:
        return 0, upload_failed, upload_errors, {}

    try:
        new_id = create_search_index(sdk, index_name, uploaded)
        update_vector_store_ids_in_tfvars(tfvars_path, new_id)
        index_ids = {"unified": new_id}
    except Exception as exc:
        print(f"  Ошибка при создании индекса: {exc}", file=sys.stderr)
        index_ids = {}
        return upload_success, upload_failed, upload_errors, index_ids

    # Удаляем старый индекс только после успешного создания нового
    if existing_index_id:
        try:
            old_index = sdk.search_indexes.get(search_index_id=existing_index_id)
            print(f"  Удаление старого индекса {existing_index_id}...")
            old_index.delete()
            print(f"  Старый индекс удалён")
        except Exception as exc:
            print(f"  Предупреждение: не удалось удалить старый индекс: {exc}", file=sys.stderr)

    return upload_success, upload_failed, upload_errors, index_ids


def main():
    parser = argparse.ArgumentParser(
        description="Загрузка документов в поисковый индекс Yandex AI Studio",
        add_help=False,
    )
    parser.add_argument("-h", "--help", action="help", help="Показать справку")
    parser.add_argument(
        "--docs-dir",
        default=None,
        help="Путь к каталогу с документами (по умолчанию: ../docs относительно скрипта)",
    )
    args = parser.parse_args()

    script_dir = os.path.dirname(os.path.abspath(__file__))

    if args.docs_dir:
        docs_dir = os.path.abspath(args.docs_dir)
    else:
        docs_dir = os.path.join(script_dir, "..", "docs")

    if not os.path.isdir(docs_dir):
        print(f"Ошибка: каталог с документами не найден: {docs_dir}", file=sys.stderr)
        sys.exit(1)

    tfvars_path = os.path.join(script_dir, "..", "terraform.tfvars")

    auth = get_auth()
    folder_id = get_folder_id(tfvars_path)
    sdk = AIStudio(auth=auth, folder_id=folder_id)

    print(f"Сканирование каталога: {docs_dir}")
    files = collect_files(docs_dir)

    if not files:
        print("Файлы для загрузки не найдены.")
        sys.exit(0)

    print(f"Найдено файлов: {len(files)}")

    existing_index_id = _read_var_from_tfvars(tfvars_path, "vector_store_ids")

    if existing_index_id:
        print(f"\nНайден vector_store_ids в terraform.tfvars: {existing_index_id}")
        try:
            sdk.search_indexes.get(search_index_id=existing_index_id)
            print(f"Индекс существует — пересоздаём с актуальными файлами")
            success, failed, errors, index_ids = create_or_recreate_index(
                sdk, files, tfvars_path, existing_index_id=existing_index_id,
            )
        except Exception:
            print(f"  Индекс {existing_index_id} не найден — создаём новый")
            success, failed, errors, index_ids = create_or_recreate_index(
                sdk, files, tfvars_path,
            )
    else:
        print(f"\nРежим: создание единого индекса «{UNIFIED_INDEX_NAME}»")
        success, failed, errors, index_ids = create_or_recreate_index(
            sdk, files, tfvars_path,
        )

    print("\n" + "=" * 50)
    print("ИТОГИ ЗАГРУЗКИ")
    print("=" * 50)
    print(f"Всего файлов : {len(files)}")
    print(f"Успешно      : {success}")
    print(f"Ошибки       : {failed}")
    print("\nПоисковые индексы:")
    for section, idx_id in index_ids.items():
        print(f"  {section}: {idx_id}")

    if errors:
        print("\nОшибки:")
        for err in errors:
            print(f"  - {err}")

    if failed > 0 and success == 0:
        sys.exit(1)


if __name__ == "__main__":
    main()
