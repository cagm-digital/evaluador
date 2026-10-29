# PROMPT POR FASES: Aplicativo "Evaluador"

## Contexto general (pégalo primero, aplica a todas las fases)

Eres un desarrollador web senior. Construirás **Evaluador**, un aplicativo de una sola página para estudiantes del **Instituto Técnico Superior de Pereira**. El estudiante ingresa las notas de los períodos 1, 2 y 3 (cada uno vale 20%) y el sistema calcula la **nota mínima que necesita en el período 4 (40%)** para aprobar con **3.0**.

Fórmula: `N4 = (3.0 − 0.2·(N1+N2+N3)) / 0.4`. Escala 1.0–5.0.

**Datos institucionales visibles:** Escudo de la Institución, Especialidad: Informática, Programa: Diseño y Desarrollo de Sitios Web, y Docente (campo configurable).

**Stack y reglas obligatorias:**
- HTML5 + **Bootstrap 5 (última versión estable, por CDN)** + PHP vanilla (sin frameworks ni Composer).
- Mobile first, responsive en móvil, tableta y escritorio.
- `index.php` en la raíz del proyecto.
- Compatible con hosting compartido cPanel (PHP 7.4+, sin dependencias de servidor, rutas relativas, sin Node en producción).
- Arquitectura visual: Material Design 3 + dashboard modular + Bento UI moderado. Estética: Material Design 3 + glasmorfismo. Aspecto similar a productos de Google.
- Tipografía **Roboto**; iconos **Material Symbols**, y Bootstrap Icons solo si hace falta.
- Modo claro y oscuro.
- Paleta derivada del escudo (verdes, blanco, gris, negro).
- Accesibilidad **WCAG 2.1 nivel AA**.
- Componentes: cards, botones, formularios, tablas, badges, alertas, modales, menú lateral y widgets.
- Código, identificadores y comentarios en inglés; textos de la interfaz en español.

**Método de trabajo:** ejecuta **una fase a la vez**. Al terminar cada una, resume lo hecho, indica cómo verificarlo y **detente a esperar mi aprobación** antes de continuar.

---

## Fase 1: Estructura y base del proyecto
- Crear el árbol de carpetas: `index.php`, `assets/css`, `assets/js`, `assets/img`, `includes/` (`config.php`, `header.php`, `footer.php`, `functions.php`).
- `config.php` con constantes: nombre de la institución, especialidad, programa, docente, nota aprobatoria (3.0), pesos (0.2, 0.2, 0.2, 0.4), escala mínima y máxima.
- `index.php` con HTML5 semántico (`header`, `nav`, `main`, `footer`), `lang="es"`, meta viewport y enlace "saltar al contenido".
- Cargar Bootstrap 5, Roboto, Material Symbols y Bootstrap Icons.
- Colocar un escudo provisional en `assets/img/` (SVG placeholder) hasta recibir el real.
- **Entregable:** página base que carga sin errores en WAMP/XAMPP.

## Fase 2: Sistema de diseño (tokens y tema)
- Crear `assets/css/theme.css` con variables CSS (`--md-sys-color-*`) para claro y oscuro, usando verdes institucionales, blanco, gris y negro.
- Definir escala tipográfica M3 (display, headline, title, body, label), elevaciones, radios y espaciado.
- Crear utilidades de glasmorfismo (`backdrop-filter`, transparencia, borde sutil) con alternativa sólida cuando no haya soporte.
- Implementar el selector de tema: respeta `prefers-color-scheme`, guarda la preferencia en `localStorage` y evita el parpadeo inicial (script inline en `<head>`).
- Verificar contraste mínimo 4.5:1 para texto y 3:1 para componentes, en ambos temas.
- **Entregable:** hoja de estilos y conmutador de tema funcionando.

## Fase 3: Layout y navegación
- Menú lateral (navigation rail/drawer M3): fijo en escritorio, offcanvas en móvil con botón hamburguesa.
- Top app bar con escudo, nombre de la institución y botón de tema.
- Cuadrícula Bento moderada: tarjeta de bienvenida, tarjeta de calculadora, tarjeta de resultado, widget de resumen y tarjeta de datos institucionales (especialidad, programa, docente).
- Footer con créditos y año dinámico (PHP).
- **Entregable:** layout responsive navegable por teclado.

## Fase 4: Calculadora (formulario y lógica)
- Formulario con 3 campos numéricos (períodos 1, 2 y 3): `step="0.1"`, `min="0"`, `max="5"`, etiquetas visibles, ayuda con `aria-describedby`.
- Validación en el cliente (JS) y en el servidor (PHP): rechazar vacíos, negativos, fuera de rango o no numéricos. Escapar toda salida con `htmlspecialchars`.
- En `functions.php`, crear la función pura `calculateMinimumGrade(float $p1, float $p2, float $p3): array`. Devuelve nota acumulada, nota mínima del período 4 y un estado:
  - **Ya aprobado**: la mínima es ≤ 0, se muestra "Ya aprobaste".
  - **Alcanzable**: la mínima está entre 0 y 5.
  - **No alcanzable**: la mínima es > 5, se muestra "Matemáticamente no alcanzable".
- Redondear a 2 decimales. Manejar `setlocale` y la coma decimal.
- Flujo con JS (`fetch` a un endpoint PHP o cálculo cliente con verificación servidor) y fallback con envío normal del formulario si JS está desactivado. Proteger con token CSRF de sesión.
- **Entregable:** cálculo correcto con casos de prueba documentados (ej.: 3.0/3.0/3.0 → 3.00; 5/5/5 → ya aprobado; 1/1/1 → no alcanzable).

## Fase 5: Presentación de resultados
- Tarjeta de resultado con número grande, badge de estado (verde, ámbar, rojo; nunca solo color: añadir icono y texto) y mensaje claro.
- Tabla con el desglose: período, nota, peso, aporte.
- Alerta con `role="status"` y `aria-live="polite"` para anunciar el resultado a lectores de pantalla.
- Modal con la explicación de la fórmula y botón "Calcular de nuevo" / limpiar.
- Animaciones sutiles que respeten `prefers-reduced-motion`.
- **Entregable:** flujo completo de ingreso, cálculo y resultado.

## Fase 6: Accesibilidad WCAG 2.1 AA
- Auditoría: orden de foco, indicador de foco visible, objetivos táctiles de al menos 44×44 px, `alt` en el escudo, jerarquía de encabezados, landmarks, mensajes de error asociados a cada campo, `aria-invalid`, reflow a 320 px y zoom al 200%.
- Probar con teclado, lector de pantalla (NVDA) y Lighthouse/axe. Corregir hallazgos.
- **Entregable:** informe breve de verificación con la lista de criterios cubiertos.

## Fase 7: Seguridad, rendimiento y despliegue en cPanel
- Cabeceras de seguridad vía `.htaccess` (X-Content-Type-Options, X-Frame-Options, Referrer-Policy, CSP básica), compresión y caché de estáticos, bloqueo de acceso directo a `includes/`.
- Sesiones seguras, sin dependencias de `exec` ni extensiones no estándar, y rutas relativas.
- Optimizar imágenes y cargar fuentes con `display=swap`.
- `README.md` con pasos para subir por el Administrador de archivos o FTP de cPanel, versión de PHP requerida y cómo cambiar docente y escudo.
- **Entregable:** paquete listo para `public_html` (o subcarpeta) y checklist de despliegue.

## Fase 8: Pruebas finales
- Matriz de pruebas: Chrome, Firefox, Edge, Safari iOS y Chrome Android; anchos de 320, 768 y 1280 px; ambos temas.
- Revisar casos límite de la calculadora (decimales, 0, 5, valores vacíos, entradas maliciosas).
- Entregar la lista final de archivos y los pendientes, si los hay.
