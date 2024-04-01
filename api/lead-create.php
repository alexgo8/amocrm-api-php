<?php

use AmoCRM\Client\AmoCRMApiClient;
use AmoCRM\Collections\CustomFieldsValuesCollection;
use AmoCRM\Collections\TagsCollection;
use AmoCRM\Models\CustomFieldsValues\TextCustomFieldValuesModel;
use AmoCRM\Models\CustomFieldsValues\ValueCollections\TextCustomFieldValueCollection;
use AmoCRM\Models\CustomFieldsValues\ValueModels\TextCustomFieldValueModel;
use AmoCRM\Models\LeadModel;
use AmoCRM\Models\TagModel;
use League\OAuth2\Client\Token\AccessToken;
use Symfony\Component\Dotenv\Dotenv;

include_once '../vendor/autoload.php';

if (!isset($_POST['name']) || !isset($_POST['phone']) || !isset($_POST['comment'])) {
  exit('INVALID REQUEST');
}

$dotenv = new Dotenv;
$dotenv->load('../.env');

//токен
$apiClient = new AmoCRMApiClient(
  $_ENV['CLIENT_ID'],
  $_ENV['CLIENT_SECRET'],
  $_ENV['CLIENT_REDIRECT_URI']
);
$rawToken = json_decode(file_get_contents('../token.json'), 1);
$token = new AccessToken($rawToken);
$apiClient->setAccessToken($token)
  ->setAccountBaseDomain($_ENV['ACCOUNT_DOMAIN']);


//Создание сущности  
$lead = new LeadModel();
//Создаём название сделки
$lead->setName("Заявка с сайта " . date("Y-m-d H:i:s"));

$lead->setId(1);
//Создадим коллекцию полей сущности
$leadCustomFieldsValues = new CustomFieldsValuesCollection();
//Создадим модель значений поля типа текст
$textCustomFieldValuesModel = new TextCustomFieldValuesModel();
//Укажем ID поля
$textCustomFieldValuesModel->setFieldId($_ENV['NAME_FIELD_ID']);
//Добавим значения
$textCustomFieldValuesModel->setValues(
    (new TextCustomFieldValueCollection())
        ->add((new TextCustomFieldValueModel())->setValue($_POST['name']))
);
//Добавим значение в коллекцию полей сущности
$leadCustomFieldsValues->add($textCustomFieldValuesModel);
//Установим в сущности эти поля
$lead->setCustomFieldsValues($leadCustomFieldsValues);


$lead->setId(2);
$textCustomFieldValuesModel = new TextCustomFieldValuesModel();
$textCustomFieldValuesModel->setFieldId($_ENV['PHONE_FIELD_ID']);
$textCustomFieldValuesModel->setValues(
  (new TextCustomFieldValueCollection())
    ->add((new TextCustomFieldValueModel())->setValue($_POST['phone']))
);
$leadCustomFieldsValues->add($textCustomFieldValuesModel);
$lead->setCustomFieldsValues($leadCustomFieldsValues);


$lead->setId(3);
$textCustomFieldValuesModel = new TextCustomFieldValuesModel();
$textCustomFieldValuesModel->setFieldId($_ENV['COMMENT_FIELD_ID']);
$textCustomFieldValuesModel->setValues(
  (new TextCustomFieldValueCollection())
    ->add((new TextCustomFieldValueModel())->setValue($_POST['comment']))
);
$leadCustomFieldsValues->add($textCustomFieldValuesModel);
$lead->setCustomFieldsValues($leadCustomFieldsValues);


$lead->setId(4);
$textCustomFieldValuesModel = new TextCustomFieldValuesModel();
$textCustomFieldValuesModel->setFieldId($_ENV['SOURCE_FIELD_ID']);
$textCustomFieldValuesModel->setValues(
  (new TextCustomFieldValueCollection())
    ->add((new TextCustomFieldValueModel())->setValue('Сайт'))
);
$leadCustomFieldsValues->add($textCustomFieldValuesModel);
$lead->setCustomFieldsValues($leadCustomFieldsValues);


//добавляем теги
$lead->setId(5);
$lead->setTags((new TagsCollection())
    ->add(
      (new TagModel())
        ->setName('сайт')
    )
);


$lead = $apiClient->leads()->addOne($lead);

echo "Сделка успешно добавлена. LEAD_ID(Сделка_ID): " . $lead->getId();