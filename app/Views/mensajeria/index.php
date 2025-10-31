<?= $header; ?>

<!-- CSS específico para mensajería -->
<link rel="stylesheet" href="<?= base_url('assets/css/mensajeria-index.css') ?>">

<div class="container-fluid p-0" style="max-width: 100%; width: 100%; height: calc(100vh - 70px); margin: 0; padding: 0; position: relative;">
    <div class="row g-0" style="display: flex; margin: 0; width: 100%; height: 100%; align-items: stretch;">
        <!-- Panel izquierdo - Lista de conversaciones -->
        <div class="panel-conversaciones-left p-0 border-end bg-white" style="width: 260px; min-width: 260px; max-width: 260px; height: 100%;">
            <div class="d-flex flex-column h-100">
                <!-- Header del usuario -->
                <div class="p-4 border-bottom bg-light">
                    <div class="d-flex align-items-center">
                        <div class="avatar-circle me-3 bg-primary">
                            <i class="fas fa-user text-white"></i>
                        </div>
                        <div class="flex-grow-1">
                            <h6 class="mb-0 fw-bold"><?= session('usuario_nombre') ?? 'Usuario' ?></h6>
                            <small class="text-muted"><?= !empty($usuario_email) ? $usuario_email : (session('usuario_email') ?? '') ?></small>
                        </div>
                        <div class="dropdown">
                            <button class="btn btn-outline-secondary btn-sm" data-bs-toggle="dropdown">
                                <i class="fas fa-ellipsis-v"></i>
                            </button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="<?= base_url('mensajeria/configuracion') ?>">
                                    <i class="fas fa-cog me-2"></i>Configuración
                                </a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="<?= base_url('mensajeria/enviar') ?>">
                                    <i class="fas fa-plus me-2"></i>Nuevo Mensaje
                                </a></li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Barra de búsqueda -->
                <div class="p-3">
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0">
                            <i class="fas fa-search text-muted"></i>
                        </span>
                        <input type="text" class="form-control border-start-0" 
                               placeholder="Buscar conversaciones..." id="buscarConversaciones">
                    </div>
                </div>

                <!-- Lista de conversaciones -->
                <div class="flex-grow-1 overflow-auto scrollbar-custom panel-conversaciones" id="listaConversaciones">
                    <div class="text-center p-4">
                        <i class="fas fa-spinner fa-spin fa-2x text-muted"></i>
                        <p class="text-muted mt-2">Cargando conversaciones...</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Panel derecho - Chat -->
        <div class="flex-grow-1 p-0" style="flex: 1; min-width: 0; height: 100%;">
            <div class="d-flex flex-column h-100 bg-light">
                <!-- Header del chat -->
                <div class="p-3 border-bottom bg-white" id="headerChat" style="display: none;">
                    <div class="d-flex align-items-center">
                        <button class="btn btn-outline-secondary btn-sm me-3 d-md-none" id="btnToggleSidebar">
                            <i class="fas fa-bars"></i>
                        </button>
                        <div class="avatar-circle me-3 bg-success">
                            <i class="fas fa-user text-white"></i>
                        </div>
                        <div class="flex-grow-1">
                            <h6 class="mb-0 fw-bold" id="nombreDestinatario">Selecciona una conversación</h6>
                            <small class="text-muted" id="estadoDestinatario"></small>
                        </div>
                        <div class="dropdown">
                            <button class="btn btn-outline-secondary btn-sm" data-bs-toggle="dropdown">
                                <i class="fas fa-ellipsis-v"></i>
                            </button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="#" id="btnVerPerfil">
                                    <i class="fas fa-user me-2"></i>Ver Perfil
                                </a></li>
                                <li><a class="dropdown-item" href="#" id="btnEliminarConversacion">
                                    <i class="fas fa-trash me-2"></i>Eliminar Conversación
                                </a></li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Área de mensajes -->
                <div class="flex-grow-1 overflow-auto p-3 scrollbar-custom panel-mensajes" id="areaMensajes">
                    <div class="text-center p-5">
                        <div class="mb-4">
                            <i class="fas fa-comments fa-4x text-muted mb-3"></i>
                        </div>
                        <h4 class="text-muted mb-3">Selecciona una conversación</h4>
                        <p class="text-muted mb-4">Elige una conversación de la lista para comenzar a chatear</p>
                        <a href="<?= base_url('mensajeria/enviar') ?>" class="btn btn-primary btn-lg">
                            <i class="fas fa-plus me-2"></i>Nuevo Mensaje
                        </a>
                    </div>
                </div>

                <!-- Área de escritura -->
                <div class="p-3 border-top bg-white area-escritura" id="areaEscritura" style="display: none;">
                    <form id="formEnviarMensaje" enctype="multipart/form-data">
                        <input type="hidden" id="destinatario_id" name="destinatario_id">
                        <div class="input-group">
                            <button type="button" class="btn btn-outline-secondary" id="btnAdjuntar">
                                <i class="fas fa-paperclip"></i>
                            </button>
                            <input type="file" id="archivoAdjunto" name="archivo" class="d-none" accept="image/*" />
                            <input type="text" class="form-control" id="mensajeTexto" 
                                   placeholder="Escribe tu mensaje..." autocomplete="off">
                            <button type="button" class="btn btn-outline-secondary position-relative" id="btnEmoji">
                                <i class="fas fa-smile"></i>
                            </button>
                            <button type="submit" class="btn btn-primary" id="btnEnviar">
                                <i class="fas fa-paper-plane"></i>
                            </button>
                        </div>
                        <!-- Preview de imagen seleccionada -->
                        <div id="previewImagen" class="mt-2 mb-2" style="display: none;">
                            <div class="position-relative d-inline-block">
                                <img id="previewImg" src="" alt="Preview" style="max-width: 200px; max-height: 200px; border-radius: 8px; border: 1px solid #ddd;">
                                <button type="button" class="btn btn-sm btn-danger position-absolute top-0 end-0 m-1" id="btnQuitarPreview" style="border-radius: 50%; width: 25px; height: 25px; padding: 0; line-height: 1;">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Overlay para móviles -->
<div class="mobile-overlay" id="mobileOverlay"></div>

<!-- Selector de emojis -->
<div id="emojiPicker" class="bg-white border rounded shadow-lg p-3" style="display: none; position: absolute; bottom: 80px; right: 20px; width: 320px; max-height: 300px; overflow-y: auto; z-index: 1050; border-radius: 12px;">
    <div class="d-flex justify-content-between align-items-center mb-2">
        <small class="text-muted fw-bold">Emojis</small>
        <button type="button" class="btn-close btn-sm" id="btnCerrarEmoji"></button>
    </div>
    <div class="emoji-grid" style="display: grid; grid-template-columns: repeat(8, 1fr); gap: 8px;">
        <span class="emoji-item text-center p-2 rounded" data-emoji="😀" style="cursor: pointer; font-size: 1.5em; transition: background 0.2s;" onmouseover="this.style.background='#f0f0f0'" onmouseout="this.style.background='transparent'">😀</span>
        <span class="emoji-item text-center p-2 rounded" data-emoji="😃" style="cursor: pointer; font-size: 1.5em; transition: background 0.2s;" onmouseover="this.style.background='#f0f0f0'" onmouseout="this.style.background='transparent'">😃</span>
        <span class="emoji-item text-center p-2 rounded" data-emoji="😄" style="cursor: pointer; font-size: 1.5em; transition: background 0.2s;" onmouseover="this.style.background='#f0f0f0'" onmouseout="this.style.background='transparent'">😄</span>
        <span class="emoji-item text-center p-2 rounded" data-emoji="😁" style="cursor: pointer; font-size: 1.5em; transition: background 0.2s;" onmouseover="this.style.background='#f0f0f0'" onmouseout="this.style.background='transparent'">😁</span>
        <span class="emoji-item text-center p-2 rounded" data-emoji="😅" style="cursor: pointer; font-size: 1.5em; transition: background 0.2s;" onmouseover="this.style.background='#f0f0f0'" onmouseout="this.style.background='transparent'">😅</span>
        <span class="emoji-item text-center p-2 rounded" data-emoji="😂" style="cursor: pointer; font-size: 1.5em; transition: background 0.2s;" onmouseover="this.style.background='#f0f0f0'" onmouseout="this.style.background='transparent'">😂</span>
        <span class="emoji-item text-center p-2 rounded" data-emoji="🤣" style="cursor: pointer; font-size: 1.5em; transition: background 0.2s;" onmouseover="this.style.background='#f0f0f0'" onmouseout="this.style.background='transparent'">🤣</span>
        <span class="emoji-item text-center p-2 rounded" data-emoji="😊" style="cursor: pointer; font-size: 1.5em; transition: background 0.2s;" onmouseover="this.style.background='#f0f0f0'" onmouseout="this.style.background='transparent'">😊</span>
        <span class="emoji-item text-center p-2 rounded" data-emoji="😇" style="cursor: pointer; font-size: 1.5em; transition: background 0.2s;" onmouseover="this.style.background='#f0f0f0'" onmouseout="this.style.background='transparent'">😇</span>
        <span class="emoji-item text-center p-2 rounded" data-emoji="🙂" style="cursor: pointer; font-size: 1.5em; transition: background 0.2s;" onmouseover="this.style.background='#f0f0f0'" onmouseout="this.style.background='transparent'">🙂</span>
        <span class="emoji-item text-center p-2 rounded" data-emoji="🙃" style="cursor: pointer; font-size: 1.5em; transition: background 0.2s;" onmouseover="this.style.background='#f0f0f0'" onmouseout="this.style.background='transparent'">🙃</span>
        <span class="emoji-item text-center p-2 rounded" data-emoji="😉" style="cursor: pointer; font-size: 1.5em; transition: background 0.2s;" onmouseover="this.style.background='#f0f0f0'" onmouseout="this.style.background='transparent'">😉</span>
        <span class="emoji-item text-center p-2 rounded" data-emoji="😌" style="cursor: pointer; font-size: 1.5em; transition: background 0.2s;" onmouseover="this.style.background='#f0f0f0'" onmouseout="this.style.background='transparent'">😌</span>
        <span class="emoji-item text-center p-2 rounded" data-emoji="😍" style="cursor: pointer; font-size: 1.5em; transition: background 0.2s;" onmouseover="this.style.background='#f0f0f0'" onmouseout="this.style.background='transparent'">😍</span>
        <span class="emoji-item text-center p-2 rounded" data-emoji="🥰" style="cursor: pointer; font-size: 1.5em; transition: background 0.2s;" onmouseover="this.style.background='#f0f0f0'" onmouseout="this.style.background='transparent'">🥰</span>
        <span class="emoji-item text-center p-2 rounded" data-emoji="😘" style="cursor: pointer; font-size: 1.5em; transition: background 0.2s;" onmouseover="this.style.background='#f0f0f0'" onmouseout="this.style.background='transparent'">😘</span>
        <span class="emoji-item text-center p-2 rounded" data-emoji="😗" style="cursor: pointer; font-size: 1.5em; transition: background 0.2s;" onmouseover="this.style.background='#f0f0f0'" onmouseout="this.style.background='transparent'">😗</span>
        <span class="emoji-item text-center p-2 rounded" data-emoji="😙" style="cursor: pointer; font-size: 1.5em; transition: background 0.2s;" onmouseover="this.style.background='#f0f0f0'" onmouseout="this.style.background='transparent'">😙</span>
        <span class="emoji-item text-center p-2 rounded" data-emoji="😚" style="cursor: pointer; font-size: 1.5em; transition: background 0.2s;" onmouseover="this.style.background='#f0f0f0'" onmouseout="this.style.background='transparent'">😚</span>
        <span class="emoji-item text-center p-2 rounded" data-emoji="😋" style="cursor: pointer; font-size: 1.5em; transition: background 0.2s;" onmouseover="this.style.background='#f0f0f0'" onmouseout="this.style.background='transparent'">😋</span>
        <span class="emoji-item text-center p-2 rounded" data-emoji="😛" style="cursor: pointer; font-size: 1.5em; transition: background 0.2s;" onmouseover="this.style.background='#f0f0f0'" onmouseout="this.style.background='transparent'">😛</span>
        <span class="emoji-item text-center p-2 rounded" data-emoji="😝" style="cursor: pointer; font-size: 1.5em; transition: background 0.2s;" onmouseover="this.style.background='#f0f0f0'" onmouseout="this.style.background='transparent'">😝</span>
        <span class="emoji-item text-center p-2 rounded" data-emoji="😜" style="cursor: pointer; font-size: 1.5em; transition: background 0.2s;" onmouseover="this.style.background='#f0f0f0'" onmouseout="this.style.background='transparent'">😜</span>
        <span class="emoji-item text-center p-2 rounded" data-emoji="🤪" style="cursor: pointer; font-size: 1.5em; transition: background 0.2s;" onmouseover="this.style.background='#f0f0f0'" onmouseout="this.style.background='transparent'">🤪</span>
        <span class="emoji-item text-center p-2 rounded" data-emoji="🤨" style="cursor: pointer; font-size: 1.5em; transition: background 0.2s;" onmouseover="this.style.background='#f0f0f0'" onmouseout="this.style.background='transparent'">🤨</span>
        <span class="emoji-item text-center p-2 rounded" data-emoji="🧐" style="cursor: pointer; font-size: 1.5em; transition: background 0.2s;" onmouseover="this.style.background='#f0f0f0'" onmouseout="this.style.background='transparent'">🧐</span>
        <span class="emoji-item text-center p-2 rounded" data-emoji="🤓" style="cursor: pointer; font-size: 1.5em; transition: background 0.2s;" onmouseover="this.style.background='#f0f0f0'" onmouseout="this.style.background='transparent'">🤓</span>
        <span class="emoji-item text-center p-2 rounded" data-emoji="😎" style="cursor: pointer; font-size: 1.5em; transition: background 0.2s;" onmouseover="this.style.background='#f0f0f0'" onmouseout="this.style.background='transparent'">😎</span>
        <span class="emoji-item text-center p-2 rounded" data-emoji="🤩" style="cursor: pointer; font-size: 1.5em; transition: background 0.2s;" onmouseover="this.style.background='#f0f0f0'" onmouseout="this.style.background='transparent'">🤩</span>
        <span class="emoji-item text-center p-2 rounded" data-emoji="🥳" style="cursor: pointer; font-size: 1.5em; transition: background 0.2s;" onmouseover="this.style.background='#f0f0f0'" onmouseout="this.style.background='transparent'">🥳</span>
        <span class="emoji-item text-center p-2 rounded" data-emoji="😏" style="cursor: pointer; font-size: 1.5em; transition: background 0.2s;" onmouseover="this.style.background='#f0f0f0'" onmouseout="this.style.background='transparent'">😏</span>
        <span class="emoji-item text-center p-2 rounded" data-emoji="😒" style="cursor: pointer; font-size: 1.5em; transition: background 0.2s;" onmouseover="this.style.background='#f0f0f0'" onmouseout="this.style.background='transparent'">😒</span>
        <span class="emoji-item text-center p-2 rounded" data-emoji="😞" style="cursor: pointer; font-size: 1.5em; transition: background 0.2s;" onmouseover="this.style.background='#f0f0f0'" onmouseout="this.style.background='transparent'">😞</span>
        <span class="emoji-item text-center p-2 rounded" data-emoji="😔" style="cursor: pointer; font-size: 1.5em; transition: background 0.2s;" onmouseover="this.style.background='#f0f0f0'" onmouseout="this.style.background='transparent'">😔</span>
        <span class="emoji-item text-center p-2 rounded" data-emoji="😟" style="cursor: pointer; font-size: 1.5em; transition: background 0.2s;" onmouseover="this.style.background='#f0f0f0'" onmouseout="this.style.background='transparent'">😟</span>
        <span class="emoji-item text-center p-2 rounded" data-emoji="😕" style="cursor: pointer; font-size: 1.5em; transition: background 0.2s;" onmouseover="this.style.background='#f0f0f0'" onmouseout="this.style.background='transparent'">😕</span>
        <span class="emoji-item text-center p-2 rounded" data-emoji="🙁" style="cursor: pointer; font-size: 1.5em; transition: background 0.2s;" onmouseover="this.style.background='#f0f0f0'" onmouseout="this.style.background='transparent'">🙁</span>
        <span class="emoji-item text-center p-2 rounded" data-emoji="☹️" style="cursor: pointer; font-size: 1.5em; transition: background 0.2s;" onmouseover="this.style.background='#f0f0f0'" onmouseout="this.style.background='transparent'">☹️</span>
        <span class="emoji-item text-center p-2 rounded" data-emoji="😣" style="cursor: pointer; font-size: 1.5em; transition: background 0.2s;" onmouseover="this.style.background='#f0f0f0'" onmouseout="this.style.background='transparent'">😣</span>
        <span class="emoji-item text-center p-2 rounded" data-emoji="😖" style="cursor: pointer; font-size: 1.5em; transition: background 0.2s;" onmouseover="this.style.background='#f0f0f0'" onmouseout="this.style.background='transparent'">😖</span>
        <span class="emoji-item text-center p-2 rounded" data-emoji="😫" style="cursor: pointer; font-size: 1.5em; transition: background 0.2s;" onmouseover="this.style.background='#f0f0f0'" onmouseout="this.style.background='transparent'">😫</span>
        <span class="emoji-item text-center p-2 rounded" data-emoji="😩" style="cursor: pointer; font-size: 1.5em; transition: background 0.2s;" onmouseover="this.style.background='#f0f0f0'" onmouseout="this.style.background='transparent'">😩</span>
        <span class="emoji-item text-center p-2 rounded" data-emoji="🥺" style="cursor: pointer; font-size: 1.5em; transition: background 0.2s;" onmouseover="this.style.background='#f0f0f0'" onmouseout="this.style.background='transparent'">🥺</span>
        <span class="emoji-item text-center p-2 rounded" data-emoji="😢" style="cursor: pointer; font-size: 1.5em; transition: background 0.2s;" onmouseover="this.style.background='#f0f0f0'" onmouseout="this.style.background='transparent'">😢</span>
        <span class="emoji-item text-center p-2 rounded" data-emoji="😭" style="cursor: pointer; font-size: 1.5em; transition: background 0.2s;" onmouseover="this.style.background='#f0f0f0'" onmouseout="this.style.background='transparent'">😭</span>
        <span class="emoji-item text-center p-2 rounded" data-emoji="😤" style="cursor: pointer; font-size: 1.5em; transition: background 0.2s;" onmouseover="this.style.background='#f0f0f0'" onmouseout="this.style.background='transparent'">😤</span>
        <span class="emoji-item text-center p-2 rounded" data-emoji="😠" style="cursor: pointer; font-size: 1.5em; transition: background 0.2s;" onmouseover="this.style.background='#f0f0f0'" onmouseout="this.style.background='transparent'">😠</span>
        <span class="emoji-item text-center p-2 rounded" data-emoji="😡" style="cursor: pointer; font-size: 1.5em; transition: background 0.2s;" onmouseover="this.style.background='#f0f0f0'" onmouseout="this.style.background='transparent'">😡</span>
        <span class="emoji-item text-center p-2 rounded" data-emoji="🤬" style="cursor: pointer; font-size: 1.5em; transition: background 0.2s;" onmouseover="this.style.background='#f0f0f0'" onmouseout="this.style.background='transparent'">🤬</span>
        <span class="emoji-item text-center p-2 rounded" data-emoji="🤯" style="cursor: pointer; font-size: 1.5em; transition: background 0.2s;" onmouseover="this.style.background='#f0f0f0'" onmouseout="this.style.background='transparent'">🤯</span>
        <span class="emoji-item text-center p-2 rounded" data-emoji="😳" style="cursor: pointer; font-size: 1.5em; transition: background 0.2s;" onmouseover="this.style.background='#f0f0f0'" onmouseout="this.style.background='transparent'">😳</span>
        <span class="emoji-item text-center p-2 rounded" data-emoji="🥵" style="cursor: pointer; font-size: 1.5em; transition: background 0.2s;" onmouseover="this.style.background='#f0f0f0'" onmouseout="this.style.background='transparent'">🥵</span>
        <span class="emoji-item text-center p-2 rounded" data-emoji="🥶" style="cursor: pointer; font-size: 1.5em; transition: background 0.2s;" onmouseover="this.style.background='#f0f0f0'" onmouseout="this.style.background='transparent'">🥶</span>
        <span class="emoji-item text-center p-2 rounded" data-emoji="😱" style="cursor: pointer; font-size: 1.5em; transition: background 0.2s;" onmouseover="this.style.background='#f0f0f0'" onmouseout="this.style.background='transparent'">😱</span>
        <span class="emoji-item text-center p-2 rounded" data-emoji="😨" style="cursor: pointer; font-size: 1.5em; transition: background 0.2s;" onmouseover="this.style.background='#f0f0f0'" onmouseout="this.style.background='transparent'">😨</span>
        <span class="emoji-item text-center p-2 rounded" data-emoji="😰" style="cursor: pointer; font-size: 1.5em; transition: background 0.2s;" onmouseover="this.style.background='#f0f0f0'" onmouseout="this.style.background='transparent'">😰</span>
        <span class="emoji-item text-center p-2 rounded" data-emoji="😥" style="cursor: pointer; font-size: 1.5em; transition: background 0.2s;" onmouseover="this.style.background='#f0f0f0'" onmouseout="this.style.background='transparent'">😥</span>
        <span class="emoji-item text-center p-2 rounded" data-emoji="😓" style="cursor: pointer; font-size: 1.5em; transition: background 0.2s;" onmouseover="this.style.background='#f0f0f0'" onmouseout="this.style.background='transparent'">😓</span>
        <span class="emoji-item text-center p-2 rounded" data-emoji="🤗" style="cursor: pointer; font-size: 1.5em; transition: background 0.2s;" onmouseover="this.style.background='#f0f0f0'" onmouseout="this.style.background='transparent'">🤗</span>
        <span class="emoji-item text-center p-2 rounded" data-emoji="🤔" style="cursor: pointer; font-size: 1.5em; transition: background 0.2s;" onmouseover="this.style.background='#f0f0f0'" onmouseout="this.style.background='transparent'">🤔</span>
        <span class="emoji-item text-center p-2 rounded" data-emoji="🤭" style="cursor: pointer; font-size: 1.5em; transition: background 0.2s;" onmouseover="this.style.background='#f0f0f0'" onmouseout="this.style.background='transparent'">🤭</span>
        <span class="emoji-item text-center p-2 rounded" data-emoji="🤫" style="cursor: pointer; font-size: 1.5em; transition: background 0.2s;" onmouseover="this.style.background='#f0f0f0'" onmouseout="this.style.background='transparent'">🤫</span>
        <span class="emoji-item text-center p-2 rounded" data-emoji="🤥" style="cursor: pointer; font-size: 1.5em; transition: background 0.2s;" onmouseover="this.style.background='#f0f0f0'" onmouseout="this.style.background='transparent'">🤥</span>
        <span class="emoji-item text-center p-2 rounded" data-emoji="😶" style="cursor: pointer; font-size: 1.5em; transition: background 0.2s;" onmouseover="this.style.background='#f0f0f0'" onmouseout="this.style.background='transparent'">😶</span>
        <span class="emoji-item text-center p-2 rounded" data-emoji="😐" style="cursor: pointer; font-size: 1.5em; transition: background 0.2s;" onmouseover="this.style.background='#f0f0f0'" onmouseout="this.style.background='transparent'">😐</span>
        <span class="emoji-item text-center p-2 rounded" data-emoji="😑" style="cursor: pointer; font-size: 1.5em; transition: background 0.2s;" onmouseover="this.style.background='#f0f0f0'" onmouseout="this.style.background='transparent'">😑</span>
        <span class="emoji-item text-center p-2 rounded" data-emoji="😬" style="cursor: pointer; font-size: 1.5em; transition: background 0.2s;" onmouseover="this.style.background='#f0f0f0'" onmouseout="this.style.background='transparent'">😬</span>
        <span class="emoji-item text-center p-2 rounded" data-emoji="🙄" style="cursor: pointer; font-size: 1.5em; transition: background 0.2s;" onmouseover="this.style.background='#f0f0f0'" onmouseout="this.style.background='transparent'">🙄</span>
        <span class="emoji-item text-center p-2 rounded" data-emoji="😯" style="cursor: pointer; font-size: 1.5em; transition: background 0.2s;" onmouseover="this.style.background='#f0f0f0'" onmouseout="this.style.background='transparent'">😯</span>
        <span class="emoji-item text-center p-2 rounded" data-emoji="😦" style="cursor: pointer; font-size: 1.5em; transition: background 0.2s;" onmouseover="this.style.background='#f0f0f0'" onmouseout="this.style.background='transparent'">😦</span>
        <span class="emoji-item text-center p-2 rounded" data-emoji="😧" style="cursor: pointer; font-size: 1.5em; transition: background 0.2s;" onmouseover="this.style.background='#f0f0f0'" onmouseout="this.style.background='transparent'">😧</span>
        <span class="emoji-item text-center p-2 rounded" data-emoji="😮" style="cursor: pointer; font-size: 1.5em; transition: background 0.2s;" onmouseover="this.style.background='#f0f0f0'" onmouseout="this.style.background='transparent'">😮</span>
        <span class="emoji-item text-center p-2 rounded" data-emoji="😲" style="cursor: pointer; font-size: 1.5em; transition: background 0.2s;" onmouseover="this.style.background='#f0f0f0'" onmouseout="this.style.background='transparent'">😲</span>
        <span class="emoji-item text-center p-2 rounded" data-emoji="🥱" style="cursor: pointer; font-size: 1.5em; transition: background 0.2s;" onmouseover="this.style.background='#f0f0f0'" onmouseout="this.style.background='transparent'">🥱</span>
        <span class="emoji-item text-center p-2 rounded" data-emoji="😴" style="cursor: pointer; font-size: 1.5em; transition: background 0.2s;" onmouseover="this.style.background='#f0f0f0'" onmouseout="this.style.background='transparent'">😴</span>
        <span class="emoji-item text-center p-2 rounded" data-emoji="🤤" style="cursor: pointer; font-size: 1.5em; transition: background 0.2s;" onmouseover="this.style.background='#f0f0f0'" onmouseout="this.style.background='transparent'">🤤</span>
        <span class="emoji-item text-center p-2 rounded" data-emoji="😪" style="cursor: pointer; font-size: 1.5em; transition: background 0.2s;" onmouseover="this.style.background='#f0f0f0'" onmouseout="this.style.background='transparent'">😪</span>
        <span class="emoji-item text-center p-2 rounded" data-emoji="😵" style="cursor: pointer; font-size: 1.5em; transition: background 0.2s;" onmouseover="this.style.background='#f0f0f0'" onmouseout="this.style.background='transparent'">😵</span>
        <span class="emoji-item text-center p-2 rounded" data-emoji="🤐" style="cursor: pointer; font-size: 1.5em; transition: background 0.2s;" onmouseover="this.style.background='#f0f0f0'" onmouseout="this.style.background='transparent'">🤐</span>
        <span class="emoji-item text-center p-2 rounded" data-emoji="🥴" style="cursor: pointer; font-size: 1.5em; transition: background 0.2s;" onmouseover="this.style.background='#f0f0f0'" onmouseout="this.style.background='transparent'">🥴</span>
        <span class="emoji-item text-center p-2 rounded" data-emoji="🤢" style="cursor: pointer; font-size: 1.5em; transition: background 0.2s;" onmouseover="this.style.background='#f0f0f0'" onmouseout="this.style.background='transparent'">🤢</span>
        <span class="emoji-item text-center p-2 rounded" data-emoji="🤮" style="cursor: pointer; font-size: 1.5em; transition: background 0.2s;" onmouseover="this.style.background='#f0f0f0'" onmouseout="this.style.background='transparent'">🤮</span>
        <span class="emoji-item text-center p-2 rounded" data-emoji="🤧" style="cursor: pointer; font-size: 1.5em; transition: background 0.2s;" onmouseover="this.style.background='#f0f0f0'" onmouseout="this.style.background='transparent'">🤧</span>
        <span class="emoji-item text-center p-2 rounded" data-emoji="😷" style="cursor: pointer; font-size: 1.5em; transition: background 0.2s;" onmouseover="this.style.background='#f0f0f0'" onmouseout="this.style.background='transparent'">😷</span>
        <span class="emoji-item text-center p-2 rounded" data-emoji="🤒" style="cursor: pointer; font-size: 1.5em; transition: background 0.2s;" onmouseover="this.style.background='#f0f0f0'" onmouseout="this.style.background='transparent'">🤒</span>
        <span class="emoji-item text-center p-2 rounded" data-emoji="🤕" style="cursor: pointer; font-size: 1.5em; transition: background 0.2s;" onmouseover="this.style.background='#f0f0f0'" onmouseout="this.style.background='transparent'">🤕</span>
        <span class="emoji-item text-center p-2 rounded" data-emoji="🤑" style="cursor: pointer; font-size: 1.5em; transition: background 0.2s;" onmouseover="this.style.background='#f0f0f0'" onmouseout="this.style.background='transparent'">🤑</span>
        <span class="emoji-item text-center p-2 rounded" data-emoji="🤠" style="cursor: pointer; font-size: 1.5em; transition: background 0.2s;" onmouseover="this.style.background='#f0f0f0'" onmouseout="this.style.background='transparent'">🤠</span>
        <span class="emoji-item text-center p-2 rounded" data-emoji="😈" style="cursor: pointer; font-size: 1.5em; transition: background 0.2s;" onmouseover="this.style.background='#f0f0f0'" onmouseout="this.style.background='transparent'">😈</span>
        <span class="emoji-item text-center p-2 rounded" data-emoji="👿" style="cursor: pointer; font-size: 1.5em; transition: background 0.2s;" onmouseover="this.style.background='#f0f0f0'" onmouseout="this.style.background='transparent'">👿</span>
        <span class="emoji-item text-center p-2 rounded" data-emoji="👹" style="cursor: pointer; font-size: 1.5em; transition: background 0.2s;" onmouseover="this.style.background='#f0f0f0'" onmouseout="this.style.background='transparent'">👹</span>
        <span class="emoji-item text-center p-2 rounded" data-emoji="👺" style="cursor: pointer; font-size: 1.5em; transition: background 0.2s;" onmouseover="this.style.background='#f0f0f0'" onmouseout="this.style.background='transparent'">👺</span>
        <span class="emoji-item text-center p-2 rounded" data-emoji="🤡" style="cursor: pointer; font-size: 1.5em; transition: background 0.2s;" onmouseover="this.style.background='#f0f0f0'" onmouseout="this.style.background='transparent'">🤡</span>
        <span class="emoji-item text-center p-2 rounded" data-emoji="💩" style="cursor: pointer; font-size: 1.5em; transition: background 0.2s;" onmouseover="this.style.background='#f0f0f0'" onmouseout="this.style.background='transparent'">💩</span>
        <span class="emoji-item text-center p-2 rounded" data-emoji="👻" style="cursor: pointer; font-size: 1.5em; transition: background 0.2s;" onmouseover="this.style.background='#f0f0f0'" onmouseout="this.style.background='transparent'">👻</span>
        <span class="emoji-item text-center p-2 rounded" data-emoji="💀" style="cursor: pointer; font-size: 1.5em; transition: background 0.2s;" onmouseover="this.style.background='#f0f0f0'" onmouseout="this.style.background='transparent'">💀</span>
        <span class="emoji-item text-center p-2 rounded" data-emoji="☠️" style="cursor: pointer; font-size: 1.5em; transition: background 0.2s;" onmouseover="this.style.background='#f0f0f0'" onmouseout="this.style.background='transparent'">☠️</span>
        <span class="emoji-item text-center p-2 rounded" data-emoji="👽" style="cursor: pointer; font-size: 1.5em; transition: background 0.2s;" onmouseover="this.style.background='#f0f0f0'" onmouseout="this.style.background='transparent'">👽</span>
        <span class="emoji-item text-center p-2 rounded" data-emoji="👾" style="cursor: pointer; font-size: 1.5em; transition: background 0.2s;" onmouseover="this.style.background='#f0f0f0'" onmouseout="this.style.background='transparent'">👾</span>
        <span class="emoji-item text-center p-2 rounded" data-emoji="🤖" style="cursor: pointer; font-size: 1.5em; transition: background 0.2s;" onmouseover="this.style.background='#f0f0f0'" onmouseout="this.style.background='transparent'">🤖</span>
        <span class="emoji-item text-center p-2 rounded" data-emoji="🎃" style="cursor: pointer; font-size: 1.5em; transition: background 0.2s;" onmouseover="this.style.background='#f0f0f0'" onmouseout="this.style.background='transparent'">🎃</span>
        <span class="emoji-item text-center p-2 rounded" data-emoji="👋" style="cursor: pointer; font-size: 1.5em; transition: background 0.2s;" onmouseover="this.style.background='#f0f0f0'" onmouseout="this.style.background='transparent'">👋</span>
        <span class="emoji-item text-center p-2 rounded" data-emoji="🤚" style="cursor: pointer; font-size: 1.5em; transition: background 0.2s;" onmouseover="this.style.background='#f0f0f0'" onmouseout="this.style.background='transparent'">🤚</span>
        <span class="emoji-item text-center p-2 rounded" data-emoji="🖐" style="cursor: pointer; font-size: 1.5em; transition: background 0.2s;" onmouseover="this.style.background='#f0f0f0'" onmouseout="this.style.background='transparent'">🖐</span>
        <span class="emoji-item text-center p-2 rounded" data-emoji="✋" style="cursor: pointer; font-size: 1.5em; transition: background 0.2s;" onmouseover="this.style.background='#f0f0f0'" onmouseout="this.style.background='transparent'">✋</span>
        <span class="emoji-item text-center p-2 rounded" data-emoji="🖖" style="cursor: pointer; font-size: 1.5em; transition: background 0.2s;" onmouseover="this.style.background='#f0f0f0'" onmouseout="this.style.background='transparent'">🖖</span>
        <span class="emoji-item text-center p-2 rounded" data-emoji="👌" style="cursor: pointer; font-size: 1.5em; transition: background 0.2s;" onmouseover="this.style.background='#f0f0f0'" onmouseout="this.style.background='transparent'">👌</span>
        <span class="emoji-item text-center p-2 rounded" data-emoji="🤏" style="cursor: pointer; font-size: 1.5em; transition: background 0.2s;" onmouseover="this.style.background='#f0f0f0'" onmouseout="this.style.background='transparent'">🤏</span>
        <span class="emoji-item text-center p-2 rounded" data-emoji="✌️" style="cursor: pointer; font-size: 1.5em; transition: background 0.2s;" onmouseover="this.style.background='#f0f0f0'" onmouseout="this.style.background='transparent'">✌️</span>
        <span class="emoji-item text-center p-2 rounded" data-emoji="🤞" style="cursor: pointer; font-size: 1.5em; transition: background 0.2s;" onmouseover="this.style.background='#f0f0f0'" onmouseout="this.style.background='transparent'">🤞</span>
        <span class="emoji-item text-center p-2 rounded" data-emoji="🤟" style="cursor: pointer; font-size: 1.5em; transition: background 0.2s;" onmouseover="this.style.background='#f0f0f0'" onmouseout="this.style.background='transparent'">🤟</span>
        <span class="emoji-item text-center p-2 rounded" data-emoji="🤘" style="cursor: pointer; font-size: 1.5em; transition: background 0.2s;" onmouseover="this.style.background='#f0f0f0'" onmouseout="this.style.background='transparent'">🤘</span>
        <span class="emoji-item text-center p-2 rounded" data-emoji="🤙" style="cursor: pointer; font-size: 1.5em; transition: background 0.2s;" onmouseover="this.style.background='#f0f0f0'" onmouseout="this.style.background='transparent'">🤙</span>
        <span class="emoji-item text-center p-2 rounded" data-emoji="👈" style="cursor: pointer; font-size: 1.5em; transition: background 0.2s;" onmouseover="this.style.background='#f0f0f0'" onmouseout="this.style.background='transparent'">👈</span>
        <span class="emoji-item text-center p-2 rounded" data-emoji="👉" style="cursor: pointer; font-size: 1.5em; transition: background 0.2s;" onmouseover="this.style.background='#f0f0f0'" onmouseout="this.style.background='transparent'">👉</span>
        <span class="emoji-item text-center p-2 rounded" data-emoji="👆" style="cursor: pointer; font-size: 1.5em; transition: background 0.2s;" onmouseover="this.style.background='#f0f0f0'" onmouseout="this.style.background='transparent'">👆</span>
        <span class="emoji-item text-center p-2 rounded" data-emoji="🖕" style="cursor: pointer; font-size: 1.5em; transition: background 0.2s;" onmouseover="this.style.background='#f0f0f0'" onmouseout="this.style.background='transparent'">🖕</span>
        <span class="emoji-item text-center p-2 rounded" data-emoji="👇" style="cursor: pointer; font-size: 1.5em; transition: background 0.2s;" onmouseover="this.style.background='#f0f0f0'" onmouseout="this.style.background='transparent'">👇</span>
        <span class="emoji-item text-center p-2 rounded" data-emoji="☝️" style="cursor: pointer; font-size: 1.5em; transition: background 0.2s;" onmouseover="this.style.background='#f0f0f0'" onmouseout="this.style.background='transparent'">☝️</span>
        <span class="emoji-item text-center p-2 rounded" data-emoji="👍" style="cursor: pointer; font-size: 1.5em; transition: background 0.2s;" onmouseover="this.style.background='#f0f0f0'" onmouseout="this.style.background='transparent'">👍</span>
        <span class="emoji-item text-center p-2 rounded" data-emoji="👎" style="cursor: pointer; font-size: 1.5em; transition: background 0.2s;" onmouseover="this.style.background='#f0f0f0'" onmouseout="this.style.background='transparent'">👎</span>
        <span class="emoji-item text-center p-2 rounded" data-emoji="✊" style="cursor: pointer; font-size: 1.5em; transition: background 0.2s;" onmouseover="this.style.background='#f0f0f0'" onmouseout="this.style.background='transparent'">✊</span>
        <span class="emoji-item text-center p-2 rounded" data-emoji="👊" style="cursor: pointer; font-size: 1.5em; transition: background 0.2s;" onmouseover="this.style.background='#f0f0f0'" onmouseout="this.style.background='transparent'">👊</span>
        <span class="emoji-item text-center p-2 rounded" data-emoji="🤛" style="cursor: pointer; font-size: 1.5em; transition: background 0.2s;" onmouseover="this.style.background='#f0f0f0'" onmouseout="this.style.background='transparent'">🤛</span>
        <span class="emoji-item text-center p-2 rounded" data-emoji="🤜" style="cursor: pointer; font-size: 1.5em; transition: background 0.2s;" onmouseover="this.style.background='#f0f0f0'" onmouseout="this.style.background='transparent'">🤜</span>
        <span class="emoji-item text-center p-2 rounded" data-emoji="👏" style="cursor: pointer; font-size: 1.5em; transition: background 0.2s;" onmouseover="this.style.background='#f0f0f0'" onmouseout="this.style.background='transparent'">👏</span>
        <span class="emoji-item text-center p-2 rounded" data-emoji="🙌" style="cursor: pointer; font-size: 1.5em; transition: background 0.2s;" onmouseover="this.style.background='#f0f0f0'" onmouseout="this.style.background='transparent'">🙌</span>
        <span class="emoji-item text-center p-2 rounded" data-emoji="👐" style="cursor: pointer; font-size: 1.5em; transition: background 0.2s;" onmouseover="this.style.background='#f0f0f0'" onmouseout="this.style.background='transparent'">👐</span>
        <span class="emoji-item text-center p-2 rounded" data-emoji="🤲" style="cursor: pointer; font-size: 1.5em; transition: background 0.2s;" onmouseover="this.style.background='#f0f0f0'" onmouseout="this.style.background='transparent'">🤲</span>
        <span class="emoji-item text-center p-2 rounded" data-emoji="🤝" style="cursor: pointer; font-size: 1.5em; transition: background 0.2s;" onmouseover="this.style.background='#f0f0f0'" onmouseout="this.style.background='transparent'">🤝</span>
        <span class="emoji-item text-center p-2 rounded" data-emoji="🙏" style="cursor: pointer; font-size: 1.5em; transition: background 0.2s;" onmouseover="this.style.background='#f0f0f0'" onmouseout="this.style.background='transparent'">🙏</span>
        <span class="emoji-item text-center p-2 rounded" data-emoji="💪" style="cursor: pointer; font-size: 1.5em; transition: background 0.2s;" onmouseover="this.style.background='#f0f0f0'" onmouseout="this.style.background='transparent'">💪</span>
        <span class="emoji-item text-center p-2 rounded" data-emoji="❤️" style="cursor: pointer; font-size: 1.5em; transition: background 0.2s;" onmouseover="this.style.background='#f0f0f0'" onmouseout="this.style.background='transparent'">❤️</span>
        <span class="emoji-item text-center p-2 rounded" data-emoji="🧡" style="cursor: pointer; font-size: 1.5em; transition: background 0.2s;" onmouseover="this.style.background='#f0f0f0'" onmouseout="this.style.background='transparent'">🧡</span>
        <span class="emoji-item text-center p-2 rounded" data-emoji="💛" style="cursor: pointer; font-size: 1.5em; transition: background 0.2s;" onmouseover="this.style.background='#f0f0f0'" onmouseout="this.style.background='transparent'">💛</span>
        <span class="emoji-item text-center p-2 rounded" data-emoji="💚" style="cursor: pointer; font-size: 1.5em; transition: background 0.2s;" onmouseover="this.style.background='#f0f0f0'" onmouseout="this.style.background='transparent'">💚</span>
        <span class="emoji-item text-center p-2 rounded" data-emoji="💙" style="cursor: pointer; font-size: 1.5em; transition: background 0.2s;" onmouseover="this.style.background='#f0f0f0'" onmouseout="this.style.background='transparent'">💙</span>
        <span class="emoji-item text-center p-2 rounded" data-emoji="💜" style="cursor: pointer; font-size: 1.5em; transition: background 0.2s;" onmouseover="this.style.background='#f0f0f0'" onmouseout="this.style.background='transparent'">💜</span>
        <span class="emoji-item text-center p-2 rounded" data-emoji="🖤" style="cursor: pointer; font-size: 1.5em; transition: background 0.2s;" onmouseover="this.style.background='#f0f0f0'" onmouseout="this.style.background='transparent'">🖤</span>
        <span class="emoji-item text-center p-2 rounded" data-emoji="🤍" style="cursor: pointer; font-size: 1.5em; transition: background 0.2s;" onmouseover="this.style.background='#f0f0f0'" onmouseout="this.style.background='transparent'">🤍</span>
        <span class="emoji-item text-center p-2 rounded" data-emoji="🤎" style="cursor: pointer; font-size: 1.5em; transition: background 0.2s;" onmouseover="this.style.background='#f0f0f0'" onmouseout="this.style.background='transparent'">🤎</span>
        <span class="emoji-item text-center p-2 rounded" data-emoji="💯" style="cursor: pointer; font-size: 1.5em; transition: background 0.2s;" onmouseover="this.style.background='#f0f0f0'" onmouseout="this.style.background='transparent'">💯</span>
        <span class="emoji-item text-center p-2 rounded" data-emoji="✅" style="cursor: pointer; font-size: 1.5em; transition: background 0.2s;" onmouseover="this.style.background='#f0f0f0'" onmouseout="this.style.background='transparent'">✅</span>
        <span class="emoji-item text-center p-2 rounded" data-emoji="❌" style="cursor: pointer; font-size: 1.5em; transition: background 0.2s;" onmouseover="this.style.background='#f0f0f0'" onmouseout="this.style.background='transparent'">❌</span>
        <span class="emoji-item text-center p-2 rounded" data-emoji="⭕" style="cursor: pointer; font-size: 1.5em; transition: background 0.2s;" onmouseover="this.style.background='#f0f0f0'" onmouseout="this.style.background='transparent'">⭕</span>
        <span class="emoji-item text-center p-2 rounded" data-emoji="❓" style="cursor: pointer; font-size: 1.5em; transition: background 0.2s;" onmouseover="this.style.background='#f0f0f0'" onmouseout="this.style.background='transparent'">❓</span>
        <span class="emoji-item text-center p-2 rounded" data-emoji="❗" style="cursor: pointer; font-size: 1.5em; transition: background 0.2s;" onmouseover="this.style.background='#f0f0f0'" onmouseout="this.style.background='transparent'">❗</span>
        <span class="emoji-item text-center p-2 rounded" data-emoji="🔥" style="cursor: pointer; font-size: 1.5em; transition: background 0.2s;" onmouseover="this.style.background='#f0f0f0'" onmouseout="this.style.background='transparent'">🔥</span>
        <span class="emoji-item text-center p-2 rounded" data-emoji="⭐" style="cursor: pointer; font-size: 1.5em; transition: background 0.2s;" onmouseover="this.style.background='#f0f0f0'" onmouseout="this.style.background='transparent'">⭐</span>
        <span class="emoji-item text-center p-2 rounded" data-emoji="🌟" style="cursor: pointer; font-size: 1.5em; transition: background 0.2s;" onmouseover="this.style.background='#f0f0f0'" onmouseout="this.style.background='transparent'">🌟</span>
        <span class="emoji-item text-center p-2 rounded" data-emoji="💝" style="cursor: pointer; font-size: 1.5em; transition: background 0.2s;" onmouseover="this.style.background='#f0f0f0'" onmouseout="this.style.background='transparent'">💝</span>
        <span class="emoji-item text-center p-2 rounded" data-emoji="💖" style="cursor: pointer; font-size: 1.5em; transition: background 0.2s;" onmouseover="this.style.background='#f0f0f0'" onmouseout="this.style.background='transparent'">💖</span>
        <span class="emoji-item text-center p-2 rounded" data-emoji="💗" style="cursor: pointer; font-size: 1.5em; transition: background 0.2s;" onmouseover="this.style.background='#f0f0f0'" onmouseout="this.style.background='transparent'">💗</span>
        <span class="emoji-item text-center p-2 rounded" data-emoji="💓" style="cursor: pointer; font-size: 1.5em; transition: background 0.2s;" onmouseover="this.style.background='#f0f0f0'" onmouseout="this.style.background='transparent'">💓</span>
        <span class="emoji-item text-center p-2 rounded" data-emoji="💞" style="cursor: pointer; font-size: 1.5em; transition: background 0.2s;" onmouseover="this.style.background='#f0f0f0'" onmouseout="this.style.background='transparent'">💞</span>
        <span class="emoji-item text-center p-2 rounded" data-emoji="💕" style="cursor: pointer; font-size: 1.5em; transition: background 0.2s;" onmouseover="this.style.background='#f0f0f0'" onmouseout="this.style.background='transparent'">💕</span>
        <span class="emoji-item text-center p-2 rounded" data-emoji="💟" style="cursor: pointer; font-size: 1.5em; transition: background 0.2s;" onmouseover="this.style.background='#f0f0f0'" onmouseout="this.style.background='transparent'">💟</span>
        <span class="emoji-item text-center p-2 rounded" data-emoji="❣️" style="cursor: pointer; font-size: 1.5em; transition: background 0.2s;" onmouseover="this.style.background='#f0f0f0'" onmouseout="this.style.background='transparent'">❣️</span>
        <span class="emoji-item text-center p-2 rounded" data-emoji="💔" style="cursor: pointer; font-size: 1.5em; transition: background 0.2s;" onmouseover="this.style.background='#f0f0f0'" onmouseout="this.style.background='transparent'">💔</span>
    </div>
</div>

<!-- Sonido de notificación -->
<audio id="sndMensaje" preload="auto">
    <source src="<?= base_url('assets/img/kaiadmin/notify.mp3') ?>" type="audio/mpeg">
    <source src="<?= base_url('assets/img/kaiadmin/notify.ogg') ?>" type="audio/ogg">
  </audio>



<script>
let conversaciones = [];
let conversacionActual = null;
let timeoutBusquedaMensajes = null; // Renombrado para evitar conflicto con header.php
let contadorMensajesPorChat = {}; // para detectar nuevos mensajes

$(document).ready(function() {
    cargarConversaciones();
    iniciarHeartbeat();
    
    // Configurar eventos
    $('#buscarConversaciones').on('input', function() {
        const termino = $(this).val().toLowerCase();
        filtrarConversaciones(termino);
    });
    
    $('#formEnviarMensaje').on('submit', function(e) {
        e.preventDefault();
        enviarMensaje();
    });

    // Adjuntos - Preview de imágenes
    $('#btnAdjuntar').on('click', function(){ $('#archivoAdjunto').click(); });
    
    $('#archivoAdjunto').on('change', function(e) {
        const file = e.target.files[0];
        if (file && file.type.startsWith('image/')) {
            const reader = new FileReader();
            reader.onload = function(event) {
                $('#previewImg').attr('src', event.target.result);
                $('#previewImagen').show();
            };
            reader.readAsDataURL(file);
        } else {
            $('#previewImagen').hide();
        }
    });
    
    $('#btnQuitarPreview').on('click', function() {
        $('#archivoAdjunto').val('');
        $('#previewImagen').hide();
    });
    
    // Selector de emojis
    $('#btnEmoji').on('click', function(e) {
        e.stopPropagation();
        const picker = $('#emojiPicker');
        const areaEscritura = $('#areaEscritura');
        const btnOffset = $(this).offset();
        const areaOffset = areaEscritura.offset();
        
        if (picker.is(':visible')) {
            picker.hide();
        } else {
            picker.css({
                'bottom': (areaEscritura.outerHeight() + 10) + 'px',
                'right': '20px'
            }).show();
        }
    });
    
    $('#btnCerrarEmoji').on('click', function() {
        $('#emojiPicker').hide();
    });
    
    // Cerrar emoji picker al hacer clic fuera
    $(document).on('click', function(e) {
        if (!$(e.target).closest('#btnEmoji, #emojiPicker').length) {
            $('#emojiPicker').hide();
        }
    });
    
    // Insertar emoji en input
    $('.emoji-item').on('click', function() {
        const emoji = $(this).data('emoji');
        const input = $('#mensajeTexto');
        const start = input[0].selectionStart || input.val().length;
        const end = input[0].selectionEnd || input.val().length;
        const text = input.val();
        input.val(text.substring(0, start) + emoji + text.substring(end));
        input[0].selectionStart = input[0].selectionEnd = start + emoji.length;
        input.focus();
    });
    
    // Typing indicator con manejo de errores silencioso
    let typingTimeout;
    $('#mensajeTexto').on('input', function(){
        if (!conversacionActual) return;
        // Enviar typing start con manejo silencioso de errores
        $.post('<?= base_url('mensajeria/typingStart') ?>/' + conversacionActual)
            .fail(function() {
                // Silencioso - no mostrar errores en consola
            });
        clearTimeout(typingTimeout);
        typingTimeout = setTimeout(function(){
            if (conversacionActual) {
                $.post('<?= base_url('mensajeria/typingStop') ?>/' + conversacionActual)
                    .fail(function() {
                        // Silencioso
                    });
            }
        }, 2000);
    });
    
    $('#mensajeTexto').on('keypress', function(e) {
        if (e.which === 13) {
            e.preventDefault();
            enviarMensaje();
        }
    });
    
    // Toggle sidebar en móviles
    $('#btnToggleSidebar').on('click', function() {
        $('.panel-conversaciones-left').toggleClass('show');
        $('#mobileOverlay').toggleClass('show');
    });
    
    // Cerrar sidebar al hacer clic en overlay
    $('#mobileOverlay').on('click', function() {
        $('.panel-conversaciones-left').removeClass('show');
        $('#mobileOverlay').removeClass('show');
    });
    
    // Cerrar sidebar al hacer clic fuera en móviles
    $(document).on('click', function(e) {
        if ($(window).width() <= 768) {
            if (!$(e.target).closest('.panel-conversaciones-left, #btnToggleSidebar').length) {
                $('.panel-conversaciones-left').removeClass('show');
                $('#mobileOverlay').removeClass('show');
            }
        }
    });
});

function cargarConversaciones() {
    $.get('<?= base_url('mensajeria/getConversaciones') ?>')
        .done(function(response) {
            if (response.success) {
                conversaciones = response.data;
                mostrarConversaciones(conversaciones);
            } else {
                mostrarError('Error al cargar conversaciones: ' + response.message);
            }
        })
        .fail(function() {
            mostrarError('Error de conexión al cargar conversaciones');
        });
}

function mostrarConversaciones(conversaciones) {
    const container = $('#listaConversaciones');
    
    if (conversaciones.length === 0) {
        container.html(`
            <div class="text-center p-4">
                <i class="fas fa-comments fa-3x text-muted mb-3"></i>
                <h5 class="text-muted mb-3">No tienes conversaciones</h5>
                <p class="text-muted mb-4">Comienza una nueva conversación</p>
                <a href="<?= base_url('mensajeria/enviar') ?>" class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i>Nuevo Mensaje
                </a>
            </div>
        `);
        return;
    }
    
    let html = '';
    conversaciones.forEach(function(conv) {
        const ultimoMensaje = conv.ultimo_mensaje || 'Sin mensajes';
        const horaUltimoMensaje = conv.fecha_ultimo_mensaje ? 
            new Date(conv.fecha_ultimo_mensaje).toLocaleTimeString('es-ES', {hour: '2-digit', minute: '2-digit'}) : '';
        
        // Truncar mensaje largo
        const mensajeTruncado = ultimoMensaje.length > 50 ? 
            ultimoMensaje.substring(0, 50) + '...' : ultimoMensaje;
        
        html += `
            <div class="conversacion-item" data-conversacion-id="${conv.usuario_id}" onclick="abrirConversacion(${conv.usuario_id})">
                <div class="d-flex align-items-center">
                    <div class="conversacion-avatar me-3">
                        <i class="fas fa-user"></i>
                    </div>
                    <div class="flex-grow-1">
                        <div class="d-flex justify-content-between align-items-start">
                            <h6 class="mb-1 fw-bold">${conv.nombre_completo}</h6>
                            <small class="text-muted">${horaUltimoMensaje}</small>
                        </div>
                        <p class="mb-0 text-muted small">${mensajeTruncado}</p>
                    </div>
                    ${conv.mensajes_no_leidos > 0 ? `<span class="badge bg-danger">${conv.mensajes_no_leidos}</span>` : ''}
                </div>
            </div>
        `;
    });
    
    container.html(html);
}

function filtrarConversaciones(termino) {
    clearTimeout(timeoutBusquedaMensajes);
    timeoutBusquedaMensajes = setTimeout(() => {
        const conversacionesFiltradas = conversaciones.filter(conv => 
            conv.nombre_completo.toLowerCase().includes(termino)
        );
        mostrarConversaciones(conversacionesFiltradas);
    }, 300);
}

function abrirConversacion(usuarioId) {
    conversacionActual = usuarioId;
    ultimoMensajeId = 0; // Reset para cargar todos los mensajes
    
    // Actualizar UI
    $('.conversacion-item').removeClass('active');
    $(`.conversacion-item[data-conversacion-id="${usuarioId}"]`).addClass('active');
    
    // Ocultar sidebar en móviles
    $('.panel-conversaciones-left').removeClass('show');
    $('#mobileOverlay').removeClass('show');
    
    // Mostrar área de chat
    $('#headerChat').show();
    $('#areaEscritura').show();
    $('#destinatario_id').val(usuarioId);
    
    // Cargar mensajes de la conversación (solo esta vez mostrar loading)
    cargarMensajesConversacion(usuarioId, true);
    
    // Actualizar header
    const conversacion = conversaciones.find(c => c.usuario_id == usuarioId);
    if (conversacion) {
        $('#nombreDestinatario').text(conversacion.nombre_completo);
        actualizarPresence(); // Solo una vez al abrir
    }
    
    // Actualizar lista de conversaciones inmediatamente para eliminar contador
    // Esperar un momento para que se completen las operaciones de marca como leído
    setTimeout(() => {
        cargarConversaciones();
    }, 500);
}

let ultimoMensajeId = 0; // Track del último mensaje cargado

function cargarMensajesConversacion(usuarioId, mostrarLoading = true) {
    if (mostrarLoading) {
        $('#areaMensajes').html(`
            <div class="text-center p-3">
                <i class="fas fa-spinner fa-spin text-primary"></i>
            </div>
        `);
    }
    
    // Agregar cache buster para evitar caché
    $.get(`<?= base_url('mensajeria/getMensajesConversacion') ?>/${usuarioId}`, { _: Date.now() })
        .done(function(response) {
            if (response.success && response.data) {
                // Verificar si hay nuevos mensajes
                const nuevoUltimoId = response.data.length > 0 ? 
                    Math.max(...response.data.map(m => parseInt(m.id) || 0)) : 0;
                
                // Solo actualizar si hay nuevos mensajes o es la primera carga
                const hayNuevosMensajes = nuevoUltimoId > ultimoMensajeId;
                if (hayNuevosMensajes || ultimoMensajeId === 0) {
                    mostrarMensajes(response.data);
                    if (hayNuevosMensajes) {
                        // Scroll solo si hay nuevos mensajes (no en primera carga) - más rápido
                        setTimeout(scrollToBottom, 50);
                    }
                    ultimoMensajeId = nuevoUltimoId;
                }
                
                // Actualizar lista de conversaciones de forma asíncrona - reducido delay
                if (mostrarLoading) {
                    setTimeout(() => {
                        cargarConversaciones();
                    }, 200);
                }
            } else if (!mostrarLoading && response.success) {
                // Silencioso, no hacer nada si no hay cambios
            } else {
                if (mostrarLoading) {
                    mostrarError('Error al cargar mensajes: ' + (response.message || 'Error desconocido'));
                }
            }
        })
        .fail(function() {
            if (mostrarLoading) {
                mostrarError('Error de conexión al cargar mensajes');
            }
        });
}

function scrollToBottom() {
    const container = $('#areaMensajes');
    container.scrollTop(container[0].scrollHeight);
}

// Heartbeat periódico para mantener presencia (cada 60 segundos)
function iniciarHeartbeat(){
    // Enviar heartbeat inmediatamente y luego cada 60 segundos
    $.post('<?= base_url('mensajeria/heartbeat') ?>').fail(function() {
        // Silencioso si falla
    });
    
    // Mantener heartbeat periódico para presencia
    setInterval(function() {
        $.post('<?= base_url('mensajeria/heartbeat') ?>').fail(function() {
            // Silencioso si falla
        });
    }, 60000); // Cada 60 segundos
}

// Actualizar presencia periódicamente pero sin errores visibles
function actualizarPresence(){
    if (!conversacionActual) {
        $('#estadoDestinatario').html('');
        return;
    }
    // Actualizar cada 30 segundos de forma silenciosa
    $.get('<?= base_url('mensajeria/getPresence') ?>/' + conversacionActual)
        .done(function(r){
            if (r.success) {
                $('#estadoDestinatario').html(r.online ?
                    '<i class="fas fa-circle text-success" style="font-size:8px;"></i> En línea' :
                    '<i class="fas fa-circle text-muted" style="font-size:8px;"></i> Desconectado');
            }
        })
        .fail(function() {
            // Si falla, mostrar como desconectado pero sin error
            $('#estadoDestinatario').html('<i class="fas fa-circle text-muted" style="font-size:8px;"></i> Desconectado');
        });
}

// Actualizar presencia cada 30 segundos si hay conversación abierta
let intervaloPresencia = null;
if (intervaloPresencia) clearInterval(intervaloPresencia);
intervaloPresencia = setInterval(function() {
    if (conversacionActual) {
        actualizarPresence();
    }
}, 30000);

// Typing solo cuando se envía un mensaje o se escribe, no constantemente
function consultarTyping(){
    // Deshabilitado - no consultar typing constantemente
    // Se puede activar solo cuando sea necesario (al escribir)
}

function mostrarMensajes(mensajes) {
    const container = $('#areaMensajes');
    const usuarioActual = <?= (int)(session()->get('usuario_id') ?? 0) ?>;
    
    if (mensajes.length === 0) {
        container.html(`
            <div class="text-center p-5">
                <i class="fas fa-comment-slash fa-3x text-muted mb-3"></i>
                <h5 class="text-muted mb-3">No hay mensajes</h5>
                <p class="text-muted">Envía el primer mensaje para comenzar</p>
            </div>
        `);
        return;
    }
    
    let html = '';
    let fechaAnterior = '';
    
    mensajes.forEach(function(mensaje) {
        const fechaMensaje = new Date(mensaje.fecha_envio).toLocaleDateString('es-ES');
        const horaMensaje = new Date(mensaje.fecha_envio).toLocaleTimeString('es-ES', {hour: '2-digit', minute: '2-digit'});
        
        // Mostrar fecha si es diferente a la anterior
        if (fechaMensaje !== fechaAnterior) {
            html += `<div class="fecha-separador"><span>${fechaMensaje}</span></div>`;
            fechaAnterior = fechaMensaje;
        }
        
        const esPropio = mensaje.remitente_id == usuarioActual;
        const claseBubble = esPropio ? 'mensaje-propio' : 'mensaje-recibido';
        
        // Badge de tipo de mensaje
        let tipoBadge = '';
        if (mensaje.tipo !== 'normal') {
            const tipoClass = mensaje.tipo === 'importante' ? 'badge-importante' : 'badge-urgente';
            const tipoTexto = mensaje.tipo === 'importante' ? 'Importante' : 'Urgente';
            tipoBadge = `<span class="badge-tipo ${tipoClass}">${tipoTexto}</span>`;
        }
        
        // Checks de estado estilo WhatsApp (solo si existe la propiedad status)
        let checks = '';
        if (esPropio) {
            const st = (mensaje.status || '').toLowerCase();
            if (st === 'read') {
                checks = '<span class="ms-1 text-primary">✔✔</span>';
            } else if (st === 'delivered') {
                checks = '<span class="ms-1 text-secondary">✔✔</span>';
            } else {
                checks = '<span class="ms-1 text-secondary">✔</span>';
            }
        }

        // Contenido con soporte básico de adjunto
        let contenidoHTML = mensaje.contenido;
        const matchAdj = contenidoHTML.match(/\[archivo\]\s+(https?:[^\s]+)/i);
        if (matchAdj) {
            const url = matchAdj[1];
            const isImg = /(\.png|\.jpg|\.jpeg|\.gif|\.webp)$/i.test(url);
            const link = `<a href="${url}" target="_blank" rel="noopener">Ver archivo</a>`;
            contenidoHTML = contenidoHTML.replace(matchAdj[0], isImg ? `<div class=\"mb-1\"><img src=\"${url}\" alt=\"adjunto\" style=\"max-width:220px;border-radius:6px\"/></div>${link}` : link);
        }

        html += `
            <div class="d-flex ${esPropio ? 'justify-content-end' : 'justify-content-start'} mb-2">
                <div class="mensaje-bubble ${claseBubble}">
                    ${tipoBadge}
                    <div>${contenidoHTML}</div>
                    <div class="mensaje-hora">${horaMensaje} ${checks}</div>
                </div>
            </div>
        `;
    });
    
    container.html(html);
    
    // Scroll al final
    container.scrollTop(container[0].scrollHeight);

    // Sonido si hay nuevos mensajes recibidos
    const actual = mensajes.length;
    const prev = contadorMensajesPorChat[conversacionActual] || 0;
    if (actual > prev) {
        const ultimo = mensajes[mensajes.length - 1];
        if (ultimo && String(ultimo.remitente_id) !== String(usuarioActual)) {
            const snd = document.getElementById('sndMensaje');
            if (snd && snd.play) { snd.play().catch(()=>{}); }
        }
    }
    contadorMensajesPorChat[conversacionActual] = actual;
}

let enviandoMensaje = false; // Flag para evitar envíos simultáneos

function enviarMensaje() {
    const texto = $('#mensajeTexto').val().trim();
    const destinatarioId = $('#destinatario_id').val();
    
    // Validaciones básicas
    if (!texto || !destinatarioId) {
        return;
    }
    
    // Si ya está enviando, permitir el siguiente después de un breve delay
    if (enviandoMensaje) {
        setTimeout(() => enviarMensaje(), 100);
        return;
    }
    
    enviandoMensaje = true;
    const $btnEnviar = $('#btnEnviar');
    const iconoOriginal = $btnEnviar.html();
    
    // Limpiar inmediatamente el campo para permitir escribir el siguiente mensaje
    $('#mensajeTexto').val('');
    $btnEnviar.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i>');
    
    // Obtener el token CSRF
    const csrfToken = $('meta[name="csrf-token"]').attr('content');
    
    const fd = new FormData();
    const archivo = document.getElementById('archivoAdjunto').files[0];
    if (archivo) {
        if (archivo.size > 30 * 1024 * 1024) {
            mostrarError('El archivo excede el límite de 30 MB');
            $btnEnviar.prop('disabled', false).html(iconoOriginal);
            enviandoMensaje = false;
            $('#mensajeTexto').focus();
            return;
        }
        fd.append('archivo', archivo);
    }
    fd.append('destinatario_id', destinatarioId);
    fd.append('asunto', 'Mensaje directo');
    fd.append('contenido', texto);
    fd.append('tipo', 'normal');

    // Enviar de forma asíncrona sin bloquear
    $.ajax({
        url: '<?= base_url('mensajeria/procesarEnvio') ?>',
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': csrfToken },
        data: fd,
        contentType: false,
        processData: false,
        timeout: 5000, // Reducido a 5 segundos para respuesta más rápida
        success: function(response) {
            if (response.success) {
                // Limpiar archivo si existe
                $('#archivoAdjunto').val('');
                $('#previewImagen').hide();
                
                // Cargar mensajes de forma asíncrona sin bloquear
                ultimoMensajeId = 0;
                // Usar setTimeout para no bloquear el siguiente envío
                setTimeout(() => {
                    cargarMensajesConversacion(destinatarioId, false);
                    cargarConversaciones();
                }, 100);
            } else {
                mostrarError(response.message || 'Error al enviar mensaje');
            }
        },
        error: function(xhr, status, error) {
            // Solo mostrar error si es crítico, no bloquear
            if (xhr.status === 401) {
                window.location.href = '<?= base_url('login') ?>';
                return;
            } else if (xhr.status === 403) {
                mostrarError('Acceso denegado. Verifique su sesión.');
            } else if (status !== 'abort') {
                // Solo mostrar error si no fue cancelado manualmente
                console.error('Error al enviar mensaje:', error);
            }
        },
        complete: function() {
            // SIEMPRE resetear el estado, incluso si hay error
            $btnEnviar.prop('disabled', false).html(iconoOriginal);
            enviandoMensaje = false;
            $('#mensajeTexto').focus(); // Enfocar de nuevo para escribir rápido
        }
    });
}

function mostrarError(mensaje) {
    Swal.fire({
        title: 'Error',
        text: mensaje,
        icon: 'error',
        confirmButtonText: 'OK',
        confirmButtonColor: '#dc3545'
    });
}

// Verificar nuevos mensajes sin recargar toda la vista (solo backend check)
let ultimoCheckConversaciones = Date.now();

function verificarNuevosMensajes() {
    if (!conversacionActual) return;
    
    // Verificar solo el ID del último mensaje de forma ligera
    $.get(`<?= base_url('mensajeria/getMensajesConversacion') ?>/${conversacionActual}`)
        .done(function(response) {
            if (response.success && response.data && response.data.length > 0) {
                const nuevoUltimoId = Math.max(...response.data.map(m => parseInt(m.id) || 0));
                if (nuevoUltimoId > ultimoMensajeId) {
                    // Solo recargar si hay un mensaje nuevo
                    cargarMensajesConversacion(conversacionActual, false); // Sin mostrar loading
                    // Actualizar lista de conversaciones cuando llega un nuevo mensaje
                    setTimeout(() => {
                        cargarConversaciones();
                    }, 300);
                }
            }
        })
        .fail(function() {
            // Silencioso, no mostrar errores en verificación
        });
}

function verificarNuevasConversaciones() {
    if (!conversacionActual) {
        // Solo actualizar lista de conversaciones si no hay conversación abierta
        const ahora = Date.now();
        if (ahora - ultimoCheckConversaciones > 30000) { // Cada 30 segundos
            cargarConversaciones();
            ultimoCheckConversaciones = ahora;
        }
    }
}

// Verificar nuevos mensajes cada 5 segundos (sin mostrar loading)
setInterval(verificarNuevosMensajes, 5000);

// Verificar nuevas conversaciones cada 30 segundos (solo si no hay chat abierto)
setInterval(verificarNuevasConversaciones, 30000);

// Limpiar intervalo de presencia constante - solo actualizar cuando se abre conversación
// (eliminado el heartbeat constante y consultas de presencia/typing)
</script>

<?= $footer ?>