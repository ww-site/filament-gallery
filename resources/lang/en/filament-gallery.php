<?php

declare(strict_types=1);

return [
    'navigation' => [
        'group' => 'Media',
        'gallery_content' => 'Gallery Content',
        'media_sources' => 'Media Sources',
    ],
    'resource' => [
        'media_source' => [
            'label' => 'media source',
            'plural_label' => 'media sources',
            'sections' => [
                'main' => 'Main',
            ],
            'fields' => [
                'name' => 'Name',
                'slug' => 'Slug',
                'description' => 'Description',
                'is_active' => 'Active',
                'settings_count' => 'Settings',
                'updated_at' => 'Updated',
            ],
            'settings' => [
                'title' => 'Settings',
                'fields' => [
                    'key' => 'Key',
                    'value' => 'Value',
                    'sort_order' => 'Sort order',
                ],
            ],
        ],
    ],
    'gallery_page' => [
        'source_label' => 'Media Source',
        'disk' => 'Disk',
        'directory' => 'Directory',
        'files_count' => 'Files',
        'refresh' => 'Refresh',
        'delete_selected' => 'Delete Selected',
        'empty' => 'There are no files for the selected source yet.',
        'file_without_preview' => 'File without preview',
        'upload_title' => 'File Upload',
        'upload_help' => 'Images and MP4 videos are supported.',
        'upload_submit' => 'Upload',
        'upload_footer' => 'Images and MP4 up to :size MB.',
        'notifications' => [
            'source_not_found' => 'Media source not found',
            'files_uploaded' => 'Files uploaded',
            'select_files_first' => 'Select files first',
            'selected_files_deleted' => 'Selected files deleted',
        ],
    ],
    'picker' => [
        'upload_action' => 'Upload image',
        'upload_heading' => 'Image upload',
        'select_action' => 'Choose from gallery',
        'select_heading' => 'Choose image from gallery',
        'clear_action' => 'Clear',
        'upload_field' => 'Image',
        'gallery_label' => 'Images',
        'preview_empty' => 'No image selected',
        'path_empty' => 'No file selected yet.',
        'submit_upload' => 'Upload',
        'submit_select' => 'Choose',
        'notifications' => [
            'source_not_found' => 'Media source not found',
            'file_not_uploaded' => 'File was not uploaded',
            'image_uploaded' => 'Image uploaded',
            'select_image_first' => 'Select an image first',
            'image_selected' => 'Image selected',
        ],
    ],
];
