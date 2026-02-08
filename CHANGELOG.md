# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

## [2.0.0] - 2026-02-08

### Added
- `JsonReporter` for machine-readable CI/CD output
- `JUnitReporter` for GitHub Actions, GitLab CI, and Jenkins integration
- `--format` CLI option (`console`, `json`, `junit`)
- GitHub Actions CI workflow (tests on PHP 8.1–8.4, PHPStan, code style)
- `phpunit.xml.dist` configuration
- `phpstan.neon.dist` configuration (level 5, PHPStan 2.x compatible)
- `.php-cs-fixer.dist.php` configuration
- `.editorconfig` for consistent code style

### Fixed
- Binary path in `composer.json` now correctly points to `bin/php-architecture-guardian`
- **Namespace matching** — `namespaceMatches()` now uses segment-based matching instead of prefix-only or substring matching, correctly handling vendor-prefixed namespaces like `App\Domain\Entity`
- **Class name extraction** — regex no longer matches `class` keyword in comments
- **Analyzer configuration** — `ArchitectureAnalyzer::configure()` now passes full config to each rule instead of per-rule-name lookup
- 9 PHPStan level 5 errors (unused closure variables, undefined variables, redundant null coalescing)
- PHP-CS-Fixer compliance for all 30 source files
- Removed deprecated `checkMissingIterableValueType` from PHPStan config (incompatible with PHPStan 2.x)

### Changed
- **BREAKING**: Minimum PHP version bumped from `^8.0` to `^8.1` (required for `match` expression)
- **BREAKING**: Default `application_namespaces` in DDD rules no longer includes `App` (conflicted with common vendor prefix `App\`)
- Extended Symfony compatibility to `^7.0`
- Extended PHPUnit compatibility to `^10.0|^11.0`
- Extended PHPStan compatibility to `^2.0`
- Moved CLI entrypoint from project root to `bin/` directory
- Improved `.gitignore` with common IDE and cache exclusions
- All source files formatted per PSR-12 with ordered imports, trailing commas, and `not_operator_with_successor_space`

## [0.1.0] - Initial Release

### Added
- Domain-Driven Design (DDD) analyzer with Domain, Application, Infrastructure layer rules
- Clean Architecture analyzer with Entity, UseCase, Controller rules
- Hexagonal Architecture analyzer (Ports & Adapters)
- Custom architecture rules support (naming conventions, dependency constraints)
- Console reporter with color-coded severity levels
- Configurable analysis via `.architecture-guardian.php`
- Symfony Finder-based file scanning
