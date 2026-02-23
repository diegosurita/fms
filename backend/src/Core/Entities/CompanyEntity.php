<?php

namespace FMS\Core\Entities;

class CompanyEntity
{
	private ?int $id = null;
	private string $name;
	private ?\DateTimeInterface $createdAt = null;
	private ?\DateTimeInterface $updatedAt = null;

	public function getId(): ?int
	{
		return $this->id;
	}

	public function setId(?int $id): void
	{
		$this->id = $id;
	}

	public function getName(): string
	{
		return $this->name;
	}

	public function setName(string $name): void
	{
		$this->name = $name;
	}

	public function getCreatedAt(): ?\DateTimeInterface
	{
		return $this->createdAt;
	}

	public function setCreatedAt(?\DateTimeInterface $createdAt): void
	{
		$this->createdAt = $createdAt;
	}

	public function getUpdatedAt(): ?\DateTimeInterface
	{
		return $this->updatedAt;
	}

	public function setUpdatedAt(?\DateTimeInterface $updatedAt): void
	{
		$this->updatedAt = $updatedAt;
	}
}
