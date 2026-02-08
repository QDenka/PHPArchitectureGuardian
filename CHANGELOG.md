# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

### Added
- `JsonReporter` for machine-readable CI/CD output
- `JUnitReporter` for GitHub Actions, GitLab CI, and Jenkins integration
- `--format` CLI option (`console`, `json`, `junit`)
- GitHub Actions CI workflow (tests on PHP 8.1–8.4, PHPStan, code style)
- `phpunit.xml.dist` configuration
- `phpstan.neon.dist` configuration
- `.php-cs-fixer.dist.php` configuration
- `.editorconfig` for consistent code style
- `CHANGELOG.md`

### Fixed
- Binary path in `composer.json` now correctly points to `bin/php-architecture-guardian`
- Extended Symfony compatibility to `^7.0`
- Extended PHPUnit compatibility to `^10.0|^11.0`
- Extended PHPStan compatibility to `^2.0`
- Minimum PHP version bumped to `^8.1` (required for `match` expression)

### Changed
- Moved CLI entrypoint from project root to `bin/` directory
- Improved `.gitignore` with common IDE and cache exclusions

## [0.1.0] - Initial Release

### Added
- Domain-Driven Design (DDD) analyzer with Domain, Application, Infrastructure layer rules
- Clean Architecture analyzer with Entity, UseCase, Controller rules
- Hexagonal Architecture analyzer (Ports & Adapters)
- Custom architecture rules support (naming conventions, dependency constraints)
- Console reporter with color-coded severity levels
- Configurable analysis via `.architecture-guardian.php`
- Symfony Finder-based file scanning
