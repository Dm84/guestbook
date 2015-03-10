<?php

namespace AppBundle\Model;

use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\EquatableInterface;
use AppBundle\Entity\User;

class AppUser implements UserInterface, EquatableInterface  {
	
	/**
	 * 
	 * @var User
	 */
	private $user;
		
	public function __construct(User $user)
	{
		$this->user = $user;
	}
	
	public function __get($name)
	{
		switch ($name) {
			case 'id': return $this->user->getId();
			default: throw \Exception('uknown property: ' + $name); 
		}
	}
	
	public function getUsername() {
		return $this->user->getUsername();
	}
	
	public function getPassword() {
		return $this->user->getPassword();
	}
	
	/**
	 * (non-PHPdoc)
	 * @see \Symfony\Component\Security\Core\User\UserInterface::getRoles()
	 */
	public function getRoles() {
		return array('ROLE_USER');
	}
	
	public function getSalt() {
		return null;
	}
	
	/**
	 * (non-PHPdoc)
	 * 
	 * @see \Symfony\Component\Security\Core\User\UserInterface::eraseCredentials()
	 */
	public function eraseCredentials() {
	}
	
	/**
	 * (non-PHPdoc)
	 * 
	 * @see \Symfony\Component\Security\Core\User\EquatableInterface::isEqualTo()
	 */
	public function isEqualTo(UserInterface $user) {
		if (! $user instanceof AppUser) {
			return false;
		}
		
		if ($this->user->getPassword() !== $user->getPassword ()) {
			return false;
		}
		
		if ($this->user->getUsername() !== $user->getUsername ()) {
			return false;
		}
		
		return true;
	}
}