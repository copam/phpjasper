# PHPJasper
##### Por favor confira a versão atualizada desta lib em: https://github.com/PHPJasper/phpjasper
[![License](https://poser.pugx.org/copam/phpjasper/license)](https://packagist.org/packages/copam/phpjasper) [![Total Downloads](https://poser.pugx.org/copam/phpjasper/downloads)](https://packagist.org/packages/copam/phpjasper)

**Nota para servidores Linux**

Não esqueça de fornecer permissão 777 para o diretório **/vendor/copam/phpjasper/src/JasperStarter/bin** e o arquivo binário **jasperstarter**

## Introdução
Este pacote é a solução perfeita para compilar e processar relatórios Jasper (.jrxml & .jasper) com PHP puro ou através do Laravel Framework.

### Por quê preciso do PHPJasper?

Alguma vez você precisou de um relatório complexo em PHP para seu sistema web?

A maioria das soluções é complexa e você precisa escrever *HTML* + *CSS* para gerar um *PDF*, isso não faz sentido, além de ser muito trabalhoso :)

Apresento para vocês **JasperReports** a melhor solução open source que existe para relatórios.

### O que eu posso fazer com isso?

**Texto tirado do site JasperSoft:**

> A biblioteca JasperReports é o mecanismo de geração de relatórios de código aberto mais popular do mundo. É inteiramente escrito em Java e é capaz de usar dados provenientes de qualquer tipo de fonte de dados e gerar documentos perfeitos que podem ser visualizado, impressom ou exportadom em uma variedade de formatos de documentos, incluindo HTML, PDF, Excel, OpenOffice e Word .

*Exemplos do que você pode fazer:*

* Faturas
* Relatórios
* Listas

## Requisitos

* Java JDK 1.8
* PHP [exec()](http://php.net/manual/function.exec.php)
* [opcional] [Mysql Connector](http://dev.mysql.com/downloads/connector/j/) (se você pretende usar essa base dados)
* [opcional] [PostgreSQL Connector](https://jdbc.postgresql.org/download.html) (se você pretende usar essa base dados)
* [opcional] [Jaspersoft Studio](http://community.jaspersoft.com/project/jaspersoft-studio) (para criar e compilar seus relatórios)

### Notas sobre o Java

Verifique se o Java está instalado executando o comando:

```
$ java -version
java version "1.8.0_101"
Java(TM) SE Runtime Environment (build 1.8.0_101-b13)
Java HotSpot(TM) 64-Bit Server VM (build 25.101-b13, mixed mode)
```

Se você obter esse retorno:

    command not found: java

Instale o java no: (Ubuntu/Debian)

    $ sudo apt-get install default-jdk

Para instalar no: (centOS/Fedora)

    # yum install java-1.8.0-openjdk.x86_64

Para o windows siga o link-> [JDK](http://www.oracle.com/technetwork/pt/java/javase/downloads/jdk8-downloads-2133151.html) e procure a versão mais apropriada para seu Sistema Operacional.

Execute o novamente o comando `java -version` e verifique se a saída está ok.

## Instalação

1. Instale o [Composer](http://getcomposer.org) se você ainda não possui e então rode o comando:
```
composer require copam/phpjasper
```
Crie um arquivo 'composer.json' e escreva o seguinte código:

```javascript
{
    "require": {
        "copam/phpjasper": "1.*"
    }
}
```

Rode o comando:

    composer install

Você acaba de instalar PHPJasper

## Exemplos

### O exemplo *Hello World*.

Vá para o diretório de exemplos na raiz do repositório (`vendor/copam/phpjasper/examples`).
Abra o arquivo `hello_world.jrxml` com o JasperStudio ou seu editor favorito  e dê uma olhada no código.

#### Compilando

Primeiro precisamos compilar o arquivo com a extensão `.JRXML` em um arquivo binário do tipo `.JASPER`

**Nota:** Caso você não queira usar *Jaspersoft Studio*. É possivel compilar o seu arquivo .jrxml da seguinte forma:

```php

require __DIR__ . '/vendor/autoload.php';

use JasperPHP\JasperPHP;

$input = __DIR__ . '/vendor/copam/phpjasper/examples/hello_world.jrxml';   

$jasper = new JasperPHP;
$jasper->compile($input)->execute();
```

Esta comando compila o arquivo fonte `hello_world.jrxml` em um arquivo `hello_world.jasper`

#### Processing

Agora vamos processar o nosso relatório:

```php

require __DIR__ . '/vendor/autoload.php';

use JasperPHP\JasperPHP;

$input = __DIR__ . '/vendor/copam/phpjasper/examples/hello_world.jasper';  
$output = __DIR__ . '/vendor/copam/phpjasper/examples';    

$jasper = new JasperPHP;

$jasper->process(
    $input,
    $output,
    array("pdf", "rtf")
)->execute();
```

Agora olhe a pasta examples! :) Ótimo trabalho? Você tem  2 arquivos, `hello_world.pdf` e `hello_world.rtf`.

Check the *API* of the  `compile` and `process` functions in the file `src/JasperPHP/JasperPHP.php` file.

#### Listando parâmetros

Consultando o arquivo jasper para examinar os parâmetros disponíveis no relatório:

```php

require __DIR__ . '/vendor/autoload.php';

use JasperPHP\JasperPHP;

$input = __DIR__ . '/vendor/copam/phpjasper/examples/hello_world_params.jrxml';

$jasper = new JasperPHP;
$output = $jasper->list_parameters($input)->execute();

foreach($output as $parameter_description)
    print $parameter_description . '<pre>';
```

### Relatórios a partir de um banco de dados

Adicione os parâmetros específicos para seu banco de dados

```php

require __DIR__ . '/vendor/autoload.php';

use JasperPHP\JasperPHP;    

$input = __DIR__ . '/vendor/copam/phpjasper/examples/hello_world.jrxml';   
$output = __DIR__ . '/vendor/copam/phpjasper/examples';    

$jasper = new JasperPHP;
$jasper->process(
    $input,
    $output,
    array("pdf", "rtf"),
    array("php_version" => phpversion()),
    array(
        'driver' => 'postgres',
        'username' => 'vagrant',
        'host' => 'localhost',
        'database' => 'samples',
        'port' => '5432',
    ), 'pt_BR' //locale                      
)->execute();
```

### Usando JasperPHP com Laravel 5.*

1. Instale o  [Composer](http://getcomposer.org)
```
composer require copam/phpjasper
```
Crie um arquivo 'composer.json':

```javascript
{
    "require": {
        "copam/phpjasper": "1.*"
    }
}
```
2. Rode:

    **composer update**

3. Adicione o provider ao array providers em config/app.php:

    **JasperPHP\JasperPHPServiceProvider::class,**

4. Crie a pasta **/report** em **/public directory**

5. Copie o arquivo **hello_world.jrxml** em **/vendor/copam/phpjasper/examples** para a pasta: **/public/report**

6. Rode **php artisan serve**

7. Acesse **localhost:8000/reports**

8. Verifique a pasta **/public/report**. Você tem 3 arquivos, `hello_world.pdf`, `hello_world.rtf` e `hello_world.xml`.

**Copie o código abaixo para seu arquivo route.php**

```php
use JasperPHP\JasperPHP;

Route::get('/reports', function () {
    
    $output = public_path() . '/report/'.time().'_hello_world';
    $report = new JasperPHP;
    $report->process(
        public_path() . '/report/hello_world.jrxml', 
        $output, 
        array('pdf', 'rtf', 'xml'),
        array(),
        array(),
        'pt_BR' //locale  
        )->execute();
});
```
Neste exemplo nós geramos 3 arquivos: pdf, rtf and xml.


### Relatórios a partir de um xml em PHP/Laravel 5.*

Veja como é fácil gerar um relatório com uma origem de um arquivo XML

```php

use JasperPHP\JasperPHP;

public function xmlToPdf()
    {
        $output = public_path() . '/report/'.time().'_CancelAck';
        $ext = "pdf";
        $data_file = public_path() . '/report/CancelAck.xml';
        $driver = 'xml';
        $xml_xpath = '/CancelResponse/CancelResult/ID';
        
        $php_jasper = new JasperPHP;
        
        $php_jasper->process(
            public_path() . '/report/CancelAck.jrxml',
            $output,
            array($ext),
            array(),
            array('data_file' => $data_file, 'driver' => $driver, 'xml_xpath' => $xml_xpath))->execute();
    
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename='.time().'_CancelAck.'.$ext);
        header('Content-Transfer-Encoding: binary');
        header('Expires: 0');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Content-Length: ' . filesize($output.'.'.$ext));
        flush();
        readfile($output.'.'.$ext);
        unlink($output.'.'.$ext);
    }
```
**Nota:** 

Para usar os exemplos acima você precisa de uma cópia dos arquivos localizados em:

**\vendor\copam\phpjasper\examples\CancelAck.jrxml** 
e
**\vendor\copam\phpjasper\examples\CancelAck.xml** 
para a pasta:
**\public\report** 


### Relatórios a partir de um arquivo JSON em PHP/Laravel 5.*

Veja como é fácil gerar um relatório com uma fonte de um arquivo JSON:

```php

use JasperPHP\JasperPHP;

public function jsonToPdf()
    {
        $output = public_path() . '/report/'.time().'_Contacts';
        $ext = "pdf";
        $driver = 'json';
        $json_query= "contacts.person";
        $data_file = public_path() . '/report/contacts.json';
            
        $php_jasper = new JasperPHP;
        
        $php_jasper->process(
            public_path() . '/report/json.jrxml',
            $output,
            array($ext),
            array(),
            array('data_file' => $data_file, 'driver' => $driver, 'json_query' => $json_query))->execute();
    
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename='.time().'_Contacts.'.$ext);
        header('Content-Transfer-Encoding: binary');
        header('Expires: 0');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Content-Length: ' . filesize($output.'.'.$ext));
        flush();
        readfile($output.'.'.$ext);
        unlink($output.'.'.$ext);
    }
```
**Nota:**

Para usar os exemplos acima você precisa de uma cópia dos arquivos localizados em:

**\vendor\copam\phpjasper\examples\json.jrxml**
e
**\vendor\copam\phpjasper\examples\contacts.json**
para a pasta:
**\public\report**


### MySQL

Nós incluimos [MySQL connector](http://dev.mysql.com/downloads/connector/j/) (v5.1.39) na pasta `/src/JasperStarter/jdbc/`

### PostgreSQL

Nós incluimos [PostgreSQL](https://jdbc.postgresql.org/) (v9.4-1203) na pasta`/src/JasperStarter/jdbc/`

### MSSQL

[Microsoft JDBC Drivers 6.0, 4.2, 4.1, and 4.0 for SQL Server
](https://www.microsoft.com/en-us/download/details.aspx?displaylang=en&id=11774).

## Performance

Varia de acordo com o tamanho do seu relatório

## Agradecimentos

A [Cenote GmbH](http://www.cenote.de/) por [JasperStarter](http://jasperstarter.sourceforge.net/).

A [JetBrains](https://www.jetbrains.com/) pelo [PhpStorm](https://www.jetbrains.com/phpstorm/) e todas as ótimas soluções.

## Dúvidas?

Abra uma [Issue](https://github.com/copam/phpjasper/issues), ou pesquise por Issues antigas.

## Licença

MIT

## Contribuição

Contribua com a comunidade PHP e Laravel, fique a vontade para fazer um fork!!
