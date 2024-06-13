controlledForm = null;

function controlledFormSubmit(frm) {
  controlledForm = frm;
  document.getElementById("btnControlSubmit").disabled = true;
  document.getElementById("skillsIds").value = "";
  //First find or create each skill to get id
//   document.getElementById("skillsText").value = document
//     .getElementById("skillsText")
//     .value.replace(/,+$/, "");
  //First find or create each skill to get id
  var skilltextInput = document
    .getElementById("skillsText")
    .value
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
}

function findOrCreateSkill(searchStr) {
    var data = new FormData();
    data.append("create", "skill");
    data.append("value", searchStr);
    data.append("partnerkey", "session");
    var xhr = new XMLHttpRequest();
    xhr.withCredentials = true;

    xhr.addEventListener("readystatechange", function() {
        if(this.readyState === 4) {
            if (this.responseText && JSON.parse(this.responseText)) {
                var responseObj = JSON.parse(this.responseText);
                document.getElementById("skillsIds").value += responseObj.id + ",";
                checkReadySubmit();
            }
        }
    });
    xhr.open("POST", findApiPath());
    xhr.send(data);
}

function checkReadySubmit() {
    var searchStr=document.getElementById("skillsText").value + ",";
    searchStr=searchStr.split(",");
    var searchIds=document.getElementById("skillsIds").value;
    searchIds=searchIds.split(",");
    if (searchStr.length === searchIds.length) {
        console.log("submitting now!");
        setTimeout(function() {
            document.getElementById(controlledForm).submit();
        }, 250);
    } else {
        console.log("Not ready to submit because " + searchStr.length + " != " + searchIds.length);
    }
}

function findApiPath() {
    var currLoc = window.location.href;
    var basePath = currLoc.slice(0, currLoc.lastIndexOf("/"));
    return basePath + "/api/skills/index.php"
}
