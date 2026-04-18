<?php

declare(strict_types=1);

return [
    'navigation' => [
        'group' => 'Медиа',
        'gallery_content' => 'Контент галереи',
        'media_sources' => 'Источники медиа',
    ],
    'resource' => [
        'media_source' => [
            'label' => 'источник медиа',
            'plural_label' => 'источники медиа',
            'sections' => [
                'main' => 'Основное',
            ],
            'fields' => [
                'name' => 'Название',
                'slug' => 'Slug',
                'description' => 'Описание',
                'is_active' => 'Активен',
                'settings_count' => 'Настроек',
                'updated_at' => 'Обновлён',
            ],
            'settings' => [
                'title' => 'Настройки',
                'fields' => [
                    'key' => 'Ключ',
                    'value' => 'Значение',
                    'sort_order' => 'Сортировка',
                ],
            ],
        ],
    ],
    'gallery_page' => [
        'source_label' => 'Источник медиа',
        'disk' => 'Диск',
        'directory' => 'Директория',
        'files_count' => 'Файлов',
        'refresh' => 'Обновить',
        'delete_selected' => 'Удалить выбранные',
        'empty' => 'Для выбранного источника пока нет файлов.',
        'file_without_preview' => 'Файл без превью',
        'upload_title' => 'Загрузка файлов',
        'upload_help' => 'Поддерживаются изображения и видео MP4.',
        'upload_submit' => 'Загрузить',
        'upload_footer' => 'Изображения и MP4 до :size МБ.',
        'notifications' => [
            'source_not_found' => 'Источник медиа не найден',
            'files_uploaded' => 'Файлы загружены',
            'select_files_first' => 'Сначала выберите файлы',
            'selected_files_deleted' => 'Выбранные файлы удалены',
        ],
    ],
    'picker' => [
        'upload_action' => 'Загрузить изображение',
        'upload_heading' => 'Загрузка изображения',
        'select_action' => 'Выбрать из галереи',
        'select_heading' => 'Выбор изображения из галереи',
        'clear_action' => 'Очистить',
        'upload_field' => 'Изображение',
        'gallery_label' => 'Изображения',
        'preview_empty' => 'Изображение не выбрано',
        'path_empty' => 'Файл пока не выбран.',
        'submit_upload' => 'Загрузить',
        'submit_select' => 'Выбрать',
        'notifications' => [
            'source_not_found' => 'Источник медиа не найден',
            'file_not_uploaded' => 'Файл не был загружен',
            'image_uploaded' => 'Изображение загружено',
            'select_image_first' => 'Сначала выберите изображение',
            'image_selected' => 'Изображение выбрано',
        ],
    ],
];
