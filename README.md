# PHP Arquivei NFe API

This is a simple package that allows access to the Arquivei NFe API.

## Information
Package create for Arquivei NFe

Documentation -
[https://docs.arquivei.com.br/?urls.primaryName=Arquivei%20API](https://docs.arquivei.com.br/?urls.primaryName=Arquivei%20API) 

## Installation

Import private package 
```
"repositories": [
        {
            "type": "vcs",
            "url": "ssh://git@github.com/medeirosdev/arquivei-nfe.git"
        }
    ],
```
Install the package using composer:
```bash
$ export COMPOSER_MEMORY_LIMIT=-1
$ composer require medeirosdev/arquivei-nfe
```

## Frameworks

At the moment we only have framework compatibility for Laravel. However, we welcome PRs to add further framework
specific behavior as long as it doesn't prevent the package working for others

### Laravel

If you are using Laravel then you can use our service provider. If you have Laravel >5.5 then the package
will be auto discovered upon install. Else, add the following to your `config/app.php` file:

```php
'providers' => [
    ...
    \MedeirosDev\Arquivei\Frameworks\Laravel\ArquiveiServiceProvider::class,
]
```

#### Facades

If you are using Laravel >5.5 then the facade will
be automatically discovered. Else, you can add it in your `config/app.php` file.

```php
'aliases' => [
    ...
    'Arquivei' => \MedeirosDev\Arquivei\Frameworks\Laravel\Arquivei::class,
]
```
#### Configuration

First, make sure you have copied the configuration file:

```
$ php artisan vendor:publish --tag=config --provider="MedeirosDev\Arquivei\Frameworks\Laravel\ArquiveiServiceProvider"
```

This will make a `config/arquivei.php` file, this is where your API Key / License information is fetched from.
By default we use the `.env` configuration values to get your API key.

Use the App ID and App Code then you should add
the following to your `.env`:

```
ARQUIVEI_BASE_URL=MY-BASE-URL
ARQUIVEI_VERSION=MY-API-VERSION
ARQUIVEI_API_ID=MY-API-ID
ARQUIVEI_API_KEY=MY-API-KEY
```

Please, make sure you don't store your keys in version control!

## Usage

#### License / API Key

Before making requests you need to create your License object.
You will need is your API key, then you can create your license as follows:
```php
$license = new License($apiId, $apiKey);
```

Then, you can start making your request:
```php
$arquivei = new Arquivei;
$arquivei->setLicense($license);

// or Laravel framework license is auto generated

$request = new Arquivei;
```

#### Basic usage

```php
$page = 0;

$response = (new Arquivei)
            ->setLicense($license)
            ->request($page);

// Get data with list NFe  
$response->data

// Or key access_key for first NFe
$response->data[0]->access_key
```

#### Basic usage in Laravel
##### No need to enter license object

```php
$response = (new Arquivei)->request();
```


#### Supported Method
request
```php
$arquivei = new Arquivei;
$response = $arquivei->request();
```

parse(int $cursor = 0)
```php
$arquivei = new Arquivei;
$cursor = 0;
$response = $arquivei->request($cursor);
$listNfe = $arquivei->parse($response);
```

store(XmlParser ...$listNfe)

Example Store implementing StoreInterface
```php
class MyStore implements StoreInterface {
    public function store (XmlParser $nfe): bool
    {
        Storage::disk('local')->put($nfe->accessKey . '.xml', $nfe->xml);
        return true;
    }
}
```

```php
$arquivei = new Arquivei;
$response = $arquivei->request();
$listNfe = $arquivei->parse($response);

$arquivei->setStore(new MyStore());
$store = $arquivei->store(...$ListNfe)
```

store(XmlParser ...$listNfe)
```php
$arquivei = new Arquivei(new MyStore());
$response = $arquivei->request();
$listNfe = $arquivei->parse($response);

$store = $arquivei->store(...$ListNfe)
```

requestAll
```php
$arquivei = new Arquivei;
$responses = $arquivei->requestAll();
```

requestAllAndStore
```php
$arquivei = new Arquivei(new MyStore());
$responses = $arquivei->requestAllAndStore();
```