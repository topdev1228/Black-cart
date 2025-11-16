<!DOCTYPE html>
<html lang="en">
<head>
    <title>Blackcart</title>
    <meta charset="UTF-8" />
    <meta name="shopify-api-key" content="{{ config('services.shopify.client_id') }}" />
    <script src="https://cdn.shopify.com/shopifycloud/app-bridge.js"></script>
    <!-- Ensures that the UI is properly scaled in the Shopify Mobile app -->
    <meta name="viewport" content="width=device-width, initial-scale=1">
    @viteReactRefresh
    @vite('resources/js/index.tsx')
</head>
<body>
<div id="app"><!--index.jsx injects App.jsx here--></div>
</body>
</html>
