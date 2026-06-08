let chartCategoriasInstance = null;
let chartEvolucionInstance = null;
let confetiInicialLanzado = false;

function lanzarConfeti() {
  var duration = 3 * 1000;
  var animationEnd = Date.now() + duration;
  var defaults = { startVelocity: 30, spread: 360, ticks: 60, zIndex: 1060 }; // zIndex alto para mostrar sobre modales/sweetalert

  function randomInRange(min, max) {
    return Math.random() * (max - min) + min;
  }

  var interval = setInterval(function() {
    var timeLeft = animationEnd - Date.now();
    if (timeLeft <= 0) {
      return clearInterval(interval);
    }
    var particleCount = 50 * (timeLeft / duration);
    confetti(Object.assign({}, defaults, { particleCount, origin: { x: randomInRange(0.1, 0.3), y: Math.random() - 0.2 } }));
    confetti(Object.assign({}, defaults, { particleCount, origin: { x: randomInRange(0.7, 0.9), y: Math.random() - 0.2 } }));
  }, 250);
}

function lanzarConfetiAvataresPagados() {
  $('.user-grid-item').each(function() {
    var $avatar = $(this).find('.avatar-grid-img.pagado:not(.d-none)');
    if ($avatar.length > 0) {
      var rect = $avatar[0].getBoundingClientRect();
      // Calcular posición en porcentaje (0 a 1)
      var x = (rect.left + (rect.width / 2)) / window.innerWidth;
      var y = (rect.top + (rect.height / 2)) / window.innerHeight;
      
      confetti({
        particleCount: 60,
        spread: 80,
        startVelocity: 25,
        origin: { x: x, y: y },
        zIndex: 1060,
        colors: ['#10b981', '#3b82f6', '#fbbf24'] // Verde, azul y amarillo
      });
    }
  });
}

$(document).ready(function () {
  // Setear mes y año actual
  const fecha = new Date();
  const mesActual = ("0" + (fecha.getMonth() + 1)).slice(-2);
  const anioActual = fecha.getFullYear().toString();
  
  $("#filtroMes").val(mesActual);
  $("#filtroAnio").val(anioActual);

  cargarDashboard();

  $("#btnFiltrar").click(function() {
    cargarDashboard();
  });

  botonesUsuarios();
});

function cargarDashboard() {
  const mes = $("#filtroMes").val();
  const anio = $("#filtroAnio").val();

  trearImpuestos(mes, anio);
  traerUsuarios(mes, anio);
  cargarEstadisticas();
}

function trearImpuestos(mes, anio) {
  $.ajax({
    url: "ajax/impuestosMensuales.php",
    type: "POST",
    data: { action: "buscar", mes: mes, anio: anio },
    success: function (response) {
      if (response.status === "notData") {
        $("#listaFacturas").html('<li class="list-group-item text-center text-secondary border-0 py-4"><i class="fa-regular fa-folder-open fa-3x mb-3 text-dark"></i><br>No hay facturas para este mes</li>');
        $("#montoTotalMes").text("$0.00");
      } else {
        var js = JSON.parse(response);
        var cards = "";
        var sumaCosto = 0;

        for (var i = 0; i < js.length; i++) {
          let ruta = "";
          if (js[i].ruta_comprobante_imp && js[i].ruta_comprobante_imp !== "") {
            // Verificar si es pdf o imagen para cambiar el icono
            let iconoArchivo = js[i].ruta_comprobante_imp.toLowerCase().endsWith('.pdf') ? 'fa-file-pdf text-danger' : 'fa-image text-info';
            
            ruta = `<a target="_blank" href="${js[i].ruta_comprobante_imp}" class="btn btn-sm btn-outline-dark border-light-subtle ms-2 rounded-circle shadow-sm" title="Ver Comprobante"><i class="fa-solid ${iconoArchivo}"></i></a>`;
          }

          let icon = getIcon(js[i].tipo_imp);

          cards += `
            <li class="list-group-item d-flex justify-content-between align-items-center py-3 border-0 border-bottom border-light-subtle bg-transparent">
              <div class="d-flex align-items-center">
                <div class="sre-card rounded-circle p-2 me-3 text-accent border border-light-subtle" style="width:45px; height:45px; display:flex; align-items:center; justify-content:center;">
                  <i class="fa-solid ${icon} fa-lg"></i>
                </div>
                <div>
                  <h6 class="my-0 fw-bold text-dark">${js[i].nombre_imp}</h6>
                  <small class="text-secondary"><i class="fa-solid fa-building me-1"></i>${js[i].proveedor_imp} | Vto: ${js[i].fecha_vencimiento_imp}</small>
                  <span style="display:none;" class="id-impuesto">${js[i].id_imp}</span>
                </div>
              </div>
              <div class="d-flex align-items-center">
                <span class="badge bg-light text-dark border border-light-subtle rounded-pill fs-6 px-3 py-2 shadow-sm">$${js[i].costo_imp}</span>
                ${ruta}
              </div>
            </li>`;

          sumaCosto += parseFloat(js[i].costo_imp);
        }

        $("#listaFacturas").html(cards);
        $("#montoTotalMes").text("$" + sumaCosto.toFixed(2));

        // Menu contextual
        if(IS_ADMIN) {
          $("#listaFacturas").on("contextmenu", "li", function (e) {
            e.preventDefault(); 
            let idSeleccionado = $(this).find(".id-impuesto").text();
            var menuContextual = $("#menuContextual");
            
            menuContextual.css({
              display: "block",
              left: e.pageX + "px",
              top: e.pageY + "px"
            });

            $("#editar").off("click").on("click", function() {
              menuContextual.hide();
              editarImpuesto(idSeleccionado);
            });

            $("#eliminar").off("click").on("click", function() {
              menuContextual.hide();
              eliminarImpuesto(idSeleccionado);
            });
          });

          $(document).on("click", function () {
            $("#menuContextual").hide();
          });
        }
      }
    }
  });
}

window.cargarListaGastos = function() {
  $.ajax({
    url: "ajax/traerTodosLosImpuestos.php",
    type: "GET",
    success: function(response) {
      var data = JSON.parse(response);
      var tbody = $("#tablaAdministrarGastosBody");
      tbody.empty();
      
      if (data.length === 0) {
        tbody.append('<tr><td colspan="6" class="text-center text-muted">No hay gastos registrados</td></tr>');
        return;
      }
      
      data.forEach(function(imp) {
        var fecha = new Date(imp.fecha_vencimiento_imp + "T00:00:00");
        var fechaStr = ("0" + fecha.getDate()).slice(-2) + "/" + ("0" + (fecha.getMonth() + 1)).slice(-2) + "/" + fecha.getFullYear();
        var link = imp.ruta_comprobante_imp ? `<a href="${imp.ruta_comprobante_imp}" target="_blank" class="btn btn-sm btn-outline-primary" title="Ver Comprobante"><i class="fa-solid fa-file-pdf"></i></a>` : '';
        
        var row = `
          <tr>
            <td class="text-secondary fw-bold">#${imp.id_imp}</td>
            <td class="fw-bold">${imp.nombre_imp}</td>
            <td>${imp.proveedor_imp}</td>
            <td>${fechaStr}</td>
            <td class="fw-bold text-accent">$${imp.costo_imp}</td>
            <td class="text-center">
              ${link}
              <button class="btn btn-sm btn-outline-warning mx-1" onclick="editarImpuesto(${imp.id_imp}); $('#modalAdministrarGastos').modal('hide');" title="Editar"><i class="fa-solid fa-pen"></i></button>
              <button class="btn btn-sm btn-outline-danger" onclick="eliminarImpuestoDesdeABM(${imp.id_imp})" title="Eliminar"><i class="fa-solid fa-trash"></i></button>
            </td>
          </tr>
        `;
        tbody.append(row);
      });
    }
  });
};

window.eliminarImpuestoDesdeABM = function(id) {
  Swal.fire({
    title: '¿Estás seguro?',
    text: "¡El gasto será eliminado de la base de datos (y de los usuarios asignados)!",
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#d33',
    cancelButtonColor: '#6c757d',
    confirmButtonText: '<i class="fa-solid fa-trash me-1"></i> Sí, eliminar',
    cancelButtonText: 'Cancelar'
  }).then((result) => {
    if (result.isConfirmed) {
      $.post("ajax/eliminar.php", { id: id }, function(res) {
        Swal.fire("¡Eliminado!", "El gasto ha sido eliminado.", "success");
        cargarListaGastos();
        trearImpuestos($("#mes_seleccionado").val(), $("#anio_seleccionado").val());
      }).fail(function() {
        Swal.fire("Error", "Ocurrió un error al eliminar el registro.", "error");
      });
    }
  });
};

window.editarImpuesto = function(id) {
  obtenerDatosEdicion(id);
};

window.eliminarImpuesto = function(id) {
  Swal.fire({
    title: '¿Estás seguro?',
    text: "¡La factura será eliminada permanentemente!",
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#d33',
    cancelButtonColor: '#6c757d',
    confirmButtonText: '<i class="fa-solid fa-trash me-1"></i> Sí, eliminar',
    cancelButtonText: 'Cancelar'
  }).then((result) => {
    if (result.isConfirmed) {
      $.post("ajax/eliminar.php", { id: id }, function(res) {
        Swal.fire("¡Eliminado!", "La factura ha sido eliminada.", "success");
        trearImpuestos($("#mes_seleccionado").val(), $("#anio_seleccionado").val());
      }).fail(function() {
        Swal.fire("Error", "Ocurrió un error al eliminar el registro.", "error");
      });
    }
  });
};

window.mostrarLogin = function() {
  Swal.fire({
    title: 'Iniciar Sesión',
    html: `
      <div style="overflow-x: hidden; padding: 10px 0;">
        <input type="text" id="loginUsername" class="swal2-input bg-light text-dark border-light-subtle" style="max-width: 85%;" placeholder="Usuario">
        <input type="password" id="loginPassword" class="swal2-input bg-light text-dark border-light-subtle" style="max-width: 85%;" placeholder="Contraseña">
      </div>
    `,
    confirmButtonText: 'Entrar',
    focusConfirm: false,
    didOpen: () => {
      const usernameInput = Swal.getPopup().querySelector('#loginUsername');
      const passwordInput = Swal.getPopup().querySelector('#loginPassword');
      const confirmButton = Swal.getConfirmButton();
      usernameInput.addEventListener('keypress', (e) => {
        if (e.key === 'Enter') confirmButton.click();
      });
      passwordInput.addEventListener('keypress', (e) => {
        if (e.key === 'Enter') confirmButton.click();
      });
    },
    preConfirm: () => {
      const username = Swal.getPopup().querySelector('#loginUsername').value;
      const password = Swal.getPopup().querySelector('#loginPassword').value;
      if (!username || !password) {
        Swal.showValidationMessage(`Por favor ingrese usuario y contraseña`);
      }
      return { username: username, password: password };
    }
  }).then((result) => {
    if (result.isConfirmed) {
      $.post("ajax/login.php", { action: "login", username: result.value.username, password: result.value.password }, function(res) {
        if(res === 'success') {
          location.reload();
        } else {
          Swal.fire('Error', 'Credenciales incorrectas', 'error');
        }
      });
    }
  });
};

window.logoutAdmin = function() {
  $.post("ajax/login.php", { action: "logout" }, function(res) {
    if(res === 'success') {
      location.reload();
    }
  });
};

function traerUsuarios(mes, anio) {
  $.ajax({
    url: "ajax/traerUsuarios.php",
    type: "POST",
    data: { action: "buscar", mes: mes, anio: anio },
    success: function (response) {
      if (response.status === "notData") {
        $("#gridUsuarios").html('<div class="col-12 p-4 text-center text-secondary"><i class="fa-solid fa-users-slash fa-3x mb-3 text-dark"></i><br>No hay gastos asignados a usuarios en este mes</div>');
        $("#montoTotalPagado").text("$0.00");
        $("#montoTotalPendiente").text("$0.00");
      } else {
        var js = JSON.parse(response);
        var usuarios = {};
        
        var totalPagado = 0;
        var totalPendiente = 0;

        js.forEach(function (item) {
          if (!usuarios[item.id_usuario]) {
            usuarios[item.id_usuario] = {
              nombre: item.nombre_usuario,
              telefono: item.telefono,
              telefono2: item.telefono2,
              telegram_id: item.telegram_id,
              impuestos: []
            };
          }
          usuarios[item.id_usuario].impuestos.push({
            nombre: item.nombre_imp,
            monto: item.monto,
            id: item.id_imp,
            pago: item.pago
          });
        });

        window.usuariosActivos = usuarios; // Para el modal

        var cards = "";
        for (var id in usuarios) {
          var usuario = usuarios[id];
          var sumaCostoImp = 0;
          
          // Verificar si TODOS los impuestos del usuario están pagados para marcar el check principal
          var isPagado = false;
          if (usuario.impuestos.length > 0) {
            var pagadosCount = 0;
            usuario.impuestos.forEach(function (impuesto) {
              if (impuesto.pago == "1" || impuesto.pago == "true" || impuesto.pago == true || impuesto.pago === 'PAGADO' || impuesto.pago === 'SI') {
                pagadosCount++;
              }
            });
            isPagado = (pagadosCount === usuario.impuestos.length);
          }

          usuario.impuestos.forEach(function (impuesto) {
            sumaCostoImp += parseFloat(impuesto.monto);
          });

          if(isPagado) {
            totalPagado += sumaCostoImp;
          } else {
            totalPendiente += sumaCostoImp;
          }

          var rnd = new Date().getTime();
          var imgUrl = `img/${usuario.nombre}.png?v=${rnd}`;
          var videoUrl = `img/${usuario.nombre}.mp4?v=${rnd}`;
          var badgeHtml = isPagado ? '<span class="badge bg-success border border-dark shadow-sm px-2 py-1 mt-1" style="font-size: 0.65rem;">PAGADO</span>' : '<span class="badge bg-danger border border-dark shadow-sm px-2 py-1 mt-1" style="font-size: 0.65rem;">PENDIENTE</span>';
          var pagadoClass = isPagado ? 'pagado' : '';

          cards += `
          <div class="col text-center">
            <div class="sre-card rounded-4 p-3 cursor-pointer user-grid-item h-100 d-flex flex-column align-items-center justify-content-center" data-bs-toggle="offcanvas" data-bs-target="#offcanvasUsuario" onclick="abrirDetalleUsuario('${id}')">
              <div class="position-relative mb-1 avatar-container">
                <video src="${videoUrl}" class="avatar-grid-img ${pagadoClass} d-none" autoplay loop muted playsinline id="vid_${id}"></video>
                <img src="${imgUrl}" alt="${usuario.nombre}" class="avatar-grid-img ${pagadoClass}" id="img_${id}" onerror="
                  let v = document.getElementById('vid_${id}');
                  let i = document.getElementById('img_${id}');
                  v.classList.remove('d-none');
                  i.classList.add('d-none');
                  v.addEventListener('error', function() {
                    v.classList.add('d-none');
                    i.classList.remove('d-none');
                    i.onerror = null;
                    i.src='https://ui-avatars.com/api/?name=${usuario.nombre}&background=3b82f6&color=fff';
                  }, true);
                ">
              </div>
              <div class="text-dark fw-bold small text-truncate w-100 mt-2">${usuario.nombre}</div>
              <div>${badgeHtml}</div>
            </div>
          </div>`;
        }

        $("#gridUsuarios").html(cards);
        
        // Festejo inicial si hay alguien que ya pagó
        if (!confetiInicialLanzado && totalPagado > 0) {
          setTimeout(function() {
            lanzarConfetiAvataresPagados();
          }, 800); // Delay extra para asegurar que las fotos/videos estén en el DOM y calculen su centro
          confetiInicialLanzado = true;
        }
      }
    }
  });
}

window.abrirDetalleUsuario = function(id) {
  window.usuarioActivoId = id;
  var usuario = window.usuariosActivos[id];
  if (!usuario) return;

  var sumaCostoImp = 0;
  
  var isPagado = false;
  if (usuario.impuestos.length > 0) {
    var pagadosCount = 0;
    usuario.impuestos.forEach(function (impuesto) {
      if (impuesto.pago == "1" || impuesto.pago == "true" || impuesto.pago == true || impuesto.pago === 'PAGADO' || impuesto.pago === 'SI') {
        pagadosCount++;
      }
    });
    isPagado = (pagadosCount === usuario.impuestos.length);
  }

  // Modificar Offcanvas Header para que soporte videos
  var rnd = new Date().getTime();
  var imgUrl = `img/${usuario.nombre}.png?v=${rnd}`;
  var videoUrl = `img/${usuario.nombre}.mp4?v=${rnd}`;
  var pagadoClass = isPagado ? 'pagado' : '';
  
  var offcanvasAvatarHtml = `
    <video src="${videoUrl}" class="avatar-grid-img ${pagadoClass} me-3 d-none" style="width: 65px; height: 65px;" autoplay loop muted playsinline id="offcanvasVid"></video>
    <img src="${imgUrl}" class="avatar-grid-img ${pagadoClass} me-3" style="width: 65px; height: 65px;" id="offcanvasImg" onerror="
      let v = document.getElementById('offcanvasVid');
      let i = document.getElementById('offcanvasImg');
      v.classList.remove('d-none');
      i.classList.add('d-none');
      v.addEventListener('error', function() {
        v.classList.add('d-none');
        i.classList.remove('d-none');
        i.onerror = null;
        i.src='https://ui-avatars.com/api/?name=${usuario.nombre}&background=3b82f6&color=fff';
      }, true);
    ">
  `;
  
  $("#offcanvasAvatarContainer").html(offcanvasAvatarHtml);
  $("#offcanvasNombreText").text(usuario.nombre);

  if (isPagado) {
    $("#offcanvasBadge").attr("class", "badge bg-success").html('<i class="fa-solid fa-check me-1"></i>PAGADO');
    $("#btnWhatsappIndividual").addClass("d-none");
    if (typeof IS_ADMIN !== 'undefined' && IS_ADMIN && usuario.telefono) {
      $("#btnNotificarPagoConfirmado").removeClass("d-none");
    } else {
      $("#btnNotificarPagoConfirmado").addClass("d-none");
    }
  } else {
    $("#offcanvasBadge").attr("class", "badge bg-danger").html('<i class="fa-solid fa-clock me-1"></i>PENDIENTE');
    $("#btnNotificarPagoConfirmado").addClass("d-none");
    
    // Configurar WhatsApp individual recordatorio
    if (typeof IS_ADMIN !== 'undefined' && IS_ADMIN && usuario.telefono) {
      $("#btnWhatsappIndividual").removeClass("d-none");
    } else {
      $("#btnWhatsappIndividual").addClass("d-none");
    }
  }

  var listHtml = "";
  var datosUsuario = [];

  usuario.impuestos.forEach(function (impuesto) {
    sumaCostoImp += parseFloat(impuesto.monto);
    datosUsuario.push({ id: "imp_" + id + "_" + impuesto.id, valor: impuesto.id });

    listHtml += `
      <li class="list-group-item bg-transparent border-bottom border-light-subtle px-0 py-3">
        <div class="d-flex justify-content-between align-items-center cursor-pointer" onclick="toggleGraficoHistorial('${id}', '${impuesto.nombre}', 'canvas_${id}_${impuesto.id}')" title="Toca para ver historial">
          <span class="text-dark"><i class="fa-solid fa-chart-line me-2 text-accent"></i>${impuesto.nombre}</span>
          <span class="fw-bold text-accent">$${impuesto.monto}</span>
        </div>
        <div id="container_canvas_${id}_${impuesto.id}" class="mt-3 d-none w-100 position-relative" style="height: 180px;">
          <canvas id="canvas_${id}_${impuesto.id}"></canvas>
        </div>
      </li>`;
  });

  $("#offcanvasListaImpuestos").html(listHtml);
  $("#offcanvasTotal").text("$" + sumaCostoImp.toFixed(2));

  $("#offcanvasSwitchPago").prop("checked", isPagado);
  $("#offcanvasSwitchPago").prop("disabled", false); // Siempre habilitado
  
  if(isPagado){
    $("#offcanvasLabelPago").text("Pagado");
  } else {
    $("#offcanvasLabelPago").text("Marcar como pagado");
  }

  $("#offcanvasSwitchPago").off("change").on("change", function() {
    var estadoDeseado = this.checked;
    cargarPago(id, datosUsuario, estadoDeseado);
    setTimeout(() => {
      var offcanvasEl = document.getElementById('offcanvasUsuario');
      var offcanvas = bootstrap.Offcanvas.getInstance(offcanvasEl);
      if(offcanvas) offcanvas.hide();
    }, 500);
  });
}

function cargarEstadisticas() {
  $.ajax({
    url: "ajax/estadisticas.php",
    type: "POST",
    data: { action: "estadisticas" },
    success: function (response) {
      if(response) {
        var data = JSON.parse(response);
        
        // Chart Categorias
        if(chartCategoriasInstance) chartCategoriasInstance.destroy();
        var ctxCat = document.getElementById('chartCategorias').getContext('2d');
        chartCategoriasInstance = new Chart(ctxCat, {
            type: 'doughnut',
            data: {
                labels: data.categorias,
                datasets: [{
                    data: data.totales_cat,
                    backgroundColor: ['#0d6efd', '#198754', '#ffc107', '#dc3545', '#0dcaf0', '#6f42c1', '#adb5bd'],
                    borderWidth: 0
                }]
            },
            options: { 
              plugins: { legend: { position: 'bottom' } },
              cutout: '70%'
            }
        });

        // Chart Evolucion
        if(chartEvolucionInstance) chartEvolucionInstance.destroy();
        var ctxEvo = document.getElementById('chartEvolucion').getContext('2d');
        chartEvolucionInstance = new Chart(ctxEvo, {
            type: 'bar',
            data: {
                labels: data.meses,
                datasets: [{
                    label: 'Gasto Mensual',
                    data: data.totales_meses,
                    backgroundColor: 'rgba(13, 110, 253, 0.7)',
                    borderColor: 'rgba(13, 110, 253, 1)',
                    borderWidth: 1,
                    borderRadius: 6
                }]
            },
            options: {
              scales: { 
                y: { 
                  beginAtZero: true,
                  grid: { color: 'rgba(0,0,0,0.05)' }
                },
                x: {
                  grid: { display: false }
                }
              },
              plugins: { legend: { display: false } }
            }
        });
      }
    }
  });
}

let graficosHistorial = {}; // Para guardar las instancias de los gráficos

window.toggleGraficoHistorial = function(id_usu, nombre_imp, canvasId) {
  var containerId = "container_" + canvasId;
  var $container = $("#" + containerId);
  
  // Si el contenedor ya está visible, lo ocultamos y destruimos el gráfico para liberar memoria
  if (!$container.hasClass("d-none")) {
    $container.addClass("d-none");
    if (graficosHistorial[canvasId]) {
      graficosHistorial[canvasId].destroy();
      delete graficosHistorial[canvasId];
    }
    return;
  }
  
  // Mostrar el contenedor e inicializar gráfico
  $container.removeClass("d-none");
  
  // Obtener datos del servidor
  $.ajax({
    url: "ajax/historialImpuestoUsuario.php",
    type: "POST",
    data: { 
      id_usu: id_usu, 
      nombre_imp: nombre_imp,
      anio: $("#anio_seleccionado").val() || new Date().getFullYear()
    },
    success: function(response) {
      var data = JSON.parse(response);
      if(data.status === "error" || data.length === 0) {
        $container.html('<div class="text-center text-secondary small w-100 mt-4">No hay historial disponible</div>');
        return;
      }
      
      // Formatear datos para el gráfico
      var labels = [];
      var values = [];
      var hasData = false;
      
      // Los datos vienen ordenados por fecha ascendente
      data.forEach(function(row) {
        // Extraer mes/año de la fecha (ej: 2026-05-12 -> May 2026)
        var date = new Date(row.fecha + "T00:00:00"); 
        var mesStr = date.toLocaleString('es-ES', { month: 'short' });
        labels.push(mesStr.charAt(0).toUpperCase() + mesStr.slice(1) + " " + date.getFullYear().toString().slice(-2));
        
        if (row.monto !== null) {
          values.push(parseFloat(row.monto));
          hasData = true;
        } else {
          values.push(null);
        }
      });

      if (!hasData) {
        $container.html('<div class="text-center text-secondary small w-100 mt-4">No hay historial cargado para este año</div>');
        return;
      }
      
      var ctx = document.getElementById(canvasId).getContext('2d');
      
      // Crear nuevo gráfico (si por error ya existía, lo pisamos)
      if(graficosHistorial[canvasId]) {
        graficosHistorial[canvasId].destroy();
      }
      
      graficosHistorial[canvasId] = new Chart(ctx, {
        type: 'line',
        data: {
          labels: labels,
          datasets: [{
            label: 'Costo a pagar ($)',
            data: values,
            borderColor: '#3b82f6', // Accent blue
            backgroundColor: 'rgba(59, 130, 246, 0.2)',
            borderWidth: 2,
            pointBackgroundColor: '#fff',
            pointBorderColor: '#3b82f6',
            pointBorderWidth: 2,
            pointRadius: 4,
            fill: true,
            tension: 0.3
          }]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          plugins: {
            legend: { display: false },
            tooltip: {
              callbacks: {
                label: function(context) {
                  return '$ ' + context.parsed.y.toLocaleString('es-AR', {minimumFractionDigits: 2});
                }
              }
            }
          },
          scales: {
            x: {
              grid: { display: false, drawBorder: false },
              ticks: { color: '#64748b', font: { size: 10 } }
            },
            y: {
              border: { display: false },
              grid: { color: 'rgba(0,0,0,0.05)' },
              ticks: {
                color: '#64748b',
                font: { size: 10 },
                callback: function(value) {
                  return '$' + value;
                }
              }
            }
          }
        }
      });
    }
  });
}

function getIcon(tipo) {
  switch (tipo) {
    case 'AGUA': return 'fa-faucet-drip text-info';
    case 'GAS': return 'fa-fire-flame-simple text-danger';
    case 'LUZ': return 'fa-lightbulb text-warning';
    case 'INTERNET': return 'fa-wifi text-primary';
    case 'IMPUESTO INMOBILIARIO': return 'fa-house text-success';
    case 'TASAS MUNICIPALES': return 'fa-building text-secondary';
    default: return 'fa-file-invoice text-dark';
  }
}

function cargarPago(usuarioId, datosUsuario, estado) {
  const data = { usuarioId: usuarioId, impuestos: datosUsuario, estado: estado };
  $.ajax({
    url: "ajax/pagoUsuarios.php",
    type: "POST",
    data: JSON.stringify(data),
    contentType: "application/json",
    success: function (response) {
      Swal.fire({
        title: estado ? "¡Pago Registrado!" : "Pago Anulado",
        text: estado ? "Se ha marcado como pagado exitosamente." : "El estado ha vuelto a Pendiente.",
        icon: "success",
        timer: 1500,
        showConfirmButton: false
      }).then(() => {
        var filtroMes = $("#filtroMes").val();
        var filtroAnio = $("#filtroAnio").val();
        traerUsuarios(filtroMes, filtroAnio);
      });
      
      if (estado) {
        lanzarConfeti();
      }
      
      cargarDashboard(); // Refrescar totales
    }
  });
}

function botonesUsuarios() {
  $.ajax({
    url: "ajax/traerUsuariosBotones.php",
    type: "POST",
    data: { action: "buscar" },
    success: function (response) {
      // Limpiar posibles espacios en blanco o saltos de linea
      response = response.trim();
      if (response !== "notData") {
        try {
          var js = JSON.parse(response);
          var cards1 = "";
          for (var i = 0; i < js.length; i++) {
            cards1 += `
              <div>
                <input type="checkbox" class="btn-check" id="btn-check-${js[i].id_usuario}" data-id="${js[i].id_usuario}" autocomplete="off">
                <label class="btn btn-user-select rounded-pill btn-sm mb-2" for="btn-check-${js[i].id_usuario}">
                  <i class="fa-solid fa-user me-1"></i>${js[i].nombre_usuario}
                </label>
              </div>`;
          }
          $("#listaUsu").html(cards1);
        } catch (e) {
          console.error("Error al parsear usuarios:", e, response);
        }
      } else {
        $("#listaUsu").html('<span class="text-secondary small">No hay usuarios cargados</span>');
      }
    }
  });
}

$(document).ready(function () {
  $("#formSubirImpuestos").submit(function (e) {
    e.preventDefault();
    var formData = new FormData(this);
    $(".btn-check:checked").each(function () {
      formData.append("usuariosSeleccionados[]", $(this).data("id"));
    });

    $.ajax({
      url: $(this).attr("action"),
      type: "POST",
      data: formData,
      processData: false,
      contentType: false,
      success: function (response) {
        if (response.trim() === 'success') {
          Swal.fire("¡Guardado!", "Gasto registrado con éxito", "success");
          $("#modalAgregar").modal("hide");
          $("#formSubirImpuestos")[0].reset();
          $(".btn-check").prop("checked", false);
          cargarDashboard();
        } else {
          Swal.fire("Error", response, "error");
        }
      },
      error: function() {
        Swal.fire("Error", "Ocurrió un problema de red o de servidor.", "error");
      }
    });
  });

  $("#formSubirUsuarios").submit(function (e) {
    e.preventDefault();
    var formData = new FormData(this);
    $.ajax({
      url: $(this).attr("action"),
      type: "POST",
      data: formData,
      processData: false,
      contentType: false,
      success: function () {
        Swal.fire("¡Agregado!", "Usuario creado exitosamente", "success");
        $("#modalAgregarUsuario").modal("hide");
        $("#formSubirUsuarios")[0].reset();
        botonesUsuarios();
      }
    });
  });
});

function obtenerDatosEdicion(id) {
  $.ajax({
    url: "ajax/obtenerDatosEdicion.php",
    type: "POST",
    data: { action: "buscar", id: id },
    success: function (response) {
      if (response != "notData") {
        var js = JSON.parse(response);
        for (var i = 0; i < js.length; i++) {
          $("#id_imp_edit").val(js[i].id_imp);
          $("#nombre_imp_edit").val(js[i].nombre_imp);
          $("#proveedor_imp_edit").val(js[i].proveedor_imp);
          $("#tipo_imp_edit").val(js[i].tipo_imp);
          $("#fecha_vencimiento_imp_edit").val(js[i].fecha_vencimiento_imp);
          $("#costo_imp_edit").val(js[i].costo_imp);

          if (js[i].ruta_comprobante_imp) {
            $("#tieneActa").val("SI");
            $("#factMsg").html(`<a href="${js[i].ruta_comprobante_imp}" target="_blank" class="btn btn-sm btn-outline-info rounded-pill mb-2"><i class="fa-solid fa-file-pdf me-1"></i> Ver comprobante actual</a>`);
          } else {
            $("#tieneActa").val("NO");
            $("#factMsg").html('');
          }
        }
        $("#modalEditar").modal("show");
      }
    }
  });
}

$("#formEditarImpuestos").submit(function (e) {
  e.preventDefault();
  var formData = new FormData(this);
  $.ajax({
    url: $(this).attr("action"),
    type: "POST",
    data: formData,
    processData: false,
    contentType: false,
    success: function () {
      Swal.fire("¡Actualizado!", "Los datos fueron guardados", "success");
      $("#modalEditar").modal("hide");
      cargarDashboard();
    }
  });
});

  // Editar Usuario click
  $(document).on("click", "#btnEditarUsuario", function() {
    if(window.usuarioActivoId && window.usuariosActivos[window.usuarioActivoId]) {
      var u = window.usuariosActivos[window.usuarioActivoId];
      $("#edit_id_usu").val(window.usuarioActivoId);
      $("#edit_nombre_usu").val(u.nombre);
      $("#edit_telefono_usu").val(u.telefono || '');
      $("#edit_telefono2_usu").val(u.telefono2 || '');
      
      var offcanvasEl = document.getElementById('offcanvasUsuario');
      var offcanvas = bootstrap.Offcanvas.getInstance(offcanvasEl);
      if(offcanvas) offcanvas.hide();
      
      $("#modalEditarUsuario").modal("show");
    }
  });

  // Submit Editar Usuario
  $("#formEditarUsuario").on("submit", function (e) {
    e.preventDefault();
    $.ajax({
      url: $(this).attr("action"),
      type: $(this).attr("method"),
      data: new FormData(this),
      contentType: false,
      cache: false,
      processData: false,
      success: function (res) {
        if (res.trim() == "success") {
          Swal.fire({
            title: "Éxito",
            text: "Usuario modificado correctamente",
            icon: "success",
            timer: 1500,
            showConfirmButton: false
          }).then(() => {
            $("#modalEditarUsuario").modal("hide");
            var mesSeleccionado = $("#mes_seleccionado").val();
            var anioSeleccionado = $("#anio_seleccionado").val();
            traerUsuarios(mesSeleccionado, anioSeleccionado);
          });
        } else {
          Swal.fire("Error", "No se pudo modificar el usuario", "error");
        }
      }
    });
  });

  window.notificarTodos = function() {
    Swal.fire({
      title: 'Notificar Mensualidad',
      text: "¿Enviar mensajes de WhatsApp a todos los usuarios con sus montos a pagar del mes?",
      icon: 'question',
      showCancelButton: true,
      confirmButtonText: '<i class="fa-solid fa-paper-plane me-1"></i> Sí, enviar',
      cancelButtonText: 'Cancelar'
    }).then((result) => {
      if(result.isConfirmed) {
        Swal.fire({
          title: 'Enviando...',
          html: 'Por favor, no cierres esta ventana',
          allowOutsideClick: false,
          didOpen: () => { Swal.showLoading(); }
        });
        $.post("ajax/notificar.php", { action: 'mensualidad', mes: $("#mes_seleccionado").val(), anio: $("#anio_seleccionado").val() }, function(res) {
          try {
            var response = JSON.parse(res);
            if (response.status == 'success') {
              Swal.fire("¡Enviado!", response.message, "success");
            } else {
              Swal.fire("Atención", response.message, "warning");
            }
          } catch(e) {
            Swal.fire("Error", "Ocurrió un error al enviar las notificaciones.", "error");
          }
        });
      }
    });
  }

  window.notificarDeudaIndividual = function() {
    if(!window.usuarioActivoId) return;
    Swal.fire({
      title: 'Enviando Recordatorio...',
      allowOutsideClick: false,
      didOpen: () => { Swal.showLoading(); }
    });
    $.post("ajax/notificar.php", { action: 'mensualidad_individual', id_usuario: window.usuarioActivoId, mes: $("#mes_seleccionado").val(), anio: $("#anio_seleccionado").val() }, function(res) {
      try {
        var response = JSON.parse(res);
        if (response.status == 'success') {
          Swal.fire("¡Enviado!", response.message, "success");
        } else {
          Swal.fire("Error", response.message, "error");
        }
      } catch(e) {
        Swal.fire("Error", "Error al procesar la respuesta del servidor", "error");
      }
    });
  };

  window.notificarPagoConfirmado = function() {
    if(!window.usuarioActivoId) return;
    Swal.fire({
      title: 'Enviando Confirmación...',
      allowOutsideClick: false,
      didOpen: () => { Swal.showLoading(); }
    });
    $.post("ajax/notificar.php", { action: 'pago_confirmado', id_usuario: window.usuarioActivoId, mes: $("#mes_seleccionado").val(), anio: $("#anio_seleccionado").val() }, function(res) {
      try {
        var response = JSON.parse(res);
        if (response.status == 'success') {
          Swal.fire("¡Enviado!", response.message, "success");
        } else {
          Swal.fire("Error", response.message, "error");
        }
      } catch(e) {
        Swal.fire("Error", "Error al procesar la respuesta del servidor", "error");
      }
    });
  };
