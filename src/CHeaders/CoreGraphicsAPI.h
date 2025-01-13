#ifndef COREGRAPHICS_API_H
#define COREGRAPHICS_API_H

typedef struct CGPoint {
    double x;
    double y;
} CGPoint;

typedef void* CGEventRef;
typedef unsigned int CGEventType;
typedef unsigned int CGMouseButton;
typedef void* CGEventSourceRef;
typedef unsigned short CGKeyCode;

CGEventRef CGEventCreateMouseEvent(CGEventSourceRef source, CGEventType mouseType, CGPoint mouseCursorPosition, CGMouseButton mouseButton);
void CGDisplayMoveCursorToPoint(int display, CGPoint point);
void CGEventPost(int tap, CGEventRef event);
CGEventRef CGEventCreate(int source);
CGEventRef CGEventCreateKeyboardEvent(CGEventSourceRef source, CGKeyCode virtualKey, int keyDown);
CGPoint CGEventGetLocation(CGEventRef event);
int CGEventSourceKeyState(CGEventSourceRef source, CGKeyCode keyCode);

#endif // COREGRAPHICS_API_H
