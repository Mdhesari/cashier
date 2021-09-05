Vandar Cashier is a Laravel package that provides you with a seamless integration with Vandar services. Take a look at Vandar Documentation for more information on the services we provide.

# Setup
To use Vandar Cashier, you need to install it through Composer first:
```bash
composer require vandar/cashier
```
Then, you will need to publish the package's assets and migrate your project's database to add Vandar Cashier's tables:
```php
php artisan vendor:publish --provider="Vandar\\Cashier\\VandarCashierServiceProvider"
php artisan migrate
```
After that, **if you are planning to use Vandar Cashier in relation to your users** open your User model (located in `app/User.php` or `app/Models/User.php` in most projects) and add the `Billable` trait:
```php
use Vandar\Cashier\Traits\Billable;
// ...
class User extends Authenticatable
{
    use Billable;
// ...
```
You will need to add the necessary configuration to your `.env` file:
```php
VANDAR_MOBILE=
VANDAR_PASSWORD=
VANDAR_BUSINESS_SLUG=
VANDAR_API_KEY=
```
`VANDAR_MOBILE` and `VANDAR_PASSWORD` are your login credentials to Vandar dashboard, the `VANDAR_BUSINESS_SLUG` is set by you when you add a business in Vandar and `VANDAR_API_KEY` is obtained through your business dashboard.
# Usage
Currently, Vandar provides two services: **IPG** and **Direct Debit**. IPG is the more common method used which provides you with a link that the user can use to pay for a service. The direct debit service works by requesting access from a user's bank account and charging them periodically without a need for user interaction.
## IPG
### Independent
if you're creating a donation form, or you don't really need a logged-in user to make payments, you will need two paths. The first path is going to be initiating the payment and sending it to payment gateway. The second path (also known as callback url) will verify the transaction once your user has returned.
```php
use Vandar\Cashier\Models\Payment;
Route::get('/initiate-payment', function(Request $request) {
    $payment = Payment::create($data);
    return redirect($payment->url);
});
```
### User-Dependent
In a user-dependant scenario, we are assuming that anyone making a payment is already logged-in to your system, therefore, you can create a payment link for them to redirect them to through their user model:
```php
Route::get('/initiate-payment', function(Request $request){
    $user = auth()->user(); // Added as a separate variable for clarity
    $payment = $user->payments()->create($payload);
    return redirect($payment->url); // See documentation for info on payload and callback
});
```

### Callback URL
Once the transaction finishes (successfully or not), they will be redirect back to the path you defined in callback, you may define a controller or a route to verify the payment using the `Payment::verify($request)` method:
```php
use Vandar\Cashier\Models\Payment;
Route::get('/callback', function(Request $request){
    if(Payment::verifyFromRequest($request)){
        return 'Success!';
    } 
    else {
        return 'Failed!';
    }
});
```
The verify method automatically updates the transaction status in your database. 

Also, for IPG, you're going to need to define a callback url for when users are returning from Vandar to your application, you can either set the `VANDAR_CALLBACK_URL` environment variable or modify `config/vandar.php`. You will also need to add the callback URL in your Business Dashboard in Vandar or otherwise it will lead into an error.

## Direct-Debit
When setting up direct-debit, you have two main steps to take.
1. Get user to allow for access to their account (also known as a Mandate)
2. Request a withdrawal from a user's account

### Mandate
Before any withdrawal from a user's account can be done, it is required to check whether they have a valid mandate, in other words, whether they are still allowing us to access their account. This is done using the `hasValidMandate` method in the User model. See the examples below.

if the user is not a valid subscriber, you can use the `authorizeMandate` method to generate a link for them to authorize it.

### Withdrawal
Once the mandate is verified, you may create a withdrawal using the `User::withdrawals()->create()` method.

All the code below is used in this example:
```php
$user = User::find(1);
if($user->hasValidMandate()){
    $user->withdrawals()->create($payload);
}
else {
    return redirect($user->authorizeMandate());
}
```

# License
All material in this project (unless otherwise noted) are available under the MIT License. See LICENSE for more information.