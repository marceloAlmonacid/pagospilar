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
            '<li class="list-group-item d-flex justify-content-between lh-sm"> <div> <h6 class="my-0">' +
            js[i].nombre_imp +
            '</h6> <h6 class="my-0" style="display: none;">' +
            js[i].id_imp +
            '</h6> <small class="text-muted">' +
            js[i].proveedor_imp +
            '</small> </div> <div class="d-flex align-items-center"> ' +
            ruta +
            ' <span class="text-muted">$' +
            js[i].costo_imp +
            "</span> </div> </li>";

          sumaCosto += parseFloat(js[i].costo_imp);

        }
        cards1 +=
          '<span class="text-primary">Pago total mes: $' +
          sumaCosto.toFixed(2).toLocaleString();
        +"</span>";

        cards2 +=
          '<li class="list-group-item d-flex justify-content-between lh-sm" style="border: none;">  <h6 class="my-1"> </h6>  <span id="cost1" class="text-bold ">Total: $' +
          sumaCostoImp +
          '</span> </li></ul> ';

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

          $("#pagado").on("click", function () {
            alert("Pagado");
            // Aquí va tu lógica para marcar como pagado
          });

          // Evita la propagación del evento (opcional, dependiendo de tu caso de uso)
          return false;
        });

        $("#costoTotal").html(cards1);
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
    async: true,
    data: {
      action: action,
    },
    beforeSend: function () {},

    success: function (response) {
      if (response.status && response.status === "notData") {
      } else {
        var js = JSON.parse(response);
        var cards = "";
        var cards1 = "";
        var sumaCostoImp = 0;

        for (var i = 0; i < js.length; i++) {
            sumaCostoImp += parseFloat(js[i].monto);
          cards +=
            '<div class="accordion-item"> <h2 class="accordion-header"> <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapse' +
            js[i].id_usuario +
            '" aria-expanded="false" aria-controls="flush-collapse' +
            js[i].id_usuario +
            '">' +
            js[i].nombre_usuario +
            '</button> </h2> <div id="flush-collapse' +
            js[i].id_usuario +
            '" class="accordion-collapse collapse" data-bs-parent="#accordionFlushExample"> <div class="accordion-body"><ul class="list-group mb-3 col-lg-11" > <li class="list-group-item d-flex justify-content-between lh-sm">  <h6 class="my-1">' +js[i].nombre_imp +
            '</h6>  <span id="cost1" class="text-muted ">$' +js[i].monto +
            '</span> </li>   <li class="list-group-item d-flex justify-content-between lh-sm" style="border: none;">  <h6 class="my-1"> </h6>  <span id="cost1" class="text-bold ">Total: $' +
            sumaCostoImp +
            '</span> </li></ul>     <div id="imp' +
            js[i].id_usuario +
            '"> </div> </div> </div> </div>';

          cards1 +=
            '<input type="checkbox" class="btn-check" id="btn-check-' +
            js[i].id_usuario +
            '" data-id="' +
            js[i].id_usuario +
            '" autocomplete="off">' +
            '<label class="btn btn-outline-success" for="btn-check-' +
            js[i].id_usuario +
            '">' +
            js[i].nombre_usuario +
            "</label>";
        }

        $("#accordionFlushExample").html(cards);
        $("#listaUsu").html(cards1);
      }
    },
    error: function (error) {
      console.log(error);
    },
  });
}

function traerMontos(dato){
    // alert(dato);

    $("#imp"+dato).html(dato);
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
      e.preventDefault();  // Detener la ejecución normal del formulario

      var formData = new FormData(this);  // Crear un objeto FormData con los datos del formulario

      // Recoger todos los checkboxes marcados de los usuarios y agregarlos al formData
      $(".btn-check:checked").each(function() {
        formData.append('usuariosSeleccionados[]', $(this).data('id'));
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
        //   alert("Impuesto y asignaciones guardadas con éxito!");
        //   location.reload(); // Opcional: recargar la página para ver los cambios
            console.log(response);
        },
        error: function (jqXHR, textStatus, errorThrown) {
          alert("Error al guardar la información");
        }
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
    //   var esto = formData.get("id_Cela");

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
          //   trearImpuestos();
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
