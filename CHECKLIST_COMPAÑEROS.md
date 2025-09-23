# ✅ CHECKLIST RÁPIDO - SISTEMA RENIEC

## 🚀 **INSTALACIÓN RÁPIDA (5 MINUTOS)**

### **1. 📥 Obtener el Código**
```bash
git pull origin main
composer install
```

### **2. ⚙️ Configurar .env**
```bash
cp .env.example .env
# Editar .env con tus datos de BD
```

### **3. 🗄️ Base de Datos**
```bash
php spark migrate
```

### **4. ✅ Verificar Sistema**
```bash
php verificar_sistema.php
```

---

## 🧪 **PRUEBAS RÁPIDAS (2 MINUTOS)**

### **✅ Test 1: Login**
- URL: `http://localhost/ishume`
- Usuario: `admin@ishume.com`
- Password: `admin123`

### **✅ Test 2: RENIEC**
- Ir a: **Dashboard → Usuarios → Crear Usuario**
- DNI: `60752963`
- Esperar 2-3 segundos
- **Resultado esperado:** Autocompletado con "FABIAN ALONSO YATACO TASAYCO"

### **✅ Test 3: Error Handling**
- DNI: `12345678`
- **Resultado esperado:** "DNI no encontrado en RENIEC"

---

## 🚨 **SI ALGO FALLA**

### **❌ No autocompleta DNI:**
1. Verificar internet: `ping google.com`
2. Verificar logs: `tail -f writable/logs/log-*.log`
3. Ejecutar: `php verificar_sistema.php`

### **❌ Error de BD:**
```bash
php spark migrate
```

### **❌ Error 500:**
- Verificar permisos carpeta `writable/`
- Revisar logs en `writable/logs/`

---

## 📞 **CONTACTO RÁPIDO**

**Si tienes problemas:**
1. Ejecuta: `php verificar_sistema.php`
2. Copia la salida completa
3. Envía por WhatsApp/Slack con descripción del problema

---

## 🎯 **RESULTADO FINAL**

Si los 3 tests pasan → **¡Sistema 100% funcional!** 🎉

**Tiempo total:** ~7 minutos
