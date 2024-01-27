<form action="{{ route('razorpay.payment.store') }}" method="POST">
    @csrf
    <script src="https://checkout.razorpay.com/v1/checkout.js"></script>
    <button id="payBtn">Pay 100 INR
    </button>
</form>

<script>
    var options = {
        "key": "{{ env('RAZORPAY_API_KEY') }}",
        "amount": "10000",
        "name": "BotConfigHub",
        "description": "Razorpay payment",
        "image": "https://cdn.razorpay.com/logos/NSL3kbRT73axfn_medium.png",
        "prefill": {
            "name": "ABC",
            "email": "abc@gmail.com"
        },
        "theme": {
            "color": "#0F408F"
        },
        "handler" : function(res) {
            console.log(res);

            $.ajax({
                url: "/payment/create",
                type: 'POST',
                dataType: 'json',
                data: {
                    _token: "{{ csrf_token() }}", // CSRF token
                    response: {
                        razorpay_payment_id: res.razorpay_payment_id
                    }
                },
                success: function(res) {
                    console.log('Payment data sent to server', res);
                    if (res.success = true) {
                        window.location.href = '/'; //payment failure
                    }
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    console.log('Error sending payment failure information:', textStatus, errorThrown);
                }
            });
        },
        "modal": {
            "ondismiss": function() {
                // This function is called when the user closes the modal
                window.location.href = '/'; // Redirect to your failure page
            }
        }
    };

    var rzp = new Razorpay(options);
    document.getElementById('payBtn').onclick = function(e) {
        rzp.open();
        e.preventDefault();
    }

    rzp.on('payment.failed', function(response) {

        event.preventDefault();
        if (response.reason = "payment_failed") {
            const {
                error,
                reason
            } = response;
            $.ajax({
                url: "/payment/failure",
                type: 'POST',
                dataType: 'json',
                data: {
                    _token: "{{ csrf_token() }}", // CSRF token
                    response: {
                        error,
                        reason
                    }
                },
                success: function(response) {
                    console.log('Payment failure data sent to server', response);
                    if (response.success = true) {
                        window.location.href = '/402'; //payment failure
                    }
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    console.log('Error sending payment failure information:', textStatus, errorThrown);
                }
            });
        }
    });
</script>