<?php

use AmoCRM\Client\AmoCRMApiClient;
use League\OAuth2\Client\Token\AccessToken;
use Symfony\Component\Dotenv\Dotenv;

include_once '../vendor/autoload.php';

$dotenv = new Dotenv;
$dotenv->load('../.env');

$apiClient = new AmoCRMApiClient(
  $_ENV['CLIENT_ID'],
  $_ENV['CLIENT_SECRET'],
  $_ENV['CLIENT_REDIRECT_URI']
);

$apiClient->setAccountBaseDomain($_ENV['ACCOUNT_DOMAIN']);
$rawToken = json_decode(file_get_contents('../token.json'), 1);
$token = new AccessToken($rawToken);
$apiClient->setAccessToken($token);


//обновление токена
$apiClient->onAccessTokenRefresh(function ($token) {
  file_put_contents('../token.json', json_encode($token->jsonSerialize(), JSON_PRETTY_PRINT));
});


$account = $apiClient->account()->getCurrent();

echo '<pre>';
print_r($account->toArray()) . PHP_EOL;


$subdomain = '461811211';
function printLink($method, $title, $subdomain) {
    echo '<br>';
    echo "<a href='https://$subdomain.amocrm.ru/$method' target='_blank'>$title</a>";
    echo '<br>';
}

printLink('api/v4/leads/custom_fields', 'Список utm меток', $subdomain);
printLink('api/v4/users', 'Список пользователей', $subdomain);
printLink('api/v4/contacts/custom_fields', 'Список полей контакта', $subdomain);

