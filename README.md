# PHPArchitectureGuardian

[![Latest Version on Packagist](https://img.shields.io/packagist/v/qdenka/php-architecture-guardian.svg?style=flat-square)](https://packagist.org/packages/qdenka/php-architecture-guardian)
[![GitHub Tests Action Status](https://img.shields.io/github/workflow/status/qdenka/php-architecture-guardian/run-tests?label=tests)](https://github.com/qdenka/php-architecture-guardian/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/workflow/status/qdenka/php-architecture-guardian/Check%20&%20fix%20styling?label=code%20style)](https://github.com/qdenka/php-architecture-guardian/actions?query=workflow%3A"Check+%26+fix+styling"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/qdenka/php-architecture-guardian.svg?style=flat-square)](https://packagist.org/packages/qdenka/php-architecture-guardian)

**PHPArchitectureGuardian** is a powerful static analysis tool that helps you enforce architectural principles in your PHP projects. It ensures your code follows architectural patterns like Domain-Driven Design (DDD), Clean Architecture, or Hexagonal Architecture (Ports & Adapters).

## Features

- 🏛️ Verify adherence to various architectural patterns:
    - **Domain-Driven Design (DDD)**
    - **Clean Architecture**
    - **Hexagonal Architecture (Ports & Adapters)**
    - **Custom architecture rules**

- 🔍 Enforce architectural constraints:
    - Prevent improper dependencies between layers
    - Ensure correct implementation of interfaces
    - Validate proper namespace usage
    - Enforce naming conventions

- 🛠️ Highly customizable:
    - Configure namespaces for different architecture layers
    - Create custom rules with specific validation logic
    - Define custom naming conventions
    - Specify custom dependency constraints

- 📊 Comprehensive reporting:
    - Clear violation descriptions
    - Severity levels for different issues
    - Configurable output formats

## Installation

You can install the package via composer:

```bash
composer require --dev qdenka/php-architecture-guardian
```

## Usage

### Basic Usage

Run the tool in your project directory:

```bash
vendor/bin/php-architecture-guardian
```

### Configuration

Create a `.architecture-guardian.php` configuration file in your project root:

```php
<?php

return [
    'analyzers' => [
        'ddd' => [
            'enabled' => true,
            'config' => [
                'domain_namespaces' => ['Domain', 'App\\Domain'],
                'application_namespaces' => ['Application', 'App\\Application'],
                'infrastructure_namespaces' => ['Infrastructure', 'App\\Infrastructure'],
            ],
        ],
        // Additional analyzers and configurations...
    ],
];
```

See the [example configuration](.architecture-guardian.php.example) for a complete example with all options.

### Command-Line Options

```
PHPArchitectureGuardian - A tool for enforcing architectural principles in PHP projects

Usage:
  php-architecture-guardian [options]

Options:
  -c, --config    Path to configuration file (default: .architecture-guardian.php)
  -p, --path      Path to analyze (default: current directory)
  -h, --help      Display this help message
```

## Supported Architectural Patterns

### Domain-Driven Design (DDD)

Enforces the layered architecture of DDD:

- **Domain Layer**: Contains business logic, entities, value objects
- **Application Layer**: Contains application services, commands, queries
- **Infrastructure Layer**: Contains implementations of repositories, external services

Rules ensure:
- Domain layer does not depend on application or infrastructure
- Application layer does not depend on infrastructure (configurable)
- Infrastructure components implement domain interfaces

### Clean Architecture

Enforces the concentric layers of Clean Architecture:

- **Entities Layer**: Core business objects
- **Use Cases Layer**: Application-specific business rules
- **Interface Adapters Layer**: Presenters, controllers, gateways
- **Frameworks & Drivers Layer**: Web, UI, DB, devices, external interfaces

Rules ensure:
- Entities do not depend on outer layers
- Use Cases depend only on Entities
- Interface Adapters depend only on Use Cases and Entities
- Frameworks & Drivers depend on inner layers

### Hexagonal Architecture (Ports & Adapters)

Enforces the separation of:

- **Domain**: Core business logic
- **Ports**: Interfaces defining how domain interacts with outside world
- **Adapters**: Implementations of ports that connect to external systems

Rules ensure:
- Domain does not depend on anything outside
- Ports are interfaces and depend only on domain
- Adapters implement ports and connect to external systems

### Custom Architecture Rules

Create your own architecture rules:

- Define custom naming conventions for different namespaces
- Specify allowed dependencies between namespaces
- Create custom rule classes for complex validation logic

## Contributing

Contributions are welcome! Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
