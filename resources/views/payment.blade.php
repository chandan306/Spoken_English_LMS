<h2>Stripe Payment Demo</h2>

<form action="{{ route('payment.checkout') }}" method="POST">
    @csrf

    <button type="submit">
        Pay $20
    </button>

</form>