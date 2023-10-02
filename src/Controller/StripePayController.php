<?php

namespace App\Controller;

use App\Service\CartService;
use Stripe\Checkout\Session;
use Stripe\Stripe;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
// use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Annotation\Route;

class StripePayController extends AbstractController
{
    #[Route('/stripe/pay', name: 'app_stripe_pay')]
    public function index(CartService $cs): Response
    {

        $fullCart=$cs->getCartWithData();

        $line_items=[];
         foreach($fullCart as $item)
         {
            $line_items[]=[
                'price_data'=>[
                    'unit_amount'=>$item['activity']->getPrice()*100, 'currency'=>'EUR',
                    'product_data'=>['name'=>$item['activity']->getName()
                ]
                ],
                'quantity'=>$item['quantity']
            ];
         }
         Stripe::setApiKey('sk_test_51Nwh4iDM5OngB6NwOL1wMGWgJcUH4BiGccRC4SL7egFZSthM64oFL6twKM8mw8IqmfeHaduTGwXKh49eCYGPgbOS00pGc2mtZw');
         $session=Session::create([
            'success_url'=>'https://www.david.lock.cezdigit.com/commande/success',
            'cancel_url'=>'http://www.david.lock;cezdigit.com/wishList',
           // 'success_url'=>'http://127.0.0.1:8000/commande/success',
           // 'cancel_url'=>'http://1270.0.1:8000/wishList',
            'payment_method_types'=>['card'],
            'line_items'=>$line_items,
            'mode'=>'payment'
        
        ]);
        return $this->redirect($session->url, 303);

    }
    #[Route('/commande/{success}', name: 'commande')]
    public function commande($success=null): Response
    {
    
        if($success){
            $this->addFlash('succes','Merci pour votre confiance');
            return $this->redirectToRoute('app_front');
        }else{
            $this->addFlash('danger','un problme est survenu Merci de reitérer votre paiement');
            return $this->redirectToRoute('app_front');

        }
       
    ;
    }
}
