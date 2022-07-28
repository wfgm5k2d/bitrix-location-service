# bitrix-location-service

<h2> Описание </h2>
<p>
  Библиотека для получения и обработки местоположений Битрикс. На каждый ключ возвращаемого массива есть свой метод.
  Основной способ получения данных по IP - сайт https://ipwhois.io/
  Используются их методы для автоматического определения местоположения человека
  Дольше вся обработка идет через Битрикс
  Каждый класс можно использовать по отдельности избавившись от зависимостей. Функции так же можно использовать по отдельности за исключением методов get<ПОЛЕ МАССИВА>
</p>
<h2>Установка</h2>

<pre>composer require wfgm5k2d/bitrix-location-service</pre>
<p>
Для тех кто первый раз сталкивается с composer на битриксе. Библиотеки обычно ставятся в local/php_interface (cd local/php_interface). Далее в local/php_interface/init.php прописывается:
</p>
<pre>require_once 'vendor/autoload.php';</pre>

<h2>Использование</h2>

```php
    <?php 
    // Получить данные о местоположении по реальномму IP пользователя
    $data = IpWhoIsService::getRealIp()->getDataResult();
    
    // Получить данные города из массива
    $city = $data->getCity();
    
    // Получить информацию о местоположении
    $obData = $data->getBxLocation($city);
    
    // Получить REGION_ID из массива ITEM
    $regionId = $obData->getItemRegionId();
    
    // Получить данные о местоположении региона по REGION_ID
    $obRegion = $obData->getBitrixRegion($regionId);
    
    // Получить название города из региона ['VALUE']['CITY_NAME']
    $sCity = $obData->getValueCityName();
    
    // Выбираем все города
    $arCity = $obData->searchLocationInBitrixLocations(BitrixLocationService::TYPE_CITY, $sCity);
    
    // Если город не нашелся ищем в селах
    $arVillage = $obData->searchLocationInBitrixLocations(BitrixLocationService::TYPE_VILLAGE, $sCity);
    
    // Совместим результаты
    $arCities = array_merge($arCity, $arVillage);
    
    // Получим индекс этого города
    BitrixLocationService::getZipLocation($arCities['ID']);
    
    // Получим полный путь до города
    BitrixLocationService::getFullPathToLocation($arCities);
    ?>
```

### Как привязать выбранное местоположение в оформление заказа. Продуктивное использование

```php
    <?php 
    $siteId = Context::getCurrent()->getSite(); // Получили site ID
    $currencyCode = CurrencyManager::getBaseCurrency(); // Метод возвращает код базовой валюты

    $obOrder = Order::create($siteId, $USER->GetID());
    $obOrder->setPersonTypeId(1); //Установим тип плательщика (Физическое лицо)
    $obOrder->setField('CURRENCY', $currencyCode);
    
    // Устанавливаем корзину пользователя в заказ
    // Получили текущую корзину
    $obBasket = Sale\Basket::loadItemsForFUser(Sale\Fuser::getId(), Bitrix\Main\Context::getCurrent()->getSite())->getBasket(); 
    $obOrder->setBasket($obBasket); // Установили корзину в заказ

    $propertyCollection = $obOrder->getPropertyCollection();
    $propertyCodeToId = array();

    foreach ($propertyCollection as $propertyValue)
        $propertyCodeToId[$propertyValue->getField('CODE')] = $propertyValue->getField('ORDER_PROPS_ID');

    if (!empty($arCity['CODE'])) {
        $propertyValue = $propertyCollection->getItemByOrderPropertyId($propertyCodeToId['LOCATION']);
        $propertyValue->setValue($arCity['CODE']);
    }
    ?>
```

## Описание классов
- IpWhoIsService
    - getRealIp
    - getDataResult
    - toArray
    - toObject
    - getBxLocation
    - getIp
    - isSuccess
    - getType
    - getContinent
    - getContinentCode
    - getCountry
    - getCountryCode
    - getRegion
    - getRegionCode
    - getCity
    - getLatitude
    - getLongitude
    - isEu
    - getZip
    - getCallingCode
    - getCapital
    - getBorders
    - getFlagImg
    - getFlagEmoji
    - getFlagEmojiUnicode
    - getConnectionAsn
    - getConnectionOrg
    - getConnectionIsp
    - getConnectionDomain
    - getTimezoneId
    - getTimezoneAbbr
    - getTimezoneIsDst
    - getTimezoneUtc
    - getTimezoneCurrentTime
- BitrixLocationService
    - getLocation
    - getBitrixRegion
    - searchLocationInBitrixLocations
    - getZipLocation
    - getFullPathToLocation
    - getItemId
    - getItemCode
    - getItemLeftMargin
    - getItemRightMargin
    - getItemDepthLevel
    - getItemSort
    - getItemParentId
    - getItemTypeId
    - getItemLatitude
    - getItemLongitude
    - getItemCountryId
    - getItemRegionId
    - getItemCityId
    - getItemLocDefault
    - getItemNameRu
    - getItemTypeCode
    - getValueId
    - getValueSort
    - getValueCode
    - getValueCountryName
    - getValueCountryShortName
    - getValueCountryId
    - getValueCountryNameOrig
    - getValueCountryNameLang
    - getValueRegionName
    - getValueRegionShortName
    - getValueRegionId
    - getValueRegionNameOrig
    - getValueRegionNameLang
    - getValueCityName
    - getValueCityShortName
    - getValueCityId
    - getValueCityNameOrig
    - getValueCityNameLang
    - getLocationId
    - getFormat
    - getRegionId
- BitrixLocationRegionService
    - getLocationRegion
    - getId
    - getCode
    - getLeftMargin
    - getRightMargin
    - getDepthLevel
    - getSort
    - getParentId
    - getTypeId
    - getLatitude
    - getLongitude
    - getCountryId
    - getRegionId
    - getCityId
    - getLocDefault
    - getNameRu
    - getTypeCode
