<?php

namespace FMS\Core\Entities;

class FundEntity
{
	private ?int $id = null;
	private string $name;
	private int $startYear;
	private int $managerId;
	/** @var string[] */
	private array $aliases = [];
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

	public function getStartYear(): int
	{
		return $this->startYear;
	}

	public function setStartYear(int $startYear): void
	{
		$this->startYear = $startYear;
	}

	public function getManagerId(): int
	{
		return $this->managerId;
	}

	public function setManagerId(int $managerId): void
	{
		$this->managerId = $managerId;
	}

	/**
	 * @return string[]
	 */
	public function getAliases(): array
	{
		return $this->aliases;
	}

	/**
	 * @param string[] $aliases
	 */
	public function setAliases(array $aliases): void
	{
		$this->aliases = $aliases;
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
