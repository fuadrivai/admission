<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Merchandise Mobile API - Swagger UI</title>
    <link rel="stylesheet" href="https://unpkg.com/swagger-ui-dist@5/swagger-ui.css">
    <style>
        html { box-sizing: border-box; }
        *, *::before, *::after { box-sizing: inherit; }
        body { margin: 0; background: #fafafa; }
        .docs-header { padding: 18px max(20px, calc((100vw - 1200px) / 2)); background: #690f24; color: #fff; font: 14px/1.5 Inter, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif; }
        .docs-header h1 { margin: 0 0 4px; font-size: 22px; }
        .docs-header p { margin: 0; color: rgba(255,255,255,.82); }
        .docs-note { width: min(100% - 32px, 1200px); margin: 18px auto 0; padding: 12px 16px; border-left: 4px solid #bd7d19; background: #fff8e8; color: #55471f; font: 14px/1.55 Inter, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif; }
        .docs-note code { padding: 1px 4px; background: rgba(0,0,0,.06); }
        #swagger-ui { max-width: 1240px; margin: 0 auto; }
        .swagger-ui .topbar { display: none; }
        .swagger-ui .info { margin-top: 24px; }
        @media (max-width: 600px) {
            .docs-header { padding: 15px 16px; }
            .docs-header h1 { font-size: 19px; }
            .swagger-ui .wrapper { padding: 0 12px; }
        }
    </style>
</head>
<body>
    <header class="docs-header">
        <h1>MHIS Merchandise Mobile API</h1>
        <p>Interactive API reference. Use "Try it out" to send requests to this environment.</p>
    </header>
    <aside class="docs-note">
        No account login is required to use this API. Cart and checkout endpoints require the anonymous
        <code>X-Cart-Token</code> returned by <code>POST /carts</code>; this token identifies your cart and is not a login credential.
        A Portal username alone is not verified identity, so this API does not expose order history by username.
    </aside>
    <div id="swagger-ui"></div>
    <script src="https://unpkg.com/swagger-ui-dist@5/swagger-ui-bundle.js"></script>
    <script>
        window.onload = function () {
            window.ui = SwaggerUIBundle({
                url: @json(url('/api/v1/merchandise/openapi.json')),
                dom_id: '#swagger-ui',
                deepLinking: true,
                displayRequestDuration: true,
                persistAuthorization: false,
                presets: [SwaggerUIBundle.presets.apis],
                layout: 'BaseLayout'
            });
        };
    </script>
</body>
</html>
