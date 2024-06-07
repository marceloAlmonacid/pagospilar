function trearImpuestos() {
  const action = "buscar";

  $.ajax({
    url: "../ajax/impuestosMensuales.php",
    type: "POST",
    async: true,
    data: {
      action: action,
    },
    beforeSend: function () {},

    success: function (response) {
      if (response.status && response.status === "notData") {
      } else {
        var js = JSON.parse(response);
        let idSeleccionado = "";
        var cards = "";
        var cards1 = "";
        var cards2 = '<ul class="list-group mb-3 col-lg-11" >';
        var sumaCosto = 0;
        var sumaCostoImp = 0;
        var costoImp = 0;
        var baseUrl =
          "<?php echo 'http://'.$_SERVER['HTTP_HOST'].'/itpetro'; ?>";

        for (var i = 0; i < js.length; i++) {
          if (js[i].ruta_comprobante_imp) {
            ruta =
              '<a target="_black" href="' +
              js[i].ruta_comprobante_imp +
              '" class="icon-button" id="fac"><img src="../img/pdfIcon.png" alt="Icono"></a>';
          } else {
            ruta = "";
          }

          cards +=
            '<li class="list-group-item d-flex justify-content-between lh-sm"> <div> <h6 class="my-0 ml-1">' +
            js[i].nombre_imp +
            '</h6> <h6 class="my-0" style="display: none;">' +
            js[i].id_imp +
            '</h6> <small class="text-muted">' +
            js[i].proveedor_imp +
            '</small> </div> <div class="d-flex align-items-center"> ' +
            ruta +
            ' <span class="gif-container"></span> <span class="text-muted">$' +
            js[i].costo_imp +
            "</span> </div> </li>";

          sumaCosto += parseFloat(js[i].costo_imp);
        }
        cards1 +=
          '<span class="text-primary">Total del mes: <b>$' +
          sumaCosto.toFixed(2).toLocaleString();
        +"</b></span> ";

        cards2 +=
          '<li class="list-group-item d-flex justify-content-between lh-sm" style="border: none;">  <h6 class="my-1"> </h6>  <span id="cost1"class="bold">Total: $' +
          sumaCostoImp +
          "</span> </li></ul> ";

        $("#tabla").html(cards);

        // Menu contextual
        $("#tabla").on("contextmenu", "li", function (e) {
          e.preventDefault(); // Previene el menú contextual predeterminado

          idSeleccionado = $(this).find("h6").eq(1).text(); // Accede al segundo <h6> que contiene el id
          var menuContextual = $("#menuContextual");
          var pos = $(this).offset(); // Obtiene la posición del elemento `li`
          var height = $(this).outerHeight(); // Obtiene la altura del elemento `li`

          // Posiciona el menú contextual justo debajo del elemento `li`
          menuContextual.css({
            display: "block",
            left: pos.left, // Usa la misma posición horizontal del elemento `li`
            top: pos.top + height, // Posición vertical debajo del elemento `li`
          });

          // Asegúrate de ocultar el menú contextual si el usuario hace clic en otro lugar
          $(document).on("click", function () {
            menuContextual.hide();
          });

          // Manejar clics en opciones del menú
          $("#editar").on("click", function () {
            obtenerDatosEdicion(idSeleccionado);
          });

          $("#registrar").on("click", function () {
            registrarImp(idSeleccionado);
          });

          $("#eliminar").on("click", function () {
            swal("Esta seguro de eliminar?", {
              buttons: {
                agregar: {
                  text: "Eliminar",
                  value: "eliminar",
                },
                cancel: "Cancelar",
              },
              icon: "warning",
            }).then((value) => {
              switch (value) {
                case "eliminar":
                  if (idSeleccionado) {
                    $.ajax({
                      url: "../ajax/eliminar.php", // Asegúrate de reemplazar esto con la ruta correcta a tu script PHP
                      type: "POST",
                      data: { id: idSeleccionado },
                      success: function (response) {
                        trearImpuestos();
                      },
                      error: function () {
                        alert("Error al eliminar el registro");
                      },
                    });
                  }
                  break;

                default:
              }
            });
          });

          // Evita la propagación del evento (opcional, dependiendo de caso de uso)
          return false;
        });

        $("#costoTotal").html(
          cards1 +
            '<img src="../img/coins.gif" alt="Info Icon" style="width: 40px; height: 40px; margin-right: 10px;">'
        );
        $("#adriImp").html(cards2);
      }
    },
    error: function (error) {
      console.log(error);
    },
  });
}

//TRAER USUARIOS
function traerUsuarios() {
  const action = "buscar";
  $.ajax({
    url: "../ajax/traerUsuarios.php",
    type: "POST",
    data: { action: action },
    beforeSend: function () {},
    success: function (response) {
      if (response.status && response.status === "notData") {
        // Manejo cuando no hay datos
      } else {
        var js = JSON.parse(response);
        var usuarios = {};

        js.forEach(function (item) {
          if (!usuarios[item.id_usuario]) {
            usuarios[item.id_usuario] = {
              nombre: item.nombre_usuario,
              impuestos: [],
            };
          }
          usuarios[item.id_usuario].impuestos.push({
            nombre: item.nombre_imp,
            monto: item.monto,
          });
        });

        var cards = "";
        for (var id in usuarios) {
          var usuario = usuarios[id];
          var sumaCostoImp = 0;

          usuario.impuestos.forEach(function (impuesto) {
            sumaCostoImp += parseFloat(impuesto.monto);
          });

          cards += `<div class="accordion-item">
        <h2 class="accordion-header">
            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapse${id}" aria-expanded="false" aria-controls="flush-collapse${id}">
            
                <img src="../img/${usuario.nombre}.png" alt="Imagen de ${usuario.nombre}" id="imgUsu${id}" style="width: 50px; height: 50px; margin-right: 15px; border-radius: 50%;">
                ${usuario.nombre}
                <span id="status-${id}" class="status-text ms-auto pe-2"></span>
                
            </button>
        </h2>
        <div id="flush-collapse${id}" class="accordion-collapse collapse" data-bs-parent="#accordionFlushExample">
            <div class="accordion-body">
                <ul class="list-group mb-3 col-lg-11">`;

          usuario.impuestos.forEach(function (impuesto) {
            cards += `<li class="list-group-item d-flex justify-content-between lh-sm">
              <h6 class="my-1" id="">${impuesto.nombre}</h6>
              <div class="d-flex align-items-center">
                <div id="gif-${id}-${impuesto.nombre}" class="gif-container" data-user-id="${id}" style="display: none; width: 20px; height: 20px;">



                <img src="../img/ok.gif" id="gif${id}" alt="Cargando..." style="width: 100%; height: 100%;" />
                  
                </div>
                <span class="" style="margin-left: 5px; color: black;">$${impuesto.monto}</span>
              </div>
            </li>`;
          });

          cards += `<li class="list-group-item d-flex justify-content-between lh-sm" style="border: none;">
                <div class="form-check form-switch">
                  <input class="form-check-input pago-checkbox" type="checkbox" role="switch" id="flexSwitchCheckDefault-${id}">
                  <label class="form-check-label" for="flexSwitchCheckDefault-${id}">Pago</label>
                </div>

                  <div class="d-flex align-items-center">
                    <div id="" class="gif-container1" data-user-id="${id}" style="display: none; width: 40px; height: 40px;">



                    <img src="../img/aplauso.gif" id="aplauso${id}" alt="Cargando..." style="width: 100%; height: 100%;" />
                      
                    </div>
                    <span class="total" id="total${id}" >Total: $${sumaCostoImp}</span>
                  </div>
                
                
              </li>
            </ul>
          </div>
        </div>
      </div>`;
        }

        $("#accordionFlushExample").html(cards);

        // Añadido manejador de eventos para los checkboxes
        $(".pago-checkbox").change(function () {
          var checkboxId = this.id.replace("flexSwitchCheckDefault-", ""); // Extrae el ID del usuario directamente del ID del checkbox
          var total = document.getElementById("total" + checkboxId);
          var aplauso = document.getElementById("aplauso" + checkboxId);
          var check = document.getElementById("gif" + checkboxId);

          total.classList.remove(
            "animate__animated",
            "animate__backInLeft",
            "animate__wobble",
            "animate-color"
          );

          if (this.checked) {
            $(`.gif-container[data-user-id='${checkboxId}']`).show(); // Muestra todos los GIFs asociados con este usuario

            aplauso.setAttribute("src", "../img/aplauso.gif");
            total.classList.add("animate__animated", "animate__wobble");
            total.addEventListener("animationend", () => {
              total.classList.add("totalPost");
              check.setAttribute("src", "../img/ok.gif");
              $(`.gif-container1[data-user-id='${checkboxId}']`).show();
              cargarPago(checkboxId);
            });
          } else {
            $(`.gif-container[data-user-id='${checkboxId}']`).hide(); // Oculta todos los GIFs
            $(`.gif-container1[data-user-id='${checkboxId}']`).hide(); // Oculta todos los GIFs
            total.classList.remove("totalPost");
            total.classList.add("total");
          }
        });
      }
    },
    error: function (error) {
      console.log(error);
    },
  });
}

// function getInfo(){
//   const action = "buscar";
//   $.ajax({
//     url: "../ajax/traerInfo.php",
//     type: "POST",
//     data: { action: action },
//     beforeSend: function () {},
//     success: function (response) {
//       if (response.status && response.status === "notData") {
//         // Manejo cuando no hay datos
//       } else {
//         var js = JSON.parse(response);

//         cargarPago(js);
//       }
//     },
//     error: function (error) {
//       console.log(error);
//     },
//   });
// }

function cargarPago(id) {
  var usuarioId = id;
  var h6Elements = document.querySelectorAll("#flush-collapse" + id + " h6");
  // var spanElements = document.querySelectorAll("#flush-collapse" + id + " span");

  alert(h6Elements);

  //  h6Elements.forEach((element) => {
  //   alert(element.innerHTML);
  // });

  // $.ajax({
  //   url: $(this).attr("action"),
  //   type: "POST",
  //   data: formData,
  //   mimeType: "multipart/form-data",
  //   contentType: false,
  //   cache: false,
  //   processData: false,
  //   success: function (response) {
  //     swal("Ok!", "Se creo el impuesto!", "success");
  //     $("#modalAgregar").modal("hide");
  //     console.log(response);
  //   },
  //   error: function (jqXHR, textStatus, errorThrown) {
  //     alert("Error al guardar la información");
  //   },
  // });
}

function obtenerUsuariosSeleccionados() {
  var usuariosSeleccionados = [];
  $(".btn-check:checked").each(function () {
    usuariosSeleccionados.push($(this).data("id"));
  });
  return usuariosSeleccionados;
}

//  DB INGRESO DE IMPUESTOS
$(document).ready(function () {
  $("#formSubirImpuestos").submit(function (e) {
    e.preventDefault(); // Detener la ejecución normal del formulario

    var formData = new FormData(this); // Crear un objeto FormData con los datos del formulario

    // Recoger todos los checkboxes marcados de los usuarios y agregarlos al formData
    $(".btn-check:checked").each(function () {
      formData.append("usuariosSeleccionados[]", $(this).data("id"));
    });

    $.ajax({
      url: $(this).attr("action"),
      type: "POST",
      data: formData,
      mimeType: "multipart/form-data",
      contentType: false,
      cache: false,
      processData: false,
      success: function (response) {
        swal("Ok!", "Se creo el impuesto!", "success");
        $("#modalAgregar").modal("hide");
        console.log(response);
      },
      error: function (jqXHR, textStatus, errorThrown) {
        alert("Error al guardar la información");
      },
    });
  });

  $("#modalAgregar").on("hidden.bs.modal", function (e) {
    trearImpuestos();
  });
});

//  DB INGRESO DE USUARIOS
$(document).ready(function () {
  $("#formSubirUsuarios").submit(function (e) {
    var formObj = $(this);
    var formURL = formObj.attr("action");

    if (window.FormData !== undefined) {
      var formData = new FormData(this);
      $.ajax({
        url: formURL,
        type: "POST",
        data: formData,
        mimeType: "multipart/form-data",
        contentType: false,
        cache: false,
        processData: false,
        success: function (data, textStatus, jqXHR) {
          swal("Ok!", "Se agrego el usuario!", "success");
          $("#modalAgregarUsuario").modal("hide");
        },
        error: function (jqXHR, textStatus, errorThrown) {
          alert("mal");
        },
      });
      e.preventDefault();
      // e.unbind();
    }
  });

  $("#modalAgregar").on("hidden.bs.modal", function (e) {
    trearImpuestos();
  });
});

function obtenerDatosEdicion(dato) {
  const idImp = document.getElementById("id_imp_edit");
  const tieneActa = document.getElementById("tieneActa");
  const nombreImp = document.getElementById("nombre_imp_edit");
  const proveedorImp = document.getElementById("proveedor_imp_edit");
  const tipoImp = document.getElementById("tipo_imp_edit");
  const fechaVencimientoImp = document.getElementById(
    "fecha_vencimiento_imp_edit"
  );
  const costoImp = document.getElementById("costo_imp_edit");
  const asigActa = document.getElementById("factura_imp_edit");

  const id = dato;
  const action = "buscar";
  var content = "";

  $.ajax({
    url: "../ajax/obtenerDatosEdicion.php",
    type: "POST",
    async: true,
    data: {
      action: action,
      id: id,
    },
    beforeSend: function () {},
    success: function (response) {
      if (response == "notData") {
      } else {
        var js = JSON.parse(response);
        var cards = "";
        var baseUrl = "<?php echo 'http://'.$_SERVER['HTTP_HOST'].'/'; ?>";
        for (var i = 0; i < js.length; i++) {
          idImp1 = js[i].id_imp;
          nombreImp1 = js[i].nombre_imp;
          proveedorImp1 = js[i].proveedor_imp;
          fechaVencimientoImp1 = js[i].fecha_vencimiento_imp;
          costoImp1 = js[i].costo_imp;
          rutaDoc1 = js[i].ruta_comprobante_imp;
          tipoImp1 = js[i].tipo_imp;
        }

        idImp.value = idImp1;
        nombreImp.value = nombreImp1;
        proveedorImp.value = proveedorImp1;
        tipoImp.value = tipoImp1;
        fechaVencimientoImp.value = fechaVencimientoImp1;
        costoImp.value = costoImp1;

        if (rutaDoc1) {
          rutaPre =
            '<div class="form-group col-md-12"><p>Ya existe una factura, queres reemplazarla?</p></div><embed class="mt-3 center"  src="' +
            rutaDoc1 +
            '#toolbar=0" type="" width="300" height="300">';
          ruta = '<a target="_black"  href="' + rutaDoc1 + '">Ver atca</a>';
          tieneActa.value = "SI";
        } else {
          ruta = "";
          rutaPre = "";
          tieneActa.value = "NO";
        }
        content +=
          '<div class="center">' + rutaPre + " </div><div>" + ruta + "</div>";

        $("#factMsg").html(content);
      }
    },
    error: function (error) {
      console.log(error);
    },
  });

  $("#modalEditar").modal("show");
}

//  DB EDICION DE IMPUESTOS
$(document).ready(function () {
  $("#formEditarImpuestos").submit(function (e) {
    var formData = new FormData(this);

    if (window.FormData !== undefined) {
      var formObj = $(this);
      formURL = formObj.attr("action");

      $.ajax({
        url: formURL,
        type: "POST",
        data: formData,
        mimeType: "multipart/form-data",
        contentType: false,
        cache: false,
        processData: false,
        success: function (data, textStatus, jqXHR) {
          swal("Ok!", "Se actualizaron los datos!", "success");
          $("#modalEditar").modal("hide");
        },
        error: function (jqXHR, textStatus, errorThrown) {
          alert("mal");
        },
      });
      e.preventDefault();
    }
  });

  $("#modalEditar").on("hidden.bs.modal", function (e) {
    trearImpuestos();
  });
});

function verFormData(formData) {
  let contenido = "";
  for (let [key, value] of formData.entries()) {
    contenido += key + ": " + value + "\n";
  }
  alert(contenido);
}

function registrarImp(id) {

  // Selecciona el elemento 'li' correspondiente al impuesto
  var impuestoElem = $('h6:contains(' + id + ')').closest('li');

  // Extraer datos del impuesto
  var nombreImpuesto = impuestoElem.find('h6.my-0.ml-1').text();
  var rutaFactura = impuestoElem.find('a').attr('href');
  var costoImpuesto = impuestoElem.find('.text-muted').last().text().replace('$', '');
  var proveedorImpuesto = impuestoElem.find('small.text-muted').text();
  var idImpuesto = impuestoElem.find('h6').eq(1).text(); // Asumiendo que el ID está en el segundo <h6>

  // Datos para enviar
  var datosImpuesto = {
      id: idImpuesto,
      nombre: nombreImpuesto,
      ruta_factura: rutaFactura,
      costo: costoImpuesto,
      proveedor: proveedorImpuesto
  };

  console.log(datosImpuesto);

  // Llamada AJAX para registrar el impuesto
  $.ajax({
      url: '../ajax/registrarImpuesto.php',
      type: 'POST',
      data: datosImpuesto,
      success: function(response) {
          console.log('Registro exitoso:', response);
          // Aquí puedes agregar cualquier lógica adicional post-registro exitoso
      },
      error: function(err) {
          console.error('Error al registrar el impuesto:', err);
      }
  });
}

function animaUsuario(dato) {
  var img = document.getElementById("imgUsu" + dato);
  var h6Elements = document.querySelectorAll("#flush-collapse" + dato + " h6");
  var spanElements = document.querySelectorAll(
    "#flush-collapse" + dato + " span"
  );

  // Aplicar animación a la imagen
  img.classList.add("animate__animated", "animate__bounce");

  // Ocultar inicialmente los elementos h6 y span
  // h6Elements.forEach(element => element.style.opacity = 0);
  spanElements.forEach((element) => (element.style.opacity = 0));

  // Aplicar animación y mostrar los elementos h6
  // h6Elements.forEach((element, index) => {
  //   setTimeout(() => {
  //     element.style.opacity = 1;
  //     element.classList.add('animate__animated', 'animate__backInLeft');
  //   }, 100 * index);  // Aumenta el retraso para cada h6
  // });

  // Aplicar animación y mostrar los elementos span
  spanElements.forEach((element, index) => {
    setTimeout(() => {
      element.style.opacity = 1;
      element.classList.add("animate__animated", "animate__backInLeft");
    }, 100 * (index + h6Elements.length)); // Asegurarse de que los spans se animan después de los h6
  });
}
