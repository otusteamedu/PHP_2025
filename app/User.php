<?php declare(strict_types=1);

namespace DMapper;

class User
{
	private $id;
	private $name;
	private $email;
	private $createdAt;

	public function __construct($id, $name, $email, $createdAt)
	{
		$this->id = $id;
		$this->name = $name;
		$this->email = $email;
		$this->createdAt = $createdAt;
	}

	// Геттеры
	public function getId()
	{
		return $this->id;
	}

	public function getName()
	{
		return $this->name;
	}

	public function getEmail()
	{
		return $this->email;
	}

	public function getCreatedAt()
	{
		return $this->createdAt;
	}

	// Сеттеры
	public function setId($id)
	{
		$this->id = $id;
	}

	public function setName($name)
	{
		$this->name = $name;
	}

	public function setEmail($email)
	{
		$this->email = $email;
	}

}
