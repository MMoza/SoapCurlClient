
# ?? SoapCurlClient

<p align="center">
  <img src="https://img.shields.io/badge/Version-1.0.0-blue" alt="Version">
  <img src="https://img.shields.io/badge/PHP-%3E%3D%208.0-green" alt="PHP Version">
  <img src="https://img.shields.io/badge/Status-Active-success" alt="Project Status">
</p>

## ?? **Descripci�n**

`SoapCurlClient` es una clase PHP dise�ada para realizar solicitudes SOAP utilizando cURL de manera eficiente. Su objetivo principal es simplificar el proceso de enviar y recibir solicitudes SOAP, utilizando cURL para manejar las conexiones y convirtiendo tanto las respuestas (responses) como las solicitudes (requests) a formato JSON para facilitar su manipulaci�n.

La clase permite realizar peticiones SOAP, gestionar las respuestas y registrar logs tanto en formato XML como en JSON, lo que optimiza el proceso de integraci�n con servicios SOAP y facilita el manejo de datos estructurados de manera moderna y eficiente.

---

## ?? **Estructura del Proyecto**

```bash
/project-root
??? /src
?   ??? SoapCurlClient.php              # Clase principal
?   ??? SoapCurlClientException.php     # Clase para el manejo de excepciones
??? /config
?   ??? curl_dev.php                    # Archivo de configuraci�n para opciones de cURL
?   ??? curl_prod.php                   # Archivo de configuraci�n para opciones de cURL
??? /logs
?   ??? request.log                     # Logs de solicitudes
?   ??? response.log                    # Logs de respuestas
??? /tests
?   ??? SoapCurlClientTest.php          # Pruebas unitarias
??? composer.json                       # Configuraci�n de Composer
??? README.md                           # Documentaci�n del proyecto
```

---

## ??? **Caracter�sticas**
- ? **Solicitudes SOAP:** Permite realizar solicitudes SOAP utilizando cURL.
- ?? **Manejo de Logs:** Registra tanto las solicitudes como las respuestas en formato XML y JSON.
- ?? **Conversi�n de Respuestas:** Convierte las respuestas SOAP a JSON, eliminando los namespaces.

---

## ?? **Requisitos**
- **PHP 8.0 o superior** ??
- Extensi�n **cURL** habilitada.

---

## ?? **Instalaci�n**

```bash
# Clonar el repositorio
git clone https://github.com/MMoza/SoapCurlClient.git

# Navegar al directorio del proyecto
cd SoapCurlClient

# Instalar dependencias (si las hubiera)
composer install
```

---

## ?? **Uso**

```php
require 'src/SoapCurlClient.php';

// URL del endpoint del servicio SOAP
$endpoint = 'https://example.com/soap';  // Cambia esta URL por la del servicio SOAP que desees utilizar

// Espacios de nombres (namespaces) para la solicitud SOAP, si es necesario. Puede dejarse vac�o o ajustar seg�n el servicio.
$namespaces = [
    // 'prefix' => 'namespace_url'  // Ejemplo: 'team' => 'http://example.com/namespace'
];

// Crear una instancia del cliente SOAP
$client = new SoapCurlClient($endpoint, $namespaces);

// Realizar la solicitud SOAP con el m�todo, par�metros y la acci�n SOAP correspondientes
$response = $client->call('MethodName', ['param1' => 'value1'], 'SOAPAction');

// Mostrar la respuesta obtenida
echo $response;
```

---

## ?? **Ejecutar Pruebas**

```bash
phpunit --bootstrap vendor/autoload.php tests/SoapCurlClientTest.php
```

---

## ?? **Registro de Logs**
- **Ubicaci�n:** `/logs/request.log` y `/logs/response.log`.
- **Formato:** XML y JSON.

---

## ?? **Archivo de Configuraci�n cURL**

Dentro del directorio `/config`, se encuentran dos archivos de configuraci�n: `curl_dev.php` y `curl_prod.php`. Estos archivos permiten definir y personalizar las opciones de cURL para las solicitudes SOAP, adaptadas para los entornos de desarrollo y producci�n, respectivamente. Esta organizaci�n facilita la configuraci�n de cURL seg�n el entorno de ejecuci�n y las necesidades del servicio con el que se est� integrando.

### Configuraci�n para el entorno de desarrollo (`curl_dev.php`)

```php
// /config/curl_dev.php

return [
    CURLOPT_TIMEOUT => 30,                          // Tiempo m�ximo de espera en segundos
    CURLOPT_RETURNTRANSFER => true,                 // Devolver como cadena en lugar de imprimir
    CURLOPT_FOLLOWLOCATION => true,                 // Seguir redirecciones
    CURLOPT_MAXREDIRS => 3,                         // M�ximo n�mero de redirecciones
    CURLOPT_SSL_VERIFYPEER => false,                // Desactivar verificaci�n SSL (�til para desarrollo)
    CURLOPT_USERAGENT => 'SoapCurlClient/1.0',      // Agregar un user-agent personalizado
    CURLOPT_HTTPHEADER => [
        'Content-Type: text/xml; charset=utf-8',    // Tipo de contenido de la solicitud
        'SOAPAction' => ''                           // Acci�n SOAP (se define al realizar la llamada)
    ]
];;
```

Este archivo se carga autom�ticamente cuando se realiza una solicitud, permitiendo que se apliquen las configuraciones de cURL para cada solicitud SOAP.

---

## ?? **Contribuciones**
�Las contribuciones son bienvenidas! Puedes hacer un fork del repositorio y enviar un pull request.

---

## ?? **Licencia**
Este proyecto est� bajo la licencia MIT. Puedes ver m�s detalles en el archivo `LICENSE`.

---

<p align="center">
    Hecho con ?? por Miguel �ngel Moza Barquilla(https://github.com/MMoza)
</p>
