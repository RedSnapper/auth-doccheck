<?php

namespace RedSnapper\DocCheck;

use Illuminate\Support\Arr;

class DocCheckUser
{

    public function __construct(private string $id, private array $data = [])
    {

    }

    public function getId(): string
    {
        return $this->id;
    }

    public function raw(): array
    {
        return $this->data;
    }

    public function getSalutation(): ?string
    {
        return $this->retrieveData("anrede");
    }

    public function getGender(): ?string
    {
        return $this->retrieveData("gender");
    }

    public function getTitle(): ?string
    {
        return $this->retrieveData("titel");
    }

    public function getFirstName(): ?string
    {
        return $this->retrieveData('vorname');
    }

    public function getLastName(): ?string
    {
        return $this->retrieveData('name');
    }

    public function getStreet(): ?string
    {
        return $this->retrieveData('strasse');
    }

    public function getCountryISOCode(): ?string
    {
        return $this->retrieveData('land');
    }

    /**
     * http://www2.doccheck.com/service/info/codes.php?language=com&scope=language
     * @return string|null
     */
    public function getLanguageId(): ?string
    {
        return $this->retrieveData('language_id');
    }

    /**
     * http://www2.doccheck.com/service/info/codes.php?language=com&scope=profession
     * @return string|null
     */
    public function getProfessionId(): ?string
    {
        return $this->retrieveData('beruf');
    }

    /**
     * http://www2.doccheck.com/service/info/codes.php?language=com&scope=discipline
     * @return string|null
     */
    public function getDisciplineId(): ?string
    {
        return $this->retrieveData('fachgebiet');
    }

    /**
     * http://www2.doccheck.com/service/info/codes.php?language=com&scope=activity
     * @return string|null
     */
    public function getActivityId(): ?string
    {
        return $this->retrieveData('activity');
    }

    public function getEmail(): ?string
    {
        return $this->retrieveData('email');
    }


    /**
     * http://www2.doccheck.com/service/info/codes.php?language=de&scope=address_types
     * @return string|null
     */
    public function getAddressTypeId(): ?string
    {
        return $this->retrieveData('address_type');
    }

    public function isConfirmed(): bool
    {
        return $this->retrieveData("agreement", "0") == "1";
    }

    protected function retrieveData(string $key, $default = null)
    {
        return Arr::get($this->data, "dc_".$key, $default);
    }

}