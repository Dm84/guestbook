<?php

namespace AppBundle\Model;

use HWI\Bundle\OAuthBundle\Security\Core\User\EntityUserProvider;
use Doctrine;
use AppBundle\Entity\User;
use HWI\Bundle\OAuthBundle\OAuth\Response\UserResponseInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class AppUserProvider extends EntityUserProvider {
	
	public function __construct(Doctrine\Bundle\DoctrineBundle\Registry $registry) {
		parent::__construct($registry, 'AppBundle\\Entity\\User', ['facebook' => 'facebook', 'identifier' => 'id']);
	}
	
// 	public function loadUserByUsername($username) {
// 		return new AppUser(parent::loadUserByUsername($username));
// 	}	
	
// 	/**
// 	 * (non-PHPdoc)
// 	 * @see \HWI\Bundle\OAuthBundle\Security\Core\User\EntityUserProvider::refreshUser()
// 	 */
// 	public function refreshUser(UserInterface $user) {
// 		return new AppUser(parent::refreshUser($user));
// 	}
	
	/**
	 * {@inheritdoc}
	 */	
	public function loadUserByOAuthUserResponse(UserResponseInterface $response)
	{
		$resourceOwnerName = $response->getResourceOwner()->getName();
	
		if (!isset($this->properties[$resourceOwnerName])) {
			throw new \RuntimeException(sprintf("No property defined for entity for resource owner '%s'.", $resourceOwnerName));
		}
	
		$username = $response->getUsername();
		$user = $this->repository->findOneBy(array($this->properties[$resourceOwnerName] => $username));
		
		if ($user === null) 
		{
			//не нашли? тогда создадим сами
			$user = new User();
			$user->setFacebook($username);
			$user->setUsername($username);
			$user->setPassword('');
			
			$this->em->persist($user);
			$this->em->flush();
		}
	
		return $user;
	}

	public function supportsClass($class)
	{
		return $class === 'AppBundle\\Entity\\User';
	}	
}