# CRM Application

¡Bienvenido a **CRM App**! Una plataforma ligera, rápida y moderna diseñada para la gestión de relaciones con clientes (CRM). Este proyecto utiliza una arquitectura híbrida optimizada que combina el poder del renderizado en el servidor (SSR) con una experiencia de usuario altamente reactiva en el cliente.

---

## 🚀 Características Principales

* **Gestión de clientes:** Control completo de prospectos, contactos y cuentas comerciales.
* **Interactividad fluida (Islas de reactividad):** Modales, alertas y actualizaciones de estado instantáneas sin recargar la página.
* **Validación robusta en el cliente:** Formularios protegidos de forma inobrusiva del lado del cliente sincronizados con las reglas de negocio del servidor.
* **Diseño moderno y responsivo:** Interfaz limpia adaptada a dispositivos móviles y pantallas de escritorio.
* **Arquitectura eficiente:** Carga inicial ultra rápida gracias al renderizado del lado del servidor.

---

## 🛠️ Stack Tecnológico

La aplicación está construida sobre un ecosistema tecnológico cuidadosamente seleccionado para balancear rendimiento, mantenibilidad y velocidad de desarrollo:

* **Backend & SSR:** [ASP.NET Core 10 Razor Pages](https://learn.microsoft.com/dotnet/core/) - Proporciona una estructura de servidor sólida, enrutamiento limpio y seguridad nativa.
* **Estilos & Layout:** [Bootstrap 5.3](https://getbootstrap.com/) - Sistema de grilla responsiva, componentes estilizados y soporte nativo para Modo Oscuro/Claro basado en Design Tokens.
* **Reactividad en el cliente:** [Alpine.js](https://alpinejs.dev/) - Una librería ligera y declarativa para manejar el comportamiento de la interfaz directo en el HTML.
* **Comunicación AJAX:** [Alpine AJAX](https://alpine-ajax.js.org/) - Permite realizar peticiones parciales a los *Page Handlers* de Razor de forma transparente.
* **Validación de formularios:** `jquery.validate.unobtrusive` - Validación automática en el cliente heredada de las anotaciones de datos (Data Annotations) de C#.

---

## 📦 Requisitos Previos

Antes de clonar e inicializar el proyecto, asegúrate de tener instalado:

* [.NET 10 SDK](https://dotnet.microsoft.com/download) o superior.
* Un motor de base de datos compatible (SQL Server, PostgreSQL o SQLite según configuración).
* Un navegador web moderno.

---

## 💻 Instalación y Configuración

Sigue estos pasos para ejecutar el proyecto de forma local:

1.  **Clonar el repositorio:**
    ```bash
    git clone [https://github.com/tu-usuario/crm-app.git](https://github.com/tu-usuario/crm-app.git)
    cd crm-app
    ```

2.  **Restaurar dependencias de .NET:**
    ```bash
    dotnet restore
    ```

3.  **Configurar la base de datos:**
    Actualiza la cadena de conexión en el archivo `appsettings.json` o `appsettings.Development.json`:
    ```json
    "ConnectionStrings": {
      "DefaultConnection": "Server=YOUR_SERVER;Database=CrmDb;Trusted_Connection=True;"
    }
    ```

4.  **Ejecutar las migraciones (si utilizas Entity Framework Core):**
    ```bash
    dotnet ef database update
    ```

5.  **Iniciar la aplicación:**
    ```bash
    dotnet run --project CrmApp.csproj
    ```

La aplicación estará disponible en tu navegador en `https://localhost:5001` o `http://localhost:5000`.

---

## 📐 Estructura del código (Frontend)

Para mantener el proyecto ordenado y evitar conflictos entre las distintas herramientas de JavaScript, se sigue la siguiente convención:

* **Validaciones básicas:** Se manejan de forma nativa desde C# mediante `Data Annotations`. El cliente las procesa automáticamente gracias a los atributos `data-val-*`.
* **Componentes de UI reactivos:** Modales, dropdowns y toggles utilizan directivas de **Alpine.js** (`x-data`, `x-show`, `@click`).
* **Formularios dinámicos:** Los envíos parciales que no requieren recargar toda la página utilizan **Alpine AJAX** (`x-target`) apuntando directamente a los Handlers de Razor (`asp-page-handler`).

---

## 📄 Licencia

Este proyecto está bajo la Licencia MIT. Para más detalles, consulta el archivo `LICENSE`.