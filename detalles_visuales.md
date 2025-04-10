# Detalles Visuales del Proyecto

Este documento define los aspectos visuales que se usarán en todo el proyecto, basados en el CSS inicial de la página de login.

## Colores

- **Fondo Principal:** #f4f4f4  
   Se utiliza un fondo ligeramente gris para áreas generales y formularios.

- **Contenedor de Formularios:** #ffffff  
   Los contenedores, tarjetas o áreas de contenido se presentarán con fondo blanco para un contraste con el fondo principal.

- **Encabezados y Títulos:**  
   Color principal: #2c3e50  
   Se usará para títulos importantes y nombres de compañías.

- **Botón de Acción:**  
   Fondo: #3498db  
   Hover: #2980b9  
   Botón para acciones como el login, con un efecto de cambio de color al pasar el cursor.

- **Botones Adicionales:**

  - Secundario: #95a5a6 (hover: #7f8c8d)
  - Éxito: #2ecc71 (hover: #27ae60)
  - Peligro: #e74c3c (hover: #c0392b)

- **Enlaces:**  
   Color: #3498db  
   Se mantendrá consistente con el color de botones para enlaces interactivos.

- **Elementos de Texto y Etiquetas:**  
   Color: #555 para etiquetas y descripciones secundarias.

- **Bordes:**
  Color: #ddd para separar elementos de formulario y secciones.

## Variables CSS

Se han definido variables CSS en el archivo root.css para facilitar la consistencia y el mantenimiento:

```css
:root {
  --color-bg-principal: #f4f4f4;
  --color-contenedor: #ffffff;
  --color-titulo: #2c3e50;
  --color-boton: #3498db;
  --color-boton-hover: #2980b9;
  --color-enlace: #3498db;
  --color-texto-secundario: #555;
  --color-borde: #ddd;
  --sombra-default: 0 4px 10px rgba(0, 0, 0, 0.1);
}
```

## Tipografía

- **Fuente Principal:** Arial, sans-serif  
   Se usará una fuente sans-serif genérica y limpia para todo el proyecto.

- **Jerarquía de Texto:**
  - Títulos/Encabezados:
    - H1: 28px
    - H2: 24px
    - H3: 20px
  - Cuerpo del Texto: 16px con line-height de 1.5 para buena legibilidad.
  - Texto responsivo: Se reduce a 14px en pantallas móviles (max-width: 768px).

## Estilo de Componentes

### Contenedores y Tarjetas

- Clase `.container` con ancho máximo de 1200px para contenido principal.
- Clase `.card` para contenedores con:
  - Fondo blanco
  - Bordes redondeados con un radio de 8px
  - Sombreado sutil (box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1))
  - Padding de 20px (reducido a 15px en pantallas móviles)

### Formularios

- Inputs, selects y textareas con:

  - Ancho del 100% para adaptabilidad
  - Padding de 10px
  - Tamaño de fuente de 16px
  - Borde de 1px con color #ddd
  - Border-radius de 4px
  - Margen inferior de 15px para separación

- Labels con:
  - Display block para ocupar su propia línea
  - Margen inferior de 5px
  - Color #555 para texto secundario

### Botones

- Clase base `.btn` con:

  - Padding de 12px 20px (reducido a 10px 15px en móviles)
  - Fondo azul #3498db
  - Color de texto blanco
  - Sin bordes
  - Border-radius de 4px
  - Cursor tipo pointer
  - Transición suave para el efecto hover

- Variantes de botones:
  - `.btn-secondary`: gris para acciones secundarias
  - `.btn-success`: verde para acciones de confirmación
  - `.btn-danger`: rojo para acciones peligrosas o eliminación

## Clases de Utilidad

- Alineación de texto: `.text-center`
- Márgenes superiores: `.mt-1` a `.mt-4` (0.5rem a 2rem)
- Márgenes inferiores: `.mb-1` a `.mb-4` (0.5rem a 2rem)

## Iconografía

- **Sistema de Iconos:** Google Fonts Material Icons
  - Se implementa a través de: `<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">`
  - Uso en elementos HTML: `<span class="material-icons">nombre_del_icono</span>`
  - Estilo base para iconos:
    - Alineación vertical centrada
    - Tamaño de 20px
    - Margen derecho de 5px para separación del texto
  - Ejemplos de iconos utilizados:
    - Búsqueda: `search`
    - Agregar: `add`
    - Usuario: `account_circle`
    - Inicio: `home`
    - Empleados: `people`
    - Configuración: `settings`
    - Perfil: `person`
    - Cerrar sesión: `exit_to_app`
    - Paginación: `first_page`, `chevron_left`, `chevron_right`, `last_page`

## Responsividad

Se han implementado media queries para adaptar el diseño a diferentes tamaños de pantalla:

```css
@media (max-width: 768px) {
  body {
    font-size: 14px;
  }
  h1 {
    font-size: 24px;
  }
  h2 {
    font-size: 20px;
  }
  .card {
    padding: 15px;
  }
  .btn {
    padding: 10px 15px;
  }
}
```

## Consideraciones Adicionales

- **Accesibilidad:**  
   Se mantiene un buen contraste entre el texto y el fondo.
- **Consistencia:**  
   Los mismos estilos de colores, fuentes y efectos se aplican en todas las páginas mediante variables CSS para un diseño uniforme en el proyecto.
- **Mantenibilidad:**
  El sistema de estilos utiliza variables CSS para facilitar cambios globales rápidos sin necesidad de buscar y reemplazar valores en múltiples archivos.

Este es el marco visual base que se ha implementado en el archivo root.css y se extenderá según los requerimientos específicos de cada interfaz del proyecto.
