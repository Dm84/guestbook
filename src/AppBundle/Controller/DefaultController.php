<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

use AppBundle\Entity\User;
use Doctrine\DBAL\DBALException;
use Symfony\Component\Security\Core\Exception\AuthenticationException;

class DefaultController extends Controller
{
	/**
	 * Сообщени об ошибке
	 * @var string
	 */
	private $errMsg = ""; 
	
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction()
    {
        return $this->render('default/index.html.twig');
    }
    
    /**
     * @Route("/login-check", name="login_check")
     */
    public function loginCheckAction()
    {
    	return $this->redirectToRoute('homepage');
    }    
    
    
    /**
     * @Route("/signup", name="signup")
     */
    public function signupAction() {
    	
    	$em = $this->getDoctrine()->getManager();
    	
    	$user = new User();
    	$user->setUsername($this->getRequest()->get("_username"));    	

    	/* @var $factory \Symfony\Component\Security\Core\Encoder\EncoderFactory */
    	$factory = $this->get('security.encoder_factory');    	
    	
		$encoder = $factory->getEncoder($user);    	
		$user->setPassword($encoder->encodePassword($this->getRequest()->get("_password"), $user->getSalt()));
		
		try {
			
			$em->persist($user);
			$em->flush();
			 
			$token = new UsernamePasswordToken($user, null, 'secured_area', $user->getRoles());
			$this->get('security.token_storage')->setToken($token);			
		}
		catch (\Exception $ex)
		{
			$this->errMsg = "Неверные данные регистрации, или такой пользователь уже зарегистрирован";
			return $this->loginAction();
		}
		
    	    	
    	return $this->redirectToRoute('homepage');
    }
    
   
    /**
     * @Route("/login", name="login")
     */
    public function loginAction()
    {
    	/* @var $authenticationUtils \Symfony\Component\Security\Http\Authentication\AuthenticationUtils */
    	$authenticationUtils = $this->get('security.authentication_utils');
    	 
    	$error = $authenticationUtils->getLastAuthenticationError();
    	$this->errMsg = $error instanceof AuthenticationException ? $error->getMessage() : $this->errMsg;
    	
    	$lastUsername = $authenticationUtils->getLastUsername();   	
    	
    	return $this->render('default/login.html.twig', [
    			"error" => $this->errMsg,
    			"signin_url" => $this->generateUrl('login_check'),
    			"signin_login_var" => "_username",
    			"signin_pwd_var" => "_password",    			
    			"facebook_signin_url" => $this->generateUrl('hwi_oauth_service_redirect', ["service" => "facebook"]),
    			"login" => $lastUsername,
    			"login_placeholder" => "Ваш логин",    			 			
    			
    			"pwd_placeholder" => "Ваш пароль",
    			"pwd_confirm_placeholder" => "Ваш пароль еще раз",
    			"pwd_confirm_req_msg" => "Пароли должны совпадать",
    			"pwd_len_req_msg" => "Длина пароля должна быть не менее:",
    			
    			"field_req_msg" => "Обязательное поле",
    			
    			"signup_header" => "Регистрация",    			
    			"signup_url" => $this->generateUrl('signup'),    			
    			"signup_caption" => "Зарегистрироваться",    			
    			"js_path" => "/bundles/app/js"
    	]);
    }
}
