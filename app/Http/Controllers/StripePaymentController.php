<?php
    
namespace App\Http\Controllers;
     
use Illuminate\Http\Request;
use Session;
use Stripe;
use App\Models\PhysicalPayment;
use App\Models\SeatInfo;
use App\Models\User;
use Illuminate\Support\Facades\Validator;


class StripePaymentController extends Controller
{
    /**
     * success response method.
     *
     * @return \Illuminate\Http\Response
     */
    public function stripe()
    {
        $data= [
            'title'=>'Login Page'
        ];

        return view('stripe', $data);
    }
    
    /**
     * success response method.
     *
     * @return \Illuminate\Http\Response
     */
      
    public function stripePost(Request $request)
    {
        $valid_data = [
            'name' => $request->input('username'),
            'email' => $request->input('email'),
        ];

        $validator = Validator::make($valid_data, [
            'name' => 'required|unique:users',
            'email' => 'required|email|unique:users',
            // 'password' => 'required|min:6|confirmed',
        ]);

        if ($validator->fails()) {
            // If validation fails, return to the signup page with errors
            return back()->withErrors($validator)->withInput();
        }

        Stripe\Stripe::setApiKey(env('STRIPE_SECRET'));

        try {
            $customer = Stripe\Customer::create([
                "address" => [
                    "line1" => "7742 Hall Dr. Lockport",
                    "postal_code" => "14094",
                    "city" => "New York",
                    "state" => "NY",
                    "country" => "US",
                ],
                "email" => "demo@gmail.com",
                "name" => "Lucas Wills",
                "source" => $request->stripeToken
            ]);

            $response = Stripe\Charge::create([
                "amount" => 100 * 100,
                "currency" => "usd",
                "customer" => $customer->id,
                "description" => "Test payment from LaravelTus.com.",
                "shipping" => [
                    "name" => "Jenny Rosen",
                    "address" => [
                        "line1" => "510 Townsend St",
                        "postal_code" => "98140",
                        "city" => "San Francisco",
                        "state" => "CA",
                        "country" => "US",
                    ],
                ]
            ]);

            // Check if the payment was successful
            if ($response['status'] === 'succeeded') {

                // Hash the password before storing it in the database
                $data = [
                    'name' => $request->input('username'),
                    'email' => $request->input('email'),
                    'password' => bcrypt('admin1234'),
                ];
        
                // Create a new user record
                $seat_user = User::create($data);

                // Retrieve user details
                $user = auth()->user();
                $user_name = $user->name;                
                $user_email = $user->email;
                $user_id = $user->id;
                $contact_no = '000111444666';
                $amount = $response['amount'];
                $balance_transaction = $response['balance_transaction'];
                $currency = $response['currency'];
                $date = now();

                // Insert transaction data into the database using Eloquent
                $payment = PhysicalPayment::create([
                    'physical_payment_name' => $user_name,
                    'physical_payment_email' => $user_email,
                    'physical_payment_num' => $contact_no,
                    'product_id' => '0',
                    'swap_products_id' => '0',
                    'swap_request_user_id' => '0',
                    'physical_payment_item_name' => 'test',
                    'physical_payment_item_number' => '101010',
                    'physical_payment_item_price' => '100',
                    'physical_payment_item_price_currency' => $currency,
                    'physical_payment_paid_amount' => $amount,
                    'uploaded_month' => now()->format('m'),
                    'physical_payment_paid_amount_currency' => $currency,
                    'physical_payment_txn_id' => $balance_transaction,
                    'physical_payment_status' => 'success', // Assuming this is the status you want for a successful payment
                    'physical_payment_created' => $date,
                    'physical_payment_modified' => $date,
                    'user_id' => $user_id,
                    'swap_request_id' => '0'
                ]);
                
                $seat_user_id = $seat_user->id;
                $seat_username = $request->username;
                $city = $request->city;
                $state = $request->state;
                $company = $request->company;
                $seat_email = $request->email;
                $phone = $request->phone;
                $summary = $request->summary;
                $linkedin = $request->linkedin;
                $twitter = $request->twitter;
                $github = $request->github;
            
                // Insert transaction data into the database using Eloquent
                $seat_user_data = SeatInfo::create([
                    'user_id' => $seat_user_id,
                    'username' => $seat_username,
                    'city' => $city,
                    'state' => $state,
                    'company_name' => $company,
                    'email' => $seat_email,
                    'phone_number' => $phone,
                    'profile_summary' => $summary,
                    'linkedin' => $linkedin,
                    'twitter' => $twitter,
                    'github' => $github,
                    'status' => '0',                    
                ]);

                // Flash success message
                Session::flash('success', 'Payment successful!');
            } else {
                // Flash error message if payment failed
                Session::flash('error', 'Payment failed. Please try again.');
            }
        } catch (Exception $e) {
            // Handle exceptions, log errors, etc.
            Session::flash('error', 'An error occurred while processing the payment.');
        }

    return back();
    }
}