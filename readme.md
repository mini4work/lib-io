# Mini4Work Lib IO

![PHP](https://img.shields.io/badge/PHP-^8.3-%23777BB4)

Package for mouse and keyboard manipulation.
On run can ask for systems permission! Its normal behavior, becouse access to keyboard state or mouse click can`t be secure operation!

## Abilities

### Mouse
| OS      | Move    | Click   | GetPosition |
|---------|---------|---------|-------------|
| MacOS   | +       | +       | +           |
| Linux   | in work | in work | in work     |
| Windows | in work | in work | in work     |

### Keyboard
| OS      | DownKey | UpKey   | PressKey | IsKeyPressed |
|---------|---------|---------|----------|--------------|
| MacOS   | +       | +       | +        | +            |
| Linux   | in work | in work | in work  | in work      |
| Windows | in work | in work | in work  | in work      |

## HowTo

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
echo json_encode($position) // {"x":768.359375,"y":756.7109375}
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
            echo ($isPressedState[$keyCode->name]?'Pressed key '.$keyCode->name:'Released key '.$keyCode->name).PHP_EOL;
        }
    }
}
```

## License
Mini4Work is distributed by The [MIT license](https://opensource.org/licenses/MIT).
