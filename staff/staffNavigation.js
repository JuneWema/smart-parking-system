// =====================================================
// 1. SETTINGS (values that never change use const)
// =====================================================
const metersPerPixel = 0.5;  // 1 map pixel = 0.5 meters
const speed = 5.0;           // car speed in meters per second
const entryX = 85;           // where the ENTRY gate is on the map
const entryY = 195;
const laneY = 100;           // height of the main lane
const routeLaneY = laneY + 275;  // where the dotted line turns toward the bays
const bayStopY = 250;        // where the car stops inside a bay
const firstBayX = 190;       // where P1 starts
const bayWidth = 130;

// =====================================================
// 2. PARKING DATA (later you can load this from PHP / database)
// =====================================================
const bays = [
  { name: "P1", status: "available" },
  { name: "P2", status: "occupied" },
  { name: "P3", status: "booked" },
  { name: "P4", status: "available" },
  { name: "P5", status: "occupied" }
];

const colors = { available: "#1fa463", booked: "#2f6fdb", occupied: "#d6402f" };
const labels = { available: "Available", booked: "Booked", occupied: "Occupied" };

// A value that changes uses let
let selected = 0;   // 0 means P1, 1 means P2, and so on

// =====================================================
// 3. FIND THE ELEMENTS ON THE PAGE
// =====================================================
const slotsGroup = document.getElementById("slots");
const routeLine = document.getElementById("route");
const stepsList = document.getElementById("steps");
const titleBox = document.getElementById("title");
const badgeBox = document.getElementById("badge");
const distBox = document.getElementById("dist");
const timeBox = document.getElementById("time");
const noteBox = document.getElementById("note");

// =====================================================
// 4. SMALL HELPER FUNCTIONS
// =====================================================

// Gives the middle (x position) of a bay
function getBayCenter(index) {
  return firstBayX + bayWidth * index + bayWidth / 2;
}

// 85.4 -> "85 m"
function formatDistance(meters) {
  return Math.round(meters) + " m";
}

// 95 seconds -> "1 min 35 sec"
function formatTime(seconds) {
  // round to the nearest 5 seconds, never less than 5
  seconds = Math.round(seconds / 5) * 5;
  if (seconds < 5) {
    seconds = 5;
  }

  if (seconds < 60) {
    return seconds + " sec";
  }

  const minutes = Math.floor(seconds / 60);
  const rest = seconds % 60;

  if (rest === 0) {
    return minutes + " min";
  }
  return minutes + " min " + rest + " sec";
}

// =====================================================
// 5. DRAW THE PARKING BAYS
// =====================================================
function drawBays() {
  let html = "";

  for (let i = 0; i < bays.length; i++) {
    const x = firstBayX + bayWidth * i;
    const center = getBayCenter(i);
    const color = colors[bays[i].status];

    // the selected bay gets an extra class called "sel"
    let cssClass = "slot";
    if (i === selected) {
      cssClass = "slot sel";
    }

    html += `<g class="${cssClass}" data-index="${i}">`;
    html += `<rect x="${x + 4}" y="160" width="${bayWidth - 8}" height="190" rx="4"
              fill="${color}" fill-opacity="0.38" stroke="${color}"></rect>`;
    html += `<text x="${center}" y="262" text-anchor="middle">${bays[i].name}</text>`;
    html += `<text class="st" x="${center}" y="288" text-anchor="middle">${labels[bays[i].status]}</text>`;
    html += `</g>`;
  }

  slotsGroup.innerHTML = html;

  // make every bay clickable
  const groups = slotsGroup.getElementsByClassName("slot");
  for (let j = 0; j < groups.length; j++) {
    groups[j].addEventListener("click", function () {
      const index = Number(this.getAttribute("data-index"));
      selectBay(index);
    });
  }
}

// =====================================================
// 6. WHEN THE USER PICKS A BAY
// =====================================================
function selectBay(index) {
  // validation: the bay number must exist
  if (isNaN(index) || index < 0 || index >= bays.length) {
    return;
  }
  selected = index;
  showRoute();
}

// =====================================================
// 7. BUILD THE LIST OF STEPS
// Each step has some text and a distance in pixels.
// =====================================================
function buildSteps(bay, index) {
  const center = getBayCenter(index);
  const passed = index;                  // how many bays the car drives past
  const upPx = entryY - laneY;           // from the gate up to the lane
  const acrossPx = center - entryX;      // along the lane to the chosen bay
  const intoBayPx = bayStopY - laneY;    // from the lane into the bay

  const steps = [];

  // Step 1: always the same
  steps.push({
    text: "Drive through the ENTRY gate and head up to the main lane.",
    pixels: upPx
  });

  // Steps 2 and 3: depend on how many bays we pass
  if (passed > 0) {
    let word = "bays";
    if (passed === 1) {
      word = "bay";
    }
    steps.push({
      text: `Turn right and continue straight past ${passed} ${word}.`,
      pixels: acrossPx - bayWidth / 2
    });
    steps.push({
      text: `Turn right at ${bay.name} and drive into the bay.`,
      pixels: bayWidth / 2 + intoBayPx
    });
  } else {
    steps.push({
      text: "Turn right and drive to the first bay.",
      pixels: acrossPx
    });
    steps.push({
      text: `Turn right at ${bay.name} and drive into the bay.`,
      pixels: intoBayPx
    });
  }

  // Last step: depends on the bay status (no distance, so pixels is 0)
  if (bay.status === "occupied") {
    steps.push({ text: `${bay.name} is taken. Please choose another bay.`, pixels: 0 });
  } else if (bay.status === "booked") {
    steps.push({ text: `${bay.name} is reserved. Park only if it is your booking.`, pixels: 0 });
  } else {
    steps.push({ text: `Park in ${bay.name}. You have arrived.`, pixels: 0 });
  }

  return steps;
}

// =====================================================
// 8. SHOW THE ROUTE, STEPS, DISTANCE AND TIME
// =====================================================
function showRoute() {
  drawBays();

  const bay = bays[selected];
  const color = colors[bay.status];
  const center = getBayCenter(selected);

  // --- the dotted line on the map ---
  const path = `M${entryX} ${entryY} V${routeLaneY} H${center} V${bayStopY}`;
  routeLine.setAttribute("d", path);
  routeLine.style.stroke = color;

  // --- the steps list and the total distance ---
  const steps = buildSteps(bay, selected);
  let totalPixels = 0;
  let listHtml = "";

  for (let i = 0; i < steps.length; i++) {
    totalPixels = totalPixels + steps[i].pixels;

    // only show a distance when the step has one
    let distanceText = "";
    if (steps[i].pixels > 0) {
      distanceText = formatDistance(steps[i].pixels * metersPerPixel);
    }

    listHtml += `<li><span>${i + 1}. ${steps[i].text}</span><small>${distanceText}</small></li>`;
  }
  stepsList.innerHTML = listHtml;

  // --- title, distance and time ---
  const meters = totalPixels * metersPerPixel;
  titleBox.textContent = "Route to " + bay.name;
  distBox.textContent = formatDistance(meters);
  timeBox.textContent = formatTime(meters / speed);

  // --- status badge ---
  badgeBox.textContent = labels[bay.status];
  badgeBox.style.background = color;
  if (bay.status === "booked") {
    badgeBox.style.color = "#1c1f23";
  } else {
    badgeBox.style.color = "#fff";
  }

  // --- note under the map ---
  if (bay.status === "available") {
    noteBox.textContent = "Green route: this bay is free.";
  } else if (bay.status === "booked") {
    noteBox.textContent = "Blue route: this bay is booked by someone else.";
  } else {
    noteBox.textContent = "Red route: this bay is occupied right now.";
  }
  noteBox.style.borderLeft = "5px solid " + color;
}

// =====================================================
// 9. START THE PAGE
// =====================================================
if (slotsGroup && routeLine && stepsList) {
  showRoute();
} else {
  console.error("staffNavigation.js: some ids are missing in staffNavigation.html");
}