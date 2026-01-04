<?php
/**
 * CONECTA ERP - DOCUMENTACIÓN API REST
 * Documentación completa de la API REST
 */

if (!defined('CONECTA_ERP')) {
    die('Acceso no autorizado');
}

$pageTitle = 'Documentación API';
?>

<div class="api-docs-container">
    <div class="page-header">
        <div class="page-title">
            <h1>📚 Documentación API REST</h1>
            <p>Guía completa para integrar Conecta ERP con tus aplicaciones</p>
        </div>
        <div class="page-actions">
            <a href="/app/router.php?module=api&sub=tokens" class="btn btn-primary">
                <span class="icon">🔑</span>
                Gestionar Tokens
            </a>
        </div>
    </div>

    <!-- Autenticación -->
    <div class="card">
        <div class="card-header">
            <h3>🔐 Autenticación</h3>
        </div>
        <div class="card-body">
            <p>Todas las peticiones a la API requieren autenticación mediante Bearer Token:</p>
            <pre><code>Authorization: Bearer TU_TOKEN_AQUI</code></pre>

            <h4>Ejemplo con cURL:</h4>
            <pre><code>curl -X GET "https://tudominio.com/api/v1/clientes" \
  -H "Authorization: Bearer eyJ0eXAiOiJKV1QiLCJhbGciOiJ..." \
  -H "Accept: application/json"</code></pre>
        </div>
    </div>

    <!-- Endpoints de Clientes -->
    <div class="card">
        <div class="card-header">
            <h3>👥 Endpoints - Clientes</h3>
        </div>
        <div class="card-body">
            <div class="endpoint">
                <span class="method get">GET</span>
                <code>/api/v1/clientes</code>
                <p>Listar todos los clientes</p>
            </div>

            <div class="endpoint">
                <span class="method get">GET</span>
                <code>/api/v1/clientes/{id}</code>
                <p>Obtener un cliente específico</p>
            </div>

            <div class="endpoint">
                <span class="method post">POST</span>
                <code>/api/v1/clientes</code>
                <p>Crear un nuevo cliente</p>
            </div>

            <div class="endpoint">
                <span class="method put">PUT</span>
                <code>/api/v1/clientes/{id}</code>
                <p>Actualizar un cliente</p>
            </div>

            <div class="endpoint">
                <span class="method delete">DELETE</span>
                <code>/api/v1/clientes/{id}</code>
                <p>Eliminar un cliente</p>
            </div>
        </div>
    </div>

    <!-- Endpoints de Productos -->
    <div class="card">
        <div class="card-header">
            <h3>📦 Endpoints - Productos</h3>
        </div>
        <div class="card-body">
            <div class="endpoint">
                <span class="method get">GET</span>
                <code>/api/v1/productos</code>
                <p>Listar todos los productos</p>
            </div>

            <div class="endpoint">
                <span class="method get">GET</span>
                <code>/api/v1/productos/{id}/stock</code>
                <p>Consultar stock de un producto</p>
            </div>

            <div class="endpoint">
                <span class="method post">POST</span>
                <code>/api/v1/productos</code>
                <p>Crear un nuevo producto</p>
            </div>
        </div>
    </div>

    <!-- Endpoints de Facturas -->
    <div class="card">
        <div class="card-header">
            <h3>📄 Endpoints - Facturas</h3>
        </div>
        <div class="card-body">
            <div class="endpoint">
                <span class="method get">GET</span>
                <code>/api/v1/facturas</code>
                <p>Listar facturas</p>
            </div>

            <div class="endpoint">
                <span class="method post">POST</span>
                <code>/api/v1/facturas</code>
                <p>Crear una nueva factura</p>
            </div>

            <h4>Ejemplo de creación de factura:</h4>
            <pre><code>{
  "cliente_id": 123,
  "fecha": "2025-01-04",
  "items": [
    {
      "producto_id": 456,
      "cantidad": 2,
      "precio": 15000
    }
  ],
  "forma_pago": "efectivo"
}</code></pre>
        </div>
    </div>

    <!-- Códigos de Respuesta -->
    <div class="card">
        <div class="card-header">
            <h3>📊 Códigos de Respuesta HTTP</h3>
        </div>
        <div class="card-body">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Código</th>
                        <th>Descripción</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><code>200</code></td>
                        <td>OK - Petición exitosa</td>
                    </tr>
                    <tr>
                        <td><code>201</code></td>
                        <td>Created - Recurso creado exitosamente</td>
                    </tr>
                    <tr>
                        <td><code>400</code></td>
                        <td>Bad Request - Error en los datos enviados</td>
                    </tr>
                    <tr>
                        <td><code>401</code></td>
                        <td>Unauthorized - Token inválido o expirado</td>
                    </tr>
                    <tr>
                        <td><code>404</code></td>
                        <td>Not Found - Recurso no encontrado</td>
                    </tr>
                    <tr>
                        <td><code>500</code></td>
                        <td>Server Error - Error interno del servidor</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Rate Limiting -->
    <div class="card">
        <div class="card-header">
            <h3>⏱️ Rate Limiting</h3>
        </div>
        <div class="card-body">
            <p>La API tiene los siguientes límites:</p>
            <ul>
                <li><strong>Plan Starter:</strong> 100 peticiones por hora</li>
                <li><strong>Plan Professional:</strong> 1,000 peticiones por hora</li>
                <li><strong>Plan Enterprise:</strong> 10,000 peticiones por hora</li>
            </ul>
            <p>Los headers de respuesta incluyen:</p>
            <pre><code>X-RateLimit-Limit: 1000
X-RateLimit-Remaining: 999
X-RateLimit-Reset: 1609459200</code></pre>
        </div>
    </div>
</div>

<style>
.api-docs-container pre code {
    display: block;
    background: #1e293b;
    color: #e2e8f0;
    padding: 1rem;
    border-radius: 6px;
    overflow-x: auto;
    margin: 1rem 0;
    font-family: 'Courier New', monospace;
    font-size: 0.9rem;
}

.endpoint {
    margin: 1rem 0;
    padding: 1rem;
    background: var(--bg-tertiary, #f3f4f6);
    border-radius: 6px;
    border-left: 3px solid var(--primary, #3b82f6);
}

.endpoint code {
    font-family: monospace;
    font-size: 1rem;
    font-weight: 600;
}

.method {
    display: inline-block;
    padding: 0.25rem 0.75rem;
    border-radius: 4px;
    font-weight: bold;
    font-size: 0.75rem;
    margin-right: 0.5rem;
}

.method.get { background: #10b981; color: white; }
.method.post { background: #3b82f6; color: white; }
.method.put { background: #f59e0b; color: white; }
.method.delete { background: #ef4444; color: white; }

.endpoint p {
    margin: 0.5rem 0 0 0;
    color: var(--text-secondary, #6b7280);
}
</style>
