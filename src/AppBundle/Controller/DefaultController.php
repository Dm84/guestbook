<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction()
    {
        return $this->render('default/index.html.twig');
    }
    
    /**
     * @Route("/login", name="login")
     */
    public function loginAction()
    {
    	return $this->render('default/login.html.twig', [
    			"signin_url" => "",
    			"facebook_signin_url" => $this->generateUrl('hwi_oauth_service_redirect', ["service" => "facebook"]),
    			"login" => "",
    			"login_placeholder" => "Ваш логин",
    			"pwd_placeholder" => "Ваш пароль",
    			"pwd_confirm_placeholder" => "Ваш пароль еще раз",
    			"signup_header" => "Регистрация",    			
    			"signup_url" => "",    			
    			"signup_caption" => "Зарегистрироваться",    			
    			"js_path" => "/bundles/app/js"
    	]);
    }
}
