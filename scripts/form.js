function updateStepIndicators(currentStep) {
  // Remove active class from all step indicators and step numbers
  $(".step-indicator").removeClass("active");
  $(".step-number").removeClass("active");
 
  // Add active class to current step indicator and its corresponding step number
  $(".step-indicators")
    .find(".step-indicator:nth-child(" + currentStep + ")")
    .addClass("active");
  $(".step-indicator:nth-child(" + currentStep + ") .step-number").addClass(
    "active"
  );

  // Loop through all previous steps to update indicators
  for (var i = 1; i <= 3; i++) {
    // Assuming you have 3 steps
    if (i < currentStep) {
      // If step is before current step, set color to white
      $(".step-indicator:nth-child(" + i + ")").css({
        color: "white",
      });
        $(".step-indicator:nth-child(" + i + ") .step-number").html("&#10003;");
      
    } else if (i === currentStep) {
      // If step is current step, set color to active color
      $(".step-indicator:nth-child(" + i + ") ").css({
        color: "#f5a800",
      });
       $(".step-indicator:nth-child(" + i + ") .step-number").html(i);
     
    } else if(i > currentStep){
         $(".step-indicator:nth-child(" + i + ") ").css({
           color: "rgba(255, 255, 255, 0.4)",
         });
         $(".step-indicator:nth-child(" + i + ") .step-number").html(
          i
         );
      }
  }
}
$(document).ready(function () {
  var form_count = 1,
    previous_form,
    next_form,
    total_forms;
  total_forms = $("fieldset").length;
  $(".next-form").click(function () {
    previous_form = $(this).parent();
    next_form = $(this).parent().next();
    next_form.show();
    previous_form.hide();
    setProgressBarValue(++form_count);
    updateStepIndicators(form_count);
    window.scrollTo({
      top: 0,
      behavior: "smooth", // Smooth scrolling
    });
  });
  $(".previous-form").click(function () {
    previous_form = $(this).parent();
    next_form = $(this).parent().prev();
    next_form.show();
    previous_form.hide();
    setProgressBarValue(--form_count);
    updateStepIndicators(form_count);
    window.scrollTo({
      top: 0,
      behavior: "smooth", // Smooth scrolling
    });
  });
  setProgressBarValue(form_count);
  function setProgressBarValue(value) {
    var percent = parseFloat(100 / total_forms) * value;
    percent = percent.toFixed();
    $(".progress-bar").css("width", percent + "%");
  }
  // Handle form submit and validation
});
