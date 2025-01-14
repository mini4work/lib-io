<?php

namespace M4W\LibIO\Enums;

use M4W\LibIO\OSDetector;

enum KeyCode
{
    case A;
    case B;
    case C;
    case D;
    case E;
    case F;
    case G;
    case H;
    case I;
    case J;
    case K;
    case L;
    case M;
    case N;
    case O;
    case P;
    case Q;
    case R;
    case S;
    case T;
    case U;
    case V;
    case W;
    case X;
    case Y;
    case Z;
    case One;
    case Two;
    case Three;
    case Four;
    case Five;
    case Six;
    case Seven;
    case Eight;
    case Nine;
    case Zero;
    case LeftBracket;  // [
    case RightBracket; // ]
    case Backslash;    // \
    case Semicolon;    // ;
    case Apostrophe;   // '
    case Comma;        // ,
    case Period;       // .
    case Slash;        // /
    case Equal;        // =
    case Minus;        // -
    case Space;
    case Enter;
    case Shift;
    case Control;
    case Option;
    case Command; // Win in Windows
    case CapsLock;
    case Tab;
    case Backspace;
    case Delete;
    case LeftArrow;
    case RightArrow;
    case UpArrow;
    case DownArrow;
    case Tilde;
    case Escape;
    case F1;
    case F2;
    case F3;
    case F4;
    case F5;
    case F6;
    case F7;
    case F8;
    case F9;
    case F10;
    case F11;
    case F12;

    case LMB;
    case RMB;
    case MMB;

    public function getCode(): int
    {
        $os = OSDetector::detect();

        return match ($os) {
            OS::Mac => $this->getMacCode(),
            OS::Windows => $this->getWindowsCode(),
            OS::LinuxX11 => $this->getLinuxCode(),
            default => throw new \RuntimeException("Unsupported operating system: " . $os->getName())
        };
    }

    /**
     * Returns a KeyCode enum corresponding to the given platform-specific key code.
     *
     * @param int $code The platform-specific key code.
     * @return ?self The corresponding KeyCode, or null if not found.
     */
    public static function fromCode(int $code): ?self
    {
        $os = OSDetector::detect();

        $mapping = match ($os) {
            OS::Mac => self::macMapping(),
            OS::Windows, OS::LinuxX11 => self::windowsLinuxMapping(),
            default => []
        };

        return $mapping[$code] ?? null;
    }

    /**
     * Returns the key code mapping for macOS.
     *
     * @return array<int, self>
     */
    private static function macMapping(): array
    {
        return [
            0x00 => self::A,
            0x0B => self::B,
            0x08 => self::C,
            0x02 => self::D,
            0x0E => self::E,
            0x03 => self::F,
            0x05 => self::G,
            0x04 => self::H,
            0x22 => self::I,
            0x26 => self::J,
            0x28 => self::K,
            0x25 => self::L,
            0x2E => self::M,
            0x2D => self::N,
            0x1F => self::O,
            0x23 => self::P,
            0x0C => self::Q,
            0x0F => self::R,
            0x01 => self::S,
            0x11 => self::T,
            0x20 => self::U,
            0x09 => self::V,
            0x0D => self::W,
            0x07 => self::X,
            0x10 => self::Y,
            0x06 => self::Z,
            0x12 => self::One,
            0x13 => self::Two,
            0x14 => self::Three,
            0x15 => self::Four,
            0x17 => self::Five,
            0x16 => self::Six,
            0x1A => self::Seven,
            0x1C => self::Eight,
            0x19 => self::Nine,
            0x1D => self::Zero,
            0x21 => self::LeftBracket,
            0x1E => self::RightBracket,
            0x2A => self::Backslash,
            0x29 => self::Semicolon,
            0x27 => self::Apostrophe,
            0x2B => self::Comma,
            0x2F => self::Period,
            0x2C => self::Slash,
            0x18 => self::Equal,
            0x1B => self::Minus,
            0x31 => self::Space,
            0x24 => self::Enter,
            0x38 => self::Shift,
            0x3B => self::Control,
            0x3A => self::Option,
            0x37 => self::Command,
            0x39 => self::CapsLock,
            0x30 => self::Tab,
            0x33 => self::Backspace,
            0x75 => self::Delete,
            0x7B => self::LeftArrow,
            0x7C => self::RightArrow,
            0x7E => self::UpArrow,
            0x7D => self::DownArrow,
            0x32 => self::Tilde,           // ~
            0x35 => self::Escape,          // Escape
            0x7A => self::F1,
            0x78 => self::F2,
            0x63 => self::F3,
            0x76 => self::F4,
            0x60 => self::F5,
            0x61 => self::F6,
            0x62 => self::F7,
            0x64 => self::F8,
            0x65 => self::F9,
            0x6D => self::F10,
            0x67 => self::F11,
            0x6F => self::F12,
        ];
    }

    /**
     * Returns the key code mapping for Windows and Linux.
     *
     * @return array<int, self>
     */
    private static function windowsLinuxMapping(): array
    {
        return [
            0x41 => self::A,
            0x42 => self::B,
            0x43 => self::C,
            0x44 => self::D,
            0x45 => self::E,
            0x46 => self::F,
            0x47 => self::G,
            0x48 => self::H,
            0x49 => self::I,
            0x4A => self::J,
            0x4B => self::K,
            0x4C => self::L,
            0x4D => self::M,
            0x4E => self::N,
            0x4F => self::O,
            0x50 => self::P,
            0x51 => self::Q,
            0x52 => self::R,
            0x53 => self::S,
            0x54 => self::T,
            0x55 => self::U,
            0x56 => self::V,
            0x57 => self::W,
            0x58 => self::X,
            0x59 => self::Y,
            0x5A => self::Z,
            0x31 => self::One,
            0x32 => self::Two,
            0x33 => self::Three,
            0x34 => self::Four,
            0x35 => self::Five,
            0x36 => self::Six,
            0x37 => self::Seven,
            0x38 => self::Eight,
            0x39 => self::Nine,
            0x30 => self::Zero,
            0xDB => self::LeftBracket,
            0xDD => self::RightBracket,
            0xDC => self::Backslash,
            0xBA => self::Semicolon,
            0xDE => self::Apostrophe,
            0xBC => self::Comma,
            0xBE => self::Period,
            0xBF => self::Slash,
            0xBB => self::Equal,
            0xBD => self::Minus,
            0x20 => self::Space,
            0x0D => self::Enter,
            0x10 => self::Shift,
            0x11 => self::Control,
            0x12 => self::Option, // Alt key
            0x5B => self::Command, // Windows key
            0x14 => self::CapsLock,
            0x09 => self::Tab,
            0x08 => self::Backspace,
            0x2E => self::Delete,
            0x25 => self::LeftArrow,
            0x27 => self::RightArrow,
            0x26 => self::UpArrow,
            0x28 => self::DownArrow,
            0xC0 => self::Tilde,           // ~
            0x1B => self::Escape,          // Escape
            0x70 => self::F1,
            0x71 => self::F2,
            0x72 => self::F3,
            0x73 => self::F4,
            0x74 => self::F5,
            0x75 => self::F6,
            0x76 => self::F7,
            0x77 => self::F8,
            0x78 => self::F9,
            0x79 => self::F10,
            0x7A => self::F11,
            0x7B => self::F12,
        ];
    }

    private function getMacCode(): int
    {
        return match ($this) {
            self::A => 0x00,
            self::B => 0x0B,
            self::C => 0x08,
            self::D => 0x02,
            self::E => 0x0E,
            self::F => 0x03,
            self::G => 0x05,
            self::H => 0x04,
            self::I => 0x22,
            self::J => 0x26,
            self::K => 0x28,
            self::L => 0x25,
            self::M => 0x2E,
            self::N => 0x2D,
            self::O => 0x1F,
            self::P => 0x23,
            self::Q => 0x0C,
            self::R => 0x0F,
            self::S => 0x01,
            self::T => 0x11,
            self::U => 0x20,
            self::V => 0x09,
            self::W => 0x0D,
            self::X => 0x07,
            self::Y => 0x10,
            self::Z => 0x06,
            self::One => 0x12,
            self::Two => 0x13,
            self::Three => 0x14,
            self::Four => 0x15,
            self::Five => 0x17,
            self::Six => 0x16,
            self::Seven => 0x1A,
            self::Eight => 0x1C,
            self::Nine => 0x19,
            self::Zero => 0x1D,
            self::LeftBracket => 0x21,
            self::RightBracket => 0x1E,
            self::Backslash => 0x2A,
            self::Semicolon => 0x29,
            self::Apostrophe => 0x27,
            self::Comma => 0x2B,
            self::Period => 0x2F,
            self::Slash => 0x2C,
            self::Equal => 0x18,
            self::Minus => 0x1B,
            self::Space => 0x31,
            self::Enter => 0x24,
            self::Shift => 0x38,
            self::Control => 0x3B,
            self::Option => 0x3A,
            self::Command => 0x37,
            self::CapsLock => 0x39,
            self::Tab => 0x30,
            self::Backspace => 0x33,
            self::Delete => 0x75,
            self::LeftArrow => 0x7B,
            self::RightArrow => 0x7C,
            self::UpArrow => 0x7E,
            self::DownArrow => 0x7D,
            self::Tilde => 0x32,
            self::Escape => 0x35,
            self::F1 => 0x7A,
            self::F2 => 0x78,
            self::F3 => 0x63,
            self::F4 => 0x76,
            self::F5 => 0x60,
            self::F6 => 0x61,
            self::F7 => 0x62,
            self::F8 => 0x64,
            self::F9 => 0x65,
            self::F10 => 0x6D,
            self::F11 => 0x67,
            self::F12 => 0x6F,
        };
    }

    private function getWindowsCode(): int
    {
        return match ($this) {
            self::A => 0x41,
            self::B => 0x42,
            self::C => 0x43,
            self::D => 0x44,
            self::E => 0x45,
            self::F => 0x46,
            self::G => 0x47,
            self::H => 0x48,
            self::I => 0x49,
            self::J => 0x4A,
            self::K => 0x4B,
            self::L => 0x4C,
            self::M => 0x4D,
            self::N => 0x4E,
            self::O => 0x4F,
            self::P => 0x50,
            self::Q => 0x51,
            self::R => 0x52,
            self::S => 0x53,
            self::T => 0x54,
            self::U => 0x55,
            self::V => 0x56,
            self::W => 0x57,
            self::X => 0x58,
            self::Y => 0x59,
            self::Z => 0x5A,
            self::One => 0x31,
            self::Two => 0x32,
            self::Three => 0x33,
            self::Four => 0x34,
            self::Five => 0x35,
            self::Six => 0x36,
            self::Seven => 0x37,
            self::Eight => 0x38,
            self::Nine => 0x39,
            self::Zero => 0x30,
            self::LeftBracket => 0xDB,
            self::RightBracket => 0xDD,
            self::Backslash => 0xDC,
            self::Semicolon => 0xBA,
            self::Apostrophe => 0xDE,
            self::Comma => 0xBC, // ,
            self::Period => 0xBE, // .
            self::Slash => 0xBF, // /
            self::Equal => 0xBB, // =
            self::Minus => 0xBD, // -
            self::Space => 0x20,
            self::Enter => 0x0D,
            self::Shift => 0x10,
            self::Control => 0x11,
            self::Option => 0x12,
            self::Command => 0x5B, // Windows key
            self::CapsLock => 0x14,
            self::Tab => 0x09,
            self::Backspace => 0x08,
            self::Delete => 0x2E,
            self::LeftArrow => 0x25,
            self::RightArrow => 0x27,
            self::UpArrow => 0x26,
            self::DownArrow => 0x28,
            self::Tilde => 0xC0, // `
            self::Escape => 0x1B,
            self::F1 => 0x70,
            self::F2 => 0x71,
            self::F3 => 0x72,
            self::F4 => 0x73,
            self::F5 => 0x74,
            self::F6 => 0x75,
            self::F7 => 0x76,
            self::F8 => 0x77,
            self::F9 => 0x78,
            self::F10 => 0x79,
            self::F11 => 0x7A,
            self::F12 => 0x7B,
            self::LMB => 0x01,
            self::RMB => 0x02,
            self::MMB => 0x04,
        };
    }

    private function getLinuxCode(): int
    {
        return $this->getWindowsCode();
    }
}
