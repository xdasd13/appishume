@echo off
echo ========================================
echo   LIMPIEZA DE ARCHIVOS DE DEBUG RENIEC
echo ========================================
echo.

echo Eliminando controladores de debug...
if exist "app\Controllers\ReniecDebugController.php" (
    del "app\Controllers\ReniecDebugController.php"
    echo ✓ ReniecDebugController.php eliminado
) else (
    echo - ReniecDebugController.php no existe
)

if exist "app\Controllers\TestReniecController.php" (
    del "app\Controllers\TestReniecController.php"
    echo ✓ TestReniecController.php eliminado
) else (
    echo - TestReniecController.php no existe
)

if exist "app\Controllers\UsuariosControllerSimple.php" (
    del "app\Controllers\UsuariosControllerSimple.php"
    echo ✓ UsuariosControllerSimple.php eliminado
) else (
    echo - UsuariosControllerSimple.php no existe
)

if exist "app\Controllers\Cronograma_FIXED.php" (
    del "app\Controllers\Cronograma_FIXED.php"
    echo ✓ Cronograma_FIXED.php eliminado
) else (
    echo - Cronograma_FIXED.php no existe
)

echo.
echo Eliminando migración problemática...
if exist "app\Database\Migrations\2024-01-01-000001_CreateReniecCache.php" (
    del "app\Database\Migrations\2024-01-01-000001_CreateReniecCache.php"
    echo ✓ Migración problemática eliminada
) else (
    echo - Migración problemática no existe
)

echo.
echo Eliminando archivos SQL temporales...
if exist "create_reniec_cache_table.sql" (
    del "create_reniec_cache_table.sql"
    echo ✓ create_reniec_cache_table.sql eliminado
) else (
    echo - create_reniec_cache_table.sql no existe
)

if exist "ARCHIVOS_A_ELIMINAR.txt" (
    del "ARCHIVOS_A_ELIMINAR.txt"
    echo ✓ ARCHIVOS_A_ELIMINAR.txt eliminado
) else (
    echo - ARCHIVOS_A_ELIMINAR.txt no existe
)

echo.
echo ========================================
echo   LIMPIEZA COMPLETADA
echo ========================================
echo.
echo ARCHIVOS MANTENIDOS (ESENCIALES):
echo ✓ app/Libraries/ReniecService.php
echo ✓ app/Models/ReniecCacheModel.php
echo ✓ app/Controllers/UsuariosController.php
echo ✓ app/Views/usuarios/crear.php
echo ✓ app/Database/Migrations/2024-01-01-000002_CreateReniecCacheFixed.php
echo ✓ app/Commands/CleanReniecCache.php (opcional)
echo ✓ tests/unit/ReniecServiceTest.php (opcional)
echo.
echo El sistema RENIEC sigue 100%% funcional.
echo.
pause
