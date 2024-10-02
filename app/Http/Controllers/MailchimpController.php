<?php

namespace App\Http\Controllers;

use App\Services\Mailchimp;
use Illuminate\Http\Request;

class MailchimpController extends Controller
{
 
  public function __invoke(Request $request, Mailchimp $mailchimp)
  {
    $request->validate([
      'email' => ['required', 'email']
    ]);

    $status = $mailchimp->checkMember($request->email);
    try {
      if ($status == "subscribed")
        return response("You're already subscribed", 200);

      if ($status == "pending")
        return response("We ALREADY sent you a confirmation email, please check your email to confirm your subscription", 200);

      if ($status == "unsubscribed") {
        $mailchimp->update($request->email);
        return response('We sent you a confirmation email, please check your email to confirm your subscription', 200);
      }

      $mailchimp->subscribe($request->email);
      return response('You subscribed successfully.', 200);
    } catch (\Exception $e) {
      return response()->json("Ooops something went wrong, try again later.", $e->getCode());
    }
  }

  public function ping(Mailchimp $mailchimp)
  {
    return $mailchimp->ping();
  }
}
