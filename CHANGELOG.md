# Changelog

All notable changes to `ww-site/filament-gallery` will be documented in this file.

The format follows Keep a Changelog principles, and the package versioning should follow SemVer where possible.

## [Unreleased]

### Added

- Added `media_source_image()` helper draft for resizing, converting and caching media source images.
- Added image cache configuration defaults for disk, directory, quality, format and crop mode.

## [0.1.2] - 2026-04-19

### Added

- Added `media_source_url()` helper for generating public URLs from media source slug and stored media path.
- Added `WwGallery\FilamentGallery\Support\MediaSourceUrl` service behind the helper.

## [0.1.1]

### Added

- Added tests for the package.
