#include <Arduino.h>
#include <Wire.h>
#include <LiquidCrystal_I2C.h>
#include <Keypad.h>
#include <ESP32Servo.h>


LiquidCrystal_I2C lcd(0x27, 16, 2);


const byte ROWS = 4, COLS = 4;
char keys[ROWS][COLS] = {
  {'1', '2', '3', 'A'},
  {'4', '5', '6', 'B'},
  {'7', '8', '9', 'C'},
  {'*', '0', '#', 'D'}
};

byte rowPins[ROWS] = {13, 14, 27, 26};
byte colPins[COLS] = {25, 33, 32, 23};
Keypad keypad = Keypad(makeKeymap(keys), rowPins, colPins, ROWS, COLS);


Servo barrier;
const int SERVO_PIN   = 18;
const int GATE_CLOSED = 0;
const int GATE_OPEN   = 90;
const unsigned long GATE_OPEN_MS = 5000;  // how long the barrier stays open

enum Category { CAT_NONE, CAT_STUDENT, CAT_STAFF, CAT_VISITOR };

const int NUM_SLOTS = 5;
const Category slotZone[NUM_SLOTS] = { CAT_STUDENT, CAT_STUDENT, CAT_STAFF, CAT_STAFF, CAT_VISITOR };
bool slotTaken[NUM_SLOTS] = { false, false, false, false, false };

const char* const studentIDs[] = { "202401", "202402", "202403", "202404" };
const char* const staffIDs[]   = { "100001", "100002", "100003" };

struct Visitor { const char* code; bool used; };
Visitor visitors[] = { {"900001", false}, {"900002", false}, {"900003", false} };

const int NUM_STUDENTS = sizeof(studentIDs) / sizeof(studentIDs[0]);
const int NUM_STAFF    = sizeof(staffIDs) / sizeof(staffIDs[0]);
const int NUM_VISITORS = sizeof(visitors) / sizeof(visitors[0]);

const int CODE_LENGTH  = 6;
const int MAX_ATTEMPTS = 3;
const unsigned long LOCKOUT_MS = 30000;


enum State { S_CATEGORY, S_CODE, S_GATE };
State state = S_CATEGORY;
Category category = CAT_NONE;
String code = "";
int attempts = 0;
int assignedSlot = -1;
unsigned long gateOpenedAt = 0;

void show(const String& line0, const String& line1 = "") {
  lcd.clear();
  lcd.setCursor(0, 0); lcd.print(line0);
  lcd.setCursor(0, 1); lcd.print(line1);
}

void flushKeys() { while (keypad.getKey() != NO_KEY) {} }

bool inList(const char* const list[], int n, const String& c) {
  for (int i = 0; i < n; i++) if (c == list[i]) return true;
  return false;
}

int findVisitor(const String& c) {
  for (int i = 0; i < NUM_VISITORS; i++)
    if (!visitors[i].used && c == visitors[i].code) return i;
  return -1;
}

int findFreeSlot(Category cat) {
  for (int i = 0; i < NUM_SLOTS; i++)
    if (slotZone[i] == cat && !slotTaken[i]) return i;
  return -1;
}



void showCategoryMenu() { show("Choose:A=Student", "B=Staff C=Visit"); }

void drawDigits() {
  lcd.setCursor(0, 1);
  for (int i = 0; i < CODE_LENGTH; i++) lcd.print(i < (int)code.length() ? code[i] : '_');
  lcd.print("  #OK *Del");
}

void showCodeScreen() {
  lcd.clear();
  lcd.setCursor(0, 0);
  if (category == CAT_STUDENT)    
    lcd.print("Student ID:");
  else if (category == CAT_STAFF) 
    lcd.print("Staff ID:");
  else                            
    lcd.print("Visitor code:");
  drawDigits();
}

void resetToMenu() {
  category = CAT_NONE;
  code = "";
  assignedSlot = -1;
  state = S_CATEGORY;
  flushKeys();
  showCategoryMenu();
}


void handleCategory(char k) {
  if (k == 'A')      category = CAT_STUDENT;
  else if (k == 'B') category = CAT_STAFF;
  else if (k == 'C') category = CAT_VISITOR;
  else if (k == '0') {                       // testing shortcut: free all slots
    for (int i = 0; i < NUM_SLOTS; i++) slotTaken[i] = false;
    for (int i = 0; i < NUM_VISITORS; i++) visitors[i].used = false;
    show("All slots freed", "");
    delay(1200);
    showCategoryMenu();
    return;
  } else return;

  code = "";
  state = S_CODE;
  showCodeScreen();
}

void submitCode() {
  bool valid = false;
  int visitorIdx = -1;

  if (category == CAT_STUDENT)      
    valid = inList(studentIDs, NUM_STUDENTS, code);
  else if (category == CAT_STAFF)   
    valid = inList(staffIDs, NUM_STAFF, code);
  else if (category == CAT_VISITOR) { 
    visitorIdx = findVisitor(code); valid = (visitorIdx >= 0);
   }

  if (!valid) {
    attempts++;
    if (attempts >= MAX_ATTEMPTS) {
      show("Too many tries", "Locked 30 sec");
      delay(LOCKOUT_MS);
      attempts = 0;
      resetToMenu();
      return;
    }
    show("Invalid code", "Tries left: " + String(MAX_ATTEMPTS - attempts));
    delay(1500);
    code = "";
    showCodeScreen();
    return;
  }

  attempts = 0;

  int slot = findFreeSlot(category);
  if (slot < 0) {
    show("No free space", "Try again later");
    delay(2500);
    resetToMenu();
    return;
  }

  if (visitorIdx >= 0) visitors[visitorIdx].used = true;
  slotTaken[slot] = true;
  assignedSlot = slot;

  show("Access granted", "Park at slot " + String(slot + 1));
  barrier.write(GATE_OPEN);
  gateOpenedAt = millis();
  state = S_GATE;
}

void handleCode(char k) {
  if (k == NO_KEY) return;

  if (k >= '0' && k <= '9') {
    if ((int)code.length() < CODE_LENGTH) { code += k; drawDigits(); }
  } else if (k == '*') {
    if (code.length() > 0) { code.remove(code.length() - 1); drawDigits(); }
  } else if (k == '#') {
    if ((int)code.length() == CODE_LENGTH) {
      submitCode();
    } else {
      show("Enter 6 digits", "");
      delay(1200);
      showCodeScreen();
    }
  } else if (k == 'D') {
    resetToMenu();
  }
}

void handleGate() {
  if (millis() - gateOpenedAt >= GATE_OPEN_MS) {
    barrier.write(GATE_CLOSED);
    show("Barrier closed", "Slot " + String(assignedSlot + 1) + " is yours");
    delay(2000);
    resetToMenu();
  }
}
// put function declarations here:
//int myFunction(int, int);

void setup() {
  // put your setup code here, to run once:
Wire.begin(21, 22);
lcd.init();
lcd.backlight();

ESP32PWM::allocateTimer(0);
barrier.setPeriodHertz(50);
barrier.attach(SERVO_PIN, 500, 2400);
barrier.write(GATE_CLOSED);

show("Parking system", "Starting...");
delay(1500);
showCategoryMenu();
}

void loop() {
  // put your main code here, to run repeatedly:
  char k = keypad.getKey();

  switch (state) {
    case S_CATEGORY: handleCategory(k); break;
    case S_CODE:     handleCode(k);     break;
    case S_GATE:     handleGate();      break;
  }

  delay(10);
}

// put function definitions here:
//int myFunction(int x, int y) {
  //return x + y;
//}