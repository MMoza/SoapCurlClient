
# 🚀 SoapCurlClient

<p align="center">
  <img src="https://img.shields.io/badge/Version-1.0.0-blue" alt="Version">
  <img src="https://img.shields.io/badge/PHP-%3E%3D%208.0-green" alt="PHP Version">
  <img src="https://img.shields.io/badge/Status-Active-success" alt="Project Status">
</p>

## 🌟 **Descripción**

`SoapCurlClient` es una clase PHP diseñada para realizar solicitudes SOAP utilizando cURL de manera eficiente. Su objetivo principal es simplificar el proceso de enviar y recibir solicitudes SOAP, utilizando cURL para manejar las conexiones y convirtiendo tanto las respuestas (responses) como las solicitudes (requests) a formato JSON para facilitar su manipulación.

La clase permite realizar peticiones SOAP, gestionar las respuestas y registrar logs tanto en formato XML como en JSON, lo que optimiza el proceso de integración con servicios SOAP y facilita el manejo de datos estructurados de manera moderna y eficiente.

---

## 📂 **Estructura del Proyecto**

```bash
/project-root
├── /src
│   ├── SoapCurlClient.php              # Clase principal
│   └── SoapCurlClientException.php     # Clase para el manejo de excepciones
├── /config
│   └── curl_config.php                 # Archivo de configuración para opciones de cURL
├── /logs
│   ├── request.log                     # Logs de solicitudes
│   └── response.log                    # Logs de respuestas
├── /tests
│   └── SoapCurlClientTest.php          # Pruebas unitarias
├── composer.json                       # Configuración de Composer
└── README.md                           # Documentación del proyecto
```

---

## 🛠️ **Características**
- ✨ **Solicitudes SOAP:** Permite realizar solicitudes SOAP utilizando cURL.
- 📜 **Manejo de Logs:** Registra tanto las solicitudes como las respuestas en formato XML y JSON.
- 🔄 **Conversión de Respuestas:** Convierte las respuestas SOAP a JSON, eliminando los namespaces.

---

## 🚧 **Requisitos**
- **PHP 8.0 o superior** 🐘
- Extensión **cURL** habilitada.

---

## ⚙️ **Instalación**

```bash
# Clonar el repositorio
git clone https://github.com/MMoza/SoapCurlClient.git

# Navegar al directorio del proyecto
cd SoapCurlClient

# Instalar dependencias (si las hubiera)
composer install
```

---

## 🚀 **Uso**

```php
require 'src/SoapCurlClient.php';

// URL del endpoint del servicio SOAP
$endpoint = 'https://example.com/soap';  // Cambia esta URL por la del servicio SOAP que desees utilizar

// Espacios de nombres (namespaces) para la solicitud SOAP, si es necesario. Puede dejarse vacío o ajustar según el servicio.
$namespaces = [
    // 'prefix' => 'namespace_url'  // Ejemplo: 'team' => 'http://example.com/namespace'
];

// Crear una instancia del cliente SOAP
$client = new SoapCurlClient($endpoint, $namespaces);

// Realizar la solicitud SOAP con el método, parámetros y la acción SOAP correspondientes
$response = $client->call('MethodName', ['param1' => 'value1'], 'SOAPAction');

// Mostrar la respuesta obtenida
echo $response;
```

---

## 🧪 **Ejecutar Pruebas**

```bash
phpunit --bootstrap vendor/autoload.php tests/SoapCurlClientTest.php
```

---

## 📁 **Registro de Logs**
- **Ubicación:** `/logs/request.log` y `/logs/response.log`.
- **Formato:** XML y JSON.

---

## 📂 **Archivo de Configuración cURL**

Dentro del directorio `/config`, se encuentran dos archivos de configuración: `curl_dev.php` y `curl_prod.php`. Estos archivos permiten definir y personalizar las opciones de cURL para las solicitudes SOAP, adaptadas para los entornos de desarrollo y producción, respectivamente. Esta organización facilita la configuración de cURL según el entorno de ejecución y las necesidades del servicio con el que se esté integrando.

### Configuración para el entorno de desarrollo (`curl_dev.php`)

```php
// /config/curl_dev.php

return [
    CURLOPT_TIMEOUT => 30,                          // Tiempo máximo de espera en segundos
    CURLOPT_RETURNTRANSFER => true,                 // Devolver como cadena en lugar de imprimir
    CURLOPT_FOLLOWLOCATION => true,                 // Seguir redirecciones
    CURLOPT_MAXREDIRS => 3,                         // Máximo número de redirecciones
    CURLOPT_SSL_VERIFYPEER => false,                // Desactivar verificación SSL (útil para desarrollo)
    CURLOPT_USERAGENT => 'SoapCurlClient/1.0',      // Agregar un user-agent personalizado
    CURLOPT_HTTPHEADER => [
        'Content-Type: text/xml; charset=utf-8',    // Tipo de contenido de la solicitud
        'SOAPAction' => ''                           // Acción SOAP (se define al realizar la llamada)
    ]
];
```

Este archivo se carga automáticamente cuando se realiza una solicitud, permitiendo que se apliquen las configuraciones de cURL para cada solicitud SOAP.

---

## 🤝 **Contribuciones**
¡Las contribuciones son bienvenidas! Puedes hacer un fork del repositorio y enviar un pull request.

---

## 📝 **Licencia**
Este proyecto está bajo la licencia MIT. Puedes ver más detalles en el archivo `LICENSE`.

---

<p align="center">
    Hecho con ❤️ por Miguel Ángel Moza Barquilla(https://github.com/MMoza)
</p>
