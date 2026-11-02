

// ----- 1. Settings you can change -----
var metersPerPixel = 0.08;   // how many metres one map pixel is worth
var speed = 2.2;             // car speed in metres per second (about 8 km/h)
var entryX = 85;             // where the ENTRY gate is on the map
var entryY = 170;
var laneY = 110;             // height of the main lane
var firstBayX = 190;         // where P1 starts
var bayWidth = 130;

// ----- 2. Parking data (later you can load this from PHP / database) -----
var bays = [
  { name: "P1", status: "available" },
  { name: "P2", status: "occupied" },
  { name: "P3", status: "booked" },
  { name: "P4", status: "available" },
  { name: "P5", status: "occupied" }
];

var colors = { available: "#1fa463", booked: "#e0a100", occupied: "#d6402f" };
var labels = { available: "Available", booked: "Booked", occupied: "Occupied" };

var selected = 0;   // 0 means P1, 1 means P2, and so on

// ----- 3. Get the page elements -----
var slotsGroup = document.getElementById("slots");
var routeLine = document.getElementById("route");
var stepsList = document.getElementById("steps");
var titleBox = document.getElementById("title");
var badgeBox = document.getElementById("badge");
var distBox = document.getElementById("dist");
var timeBox = document.getElementById("time");
var noteBox = document.getElementById("note");
var controlsBox = document.getElementById("ctl");

// ----- 4. Helper functions -----
function bayCenter(index) {
  return firstBayX + bayWidth * index + bayWidth / 2;
}

function formatDistance(meters) {
  return Math.round(meters) + " m";
}

function formatTime(seconds) {
  seconds = Math.round(seconds / 5) * 5;
  if (seconds < 5) {
    seconds = 5;
  }
  if (seconds < 60) {
    return seconds + " sec";
  }
  var minutes = Math.floor(seconds / 60);
  var rest = seconds % 60;
  if (rest === 0) {
    return minutes + " min";
  }
  return minutes + " min " + rest + " sec";
}

// ----- 5. Draw the five parking bays -----
function drawBays() {
  var html = "";
  for (var i = 0; i < bays.length; i++) {
    var x = firstBayX + bayWidth * i;
    var center = bayCenter(i);
    var color = colors[bays[i].status];
    var cssClass = "slot";
    if (i === selected) {
      cssClass = "slot sel";
    }
    html += '<g class="' + cssClass + '" data-index="' + i + '">';
    html += '<rect x="' + (x + 4) + '" y="160" width="' + (bayWidth - 8) + '" height="190" rx="4" ';
    html += 'fill="' + color + '" fill-opacity="0.38" stroke="' + color + '"></rect>';
    html += '<text x="' + center + '" y="262" text-anchor="middle">' + bays[i].name + '</text>';
    html += '<text class="st" x="' + center + '" y="288" text-anchor="middle">' + labels[bays[i].status] + '</text>';
    html += '</g>';
  }
  slotsGroup.innerHTML = html;

  // make every bay clickable
  var groups = slotsGroup.getElementsByClassName("slot");
  for (var j = 0; j < groups.length; j++) {
    groups[j].addEventListener("click", function () {
      var index = Number(this.getAttribute("data-index"));
      selectBay(index);
    });
  }
}

// ----- 6. When the user picks a bay -----
function selectBay(index) {
  // validation: the bay number must exist
  if (isNaN(index) || index < 0 || index >= bays.length) {
    return;
  }
  selected = index;
  showRoute();
}

// ----- 7. Show the route, steps, distance and time -----
function showRoute() {
  drawBays();

  var bay = bays[selected];
  var color = colors[bay.status];
  var center = bayCenter(selected);
  var passed = selected;   // number of bays the car drives past

  // the moving dotted line
  var path = "M" + entryX + " " + entryY + " V" + (laneY + 275) + " H" + center + " V250";
  routeLine.setAttribute("d", path);
  routeLine.style.stroke = color;

  // distances in pixels for each part of the trip
  var upPx = entryY - laneY;
  var acrossPx = center - entryX;
  var intoBayPx = 250 - laneY;
  var stepsText = [];
  var stepsPx = [];

  stepsText.push("Drive through the ENTRY gate and head up to the main lane.");
  stepsPx.push(upPx);

  if (passed > 0) {
    var word = passed === 1 ? "bay" : "bays";
    stepsText.push("Turn right and continue straight past " + passed + " " + word + ".");
    stepsPx.push(acrossPx - bayWidth / 2);
    stepsText.push("Turn right at " + bay.name + " and drive into the bay.");
    stepsPx.push(bayWidth / 2 + intoBayPx);
  } else {
    stepsText.push("Turn right and drive to the first bay.");
    stepsPx.push(acrossPx);
    stepsText.push("Turn right at " + bay.name + " and drive into the bay.");
    stepsPx.push(intoBayPx);
  }

  if (bay.status === "occupied") {
    stepsText.push(bay.name + " is taken. Please choose another bay.");
  } else if (bay.status === "booked") {
    stepsText.push(bay.name + " is reserved. Park only if it is your booking.");
  } else {
    stepsText.push("Park in " + bay.name + ". You have arrived.");
  }
  stepsPx.push(0);

  // write the steps and add up the total distance
  var totalPx = 0;
  var listHtml = "";
  for (var i = 0; i < stepsText.length; i++) {
    totalPx = totalPx + stepsPx[i];
    var dist = "";
    if (stepsPx[i] > 0) {
      dist = formatDistance(stepsPx[i] * metersPerPixel);
    }
    listHtml += "<li><span>" + (i + 1) + ". " + stepsText[i] + "</span><small>" + dist + "</small></li>";
  }
  stepsList.innerHTML = listHtml;

  // distance and time
  var meters = totalPx * metersPerPixel;
  titleBox.textContent = "Route to " + bay.name;
  distBox.textContent = formatDistance(meters);
  timeBox.textContent = formatTime(meters / speed);

  // status badge and note
  badgeBox.textContent = labels[bay.status];
  badgeBox.style.background = color;
  if (bay.status === "booked") {
    badgeBox.style.color = "#1c1f23";
  } else {
    badgeBox.style.color = "#fff";
  }

  if (bay.status === "available") {
    noteBox.textContent = "Green route: this bay is free.";
  } else if (bay.status === "booked") {
    noteBox.textContent = "Amber route: this bay is booked by someone else.";
  } else {
    noteBox.textContent = "Red route: this bay is occupied right now.";
  }
  noteBox.style.borderLeft = "5px solid " + color;
}

// ----- 8. Demo dropdowns to change a bay's status -----
function makeControls() {
  var html = "";
  /* for (var i = 0; i < bays.length; i++) {
    html += '<label>' + bays[i].name + ' <select data-index="' + i + '">';
    for (var status in labels) {
      var isChosen = (status === bays[i].status) ? " selected" : "";
      html += '<option value="' + status + '"' + isChosen + '>' + labels[status] + '</option>';
    }
    html += '</select></label> ';
  } */
  controlsBox.innerHTML = html;

  var selects = controlsBox.getElementsByTagName("select");
  for (var j = 0; j < selects.length; j++) {
    selects[j].addEventListener("change", function () {
      var index = Number(this.getAttribute("data-index"));
      var newStatus = this.value;
      // validation: only accept a known status
      if (colors[newStatus] === undefined) {
        return;
      }
      bays[index].status = newStatus;
      showRoute();
    });
  }
} 

// ----- 9. Start the page -----
if (slotsGroup && routeLine && stepsList && controlsBox) {
  makeControls();
  showRoute();
} else {
  console.error("navigation.js: some ids are missing in navigation.html");
}
