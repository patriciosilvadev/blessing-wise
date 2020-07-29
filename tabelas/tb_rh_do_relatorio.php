<?php
session_start();


require_once('../validar/class/db.class.php');
$objDb = new db();
$link = $objDb->conecta_mysql();

$id = $_POST['query_extras'];
$token = md5($id);
$_SESSION['token_form_gerencia_extra'] = $token;

$sql = "SELECT a.motivo as 'motivo', 
a.id as 'id_extra', 
a.hora_ent as 'hora_ent', 
a.hora_sai as 'hora_sai', 
a.data as 'data', 
b.cpf as 'id_funcionario', 
b.nome as 'funcionario', 
g.funcao as 'funcao_colab', 
g.id as 'id_funcao_colab',
c.refeicao as refeicao, 
c.nome as 'unidade', 
c.id as 'id_unidade', 
d.funcao as 'funcao', 
d.id as 'id_funcao', 
e.escala as 'escala', 
e.id as 'id_escala', 
e.tipo as 'tipo', 
a.status as 'status', 
f.valor as 'valor_inicial', 
h.nome as 'substituido', 
h.cpf as 'id_substituido', 
b.funcao as 'funcaoGrandeza', 
a.obs_extra as obs,
u.nome as cadastrado,
a.dt_cad as dt_cad,
concat('R$ ', format(a.valor_pagar, 2)) as valor
FROM `extras` a 
INNER JOIN `cad_funcionario` b 
on a.nome = b.cpf
INNER JOIN `cad_unidade` c 
on a.unidade_extra = c.id
INNER JOIN `cad_funcao` d 
on a.funcao_extra = d.id
INNER JOIN `cad_escala` e 
on a.escala_extra = e.id
INNER JOIN `valor_extras` f 
on a.funcao_extra = f.id_funcao
INNER JOIN `cad_funcao` g 
on b.funcao = g.id
INNER JOIN `usuarios` u
on a.cadastrado = u.id
LEFT JOIN `cad_funcionario` h 
on a.substituido = h.cpf
where a.id = '$id'";
$sql = mysqli_query($link, $sql);
$infoextras = mysqli_fetch_array($sql);

$sql = "SELECT c.funcao as grandezaFuncao, v.grandeza as gramdezaExtra, v.valor as valor
FROM `valor_extras` v 
inner JOIN cad_funcionario c
on v.id_funcao = c.funcao 
where c.funcao = " . $infoextras['funcaoGrandeza'];
$sql = mysqli_query($link, $sql);
$grandeza = mysqli_fetch_array($sql);

$sql = "SELECT c.noturno as noturno FROM `extras` e INNER JOIN cad_escala c ON e.escala_extra = c.id where e.id = $id ";
$sql = mysqli_query($link, $sql);
$noturno = mysqli_fetch_array($sql);

if ($noturno['noturno'] == '1') {
  $noturno = 10;
} else {
  $noturno = 0;
}

if ($grandeza['valor'] != '') {
  $valorExtra = $grandeza['valor'];
} else {
  $valorExtra = 0;
}


if ($infoextras['refeicao'] == '1') {
  $refeicao = 10;
} else {
  $refeicao = 0;
}



//--Query Funcionario
$sql = "SELECT * FROM cad_funcionario";
$query_funcionario = mysqli_query($link, $sql);

//--Query Funcionario substituído
$sql = "SELECT * from cad_funcionario order by `nome`";
$query_substituido = mysqli_query($link, $sql);

//--Query Função do Extra
$sql = "SELECT * from cad_funcao";
$query_funcaoextra = mysqli_query($link, $sql);

//--Query Função do Extra
$sql = "SELECT * from cad_funcao WHERE id in(1,20,33,37,38,40,44,57)";
$query_funcaoextra1 = mysqli_query($link, $sql);

//--Query Unidade do Extra
$sql = "SELECT * from cad_unidade where tipo != 6";
$query_unidadeextra = mysqli_query($link, $sql);

//--Query Unidade do Extra
$sql = "SELECT * from cad_unidade where tipo != 6";
$query_unidadeextra1 = mysqli_query($link, $sql);

//--Query Escala do Extra
$sql = "SELECT id, escala, tipo from cad_escala";
$query_escalaoextra = mysqli_query($link, $sql);

?>



<!-- jquery-validation -->
<script src="plugins/jquery-validation/jquery.validate.min.js"></script>
<script src="plugins/jquery-validation/additional-methods.min.js"></script>

     <!-- Toastr -->
     <script src="plugins/toastr/toastr.min.js"></script>
 <!-- Toastr -->
 <link rel="stylesheet" href="plugins/toastr/toastr.min.css">
<script>
  window.alert = function() {};
</script>
<div class="container">





  <!-- area de campos do form -->
  <div class="row">
    <div class="callout callout-info col-12">
      <h5><strong>ID:</strong> <?php echo $id; ?></h5>
      <p>
        <strong>CADASTRADO POR: </strong><?php echo$infoextras['cadastrado'];?><br>
        <strong>DATA: </strong> <?php echo date('d/m/Y',strtotime($infoextras["dt_cad"]));?><br>
        <strong>OBSERVAÇÃO DO COLABORADOR:</strong> <?php echo$infoextras['obs'];?><br>
      </p>
    </div>
  </div>
</div>
<div class="callout callout-danger col-12" style="background: #f2f2f2;">
  <div class="container text-sm">

    <!-- <form id="form"> -->
    <div class="row">
      <div class="form-group col-md-6 ">
        <label for="funcionario">Nome</label>
        <select name="funcionario" class="select21 " id="funcionario" disabled>
          <option value="<?php echo $infoextras['id_funcionario']; ?>"><?php echo $infoextras['funcionario']; ?></option>
        </select>
      </div>


      <div class="form-group col-md-3">
        <label for="funcao_colaborador"><strong>Função Colaborador</strong></label><br />
        <select name="funcao_colaborador" class="select21" tabindex="3" id="funcao_colaborador" disabled>
          <option value="<?php echo $infoextras['id_funcao_colab']; ?>"><?php echo $infoextras['funcao_colab']; ?></option>
        </select>
      </div>

      <div class="form-group col-md-3">
        <label for="unidade"><strong>Unidade do Extra</strong></label><br />
        <select name="unidade" class="select21" tabindex="3" id="unidade" disabled>
          <option value="<?php echo $infoextras['id_unidade']; ?>"><?php echo $infoextras['unidade']; ?></option>
        </select>
      </div>
      <div class="form-group col-md-3">
        <label for="funcao_extra"><strong>Função do Extra</strong></label><br />
        <select name="funcao_extra" class="select21" tabindex="3" id="funcao_extra" disabled>
          <option value="<?php echo$infoextras['id_funcao'];?>"><?php echo$infoextras['funcao'];?></option>
        </select>
      </div>
      <div class="form-group col-md-2">
        <label for="escala"><strong>Escala do Extra</strong></label><br />
        <select name="escala" class="select21" tabindex="3" id="escala" disabled>
          <option value="<?php echo $infoextras['id_escala']; ?>"><?php echo $infoextras['escala'] . " - " . $infoextras['tipo']; ?></option>
        </select>
      </div>
      <div class="form-group col-md-2">
        <label for="data"><strong>Data do Extra</strong></label><br />
        <input type="date" name="data" id="data" class="form-control form-control-sm" value="<?php echo $infoextras['data'] ?>" disabled>
      </div>
      <div class="form-group col-md-2">
        <label for="hora_entrada"><strong>Hora Entrada</strong></label><br />
        <input type="time" name="hora_entrada" id="hora_entrada" class="form-control form-control-sm" value="<?php echo $infoextras['hora_ent'] ?>" disabled>
      </div>
      <div class="form-group col-md-2">
        <label for="hora_saida"><strong>Hora Saída</strong></label><br />
        <input type="time" name="hora_saida" id="hora_saida" class="form-control form-control-sm" value="<?php echo $infoextras['hora_sai'] ?>" disabled>
      </div>
      <div class="form-group col-md-3">
        <label for="motivo"><strong>Motivo</strong></label><br />
        <select name="motivo" class="select21" tabindex="3" id="motivo" disabled>
          <option value="<?php echo $infoextras['motivo']; ?>"><?php echo $infoextras['motivo']; ?></option>
        </select>
      </div>
      <div class="form-group col-md-9">
        <label for="substituto"><strong>Funcionário Substituído</strong></label><br />
        <select id="substituto" class="select21" tabindex="3" name="substituto" disabled>
          <option value="<?php echo $infoextras['id_substituido']; ?>"><?php echo $infoextras['substituido']; ?></option>
        </select>
      </div>
    </div>

    <div class="row">
      <div class="form-group col-md-2">
        <label for="valor_inicial"><strong>Valor Inicial</strong></label>
        <input type="number" class="form-control form-control-sm campo" id="valor_inicial" value="<?php echo  $valorExtra; ?>" disabled>
      </div>
      <div class="form-group col-md-2">
        <label for="refeicao"><strong>Refeição</strong></label>
        <input type="number" class="form-control form-control-sm campo" id="refeicao" value="<?php echo $refeicao; ?>" disabled>
      </div>
      <div class="form-group col-md-2">
        <label for="noturno"><strong>Noturno</strong></label>
        <input type="number" class="form-control form-control-sm campo" id="noturno" value="<?php echo $noturno; ?>" disabled>
      </div>
      <div class="form-group col-md-3">
        <label for="valor_pagar"><strong>Valor a Pagar</strong></label>
        <input type="text" class="form-control form-control-sm text-danger fix-rounded-right" name="valor_pagar" id="valor_pagar" value="<?php echo $infoextras['valor']; ?>" step="any" disabled>
      </div>
    </div>
  </div>
</div>


  <!--   </form> -->



  

  <!-- jquery-validation -->
  <script src="plugins/jquery-validation/jquery.validate.min.js"></script>
  <script src="plugins/jquery-validation/additional-methods.min.js"></script>

  <script src="plugins/select2/js/select2.full.min.js"></script>
  <script>
     const Toast = Swal.mixin({
        toast: true,
        position: 'top',
        showConfirmButton: false,
        timerProgressBar: true,
        timer: 6000
        });

   

    $('.select21').select2();

    $("#negar").click(function() {
      $("#modal-cancelar").modal("show");
    })

    $("#modal_cancelar_extra").click(function() {
      $("#modal-cancelar").modal("hide");
    })

    $("#cancelar_extra").click(function() {
      var form = $("#form_nega").serialize();
      $("#cancelar_extra").hide();
      $("#btn-salvando1").show();
      $.ajax({
          url: "validar/validar.php?id=19",
          type: "POST",
          beforeSend: function() {

          },
          data: form,
          success: function(data) {
            $("#modal-cancelar").modal("hide");
            $("#exampleModal").modal("hide");
            Toast.fire({
                icon: 'success',
                title: 'Extra cancelado com sucesso!'
            })
              var form = $("#form12").serialize();
                $.ajax({
                url:'tabelas/ul.extras_para_aprovacao.php',
                type: 'POST',
                beforeSend: function(){
                  
                },
                complete: function(){
                    
                },
                data: form,
                success: function(data){
                    console.log(data);
                    $("#tabela").html(data);
                }
              })
          }
        })
    })

    $('#form1').validate({
      rules: {
        nome: {
          required: true,
        },

      },
      messages: {
        nome: {
          required: "Obrigatório",
        },
      },
      errorElement: 'span',
      errorPlacement: function(error, element) {
        error.addClass('invalid-feedback');
        element.closest('.form-group').append(error);
      },
      highlight: function(element, errorClass, validClass) {
        $(element).addClass('is-invalid');
      },
      unhighlight: function(element, errorClass, validClass) {
        $(element).removeClass('is-invalid');
      },
      submitHandler: function(form) {
        var form = $(form).serialize();
        acaoUsuario();
        $.ajax({
          url: "validar/validar.php?id=19",
          type: "POST",
          beforeSend: function() {

          },
          data: form,
          success: function(data) {
           console.log(data);
            
            if(data == "editado"){
              $(".espera").show();
              $("#btn-salvando").hide();
              Toast.fire({
                icon: 'success',
                title: 'Registros editados com sucesso!'
              })
              atualizarTabela();
            }else
            if(data == "erro_sem_valor"){
              Toast.fire({
                icon: 'error',
                title: 'Extra aprovado sem valor! <br> Por favor, informar o valor!'
              })
              $(".espera").show();
              $("#btn-salvando").hide();
            }else 
            if( data == "liberado" ){
              $("#exampleModal").modal("hide");
              Toast.fire({
                icon: 'success',
                title: 'Extra atualizado! <br> Aprovado com sucesso!'
              })
              $(".espera").show();
              $("#btn-salvando").hide();
              atualizarTabela();
            }
              
             
          }
        })
      }
    });

    
    function acaoUsuario(){
      $(".espera").hide();
      $("#btn-salvando").show();
    }

    function atualizarTabela(){
      var form = $("#form12").serialize();
              $("#tabela").html("Carregando");
                $.ajax({
                url:'tabelas/ul.extras_para_aprovacao.php',
                type: 'POST',
                beforeSend: function(){
                  
                },
                complete: function(){
                    
                },
                data: form,
                success: function(data){
                    $("#tabela").html(data);
                }
              })
            
    }
  </script>
<!-- Toastr -->
<script src="plugins/toastr/toastr.min.js"></script>
