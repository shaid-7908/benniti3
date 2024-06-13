controlledForm = null;

function controlledFormSubmit(frm) {
  controlledForm = frm;
  document.getElementById("btnControlSubmit").disabled = true;
  //Handle skills here
  document.getElementById("skillsIds").value = "";
  //First find or create each skill to get id
  //   document.getElementById("skillsText").value = document
  //     .getElementById("skillsText")
  //     .value.replace(/,+$/, "");
  //First find or create each skill to get id
  var skilltextInput = document.getElementById("skillsText").value;
  var skillDivs = document
    .getElementById("skills-list")
    .getElementsByClassName("skill");
  var skills = [];

  for (var i = 0; i < skillDivs.length; i++) {
    var skillName = skillDivs[i].innerText.trim();
    skills.push(skillName);
  }
  var skillNamesFromInput = skilltextInput.split(",");
  for (var i = 0; i < skillNamesFromInput.length; i++) {
    var skillName = skillNamesFromInput[i].trim();
    if (skillName !== "") {
      // Exclude empty strings
      skills.push(skillName);
    }
  }
  document.getElementById("skillsText").value = skills.join(",");
  var searchStr = document.getElementById("skillsText").value;
  searchStr = searchStr.split(",");
  for (var i = 0; i < searchStr.length; i++) {
    var s = searchStr[i].trim();
    findOrCreateSkill(s);
  }
  //Industry handled here
  var IndustryInput = document.getElementById("industryText").value;
  var iInput = IndustryInput.split(",");
  for(var i =0 ; i < iInput.length ; i++ ){
    var s = iInput[i].trim();
    findOrCreateIndustry(s);
  }
//Technology Handled here
var TechnologyInput = document.getElementById("technologyText").value;
var tInput = TechnologyInput.split(",");
for(var i=0;i<tInput.length ; i++){
    var s = tInput[i].trim();
    findOrCreateTechnology(s);
}
// Speciality Handled here
var SpecialityInput = document.getElementById("specialityText").value;
var sInput = SpecialityInput.split(",");
for(var i = 0 ; i < sInput.length ; i++){
    var s = sInput[i].trim();
    findOrCreateSpecialty(s);
}

}

function findOrCreateSkill(searchStr) {
  var data = new FormData();
  data.append("create", "skill");
  data.append("value", searchStr);
  data.append("partnerkey", "session");
  var xhr = new XMLHttpRequest();
  xhr.withCredentials = true;

  xhr.addEventListener("readystatechange", function () {
    if (this.readyState === 4) {
      if (this.responseText && JSON.parse(this.responseText)) {
        var responseObj = JSON.parse(this.responseText);
        document.getElementById("skillsIds").value += responseObj.id + ",";
        
      }
    }
  });
  xhr.open("POST", findSkillsApiPath());
  xhr.send(data);
}

function findOrCreateIndustry(searchStr){
  var data = new FormData();
  data.append("create","industry");
  data.append("value",searchStr);
  data.append("partnerkey", "session");
   var xhr = new XMLHttpRequest();
   xhr.withCredentials = true;
    xhr.addEventListener("readystatechange", function () {
      if (this.readyState === 4) {
        if (this.responseText && JSON.parse(this.responseText)) {
          var responseObj = JSON.parse(this.responseText);
          document.getElementById("industry_id").value += responseObj.id + ",";
          
        }
      }
    });
    xhr.open("POST", findIndustryApiPath());
    xhr.send(data);
}

function findOrCreateTechnology(searchStr) {
  var data = new FormData();
  data.append("create", "technology");
  data.append("value", searchStr);
  data.append("partnerkey", "session");

  var xhr = new XMLHttpRequest();
  xhr.withCredentials = true;

  xhr.addEventListener("readystatechange", function () {
    if (this.readyState === 4) {
      if (this.responseText && JSON.parse(this.responseText)) {
        var responseObj = JSON.parse(this.responseText);
        document.getElementById("technology_id").value += responseObj.id + ",";
        
      }
    }
  });

  xhr.open("POST", findTechnologyApiPath());
  xhr.send(data);
}

function findOrCreateSpecialty(searchStr) {
  var data = new FormData();
  data.append("create", "speciality");
  data.append("value", searchStr);
  data.append("partnerkey", "session");

  var xhr = new XMLHttpRequest();
  xhr.withCredentials = true;

  xhr.addEventListener("readystatechange", function () {
    if (this.readyState === 4) {
      if (this.responseText && JSON.parse(this.responseText)) {
        var responseObj = JSON.parse(this.responseText);
        document.getElementById("speciality_id").value += responseObj.id + ",";
        checkReadySubmit(); // You may need to define or adjust this function based on your requirements
      }
    }
  });

  xhr.open("POST", findSpecialtyApiPath()); // Make sure to define findSpecialtyApiPath function
  xhr.send(data);
}


function checkReadySubmit() {
  var searchStr = document.getElementById("skillsText").value + ",";
  searchStr = searchStr.split(",");
  var searchIds = document.getElementById("skillsIds").value;
  searchIds = searchIds.split(",");
  if (searchStr.length === searchIds.length) {
    console.log("submitting now!");
    setTimeout(function () {
      document.getElementById(controlledForm).submit();
    }, 250);
  } else {
    console.log(
      "Not ready to submit because " +
        searchStr.length +
        " != " +
        searchIds.length
    );
  }
}

function findSkillsApiPath() {
  var currLoc = window.location.href;
  var basePath = currLoc.slice(0, currLoc.lastIndexOf("/"));
  return basePath + "/api/skills/index.php";
}
function findIndustryApiPath() {
    var currLoc = window.location.href;
    var basePath = currLoc.slice(0, currLoc.lastIndexOf("/"));
    return basePath + "/api/industry/index.php"; 
}
function findTechnologyApiPath(){
     var currLoc = window.location.href;
     var basePath = currLoc.slice(0, currLoc.lastIndexOf("/"));
     return basePath + "/api/technology/index.php"; 
}
function findSpecialtyApiPath(){
    var currLoc = window.location.href;
    var basePath = currLoc.slice(0, currLoc.lastIndexOf("/"));
    return basePath + "/api/speciality/index.php";  
}