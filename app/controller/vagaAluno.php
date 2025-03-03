<?php
// if(!isset($_SESSION)) 
// { 
//     session_start(); 
// } 

require_once __DIR__ . '/../model/vagasModel.php';
require_once __DIR__ . '/../controller/FormacaoController.php';

class VagasController{
  private $vagaModel;

  public function __construct() {
    $this->vagaModel = new Vagas();
}


public function listarVagas(){
    $formacao = new FormacaoController();
    $list = $formacao->getFormacao(); 
    if($list['matricula_valida'] !== 1){
        return '<div class="alert alert-danger" role="alert">
                    Termine o cadastro de sua <b>formação</b> e aguarde a validação de sua declaração de matrícula para ver as vagas de estágio!
                </div>';
    }  
    $idCurso = $list['curso'];
    $html = '';
    $vagas = $this->vagaModel->getAllVagas($idCurso, $_SESSION['id']);
    foreach ($vagas as $value) {
        // Chama o método listarPergunta para pegar as perguntas da vaga atual
        $perguntasHtml = $this->listarPergunta($value['idVaga']);
        $html .= '
             <div class="col-xl-6">
                <div class="card">
                    <div class="card-header">
                    <h3>' . $value['nome'] . '</h3>
                    </div>
                    <div class="card-body">
                            <div class="mb-3">
                                <img src="../app/public/img/empresa.png" width="40px" heigth="40px">
                                ' . $value['nomeEmpresa'] . '
                            </div>
                        <div class="infoVaga" style="display: flex; flex-direction: row; justify-content: space-between; flex-wrap: wrap;">
                            <div class="mb-3">
                                <img src="../app/public/img/cifrao.png" width="40px" heigth="40px">
                                R$ ' . $value['salario'] . '
                            </div>
                            <div class="mb-3">
                                <img src="../app/public/img/pin.png" width="40px" heigth="40px">
                                ' . $value['nomeCidade'] . ' - ' . $value['nomeEstado'] . '
                            </div>
                            <div class="mb-3">
                                <img src="../app/public/img/pasta.png" width="40px" heigth="40px">
                            </div>
                            <input type="hidden" value="' . $value['idVaga'] . '" id="idVaga">
                        </div>
                        <br>
                        <div class="row g-3">
                            <div>
                                <button class="btn btn-secondary col-md-12" data-bs-toggle="collapse" href="#verMais' . $value['idVaga'] . '" role="button" aria-expanded="false" aria-controls="verMais" >Ver mais</button>
                            </div>
                            
                        </div>
                        <div id="verMais' . $value['idVaga'] . '" class="collapse">
                            <div class="descricao">
                                <h5>Descricao</h5>
                                <p>' . $value['descricao'] . '</p>
                            </div>
                            <div class="requisitos">
                                <h5>Requisitos</h5>
                                <p>' . $value['requisitos'] . '</p>
                          
                                
                                
                                <button class="btn btn-info col-md-12" data-bs-toggle="modal" data-bs-target="#perguntaModal' . $value['idVaga'] . '">Candidatar-se</button>
                                
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Modal de Perguntas -->
            <div class="modal fade" id="perguntaModal' . $value['idVaga'] . '" tabindex="-1" aria-labelledby="perguntaModalLabel' . $value['idVaga'] . '" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="perguntaModalLabel' . $value['idVaga'] . ' ' . $value['nome'] . '</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            ' . $perguntasHtml . '
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                            <button class="btn btn-success " onclick="candidatar(' . $value['id_empresa'] . ', ' . $value['idVaga'] . ')">Candidatar</button>
                        </div>
                    </div>
                </div>
            </div>
        ';
    }

    return $html ? $html : '<div class="alert alert-info" role="alert"> Ainda não existem vagas com o seu perfil! </div>';
}
public function listarPergunta($idVaga){
    $html = '';
    $pergunta = $this->vagaModel->listarPerguntasPorVaga($idVaga);
    
    if($pergunta) {
        $html .= '
        <style>
            * {
                margin: 0;
                padding: 0;
            }
            .rate {
                float: left;
                height: 46px;
                padding: 0 10px;
            }
            .rate:not(:checked) > input {
                position: absolute;
                top: -9999px;
            }
            .rate:not(:checked) > label {
                float: right;
                width: 1em;
                overflow: hidden;
                white-space: nowrap;
                cursor: pointer;
                font-size: 30px;
                color: #ccc;
            }
            .rate:not(:checked) > label:before {
                content: "★ ";
            }
            .rate > input:checked ~ label {
                color: #ffc700;
            }
            .rate:not(:checked) > label:hover,
            .rate:not(:checked) > label:hover ~ label {
                color: #deb217;
            }
            .rate > input:checked + label:hover,
            .rate > input:checked + label:hover ~ label,
            .rate > input:checked ~ label:hover,
            .rate > input:checked ~ label:hover ~ label,
            .rate > label:hover ~ input:checked ~ label {
                color: #c59b08;
            }
        </style>';
        foreach($pergunta as $value) {
            // Define identificadores únicos para cada pergunta
            $idPergunta = $value['id'];
            $html .= '
                <div class="pergunta">
                    <h4>' . $value['pergunta'] . '</h4>
        
                    <!-- Avaliação por estrelas com identificadores únicos -->
                    <div class="rate">
                        <input type="radio" id="star5_' . $idPergunta . '" name="rate_' . $idPergunta . '" value="5" onclick="enviarResposta(5)" />
                        <label for="star5_' . $idPergunta . '" title="5 stars">5 stars</label>
                        <input type="radio" id="star4_' . $idPergunta . '" name="rate_' . $idPergunta . '" value="4" onclick="enviarResposta(4)" />
                        <label for="star4_' . $idPergunta . '" title="4 stars">4 stars</label>
                        <input type="radio" id="star3_' . $idPergunta . '" name="rate_' . $idPergunta . '" value="3" onclick="enviarResposta(3)" />
                        <label for="star3_' . $idPergunta . '" title="3 stars">3 stars</label>
                        <input type="radio" id="star2_' . $idPergunta . '" name="rate_' . $idPergunta . '" value="2" onclick="enviarResposta(2)" />
                        <label for="star2_' . $idPergunta . '" title="2 stars">2 stars</label>
                        <input type="radio" id="star1_' . $idPergunta . '" name="rate_' . $idPergunta . '" value="1" onclick="enviarResposta(1)" />
                        <label for="star1_' . $idPergunta . '" title="1 star">1 star</label>
                    </div>
                </div>
                <br>
                <br>
                <br>
                ';
        }
        return $html;
    }

    return $html ? $html : '<div class="alert alert-info" role="alert">Sem perguntas</div>';
}


public function candidatar($idVaga, $idEmpresa){ 
    if($this->vagaModel->candidatar($idVaga, $_SESSION['id'],$idEmpresa)){
        $retorno = array('success' => true, 'tittle' => 'Sucesso', 'msg' => 'Candidatado com sucesso', 'icon' => 'success');
        echo json_encode($retorno);
        return;
    }
    $retorno = array('success' => false, 'tittle' => 'Erro', 'msg' => 'Não foi possivel se candidatar', 'icon' => 'error');
    echo json_encode($retorno);
    return;
}

public function enviarResposta( $resposta) { 
    if($this->vagaModel->salvarRespostas( $resposta,$_SESSION['id'])){
        // $retorno = array('success' => true, 'tittle' => 'Sucesso', 'msg' => 'Respondido com sucesso', 'icon' => 'success');
        // echo json_encode($retorno);
        return;
    }
    else {
        # code...
        $retorno = array('success' => false, 'tittle' => 'Erro', 'msg' => 'Não foi possivel responder', 'icon' => 'error');
        echo json_encode($retorno);
        return;
    }
}
}