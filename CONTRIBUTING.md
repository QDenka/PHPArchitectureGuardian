# Contributing to PHPArchitectureGuardian

Thank you for considering contributing to PHPArchitectureGuardian! This document provides guidelines and instructions to help you contribute effectively.

## Code of Conduct

By participating in this project, you agree to abide by our Code of Conduct. Please be respectful and considerate of others when participating in discussions, submitting code, or engaging in any project activities.

## How to Contribute

There are many ways to contribute to PHPArchitectureGuardian:

1. **Report bugs**: If you find a bug, please create an issue with clear steps to reproduce it.
2. **Suggest features**: Have an idea for a new feature? Create an issue with your suggestion.
3. **Improve documentation**: Help improve the project's documentation by fixing typos, clarifying explanations, or adding examples.
4. **Submit pull requests**: Implement new features or fix bugs by submitting pull requests.

## Development Workflow

1. **Fork the repository**: Create your own fork of the project on GitHub.
2. **Clone your fork**: Clone your fork to your local machine.
   ```
   git clone https://github.com/YOUR-USERNAME/php-architecture-guardian.git
   ```
3. **Set up the project**:
   ```
   cd php-architecture-guardian
   composer install
   ```
4. **Create a branch**: Create a branch for your changes.
   ```
   git checkout -b your-branch-name
   ```
5. **Make your changes**: Implement your feature or bug fix.
6. **Write tests**: Add tests for your changes, ensuring all tests pass.
7. **Run code quality tools**:
   ```
   composer check-all
   ```
8. **Commit your changes**: Commit your changes with a clear and descriptive commit message.
9. **Push to your fork**: Push your changes to your GitHub fork.
10. **Create a pull request**: Submit a pull request from your fork to the main repository.

## Pull Request Guidelines

- Follow the project's coding standards (PSR-12).
- Write clear commit messages that explain the "what" and "why" of your changes.
- Include tests for new features or bug fixes.
- Update documentation as needed.
- Make sure all tests pass before submitting.
- Keep pull requests focused on a single topic or fix.

## Adding New Rules or Analyzers

When adding new rules or analyzers:

1. Create a new class implementing the appropriate interface:
    - For rules: `PHPArchitectureGuardian\Core\RuleInterface`
    - For analyzers: `PHPArchitectureGuardian\Core\AnalyzerInterface`

2. Place the class in the appropriate directory:
    - Rules go in `src/Rules/YourArchitectureStyle/`
    - Analyzers go in `src/Analyzer/`

3. Write comprehensive tests for your new rule or analyzer.

4. Update the documentation to explain the new rule or analyzer.

## Testing

All contributions should include tests. We use PHPUnit for testing:

```
composer test
```

## Static Analysis

We use PHPStan for static analysis:

```
composer phpstan
```

## Code Style

We follow PSR-12 coding style. You can check and fix code style issues with:

```
composer cs-fix
```

## Documentation

Clear and comprehensive documentation is essential. If you add a new feature, please update the documentation accordingly.

## Questions?

If you have any questions or need help, feel free to create an issue labeled "question" or contact the maintainers directly.

Thank you for contributing to PHPArchitectureGuardian!
