---
description: Clean up the root directory by removing legacy and temporary files.
---
1. **Remove Legacy Directory**
// turbo
```powershell
if (Test-Path -Path "C:\daakia\old_project") { Remove-Item -Path "C:\daakia\old_project" -Recurse -Force -ErrorAction SilentlyContinue }
```

2. **Remove Large Data Dumps and Parse Scripts**
// turbo
```powershell
Remove-Item -Path "C:\daakia\logistics.sql" -Force -ErrorAction SilentlyContinue
Remove-Item -Path "C:\daakia\parse_tables.js" -Force -ErrorAction SilentlyContinue
Remove-Item -Path "C:\daakia\parse_tables.py" -Force -ErrorAction SilentlyContinue
Remove-Item -Path "C:\daakia\tables.txt" -Force -ErrorAction SilentlyContinue
```

3. **Remove Intermediate PHP Scripts**
// turbo
```powershell
Remove-Item -Path "C:\daakia\create_laravel_tables.php" -Force -ErrorAction SilentlyContinue
Remove-Item -Path "C:\daakia\list_tables.php" -Force -ErrorAction SilentlyContinue
Remove-Item -Path "C:\daakia\verify_db.php" -Force -ErrorAction SilentlyContinue
```

4. **Remove Unused Batch Deployment Scripts**
// turbo
```powershell
Remove-Item -Path "C:\daakia\prepare-for-deployment.bat" -Force -ErrorAction SilentlyContinue
Remove-Item -Path "C:\daakia\prepare-for-godaddy-no-composer.bat" -Force -ErrorAction SilentlyContinue
Remove-Item -Path "C:\daakia\test-deployment.bat" -Force -ErrorAction SilentlyContinue
Remove-Item -Path "C:\daakia\import_db.bat" -Force -ErrorAction SilentlyContinue
Remove-Item -Path "C:\daakia\start-projects.bat" -Force -ErrorAction SilentlyContinue
```

5. **Remove Deployment Config Files**
// turbo
```powershell
Remove-Item -Path "C:\daakia\exclude-backend.txt" -Force -ErrorAction SilentlyContinue
Remove-Item -Path "C:\daakia\exclude-backend-no-composer.txt" -Force -ErrorAction SilentlyContinue
```

6. **Verify Remaining Files**
// turbo
```powershell
Get-ChildItem -Path "C:\daakia" | Select-Object Name
```
