param(
  [string]$ProjectRoot = "C:\logistics"
)

$ErrorActionPreference = "Stop"

$frontendPath = Join-Path $ProjectRoot "dakia_app01"
$deployFrontendPath = Join-Path $ProjectRoot "deployment-godaddy\frontend"
$tempBuildPath = Join-Path $ProjectRoot "deployment-godaddy\.tmp-static-build"
$tempApiPath = Join-Path $tempBuildPath "src\app\api"
$tempNotFoundCatchAllPath = Join-Path $tempBuildPath "src\app\[lang]\[...not-found]"

if (-not (Test-Path $frontendPath)) {
  throw "Frontend path not found: $frontendPath"
}

Write-Host "Preparing static export for GoDaddy..."

if (Test-Path $tempBuildPath) {
  Remove-Item -Recurse -Force $tempBuildPath
}

Write-Host "Copying frontend project to temporary build folder..."
Copy-Item -Path $frontendPath -Destination $tempBuildPath -Recurse -Force

if (Test-Path $tempApiPath) {
  Write-Host "Removing src/app/api from temp build (not supported in static export)..."
  Remove-Item -Recurse -Force $tempApiPath
}

if (Test-Path -LiteralPath $tempNotFoundCatchAllPath) {
  Write-Host "Removing catch-all not-found route from temp build for export compatibility..."
  Remove-Item -Recurse -Force -LiteralPath $tempNotFoundCatchAllPath
}

try {
  Push-Location $tempBuildPath

  $env:NEXT_STATIC_EXPORT = "true"
  npx next build
  if ($LASTEXITCODE -ne 0) {
    throw "Static build failed. Fix build issues and run again."
  }

  $outPath = Join-Path $tempBuildPath "out"
  if (-not (Test-Path $outPath)) {
    throw "Build completed but 'out' folder was not generated."
  }

  if (Test-Path $deployFrontendPath) {
    Remove-Item -Recurse -Force $deployFrontendPath
  }

  New-Item -ItemType Directory -Path $deployFrontendPath | Out-Null
  Copy-Item -Path "$outPath\*" -Destination $deployFrontendPath -Recurse -Force

  Write-Host "Frontend static files copied to:"
  Write-Host $deployFrontendPath
}
finally {
  Pop-Location
  if (Test-Path $tempBuildPath) {
    Remove-Item -Recurse -Force $tempBuildPath
  }
}

Write-Host "Done."
