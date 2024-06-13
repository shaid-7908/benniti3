<!DOCTYPE html>
<html lang="en" dir="ltr">
<?php
include 'inc/header.php';
include 'inc/topbar.php';
\Stripe\Stripe::setApiKey(STRIPE_KEY_SK_TEST);

try {
    $price = \Stripe\Price::retrieve($_GET['price_id']);
    // Access the product associated with the price
    $product_id = $price->product;
    $product = \Stripe\Product::retrieve($product_id);
    $price_id = $price->id;

} catch (Exception $e) {
    // Handle any errors that occur during the request
    echo 'Error: ' . $e->getMessage();
}
?>
<div class="xt-card2" >
    <div class="card-header" style="background-color: #F5A800;color:black">
       <div style="width:450px; margin:0px auto">

      <h3 class='text-left' >Plan selected <?php echo '$' . number_format($price->unit_amount / 100, 2); ?> <?php echo ($price->recurring->interval == 'year') ?  '/year' : '/3 month'; ?></h3>
      <span><?php echo $product->name?></span>
    </div>
    </div>
    <div class="card-body">
        <form id="proceedplan" action="" method="post">
        <div class="form-group">
          <label for="email">Email address</label>
          <input type="email" id="email" name="email" class="form-control" minlength="5" required>
          <p style="margin-top: 20px;" class="inter-font font-700 "><span style="color: red;">*</span>Make sure this is a valid email address, this email will be used to register you with Bennit.</p>
          <p class="inter-font font-700 "><span style="color: red;">*</span>Bennit will not be responsible for any error in email address entered by you</p>
        </div>
        <div class="form-group">
          <button type="submit" name="register" class="btn btn-success">Proceed with plan</button>
        </div>
    </form>
    </div>
   

</div>
<script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
<script src="https://js.stripe.com/v3/"></script>
<script>
    var stripe = Stripe('<?php echo STRIPE_KEY_PK_TEST ?>');
    $(document).ready(function() {
        $('#proceedplan').submit(function(e) {
            e.preventDefault(); // Prevent the default form submission

            // Get the email from the form
            var email = $('#email').val();

            // Make an AJAX request to check if the email exists
            $.ajax({
                type: 'POST',
                url: 'check_email.php', // Path to your PHP file to check email existence
                data: { email: email },
                success: function(response) {
                         console.log(response);
                    // Check the response from the server
                    if (response.status === 'exists') {
                        // Email exists, display an error message or take appropriate action
                        alert('Email is already registered.');
                    } else {
                        // Email does not exist, proceed with Stripe redirection
                       redirectToCheckout();
                    }
                },
                error: function(xhr, status, error) {
                    // Handle errors here
                    console.error(error);
                    alert('Error checking email existence. Please try again.');
                }
            });
        });
    });

    // Function to redirect to Stripe checkout
    function redirectToCheckout() {
        // Create a Checkout session
        stripe.redirectToCheckout({
            lineItems: [{
                price: '<?php echo $price_id ?>', // Replace with the Price ID of your product
                quantity: 1,
            }],
            mode: 'subscription',
            successUrl: 'https://exchange.bennit.ai/paymentsNregister.php?&payment_id={CHECKOUT_SESSION_ID}',
            cancelUrl: 'https://exchange.bennit.ai/choosePlan.php',
            customerEmail: $('#email').val(), // Get the customer's email from the form
        }).then(function(result) {
            
            if (result.error) {
                // If redirectToCheckout fails due to a browser or network error, display the error message
                alert(result.error.message);
            }
        });
    }
</script>

<?php
include 'inc/footer2.php'
?>


</html>