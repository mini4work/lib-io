typedef unsigned int DWORD;
typedef int LONG;
typedef unsigned int UINT;
typedef unsigned long long ULONG_PTR; // 64 bit!!
typedef unsigned short WORD;
typedef unsigned short SHORT;

typedef struct {
    LONG dx;
    LONG dy;
    DWORD mouseData;
    DWORD dwFlags;
    DWORD time;
    ULONG_PTR dwExtraInfo;
} MOUSEINPUT;

typedef struct {
    WORD wVk;            // 2 bytes
    WORD wScan;          // 2 bytes
    DWORD dwFlags;       // 4 bytes
    DWORD time;          // 4 bytes
    ULONG_PTR dwExtraInfo; // 8 bytes
} KEYBDINPUT;

typedef struct {
    DWORD type;
    union {
        MOUSEINPUT mi;
        KEYBDINPUT ki;
        DWORD padding[2]; // padding if needed
    };
} INPUT;

typedef struct {
    LONG x;
    LONG y;
} POINT;

UINT SendInput(UINT nInputs, INPUT* pInputs, int cbSize);
int GetCursorPos(POINT* lpPoint);
int SetCursorPos(int X, int Y);
int GetSystemMetrics(int nIndex);
SHORT GetAsyncKeyState(int vKey);