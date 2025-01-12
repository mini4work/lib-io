# Mini4Work Lib IO

![PHP](https://img.shields.io/badge/PHP-^8.3-%23777BB4)

### A PHP package for mouse and keyboard manipulation using FFI.

> ⚠️ On first run, the system may request permission to control the keyboard or mouse. This is normal behavior, as these operations require special access for security reasons.

## Requirements

- **PHP 8.3** or higher
- **PHP FFI extension** (usually enabled by default)

### Enabling PHP FFI Extension

- **Windows**: Uncomment the following line in your `php.ini` file to enable FFI:
  ```ini
  extension=php_ffi.dll
  ```
- **macOS**: If PHP is installed via **Homebrew**, FFI support is enabled by default.
- **Linux**: Check your `php.ini` file to ensure FFI is enabled.

## Supported Platforms

| **OS**         | **PHP Version** |
|-----------------|-----------------|
| macOS Sequoia   | 8.3, 8.4        |
| Windows 11      | 8.4             |

## Installation

```bash
composer require mini4work/lib-io
```

## Features

### Mouse Control
| **OS**      | **Move** | **Click** | **GetPosition** |
|-------------|----------|-----------|-----------------|
| macOS       | ✅        | ✅         | ✅               |
| Linux       | In progress | In progress | In progress   |
| Windows     | ✅        | ✅         | ✅               |

### Keyboard Control
| **OS**      | **DownKey** | **UpKey** | **PressKey** | **IsKeyPressed** |
|-------------|-------------|-----------|--------------|------------------|
| macOS       | ✅           | ✅         | ✅            | ✅                |
| Linux       | In progress  | In progress| In progress  | In progress      |
| Windows     | ✅           | ✅         | ✅            | ✅                |

## Usage

### Mouse

Available methods:

```php
use M4W\LibIO\Enums\MouseButton;
use M4W\LibIO\OSDetector;

$mouse = OSDetector::getMouseInstance();

$mouse->move(100, 200);

$mouse->click(MouseButton::Left, 100, 200);
$mouse->click(MouseButton::Right, 200, 200);

$position = $mouse->getPosition();
echo json_encode($position); // {"x":768.359375,"y":756.7109375}
```

### Keyboard

Available methods:

```php
use M4W\LibIO\Enums\KeyCode;
use M4W\LibIO\OSDetector;

$keyboard = OSDetector::getKeyboardInstance();

$keyboard->down(KeyCode::Space);
$keyboard->up(KeyCode::Space);

$keyboard->press(KeyCode::Backspace);

$isF4Pressed = $keyboard->isKeyPressed(KeyCode::F4); // boolean
```

Pressed key loop:

```php
$isPressedState = [];

while (true) {
    foreach (KeyCode::cases() as $keyCode) {
        if (!array_key_exists($keyCode->name, $isPressedState)) {
            $isPressedState[$keyCode->name] = $keyboard->isKeyPressed($keyCode);
        }

        $isPressed = $keyboard->isKeyPressed($keyCode);
        if ($isPressedState[$keyCode->name] !== $isPressed) {
            $isPressedState[$keyCode->name] = $isPressed;
            echo ($isPressedState[$keyCode->name] ? 'Pressed key ' . $keyCode->name : 'Released key ' . $keyCode->name) . PHP_EOL;
        }
    }
    usleep(10000);
}
```

## License
Mini4Work is distributed under [The MIT license](https://opensource.org/licenses/MIT).
