<?php


class Organisatie
{
    private int $id;
    private string $name;
    private string $address;
    private string $postcode;
    private string $discription;
    private int $users_Id;

    /**
     * Organisatie constructor.
     * @param int $id
     * @param string $name
     * @param string $address
     * @param string $postcode
     * @param string $discription
     * @param int $users_Id
     */
    public function __construct(int $id, string $name, string $address, string $postcode, string $discription, int $users_Id)
    {
        $this->id = $id;
        $this->name = $name;
        $this->address = $address;
        $this->postcode = $postcode;
        $this->discription = $discription;
        $this->users_Id = $users_Id;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId(int $id): void
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getAddress(): string
    {
        return $this->address;
    }

    /**
     * @param string $address
     */
    public function setAddress(string $address): void
    {
        $this->address = $address;
    }

    /**
     * @return string
     */
    public function getPostcode(): string
    {
        return $this->postcode;
    }

    /**
     * @param string $postcode
     */
    public function setPostcode(string $postcode): void
    {
        $this->postcode = $postcode;
    }

    /**
     * @return string
     */
    public function getDiscription(): string
    {
        return $this->discription;
    }

    /**
     * @param string $discription
     */
    public function setDiscription(string $discription): void
    {
        $this->discription = $discription;
    }

    /**
     * @return int
     */
    public function getUsersId(): int
    {
        return $this->users_Id;
    }

    /**
     * @param int $users_Id
     */
    public function setUsersId(int $users_Id): void
    {
        $this->users_Id = $users_Id;
    }




}