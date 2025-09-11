<!DOCTYPE html>
<html>
<head>
    <title>Debug Test - ISHUME</title>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .test-section { border: 1px solid #ccc; padding: 15px; margin: 10px 0; }
        .success { color: green; }
        .error { color: red; }
        .info { color: blue; }
    </style>
</head>
<body>
    <h1>🔧 Debug Test - ISHUME Sistema</h1>
    
    <div class="test-section">
        <h2>Test 1: Verificación Básica</h2>
        <p class="success">✅ HTML cargado correctamente</p>
        <p id="jquery-test" class="error">❌ jQuery no cargado</p>
        <p id="sweetalert-test" class="error">❌ SweetAlert2 no cargado</p>
    </div>

    <div class="test-section">
        <h2>Test 2: Formulario Ultra Simple</h2>
        <form id="simple-form">
            <input type="text" id="test-name" placeholder="Nombre de prueba" required>
            <input type="email" id="test-email" placeholder="email@test.com" required>
            <button type="submit">Enviar Test</button>
        </form>
        <div id="form-result"></div>
    </div>

    <div class="test-section">
        <h2>Test 3: AJAX Simple</h2>
        <button id="ajax-test">Probar AJAX</button>
        <div id="ajax-result"></div>
    </div>

    <div class="test-section">
        <h2>Test 4: CodeIgniter Routes</h2>
        <button id="route-test">Probar Ruta /usuarios</button>
        <div id="route-result"></div>
    </div>

    <script>
        // Test jQuery
        $(document).ready(function() {
            $('#jquery-test').removeClass('error').addClass('success').text('✅ jQuery cargado correctamente');
            
            // Test SweetAlert2
            if (typeof Swal !== 'undefined') {
                $('#sweetalert-test').removeClass('error').addClass('success').text('✅ SweetAlert2 cargado correctamente');
            }
            
            console.log('🔧 Debug: Página cargada, jQuery funcionando');
        });

        // Test formulario simple
        $('#simple-form').submit(function(e) {
            e.preventDefault();
            console.log('🔧 Debug: Formulario enviado');
            
            var name = $('#test-name').val();
            var email = $('#test-email').val();
            
            $('#form-result').html('<p class="success">✅ Formulario funciona: ' + name + ' - ' + email + '</p>');
            
            Swal.fire({
                title: 'Test Exitoso',
                text: 'El formulario básico funciona correctamente',
                icon: 'success'
            });
        });

        // Test AJAX simple
        $('#ajax-test').click(function() {
            console.log('🔧 Debug: Iniciando test AJAX');
            $('#ajax-result').html('<p class="info">⏳ Probando AJAX...</p>');
            
            $.ajax({
                url: '<?= base_url() ?>test_simple',
                type: 'GET',
                success: function(response) {
                    console.log('🔧 Debug: AJAX exitoso');
                    $('#ajax-result').html('<p class="success">✅ AJAX funciona correctamente</p>');
                },
                error: function(xhr, status, error) {
                    console.log('🔧 Debug: Error AJAX:', error);
                    $('#ajax-result').html('<p class="error">❌ Error AJAX: ' + error + '</p>');
                }
            });
        });

        // Test rutas CodeIgniter
        $('#route-test').click(function() {
            console.log('🔧 Debug: Probando ruta /usuarios');
            $('#route-result').html('<p class="info">⏳ Probando ruta...</p>');
            
            $.ajax({
                url: '<?= base_url() ?>usuarios',
                type: 'GET',
                success: function(response) {
                    console.log('🔧 Debug: Ruta /usuarios funciona');
                    $('#route-result').html('<p class="success">✅ Ruta /usuarios accesible</p>');
                },
                error: function(xhr, status, error) {
                    console.log('🔧 Debug: Error en ruta:', xhr.status, error);
                    $('#route-result').html('<p class="error">❌ Error ruta: ' + xhr.status + ' - ' + error + '</p>');
                }
            });
        });
    </script>
</body>
</html>
