<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

use AppBundle\Entity\User;
use Doctrine\DBAL\DBALException;
use Symfony\Component\Security\Core\Exception\AuthenticationException;

use Symfony\Component\HttpFoundation\JsonResponse;
use AppBundle\Entity\Note;

class DefaultController extends Controller
{
	const RES_PATH = '/bundles/app';
	const NOTE_LIFETIME = 'PT24H';
	const PWD_LEN_REQ = 6;
	
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction()
    {
    	$user = $this->getUser();
        return $this->render('default/index.html.twig', [
        		'app_id' => $this->container->getParameter('app_id'),
        		'base_url' => $this->generateUrl('homepage'),
        		'notes_url' => $this->generateUrl('list'),
        		'profile_url' => $this->generateUrl('profile'),
        		'resources_url' => self::RES_PATH,
        		'signout_url' => $this->generateUrl('logout'),
        		'signout_label' => 'Выйти',
        		'user_id' => $user->getId(),
        		'username' => $user->getName(),
        		'post_note_label' => 'Оставить запись',
        		'edit_note_label' => 'Редактировать',
        		'profile_edit_label' => 'Редактировать профиль',
        		'profile_name_label' => 'Ваше имя',
        		'friends_label' => 'Друзья',
        		'post_label' => 'Сохранить',
        		'close_label' => 'Отмена',
        		'save_label' => 'Сохранить'
        ]);
    }
    
    /**
     * @Route("/login-check", name="login_check")
     */
    public function loginCheckAction()
    {
    	$username = $this->getRequest()->get("_username");
    	if (empty($username))
    	{
    		$this->getRequest()->getSession()->getFlashBag()->add(
    				'errors',
    				"Неверные данные входа"
    		);    		
    	}
    	
    	return $this->redirectToRoute('homepage');
    }    
    
    
    /**
     * @Route("/signup", name="signup")
     */
    public function signupAction() {
    	
    	$username = $this->getRequest()->get("_username");
    	
    	$em = $this->getDoctrine()->getManager();
    	$repo = $em->getRepository('AppBundle\\Entity\\User');    	
    	
    	$users = $repo->findBy(array('username' => $username));
    	
    	if (is_array($users) && count($users) > 0)
    	{
    		$this->getRequest()->getSession()->getFlashBag()->add(
    				'errors',
    				"Пользователь с таким логином уже существует"
    		);
    		
    		return $this->redirectToRoute('loginpage');
    	} else
    	{    	
	    	$user = new User();
	    	  	
	
	    	/* @var $factory \Symfony\Component\Security\Core\Encoder\EncoderFactory */
	    	$factory = $this->get('security.encoder_factory');    	
	    	
			$encoder = $factory->getEncoder($user);
	
			$user->
				setUsername($username)->
				setPassword($encoder->encodePassword($this->getRequest()->get("_password"), $user->getSalt()))->
				setName($username);
			
			try {
				
				$em->persist($user);
				$em->flush();
				 
				$token = new UsernamePasswordToken($user, null, 'secured_area', $user->getRoles());
				$this->get('security.token_storage')->setToken($token);			
			}
			catch (DBALException $ex)
			{ 
				$this->getRequest()->getSession()->getFlashBag()->add(
					'errors',
					"Неверные данные регистрации"
				);
			}		
	    	    	
	    	return $this->redirectToRoute('homepage');
    	}
    }
    
   
    /**
     * @Route("/login", name="loginpage")
     */
    public function loginAction()
    {
    	$errors = $this->getRequest()->getSession()->getFlashBag()->get('errors');    	
    	
    	/* @var $authenticationUtils \Symfony\Component\Security\Http\Authentication\AuthenticationUtils */
    	$authenticationUtils = $this->get('security.authentication_utils');
    	 
    	$error = $authenticationUtils->getLastAuthenticationError();
    	if ($error instanceof AuthenticationException)
		{
    		$errors[] = $error->getMessage();
		}
    	
    	$lastUsername = $authenticationUtils->getLastUsername();   	
    	
    	return $this->render('default/login.html.twig', [
    			"errors" => $errors,
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
    			"pwd_len_req" => self::PWD_LEN_REQ,
    			"field_req_msg" => "Обязательное поле",
    			
    			"signup_header" => "Регистрация",    			
    			"signup_url" => $this->generateUrl('signup'),    			
    			"signup_caption" => "Зарегистрироваться",    			
    			"resources_url" => self::RES_PATH
    	]);
    }
    
    /**
     * @Route("/notes", name="list")
     * @Method("GET")
     */
    public function notesAction() {
    	/* @var $em \Doctrine\ORM\EntityManager */
	   	$em = $this->getDoctrine()->getManager();
    	
		/* @var $qb \Doctrine\ORM\QueryBuilder */
    	$qb = $em->createQueryBuilder();
    	
		$actualDate = new \DateTime('now');
		$actualDate->sub(new \DateInterval(self::NOTE_LIFETIME));
		
    	$qb->
    		select('n.id', 'n.text', 'u.name as username', 'n.userId as user_id')->
    		from('AppBundle\\Entity\\Note', 'n')->
    		innerJoin('AppBundle\\Entity\\User', 'u', 'WITH', 'u.id = n.userId')->
			where('n.date > :actual')->
			orderBy('n.date', 'desc');
    	
    	$notes = $qb->getQuery()->setParameter('actual', $actualDate)->getArrayResult();    	
    	
    	return new JsonResponse($notes);    	
    }

    /**
     * 
     * @return \AppBundle\Entity\User
     */
    public function getUser() {

    	/* @var $user User */
    	$user = $this->get('security.context')->getToken()->getUser();
    	 
    	return $user;
    }

    /**
     * @Route("/notes/{id}", name="edit", requirements={"id": "\d+"})
     * @Method("PUT")
     */    
    public function editAction($id)
    {
    	$user = $this->getUser();
    	$em = $this->getDoctrine()->getManager();    	 
		
    	$repo = $em->getRepository('AppBundle\\Entity\\Note');
    	
    	/* @var $note Note */
    	$note = $repo->find($id);
    	
    	//можем редактировать только свой пост
    	if ($note->getUserId() === $user->getId())
    	{
    		$arg = $this->getReqObj();    		
    		$note->setText($arg->text);
    		$em->flush();
    	}    	
    	
    	return new JsonResponse($note);
    }
	
    /**
     * @Route("/notes", name="post")
     * @Method("POST") 
     */
    public function postAction() {
    	
    	$user = $this->getUser();
    	$em = $this->getDoctrine()->getManager();
    	
    	$note = new Note();
    	$note->setUserId($user->getId());
    	
    	$arg = $this->getReqObj();
    	
    	$note->setText($arg->text);
		$note->setDate(new \DateTime('now'));
    	
    	$em->persist($note);
    	$em->flush();   	
    	
    	$obj = new \stdClass();
    	$obj->text = strip_tags($note->getText());
    	$obj->user_id = $note->getUserId();
    	$obj->username = $user->getName();
    	
    	return new JsonResponse($obj);
    }
    
    public function getReqObj() {
    	$content = $this->getRequest()->getContent();
    	return json_decode($content);    	 
    }
    
    /**
     * @Route("/profile", name="profile")
     * @Method("GET")
     */
    public function profileAction() {
    	$user = $this->getUser();
    	return new JsonResponse($user);
    }

    /**
     * @Route("/profile/{id}", name="profileEdit")
     * @Method("PUT") 
     */
    public function profileEditAction() {    	
    	$user = $this->getUser();    	
    	
    	$obj = $this->getReqObj();
		$user->setName($obj->name);		
		
		$em = $this->getDoctrine()->getManager();
		$em->persist($user);
		$em->flush();
		
		return new JsonResponse($obj);		    	
    }
}
