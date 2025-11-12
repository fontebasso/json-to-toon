# Contributing to json-to-toon

Thanks for your interest in improving **json-to-toon**!

This library is simple by design — contributions that keep it clear, well-tested and easy to use are always welcome.

---

## How to Contribute

1. **Fork** this repository on GitHub
2. **Clone** your fork:
   ```bash
   git clone https://github.com/<your-username>/json-to-toon.git
   cd json-to-toon
   ```
3. Install dependencies:
  ```bash```
   composer install
   ```
4. Run tests to make sure everything works:
    ```bash
    composer test
    ```

## Before Sending a Pull Request

- Keep your code simple and consistent with the existing style.
- Add tests for any new feature or fix.
- Make sure all tests pass:
  ```bash
  vendor/bin/phpunit
  ```
- Write a short, clear commit message in English.
  > Example: `fix: handle empty JSON string correctly`

## Code Style

Follow [PSR-12](https://www.php-fig.org/psr/psr-12/).

Avoid adding dependencies — this library should stay lightweight.

## Need Help?

If you’re unsure about something, open an issue before coding — we’ll help guide you.

## License

By contributing, you agree that your code will be released under the MIT License.
