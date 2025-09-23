# 🚀 GUÍA DE INSTALACIÓN - SISTEMA RENIEC ISHUME

## 📋 **REQUISITOS PREVIOS**

### **🔧 Software Necesario:**
- ✅ PHP 8.0+ con extensiones: `curl`, `json`, `openssl`
- ✅ MySQL 5.7+ o MariaDB
- ✅ Servidor web (Apache/Nginx) o Laragon/XAMPP
- ✅ Composer (para dependencias PHP)
- ✅ Git (para clonar el repositorio)

### **🌐 Servicios Externos:**
- ✅ Token de API Decolecta (para consultas RENIEC)

---

## 🛠️ **PASOS DE INSTALACIÓN**

### **1. 📥 Clonar el Repositorio**
```bash
git clone [URL_DEL_REPOSITORIO] ishume
cd ishume
```

### **2. 📦 Instalar Dependencias**
```bash
composer install
```

### **3. ⚙️ Configurar Variables de Entorno**

#### **3.1 Copiar archivo de configuración:**
```bash
cp .env.example .env
```

#### **3.2 Editar `.env` con tus datos:**
```env
#--------------------------------------------------------------------
# DATABASE
#--------------------------------------------------------------------
database.default.hostname = localhost
database.default.database = ishume
database.default.username = tu_usuario_mysql
database.default.password = tu_password_mysql
database.default.DBDriver = MySQLi

#--------------------------------------------------------------------
# RENIEC/DECOLECTA API CONFIGURATION
#--------------------------------------------------------------------
DECOLECTA_API_TOKEN = sk_10069.nuBfTnQrhikrkOdGQ44JvDUJZvJx3NEk
DECOLECTA_API_URL = https://api.decolecta.com/v1/reniec/dni
DECOLECTA_TIMEOUT = 10
DECOLECTA_MAX_RETRIES = 2
```

### **4. 🗄️ Configurar Base de Datos**

#### **4.1 Crear base de datos:**
```sql
CREATE DATABASE ishume CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

#### **4.2 Ejecutar migraciones:**
```bash
php spark migrate
```

#### **4.3 Ejecutar seeders (opcional):**
```bash
php spark db:seed DatabaseSeeder
```

### **5. 🔐 Configurar Permisos**
```bash
# En Linux/Mac:
chmod -R 755 writable/
chmod -R 755 public/

# En Windows: Dar permisos de escritura a la carpeta writable/
```

---

## 🧪 **VERIFICACIÓN DE INSTALACIÓN**

### **1. ✅ Verificar Servidor Web**
- Accede a: `http://localhost/ishume`
- Deberías ver la página de login de ISHUME

### **2. ✅ Probar Login**
```
Usuario: admin@ishume.com
Contraseña: admin123
```

### **3. ✅ Verificar Sistema RENIEC**

#### **3.1 Ir a Gestión de Usuarios:**
- Dashboard → Usuarios → Crear Usuario

#### **3.2 Probar Validación DNI:**
- Ingresa un DNI válido: `60752963`
- Espera 2-3 segundos
- Deberías ver autocompletado: **FABIAN ALONSO YATACO TASAYCO**

#### **3.3 Probar con DNI Inválido:**
- Ingresa: `12345678`
- Debería mostrar: "DNI no encontrado en RENIEC"

---

## 🔍 **DIAGNÓSTICO DE PROBLEMAS**

### **❌ Si el DNI no se autocompleta:**

#### **1. Verificar Configuración:**
```bash
# Verificar que las extensiones PHP estén habilitadas
php -m | grep curl
php -m | grep json
php -m | grep openssl
```

#### **2. Verificar Base de Datos:**
```sql
-- Verificar que la tabla existe
SHOW TABLES LIKE 'reniec_cache';

-- Verificar estructura
DESCRIBE reniec_cache;
```

#### **3. Verificar Logs:**
```bash
# Ver logs de CodeIgniter
tail -f writable/logs/log-[FECHA].log

# Buscar errores RENIEC
grep -i "reniec\|decolecta" writable/logs/log-[FECHA].log
```

#### **4. Probar Conexión API:**
```bash
# Test manual con curl
curl -H "Authorization: Bearer sk_10069.nuBfTnQrhikrkOdGQ44JvDUJZvJx3NEk" \
     "https://api.decolecta.com/v1/reniec/dni?numero=60752963"
```

---

## 🎯 **CASOS DE PRUEBA RECOMENDADOS**

### **✅ DNIs Válidos para Probar:**
- `60752963` - FABIAN ALONSO YATACO TASAYCO
- `12345678` - (Usar DNI real de Perú)
- `87654321` - (Usar DNI real de Perú)

### **❌ DNIs Inválidos para Probar:**
- `1234567` - (7 dígitos - muy corto)
- `123456789` - (9 dígitos - muy largo)
- `abcdefgh` - (letras - formato inválido)
- `00000000` - (DNI no válido)

### **🔄 Flujo de Prueba Completo:**
1. **Crear Usuario** → Ingresar DNI válido → Verificar autocompletado
2. **Crear Usuario** → Ingresar DNI inválido → Verificar mensaje de error
3. **Crear Usuario** → Usar mismo DNI válido → Verificar cache (respuesta rápida)
4. **Listar Usuarios** → Verificar que se guardó correctamente

---

## 📊 **MONITOREO DEL SISTEMA**

### **📈 Estadísticas RENIEC:**
- Accede a: `Dashboard → Usuarios → Ver Estadísticas`
- Verifica: Total consultas, Cache hits, Errores

### **🗄️ Limpiar Cache (si es necesario):**
```bash
php spark reniec:clean-cache
```

---

## 🚨 **PROBLEMAS COMUNES Y SOLUCIONES**

### **1. Error "cURL not found"**
```bash
# Ubuntu/Debian:
sudo apt-get install php-curl

# CentOS/RHEL:
sudo yum install php-curl

# Windows (XAMPP): Descomentar en php.ini:
extension=curl
```

### **2. Error "Table 'reniec_cache' doesn't exist"**
```bash
php spark migrate
```

### **3. Error "Forbidden" al crear usuario**
- Verificar que CSRF esté habilitado en `app/Config/Filters.php`
- Limpiar cache del navegador

### **4. Token API inválido**
- Verificar token en `.env`
- Contactar administrador para token válido

---

## 📞 **SOPORTE**

### **🐛 Reportar Problemas:**
1. Incluir logs de error
2. Especificar pasos para reproducir
3. Indicar versión de PHP y sistema operativo

### **📝 Logs Importantes:**
- `writable/logs/log-[FECHA].log` - Logs generales
- Consola del navegador (F12) - Errores JavaScript
- Logs del servidor web (Apache/Nginx)

---

## ✅ **CHECKLIST DE VERIFICACIÓN**

- [ ] Servidor web funcionando
- [ ] Base de datos conectada
- [ ] Migraciones ejecutadas
- [ ] Variables .env configuradas
- [ ] Login admin funciona
- [ ] DNI válido se autocompleta
- [ ] DNI inválido muestra error
- [ ] Cache funciona (segunda consulta es rápida)
- [ ] Usuarios se crean correctamente

---

## 🎉 **¡SISTEMA LISTO!**

Si todos los pasos anteriores funcionan correctamente, el sistema RENIEC está **100% operativo** y listo para usar en producción.

**Versión:** 1.0  
**Última actualización:** Enero 2025
