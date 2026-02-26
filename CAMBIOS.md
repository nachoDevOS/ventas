# Registro de Cambios del Sistema de Ventas

## [2026-02-26] Descuento por línea de producto en el carrito de ventas

### Objetivo
Agregar un campo de **Descuento (Bs.)** por línea de producto en el carrito de ventas, aplicable tanto a unidades enteras como a fracciones.

### Fórmula de cálculo
```
Bruto        = Precio × Cantidad
Subtotal     = Bruto − Descuento       (mínimo 0)
Total Venta  = Σ Subtotales
```

---

### Archivos modificados

#### 1. `database/migrations/2026_02_26_143749_add_discount_to_sale_details_table.php`
- Nueva migración que agrega la columna `discount` (decimal 10,2, nullable, default 0) a la tabla `sale_details`.

#### 2. `app/Models/SaleDetail.php`
- Se agregó `'discount'` al array `$fillable`.

#### 3. `app/Http/Controllers/SaleController.php`
**Método `store()`:**
- Cálculo de `$amountItems` ahora resta el descuento por línea:
  - Unidad: `(qty_unit × price_unit) − discount_unit`
  - Fracción: `(qty_fraction × price_fraction) − discount_fraction`
- `SaleDetail::create()` para Entero y Fraccionado ahora incluye el campo `discount`.
- `amount` guardado ya es el valor neto (con descuento aplicado).

**Método `update()`:**
- Misma lógica que `store()` aplicada al recalcular `$amountItems` y al recrear los `SaleDetail`.

#### 4. `resources/views/sales/edit-add.blade.php`

**HTML — Encabezado de tabla:**
- Se agregó la columna `<th>Descuento</th>` entre "Cantidad" y "Subtotal".

**JavaScript — `addProductToCart(product)`:**
- Se generan inputs de descuento para la parte entera:
  - `products[id][discount_unit]` → `#input-discount-unit-{id}`
- Se generan inputs de descuento para la parte fraccionada (solo si el producto es Fraccionado):
  - `products[id][discount_fraction]` → `#input-discount-fraction-{id}`
- Se agregó una nueva celda `<td>` con `discountInputs` en el template de fila.

**JavaScript — `getSubtotal(id)`:**
- Lee los valores de descuento desde los inputs.
- Valida que el descuento no supere el bruto de la línea (si lo supera, se ajusta al máximo).
- Calcula:
  - `subtotal_unit = max(0, price_unit × quantity_unit − discount_unit)`
  - `subtotal_fraction = max(0, price_fraction × quantity_fraction − discount_fraction)`

**JavaScript — Modo edición (bloque `@if(isset($sale))`):**
- Al cargar los detalles existentes, se restaura el valor de `detail.discount` en el input correspondiente (`discount_unit` o `discount_fraction`).
- El objeto `originalData` ahora guarda también el campo `discount` por línea.

**JavaScript — `restoreOriginalQuantities(productId, originalData)`:**
- Al re-agregar un producto eliminado del carrito durante la edición, se restauran también los descuentos originales.

---

### Comportamiento esperado en la UI

| Acción | Resultado |
|--------|-----------|
| Ingresar descuento en Bs. para unidades | Subtotal se recalcula restando el descuento |
| Ingresar descuento en Bs. para fracciones | Subtotal fracciones se recalcula restando el descuento |
| Descuento mayor al bruto | Se ajusta automáticamente al valor máximo permitido |
| Total de venta | Suma de todos los subtotales netos |
| Editar venta existente | Los descuentos guardados se cargan automáticamente |

---

---

## [2026-02-26] Rediseño visual del carrito — estilo Voyager/Bootstrap 3

### Objetivo
Adaptar el diseño de las columnas Precio, Descuento y Subtotal al estilo nativo de Voyager (Bootstrap 3) para consistencia visual.

### Cambios en `resources/views/sales/edit-add.blade.php`

**Columna Precio:**
- Se reemplazaron los inputs sueltos por `input-group` con addon `Bs.` en negrita.
- Labels con icono `fa-tag` azul para unidad y fracción.

**Columna Descuento:**
- `input-group` con addon `Bs.` en rojo (`#fdf2f2` / `#e74c3c`) y borde rojizo (`#ebccd1`).
- Label con icono `fa-scissors` en rojo `#c0392b`.
- El input tiene borde rojo suave para diferenciarse visualmente.

**Columna Subtotal — breakdown visual:**
- Se eliminó el simple número; ahora muestra tres líneas por línea de producto:
  1. **Bruto** (gris `#95a5a6`): precio × cantidad
  2. **Dto** (rojo `#e74c3c`): monto descontado — solo aparece cuando el descuento > 0
  3. **Subtotal** (verde `#27ae60`, negrita): valor neto final
- Cada sección (Unidad / Fracción) separada con borde punteado.
- Icono `fa-cube` para la parte entera, `fa-vial` para la fracción.

**`getSubtotal(id)`:**
- Se actualizan dinámicamente los labels `#label-bruto-unit-{id}`, `#label-dto-unit-{id}`, `#discount-unit-display-{id}` (show/hide con flexbox).
- Misma lógica para fracción.

**Ancho de columnas:**
- Subtotal pasó de `12%` a `15%` para acomodar el nuevo contenido.

---

### Notas técnicas
- El descuento se almacena por `SaleDetail` (por línea de detalle), no a nivel de `Sale`.
- El campo `amount` en `sale_details` ahora representa el valor **neto** (ya con descuento).
- El campo `discount` en `sale_details` guarda el monto absoluto descontado en Bs.

---

## [2026-02-26] Rediseño panel de Método de Pago

### Cambios en `resources/views/sales/edit-add.blade.php`

**Método de pago — tarjetas visuales:**
- Se reemplazó el `<select>` visible por 3 tarjetas clickeables (Efectivo, QR/Transferencia, Efectivo y QR).
- El `<select#select-payment_type>` queda oculto (`display:none`) para mantener compatibilidad con el JS existente y el envío del formulario.
- Nueva función JS `selectPaymentMethod(value, card)` que sincroniza la tarjeta activa con el select y dispara `updatePaymentLogic()`.

**Inputs de montos — disposición flex:**
- Los campos `#cash-payment-section` y `#qr-payment-section` están en un contenedor flex (`.amounts-row`).
- Cuando solo uno es visible ocupa todo el ancho; cuando ambos son visibles se dividen en 50/50 automáticamente.
- Verde para Efectivo, violeta para QR.

**Total a Pagar:**
- Rediseñado con gradiente verde suave, fuente `2em` bold, separación label/valor con flexbox.

**Mensajes de Cambio / Deuda / Error:**
- Cada mensaje es un bloque flex con icono + texto a la izquierda y monto a la derecha.
- Cambio: fondo azul claro | Deuda: fondo amarillo | Error: fondo rojo suave.

**Checkbox de confirmación:** caja gris con border-radius.
**Botón Registrar/Actualizar:** `btn-success` (verde), tamaño `15px`, border-radius `6px`.

---

## [2026-02-26] Descuento General al Total de la Venta

### Objetivo
Agregar un campo **Descuento General (Bs.)** en el panel de pago, que se aplica al total de todos los productos (después de sus descuentos por línea), reduciendo el monto final a cobrar.

### Fórmula de cálculo
```
Subtotal Productos = Σ (subtotales netos por línea)
Total Final        = max(0, Subtotal Productos − Descuento General)
```

### Archivos modificados

#### 1. `database/migrations/2026_02_26_180000_add_general_discount_to_sales_table.php`
- Nueva migración que agrega la columna `general_discount` (decimal 10,2, nullable, default 0) a la tabla `sales`.

#### 2. `app/Models/Sale.php`
- Se agregó `'general_discount'` al array `$fillable`.

#### 3. `app/Http/Controllers/SaleController.php`
**Método `store()`:**
- Lee `$general_discount = floatval($request->general_discount ?? 0)`.
- `$amountTotal = max(0, $amountItems - $general_discount)`.
- `Sale::create()` incluye `'general_discount' => $general_discount`.

**Método `update()`:**
- Misma lógica aplicada al recalcular `$amountTotal`.
- `$sale->update()` incluye `'general_discount' => $general_discount`.

#### 4. `resources/views/sales/edit-add.blade.php`

**CSS:**
- Nueva clase `.general-discount-block` con estilo rojo (igual que descuentos por línea).
- `.payment-summary` rediseñado para mostrar breakdown vertical con clases `.summary-subtotal`, `.summary-dto`, `.summary-divider`.

**HTML — Panel de pago:**
- Nuevo `input-group` con addon `Bs.` rojo: `#input-general-discount` (name=`general_discount`).
- `.payment-summary` ahora muestra tres filas:
  1. **Subtotal productos** (gris): suma de subtotales por línea → `#label-subtotal-products`
  2. **Dto. General** (rojo, oculto si = 0): `#general-discount-display` / `#label-general-discount-display`
  3. **Total a Pagar** (verde, 2em): `#label-total`

**JavaScript — `getTotal()`:**
- Suma todos los `.label-subtotal` → `subtotalProducts`.
- Lee `#input-general-discount`; si excede el subtotal, se ajusta al máximo.
- `totalAmount = max(0, subtotalProducts - generalDiscount)`.
- Actualiza `#label-subtotal-products`, muestra/oculta `#general-discount-display`, actualiza `#label-total` y `#amountTotalSale`.

### Comportamiento esperado en la UI

| Acción | Resultado |
|--------|-----------|
| Ingresar descuento general en Bs. | Total a Pagar se recalcula restando el descuento |
| Descuento mayor al subtotal de productos | Se ajusta automáticamente al valor máximo |
| Sin descuento general | La fila "Dto. General" permanece oculta |
| Editar venta existente | El descuento general guardado se carga automáticamente |
