<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Servicios</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
  

  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

  <!-- SweetAlert -->
  <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>



  <link rel="stylesheet" href="../css/estilos.css">

   <!-- Animate css -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>



  <script src="../js/app.js"></script>

  <link rel="icon" href="img/favicon.ico" type="image/x-icon">
  <link rel="shortcut icon" href="img/favicon.ico" type="image/x-icon">
</head>

<body class="bg-body-tertiary">

  <div class="container">
    <main>
      <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <div class="container-fluid">
          <a class="navbar-brand" href="#"> </a>
          <button class="navbar-toggler" type="button btn-sm" data-bs-toggle="collapse" data-bs-target="#navbarNavDropdown" aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
          </button>
          <div class="collapse navbar-collapse" id="navbarNavDropdown">
            <ul class="navbar-nav">
              <li class="nav-item">
                <a class="nav-link" data-bs-toggle="modal" data-bs-target="#modalAgregar">Agregar Impuesto</a>
              </li>
              <li class="nav-item">
                <a class="nav-link" data-bs-toggle="modal" data-bs-target="#modalAgregarUsuario">Agregar Usuario</a>
              </li>
              
            </ul>
          </div>
        </div>
      </nav>
      <div class="py-3 text-center">
        <h4 class=" mb-2" id="costoTotal">

        </h4>
        
      </div>


      <!-- Accordion detalles pagos -->
      <div class="accordion accordion-flush" id="accordionFlushExample">

      </div>
     

      <div class="py-5 text-center">
      <h4 class=" mb-3" id="costoTotal" style="color: brown;">Facturas</h4>
        <ul class="list-group mb-3">
          <div id="tabla"></div>
        </ul>
      </div>


    </main>
  </div>


  <!-- Modal Agregar-->
  <div class="modal fade" id="modalAgregar" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h1 class="modal-title fs-5" id="exampleModalLabel">Agregar impuesto</h1>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>

        <form name="formSubirPresupuestos" action="ajax/cargarImpuestos.php" id="formSubirImpuestos" enctype="multipart/form-data" method="POST">
          <div class="modal-body">
            <div class="mb-3">
              <label for="nombre_imp" class="form-label">Nombre Impuesto</label>
              <input type="text" class="form-control" name="nombreImp" id="nombre_imp">
            </div>

            <div class="mb-3">
              <label for="proveedor_imp" class="form-label">Proveedor:</label>
              <input type="text" class="form-control" name="proveedorImp" id="proveedor_imp">
            </div>

            <div class="mb-3">
              <label for="">Tipo impuesto:</label>
              <select type="text" class="form-control" name="tipoImp" id="tipo_imp" placeholder="Marca disco">
                <option value="AGUA">AGUA</option>
                <option value="GAS">GAS</option>
                <option value="IMPUESTO INMOBILIARIO">IMPUESTO INMOBILIARIO</option>
                <option value="TASAS MUNICIPALES">TASAS MUNICIPALES</option>
                <option value="INTERNET">INTERNET</option>
                <option value="LUZ">LUZ</option>
              </select>
            </div>

            <div class="mb-3">
              <label for="fecha_vencimiento_imp">Fecha vencimiento:</label>
              <input type="date" class="form-control" value="<?php echo date('Y-m-d'); ?>" name="fechaVencimientoImp" id="fecha_vencimiento_imp" required>
            </div>

            <div class="mb-3">
              <label for="costo_imp" class="form-label">Costo:</label>
              <input type="number" class="form-control" step="any" name="costoImp" id="costo_imp">
            </div>

            <div class="mb-3" >
              <h6>Quienes pagaran este impuesto?</h6>
              <div style=" justify-content: center; display: flex; flex-wrap:wrap;" id="listaUsu">

              </div>
            </div>

            <div class="form-group col-md-12">
              <label for="factura_imp" class="form-label">Subir Factura:</label>
              <input class="form-control" type="file" name="facturaImp" id="factura_imp">
            </div>



          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
            <button type="submit" class="btn btn-primary">Guardar</button>
          </div>
        </form>
      </div>
    </div>
  </div>


  <!-- Modal Agregar Usuario-->
  <div class="modal fade" id="modalAgregarUsuario" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h1 class="modal-title fs-5" id="exampleModalLabel">Agregar Usuario</h1>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>

        <form name="formSubirPresupuestos" action="ajax/cargarUsuario.php" id="formSubirUsuarios" enctype="multipart/form-data" method="POST">
          <div class="modal-body">
            <div class="mb-3">
              <label for="nombre_usu" class="form-label">Nombre Usuario</label>
              <input type="text" class="form-control" name="nombreUsu" id="nombre_usu">
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
            <button type="submit" class="btn btn-primary">Guardar</button>
          </div>
        </form>
      </div>
    </div>
  </div>



  <!-- Modal Editar Impuesto-->
  <div class="modal fade" id="modalEditar" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h1 class="modal-title fs-5" id="exampleModalLabel">Agregar impuesto</h1>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>

        <form name="formSubirPresupuestos" action="ajax/editar.php" id="formEditarImpuestos" enctype="multipart/form-data" method="POST">
          <div class="modal-body">
            <input type="text" hidden="true" name="idImpEdit" id="id_imp_edit" value="">
            <input type="text" hidden="true" name="tiene_acta" id="tieneActa" value="">
            <div class="mb-3">
              <label for="nombre_imp_edit" class="form-label">Nombre Impuesto</label>
              <input type="text" class="form-control" name="nombreImpEdit" id="nombre_imp_edit">
            </div>

            <div class="mb-3">
              <label for="proveedor_imp_edit" class="form-label">Proveedor:</label>
              <input type="text" class="form-control" name="proveedorImpEdit" id="proveedor_imp_edit">
            </div>

            <div class="mb-3">
              <label for="">Tipo impuesto:</label>
              <select type="text" class="form-control" name="tipoImpEdit" id="tipo_imp_edit" placeholder="Marca disco">
                <option value="AGUA">AGUA</option>
                <option value="GAS">GAS</option>
                <option value="IMPUESTO INMOBILIARIO">IMPUESTO INMOBILIARIO</option>
                <option value="TASAS MUNICIPALES">TASAS MUNICIPALES</option>
                <option value="INTERNET">INTERNET</option>
                <option value="LUZ">LUZ</option>
              </select>
            </div>

            <div class="mb-3">
              <label for="fecha_vencimiento_imp_edit">Fecha vencimiento:</label>
              <input type="date" class="form-control" value="<?php echo date('Y-m-d'); ?>" name="fechaVencimientoImpEdit" id="fecha_vencimiento_imp_edit" required>
            </div>

            <div class="mb-3">
              <label for="costo_imp_edit" class="form-label">Costo:</label>
              <input type="number" class="form-control" step="any" name="costoImpEdit" id="costo_imp_edit">
            </div>

            <div class="mt-3">
              <div class="" id="factMsg"></div>
              <label for="factura_imp_edit" class="form-label">Subir Acta de Entrega:</label>
              <input type="file" class="form-control" name="facturaImpEdit" id="factura_imp_edit" placeholder="">
            </div>



          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
            <button type="submit" class="btn btn-primary">Submit</button>
          </div>
        </form>
      </div>
    </div>
  </div>




  <div id="menuContextual" style="display: none; position: absolute;" class="menu-contextual">
    <ul>
      <li id="editar">Editar</li>
      <li id="eliminar">Eliminar</li>
      <li id="pagado">Pagado</li>
      <li id="registrar">Registrar</li>
    </ul>
  </div>
</body>

</html>

<script>
  document.addEventListener('DOMContentLoaded', () => {

    trearImpuestos();
    traerUsuarios();
    botonesUsuarios();
  })

  function botonesUsuarios(){
   const action = "buscar";

  $.ajax({
    url: "../ajax/traerUsuariosBotones.php",
    type: "POST",
    data: { action: action },
    beforeSend: function () {},
    success: function (response) {
      // console.log(response);
      if (response.status && response.status === "notData") {
        // Manejo cuando no hay datos
      } else {
        var js = JSON.parse(response);
        var cards1 = "";

          for (var i = 0; i < js.length; i++) {
            
            // js[i].nombre_imp
            // Crear checkbox para cada usuario al ingresar un nuevo impuesto
            cards1 +=
              '<div class="mr-3"><input type="checkbox" class="btn-check" id="btn-check-' +
              js[i].id_usuario +
              '" data-id="' +
              js[i].id_usuario +
              '" autocomplete="off">' +
              '<label class="btn btn-outline-success" for="btn-check-' +
              js[i].id_usuario +
              '">' +
              js[i].nombre_usuario +
              "</label></div>";

          }

        }
         // Actualizar el HTML de la página
         $("#listaUsu").html(cards1);

    },
    error: function (error) {
      console.error("Error al enviar la solicitud de pago:", error);
      alert("Hubo un problema al enviar el pago. Intenta nuevamente.");
    }
  });
}
</script>