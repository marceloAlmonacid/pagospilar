<?php
session_start();
$isAdmin = isset($_SESSION['admin']) && $_SESSION['admin'] === true;
?>
<!DOCTYPE html>
<html lang="es" data-bs-theme="dark">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
  <title>Pagos Pilar - Dashboard</title>
  
  <!-- Bootstrap 5 CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  
  <!-- FontAwesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  
  <!-- Animate css -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
  
  <!-- Google Fonts -->
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

  <link rel="stylesheet" href="css/estilos.css">
  <link rel="icon" href="img/favicon.ico" type="image/x-icon">
</head>

<body class="sre-bg text-dark">

  <nav class="navbar navbar-expand-lg navbar-light bg-transparent border-bottom border-light-subtle mb-4">
    <div class="container">
      <ul class="navbar-nav me-auto flex-row gap-3">
        <?php if($isAdmin): ?>
        <li class="nav-item">
          <a class="nav-link text-accent fw-bold text-decoration-none" href="#" data-bs-toggle="modal" data-bs-target="#modalAgregar"><i class="fa-solid fa-file-invoice-dollar me-1"></i>Nuevo Gasto</a>
        </li>
        <li class="nav-item">
          <a class="nav-link text-accent fw-bold text-decoration-none" href="#" data-bs-toggle="modal" data-bs-target="#modalAdministrarGastos" onclick="cargarListaGastos()"><i class="fa-solid fa-list-check me-1"></i>Administrar Gastos</a>
        </li>
        <li class="nav-item">
          <a class="nav-link text-accent fw-bold text-decoration-none" href="#" data-bs-toggle="modal" data-bs-target="#modalAgregarUsuario"><i class="fa-solid fa-user-plus me-1"></i>Nuevo Usuario</a>
        </li>
        <?php endif; ?>
      </ul>
      <div class="d-flex align-items-center">
        <?php if($isAdmin): ?>
          <span class="badge bg-success me-2 rounded-pill"><i class="fa-solid fa-shield-halved me-1"></i>Admin</span>
          <button class="btn btn-sm btn-outline-info rounded-circle me-2" onclick="notificarTodos()" title="Notificar a Todos"><i class="fa-solid fa-paper-plane"></i></button>
          <button class="btn btn-sm btn-outline-danger rounded-circle" onclick="logoutAdmin()" title="Cerrar Sesión"><i class="fa-solid fa-right-from-bracket"></i></button>
        <?php else: ?>
          <button class="btn btn-sm btn-outline-secondary rounded-pill" onclick="mostrarLogin()"><i class="fa-solid fa-lock me-1"></i>Entrar</button>
        <?php endif; ?>
      </div>
    </div>
  </nav>

  <div class="container pb-5">
    
    <!-- Título Principal -->
    <div class="text-center mb-5 mt-3">
      <h3 class="fw-bold text-accent shadow-text mb-0 d-flex align-items-center justify-content-center gap-2">
        <span id="tituloMes">Total de mes:</span> 
        <span id="montoTotalMes" class="ms-1">$0.00</span>
        <i class="fa-solid fa-coins text-warning ms-1"></i>
      </h3>
    </div>

    <!-- Filtro Mes y Año Oculto pero funcional -->
    <div class="d-none">
      <select id="filtroMes"><option value="01">01</option><option value="02">02</option><option value="03">03</option><option value="04">04</option><option value="05">05</option><option value="06">06</option><option value="07">07</option><option value="08">08</option><option value="09">09</option><option value="10">10</option><option value="11">11</option><option value="12">12</option></select>
      <select id="filtroAnio"><option value="2024">2024</option><option value="2025">2025</option><option value="2026">2026</option></select>
    </div>

    <!-- Grid de Usuarios (Estilo Retro SDO) -->
    <div class="mb-5">
      <div class="text-center mb-4">
        <h5 class="fw-bold text-dark mb-1">Seleccioná tu usuario</h5>
        <small class="text-secondary">Tocá tu avatar para ver lo que te toca pagar</small>
      </div>
      <div class="row row-cols-3 row-cols-md-4 row-cols-lg-5 g-3 justify-content-center" id="gridUsuarios">
        <!-- Se inyecta por JS -->
      </div>
    </div>

    <!-- Título Facturas -->
    <div class="text-center mb-4 mt-5">
      <h4 class="fw-bold text-danger mb-0">Facturas</h4>
    </div>

    <!-- Lista de Facturas Generales -->
    <div class="card sre-card border-0 rounded-4 shadow-neon mb-5">
      <div class="card-body p-0">
        <ul class="list-group list-group-flush rounded-4" id="listaFacturas">
          <!-- Se inyecta por JS -->
        </ul>
      </div>
    </div>
    
  </div>

  <!-- Offcanvas Usuario (Bottom Sheet) -->
  <div class="offcanvas offcanvas-bottom sre-card text-dark" tabindex="-1" id="offcanvasUsuario" style="height: auto; max-height: 85vh; border-top-left-radius: 20px; border-top-right-radius: 20px;">
    <div class="offcanvas-header border-bottom border-light-subtle pb-3 pt-4">
      <div class="d-flex align-items-center">
        <div class="position-relative" id="offcanvasAvatarContainer">
          <img src="" id="offcanvasAvatar" class="avatar-grid-img me-3" alt="" style="width: 55px; height: 55px;">
        </div>
        <div>
          <h4 class="offcanvas-title fw-bold text-dark mb-0 d-flex align-items-center">
            <span id="offcanvasNombreText">Usuario</span>
            <?php if($isAdmin): ?>
            <button class="btn btn-sm text-secondary ms-2 p-0" id="btnEditarUsuario" title="Editar Usuario"><i class="fa-solid fa-pen"></i></button>
            <?php endif; ?>
          </h4>
          <span class="badge mt-1" id="offcanvasBadge"></span>
        </div>
      </div>
      <button type="button" class="btn-close btn-close" data-bs-dismiss="offcanvas"></button>
    </div>
    <div class="offcanvas-body pb-5">
      <div class="d-flex justify-content-between align-items-center mb-3 px-1">
        <h6 class="text-secondary mb-0 fw-bold">Total a pagar:</h6>
        <h3 class="text-accent shadow-text fw-bold mb-0" id="offcanvasTotal"></h3>
      </div>
      
      <div class="bg-light rounded-4 p-2 mb-4 border border-light-subtle shadow-sm">
        <ul class="list-group list-group-flush mb-0" id="offcanvasListaImpuestos">
          <!-- Inyectado JS -->
        </ul>
      </div>

      <div class="d-flex justify-content-center align-items-center pt-2 pb-4 gap-3">
        <?php if($isAdmin): ?>
        <button class="btn btn-warning rounded-pill d-none text-dark fw-bold shadow-sm px-3" id="btnWhatsappIndividual" onclick="notificarDeudaIndividual()"><i class="fa-brands fa-whatsapp fs-5 me-1"></i> Recordatorio</button>
        <button class="btn btn-success rounded-pill d-none text-white fw-bold shadow-sm px-3" id="btnNotificarPagoConfirmado" onclick="notificarPagoConfirmado()"><i class="fa-brands fa-whatsapp fs-5 me-1"></i> Confirmar Pago</button>
        <div class="form-check form-switch fs-5 mb-0">
          <input class="form-check-input cursor-pointer" type="checkbox" role="switch" id="offcanvasSwitchPago">
          <label class="form-check-label fw-bold ms-2 text-dark" for="offcanvasSwitchPago" id="offcanvasLabelPago">Marcar como pagado</label>
        </div>
        <?php endif; ?>
      </div>
    </div>
  </div>

  <!-- MODALES -->
  
  <!-- Modal Administrar Gastos -->
  <div class="modal fade" id="modalAdministrarGastos" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
      <div class="modal-content border-0 rounded-4 shadow-lg bg-light text-dark">
        <div class="modal-header border-bottom border-light-subtle">
          <h5 class="modal-title fw-bold"><i class="fa-solid fa-list-check me-2 text-accent"></i>Administrar Gastos (ABM)</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div class="table-responsive">
            <table class="table table-hover align-middle">
              <thead class="table-light">
                <tr>
                  <th>ID</th>
                  <th>Impuesto</th>
                  <th>Proveedor</th>
                  <th>Vencimiento</th>
                  <th>Costo</th>
                  <th class="text-center">Acciones</th>
                </tr>
              </thead>
              <tbody id="tablaAdministrarGastosBody">
                <!-- Se llena por AJAX -->
              </tbody>
            </table>
          </div>
        </div>
        <div class="modal-footer border-top border-light-subtle">
          <button type="button" class="btn btn-secondary rounded-pill fw-bold px-4" data-bs-dismiss="modal">Cerrar</button>
        </div>
      </div>
    </div>
  </div>

  <!-- Modal Agregar Impuesto-->
  <div class="modal fade" id="modalAgregar" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content sre-card rounded-4 border-1 border-light-subtle">
        <div class="modal-header border-bottom border-light-subtle">
          <h5 class="modal-title fw-bold text-accent"><i class="fa-solid fa-plus-circle me-2"></i> Cargar Gasto/Impuesto</h5>
          <button type="button" class="btn-close btn-close" data-bs-dismiss="modal"></button>
        </div>
        <form name="formSubirPresupuestos" action="ajax/cargarImpuestos.php" id="formSubirImpuestos" enctype="multipart/form-data" method="POST">
          <div class="modal-body text-dark">
            <div class="row g-3">
              <div class="col-12">
                <label class="form-label text-secondary fw-bold">Servicio / Descripción</label>
                <input type="text" class="form-control bg-light text-dark border-light-subtle" name="nombreImp" id="nombre_imp" required>
              </div>
              <div class="col-md-6">
                <label class="form-label text-secondary fw-bold">Proveedor</label>
                <input type="text" class="form-control bg-light text-dark border-light-subtle" name="proveedorImp" id="proveedor_imp">
              </div>
              <div class="col-md-6">
                <label class="form-label text-secondary fw-bold">Tipo</label>
                <select class="form-select bg-light text-dark border-light-subtle" name="tipoImp" id="tipo_imp">
                  <option value="AGUA">Agua</option>
                  <option value="GAS">Gas</option>
                  <option value="IMPUESTO INMOBILIARIO">Inmobiliario</option>
                  <option value="TASAS MUNICIPALES">Municipal</option>
                  <option value="INTERNET">Internet</option>
                  <option value="LUZ">Luz</option>
                  <option value="OTROS">Otros</option>
                </select>
              </div>
              <div class="col-md-6">
                <label class="form-label text-secondary fw-bold">Vencimiento</label>
                <input type="date" class="form-control bg-light text-dark border-light-subtle" name="fechaVencimientoImp" id="fecha_vencimiento_imp" required>
              </div>
              <div class="col-md-6">
                <label class="form-label text-secondary fw-bold">Importe Total ($)</label>
                <input type="number" class="form-control bg-light text-dark border-light-subtle" step="0.01" name="costoImp" id="costo_imp" required>
              </div>
              <div class="col-12 mt-3">
                <label class="form-label text-secondary fw-bold d-block"><i class="fa-solid fa-users me-1"></i> ¿Quiénes lo pagan?</label>
                <div class="d-flex flex-wrap gap-2" id="listaUsu">
                  <!-- Checkboxes usuarios inyectados -->
                </div>
              </div>
              <div class="col-12">
                <label class="form-label text-secondary fw-bold">Comprobante (PDF/Img)</label>
                <input class="form-control bg-light text-dark border-light-subtle" type="file" name="facturaImp" id="factura_imp">
              </div>
            </div>
          </div>
          <div class="modal-footer border-top border-light-subtle">
            <button type="button" class="btn btn-outline-light rounded-pill px-4" data-bs-dismiss="modal">Cancelar</button>
            <button type="submit" class="btn btn-accent rounded-pill px-4"><i class="fa-solid fa-save me-1"></i> Guardar</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- Modal Agregar Usuario-->
  <div class="modal fade" id="modalAgregarUsuario" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-sm">
      <div class="modal-content sre-card rounded-4 border-1 border-light-subtle">
        <div class="modal-header border-bottom border-light-subtle">
          <h5 class="modal-title fw-bold text-accent"><i class="fa-solid fa-user-plus me-2"></i> Nuevo Usuario</h5>
          <button type="button" class="btn-close btn-close" data-bs-dismiss="modal"></button>
        </div>
        <form action="ajax/cargarUsuario.php" id="formSubirUsuarios" method="POST">
          <div class="modal-body text-dark">
            <div class="mb-3">
              <label class="form-label text-secondary fw-bold">Nombre</label>
              <input type="text" class="form-control bg-light text-dark border-light-subtle" name="nombreUsu" id="nombre_usu" required>
            </div>
            <div class="mb-3">
              <label class="form-label text-secondary fw-bold"><i class="fa-brands fa-whatsapp text-success me-1"></i> Teléfono (WhatsApp)</label>
              <input type="text" class="form-control bg-light text-dark border-light-subtle" name="telefonoUsu" placeholder="Ej: +5491122334455">
            </div>
            <div class="mb-3">
              <label class="form-label text-secondary fw-bold"><i class="fa-brands fa-whatsapp text-success me-1"></i> Teléfono 2 (Opcional)</label>
              <input type="text" class="form-control bg-light text-dark border-light-subtle" name="telefonoUsu2" placeholder="Ej: +5491122334455">
            </div>
          </div>
          <div class="modal-footer border-top border-light-subtle">
            <button type="submit" class="btn btn-accent rounded-pill w-100">Guardar Usuario</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- Modal Editar Usuario-->
  <div class="modal fade" id="modalEditarUsuario" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-sm">
      <div class="modal-content sre-card rounded-4 border-1 border-light-subtle">
        <div class="modal-header border-bottom border-light-subtle">
          <h5 class="modal-title fw-bold text-accent"><i class="fa-solid fa-user-pen me-2"></i> Editar Usuario</h5>
          <button type="button" class="btn-close btn-close" data-bs-dismiss="modal"></button>
        </div>
        <form action="ajax/editarUsuario.php" id="formEditarUsuario" method="POST">
          <div class="modal-body text-dark">
            <input type="hidden" name="id_usuario" id="edit_id_usu">
            <div class="mb-3">
              <label class="form-label text-secondary fw-bold">Nombre</label>
              <input type="text" class="form-control bg-light text-dark border-light-subtle" name="nombreUsu" id="edit_nombre_usu" required>
            </div>
            <div class="mb-3">
              <label class="form-label text-secondary fw-bold"><i class="fa-brands fa-whatsapp text-success me-1"></i> Teléfono (WhatsApp)</label>
              <input type="text" class="form-control bg-light text-dark border-light-subtle" name="telefonoUsu" id="edit_telefono_usu" placeholder="Ej: +5491122334455">
            </div>
            <div class="mb-3">
              <label class="form-label text-secondary fw-bold"><i class="fa-brands fa-whatsapp text-success me-1"></i> Teléfono 2 (Opcional)</label>
              <input type="text" class="form-control bg-light text-dark border-light-subtle" name="telefonoUsu2" id="edit_telefono2_usu" placeholder="Ej: +5491122334455">
            </div>
          </div>
          <div class="modal-footer border-top border-light-subtle">
            <button type="submit" class="btn btn-accent rounded-pill w-100">Guardar Cambios</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- Modal Editar Impuesto -->
  <div class="modal fade" id="modalEditar" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content sre-card rounded-4 border-1 border-light-subtle">
        <div class="modal-header border-bottom border-light-subtle">
          <h5 class="modal-title fw-bold text-accent"><i class="fa-solid fa-pen-to-square me-2"></i> Editar Gasto</h5>
          <button type="button" class="btn-close btn-close" data-bs-dismiss="modal"></button>
        </div>
        <form action="ajax/editar.php" id="formEditarImpuestos" enctype="multipart/form-data" method="POST">
          <div class="modal-body text-dark">
            <input type="hidden" name="idImpEdit" id="id_imp_edit">
            <input type="hidden" name="tiene_acta" id="tieneActa">
            
            <div class="row g-3">
              <div class="col-12">
                <label class="form-label text-secondary fw-bold">Servicio</label>
                <input type="text" class="form-control bg-light text-dark border-light-subtle" name="nombreImpEdit" id="nombre_imp_edit">
              </div>
              <div class="col-md-6">
                <label class="form-label text-secondary fw-bold">Proveedor</label>
                <input type="text" class="form-control bg-light text-dark border-light-subtle" name="proveedorImpEdit" id="proveedor_imp_edit">
              </div>
              <div class="col-md-6">
                <label class="form-label text-secondary fw-bold">Tipo</label>
                <select class="form-select bg-light text-dark border-light-subtle" name="tipoImpEdit" id="tipo_imp_edit">
                  <option value="AGUA">Agua</option>
                  <option value="GAS">Gas</option>
                  <option value="IMPUESTO INMOBILIARIO">Inmobiliario</option>
                  <option value="TASAS MUNICIPALES">Municipal</option>
                  <option value="INTERNET">Internet</option>
                  <option value="LUZ">Luz</option>
                  <option value="OTROS">Otros</option>
                </select>
              </div>
              <div class="col-md-6">
                <label class="form-label text-secondary fw-bold">Vencimiento</label>
                <input type="date" class="form-control bg-light text-dark border-light-subtle" name="fechaVencimientoImpEdit" id="fecha_vencimiento_imp_edit">
              </div>
              <div class="col-md-6">
                <label class="form-label text-secondary fw-bold">Importe</label>
                <input type="number" step="0.01" class="form-control bg-light text-dark border-light-subtle" name="costoImpEdit" id="costo_imp_edit">
              </div>
              <div class="col-12 mt-3 text-center" id="factMsg"></div>
              <div class="col-12">
                <label class="form-label text-secondary fw-bold">Reemplazar Comprobante</label>
                <input type="file" class="form-control bg-light text-dark border-light-subtle" name="facturaImpEdit" id="factura_imp_edit">
              </div>
            </div>
          </div>
          <div class="modal-footer border-top border-light-subtle">
            <button type="button" class="btn btn-outline-light rounded-pill px-4" data-bs-dismiss="modal">Cancelar</button>
            <button type="submit" class="btn btn-accent rounded-pill px-4">Actualizar</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <div id="menuContextual" class="menu-contextual bg-light border-light-subtle shadow-lg">
    <ul class="m-0 p-0 text-dark">
      <li id="editar" class="text-accent py-2 px-3 border-bottom border-light-subtle"><i class="fa-solid fa-pen me-2"></i>Editar</li>
      <li id="eliminar" class="text-danger py-2 px-3"><i class="fa-solid fa-trash me-2"></i>Eliminar</li>
    </ul>
  </div>

  <!-- Scripts -->
  <script>
    const IS_ADMIN = <?php echo $isAdmin ? 'true' : 'false'; ?>;
  </script>
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <!-- Chart.js -->
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <!-- Librería para el Confeti -->
  <script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.6.0/dist/confetti.browser.min.js"></script>
  <script src="js/app.js"></script>
</body>
</html>