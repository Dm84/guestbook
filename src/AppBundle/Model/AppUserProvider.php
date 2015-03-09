<?php

namespace AppBundle\Model;

use HWI\Bundle\OAuthBundle\Security\Core\User\EntityUserProvider;
use Doctrine;
use AppBundle;

class AppUserProvider extends EntityUserProvider {
	
	public function __construct(Doctrine\Bundle\DoctrineBundle\Registry $registry) {
		parent::__construct($registry, 'AppBundle\Entity\User', ['facebook' => 'facebook']);
	}
	
	public function loadUserByOAuthUserResponse(UserResponseInterface $response)
	{
		$resourceOwnerName = $response->getResourceOwner()->getName();
	
		if (!isset($this->properties[$resourceOwnerName])) {
			throw new \RuntimeException(sprintf("No property defined for entity for resource owner '%s'.", $resourceOwnerName));
		}
	
		$username = $response->getUsername();
		if (null === $user = $this->repository->findOneBy(array($this->properties[$resourceOwnerName] => $username))) {
			//не нашли? тогда создадим сами нового
						  
		}
	
		return $user;
	}
	
}