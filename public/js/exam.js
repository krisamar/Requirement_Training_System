
$(document).ready(function () {
$('#submitBtn').on('click', function (e) {
e.preventDefault();

var isAnyAnswerNull = false;

// Loop through each question
$('.question').each(function () {
  var answer = $(this).find('input[name^="questions"]:checked').val();

  // Check if the answer is null
  if (answer === undefined) {
      isAnyAnswerNull = true;
      return false;
  }
});

// If any answer is null, prompt the user for confirmation using SweetAlert
if (isAnyAnswerNull) {
  Swal.fire({
      title: 'Some questions are not answered.',
      text: 'Are you sure you want to submit?',
      icon: 'warning',
      showCancelButton: true,
      confirmButtonText: 'submit',
      cancelButtonText: 'Cancel'
  }).then((result) => {
      if (result.isConfirmed) {
          submitForm();
      }
  });
} else {
  // All questions have been answered, proceed with AJAX submission directly
  submitForm();
}
});

// Function to submit the form via AJAX
function submitForm() {
var formData = $('#aptitudeForm').serialize();
$.ajax({
  type: 'POST',
  url: '/form',
  data: formData,
  success: function (response) {

          window.location.href = "/exam/submit"; // Redirect to the next page after successful submission
  },
  error: function (error) {
      console.log(error);
  }
});
}
});

//prevent back button
history.pushState(null, null, location.href);
       
       window.onpopstate = function () {
       
              // Push another state onto the history stack to keep the user on the current page
       
                history.pushState(null, null, location.href);
       
              };

