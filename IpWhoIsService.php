<?php

/**
 * @see https://ipwhois.io/ru/documentation#endpoint
 * Класс для определения местоположения через https://ipwhois.io/
 * Библиотека обладает базовым набором функционала для бесплатной версии
 */
class IpWhoIsService
{
    /**
     * @var string
     */
    private $sIpAddress = '';

    /**
     * @var null
     */
    private $sIpWhoIs = null;

    /**
     * Получим реальный IP из метода Битрикс
     */
    public function __construct()
    {
        $this->sIpAddress = \Bitrix\Main\Service\GeoIp\Manager::getRealIp();
    }

    /**
     * @return IpWhoIsService
     */
    public static function getRealIp() {
        return new IpWhoIsService();
    }

    /**
     * @param $sIp
     * @param $sLang
     * @return $this
     */
    public function getDataResult($sIp = '', $sLang = 'ru') {
        $sIp = $sIp ?? $this->sIpAddress;

        $ch = curl_init('http://ipwho.is/' . $sIp . '?lang=' . $sLang);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, false);
        $this->sIpWhoIs = curl_exec($ch);
        curl_close($ch);

        return $this;
    }

    /**
     * @return mixed
     * Преобразуем результат в ассоциативный массив
     */
    public function toArray() {
        return json_decode($this->sIpWhoIs, true);
    }

    /**
     * @return mixed
     * Преобразуем результат в объект
     */
    public function toObject() {
        return json_decode($this->sIpWhoIs);
    }

    /**
     * @param $sSearchParam
     * @param $nPageSize
     * @param $page
     * @return mixed
     */
    public function getBxLocation($sSearchParam, $nPageSize = 10, $page = 1) {
        return BitrixLocationService::getLocation($sSearchParam, $nPageSize, $page);
    }

    /**
     * @return mixed
     */
    public function getIp() {
        return json_decode($this->sIpWhoIs)->ip;
    }

    /**
     * @return mixed
     */
    public function isSuccess() {
        return json_decode($this->sIpWhoIs)->success;
    }

    /**
     * @return mixed
     */
    public function getType() {
        return json_decode($this->sIpWhoIs)->type;
    }

    /**
     * @return mixed
     */
    public function getContinent() {
        return json_decode($this->sIpWhoIs)->continent;
    }

    /**
     * @return mixed
     */
    public function getContinentCode() {
        return json_decode($this->sIpWhoIs)->continent_code;
    }

    /**
     * @return mixed
     */
    public function getCountry() {
        return json_decode($this->sIpWhoIs)->country;
    }

    /**
     * @return mixed
     */
    public function getCountryCode() {
        return json_decode($this->sIpWhoIs)->country_code;
    }

    /**
     * @return mixed
     */
    public function getRegion() {
        return json_decode($this->sIpWhoIs)->region;
    }

    /**
     * @return mixed
     */
    public function getRegionCode() {
        return json_decode($this->sIpWhoIs)->region_code;
    }

    /**
     * @return mixed
     */
    public function getCity() {
        return json_decode($this->sIpWhoIs)->city;
    }

    /**
     * @return mixed
     */
    public function getLatitude() {
        return json_decode($this->sIpWhoIs)->latitude;
    }

    /**
     * @return mixed
     */
    public function getLongitude() {
        return json_decode($this->sIpWhoIs)->longitude;
    }

    /**
     * @return mixed
     */
    public function isEu() {
        return json_decode($this->sIpWhoIs)->is_eu;
    }

    /**
     * @return mixed
     */
    public function getZip() {
        return json_decode($this->sIpWhoIs)->postal;
    }

    /**
     * @return mixed
     */
    public function getCallingCode() {
        return json_decode($this->sIpWhoIs)->calling_code;
    }

    /**
     * @return mixed
     */
    public function getCapital() {
        return json_decode($this->sIpWhoIs)->capital;
    }

    /**
     * @return mixed
     */
    public function getBorders() {
        return json_decode($this->sIpWhoIs)->borders;
    }

    /**
     * @return mixed
     */
    public function getFlagImg() {
        return json_decode($this->sIpWhoIs)->flag->img;
    }

    /**
     * @return mixed
     */
    public function getFlagEmoji() {
        return json_decode($this->sIpWhoIs)->flag->emoji;
    }

    /**
     * @return mixed
     */
    public function getFlagEmojiUnicode() {
        return json_decode($this->sIpWhoIs)->flag->emoji_unicode;
    }

    /**
     * @return mixed
     */
    public function getConnectionAsn() {
        return json_decode($this->sIpWhoIs)->connection->asn;
    }

    /**
     * @return mixed
     */
    public function getConnectionOrg() {
        return json_decode($this->sIpWhoIs)->connection->org;
    }

    /**
     * @return mixed
     */
    public function getConnectionIsp() {
        return json_decode($this->sIpWhoIs)->connection->isp;
    }

    /**
     * @return mixed
     */
    public function getConnectionDomain() {
        return json_decode($this->sIpWhoIs)->connection->domain;
    }

    /**
     * @return mixed
     */
    public function getTimezoneId() {
        return json_decode($this->sIpWhoIs)->timezone->id;
    }

    /**
     * @return mixed
     */
    public function getTimezoneAbbr() {
        return json_decode($this->sIpWhoIs)->timezone->abbr;
    }

    /**
     * @return mixed
     */
    public function getTimezoneIsDst() {
        return json_decode($this->sIpWhoIs)->timezone->is_dst;
    }

    /**
     * @return mixed
     */
    public function getTimezoneUtc() {
        return json_decode($this->sIpWhoIs)->timezone->utc;
    }

    /**
     * @return mixed
     */
    public function getTimezoneCurrentTime() {
        return json_decode($this->sIpWhoIs)->timezone->current_time;
    }
}