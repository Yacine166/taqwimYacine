<?php
namespace App\Services;

use MailchimpMarketing\ApiClient;

class Mailchimp {
  
  public function  __construct(public ApiClient $client)
  {
    $this->client = $client;
    $this->client=$client->setConfig([
      'apiKey' => config('services.mailchimp.key'),
      'server' => config('services.mailchimp.server')
    ]);
  }

  public function checkMember(string $email, string $list_id = null){
    $list_id??= config('services.mailchimp.lists.subscribers');
    try{
     $response= $this->client->lists->getListMember($list_id,md5($email));
    }catch(\Exception $e){
      return $e->getCode();
    }
    return $response->status;
  }

  public function subscribe(string $email,string $list_id=null)
  {
    $list_id??= config('services.mailchimp.lists.subscribers');
    return  $this->client->lists->addListMember($list_id,[
      "email_address" => $email,
      "status"=>"subscribed"
    ]);
    
    
  }
  //use this when memeber ask to resubscribe
  public function unsubscribe(string $email, string $list_id = null){
    
    $list_id??= config('services.mailchimp.lists.subscribers');
    return  $this->client->lists->setListMember($list_id, $email, [
      "email_address" => $email,
      "status" => "unsubscribed"  
    ]);
  }
  //when resending confirmation to unsubscribed member
  public function update(string $email,string $status="pending", string $list_id = null){
    $list_id??= config('services.mailchimp.lists.subscribers');
    return $this->client->lists->updateListMember($list_id, $email, [
      "email_address" => $email,
      "status" => $status,
    ]);
  }

    public function ping(){
      return  $this->client->ping->get();
    }

    public function lists(){
      return  $this->client->lists->getAllLists();
    }
}