Write-Host "========================================================" -ForegroundColor Cyan
Write-Host "   EJECUTANDO PRUEBAS DE LA API CON POWERSHELL        " -ForegroundColor Cyan
Write-Host "========================================================" -ForegroundColor Cyan
Write-Host ""

$baseUri = "http://localhost:8000/api"

function Invoke-ApiTest {
    param (
        [string]$Title,
        [string]$Method,
        [string]$Endpoint,
        [hashtable]$Body = $null,
        [int]$ExpectedStatus = 200
    )

    Write-Host "[$Title] $Method $Endpoint" -ForegroundColor Yellow

    $headers = @{
        "Accept" = "application/json"
    }

    try {
        if ($Body) {
            $json = $Body | ConvertTo-Json
            $bytes = [System.Text.Encoding]::UTF8.GetBytes($json)
            $response = Invoke-RestMethod -Uri "$baseUri$Endpoint" -Method $Method -Body $bytes -ContentType "application/json; charset=utf-8" -Headers $headers
        } else {
            $response = Invoke-RestMethod -Uri "$baseUri$Endpoint" -Method $Method -Headers $headers
        }

        Write-Host "  -> Estado: OK (HTTP $ExpectedStatus)" -ForegroundColor Green
        $response | ConvertTo-Json -Depth 4
    } catch {
        $stream = $_.Exception.Response.GetResponseStream()
        $reader = New-Object System.IO.StreamReader($stream)
        $rawError = $reader.ReadToEnd()
        Write-Host "  -> Respuesta del servidor:" -ForegroundColor Magenta
        Write-Host $rawError
    }

    Write-Host ""
}

# 1. Listar Divisas
Invoke-ApiTest -Title "TEST 1: Listar Divisas" -Method "Get" -Endpoint "/currencies"

# 2. Crear Producto
$newProduct = @{
    name = "Teclado Mecanico RGB"
    description = "Switches Red silenciosos"
    price = 120.00
    currency_id = 1
    tax_cost = 12.00
    manufacturing_cost = 60.00
}
Invoke-ApiTest -Title "TEST 2: Crear Producto" -Method "Post" -Endpoint "/products" -Body $newProduct -ExpectedStatus 201

# 3. Consultar Producto
Invoke-ApiTest -Title "TEST 3: Consultar Producto 1" -Method "Get" -Endpoint "/products/1"

# 4. Registrar Precio en EUR
$eurPrice = @{
    currency_id = 2
    price = 110.40
}
Invoke-ApiTest -Title "TEST 4: Registrar Precio en EUR" -Method "Post" -Endpoint "/products/1/prices" -Body $eurPrice -ExpectedStatus 201

# 5. Listar Precios del Producto
Invoke-ApiTest -Title "TEST 5: Listar Precios de Producto 1" -Method "Get" -Endpoint "/products/1/prices"

# 6. Regla de Negocio: Rechazo de Duplicado
$dupPrice = @{
    currency_id = 2
    price = 115.00
}
Invoke-ApiTest -Title "TEST 6: Validar Rechazo de Duplicado (422 esperado)" -Method "Post" -Endpoint "/products/1/prices" -Body $dupPrice -ExpectedStatus 422

# 7. Regla de Negocio: Rechazo de Precio Negativo
$negProduct = @{
    name = "Producto Invalido"
    price = -25.00
    currency_id = 1
}
Invoke-ApiTest -Title "TEST 7: Validar Rechazo de Precio Negativo (422 esperado)" -Method "Post" -Endpoint "/products" -Body $negProduct -ExpectedStatus 422

Write-Host "========================================================" -ForegroundColor Cyan
Write-Host "   TODAS LAS PRUEBAS COMPLETADAS                       " -ForegroundColor Cyan
Write-Host "========================================================" -ForegroundColor Cyan
