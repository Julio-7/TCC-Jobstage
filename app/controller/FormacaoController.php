<?php
// if(!isset($_SESSION)) 
// { 
//     session_start(); 
// } 
require_once __DIR__ . '/../model/FormacaoModel.php';
require_once __DIR__ . '/../controller/matricula.php';

class FormacaoController{
    private $formacaoModel;
    private $matricula;
 

    public function __construct() {
       $this->formacaoModel = new FormacaoModel();
       $this->matricula = new matricula();
     
    }

                                                                          
    public function criarFormacao($curso, $instituicao, $nivel, $estado, $cidade, $cep, $fim, $arquivo, $idAluno) {
        $nomeArquivo = $this->matricula->inserirMatricula($arquivo);
        
        if ($this->formacaoModel->criarFormacao($curso, $instituicao, $nivel, $estado, $cidade, $cep, $fim, $nomeArquivo, $idAluno)) {
            $retorno = array('tittle' => 'Sucesso', 'msg' => 'Formação cadastrada com sucesso!', 'icon' => 'success');
            echo json_encode($retorno);
            return $retorno;
        }    

        $retorno = array('tittle' => 'erro', 'msg' => 'erro', 'icon' => 'danger');
        echo json_encode($retorno);
        return $retorno;
    }


    public function editarFormacao(int $idAluno, int $idFormacao, string $curso, string $instituicao, string $nivel, $inicio, $fim, string $status, $arquivo = null): array {        
        // se existir arquivo deleta o arquivo cadastrado do usuario para substituir
        if ($arquivo){
            $nomeArquivo = $this->formacaoModel->getMatricula($idAluno, $idFormacao);
            $nomeNovoArquivo = $this->matricula->atualizarMatricula($nomeArquivo, $arquivo);
        
            if($this->formacaoModel->editarFormacao($idAluno, $idFormacao,  $curso,  $instituicao,  $nivel, $inicio, $fim, $status, $nomeNovoArquivo)){
                $retorno = array('success' => true, 'tittle' => 'Sucesso!', 'msg' => 'Formação atualizada', 'icon' => 'success');
                echo json_encode($retorno);
                return $retorno;
            }
        }

        $this->formacaoModel->editarFormacao($idAluno ,$idFormacao,  $curso,  $instituicao,  $nivel, $inicio, $fim, $status);
        $retorno = array('success' => true, 'tittle' => 'Sucesso!', 'msg' => 'Formação atualizada', 'icon' => 'success');
        echo json_encode($retorno);
        return $retorno;
    }    


    public function excluirFormacao(int $idFormacao, int $idAluno) {
        $nomeMatricula = $this->formacaoModel->getMatricula($idAluno, $idFormacao);

        $resultDeleteFormacao = $this->formacaoModel->excluirFormacao($idFormacao, $idAluno);
        
        if($resultDeleteFormacao){
            $this->matricula->excluirMatricula($nomeMatricula);

            $retorno = array('success' => true, 'tittle' => 'Sucesso!', 'msg' => 'Formação excluída!', 'icon' => 'success');
            echo json_encode($retorno);
            return $retorno;
        }

        $retorno = array('success' => false, 'tittle' => 'Erro!', 'msg' => 'Não foi possível excluir a formação!', 'icon' => 'error');
        echo json_encode($retorno);
        return $retorno;

    }


    public function listarFormacao(): string {
        $idAluno = $_SESSION['id'];
        $html = '';
        $tabelaFormacao = $this->formacaoModel->getAllformacao($idAluno);
       
        if($tabelaFormacao){
            $html .= '
            <table class="table table-striped table-hover table-bordered">
                <thead>
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">Instituição</th>
                        <th scope="col">Nível</th>
                        <th scope="col">Status</th>
                        <th scope="col">Ações</th>
                    </tr>
                </thead>
                <tbody class="table-group-divider">';
                foreach($tabelaFormacao as $value) {
                    if($value['matricula_valida'] == 0){
                        $png = 'alerta.png';
                    }elseif($value['matricula_valida'] == 1){
                        $png = 'OK.png';
                    }else{
                        $png = 'X.png';
                    } 
                    $curso = $value['curso'] == 'null'? 'Ensino médio' : $value['curso'];
                    $html .= '
                    <tr>
                        <td><img src="../app/public/img/'.$png.'" width="30px" height="30px"></td>
                        <td>' . $value['nome'] . '</td>
                        <td>Médio</td>
                        <td>' . $value['status'] . '</td>
                        <td>
                            <button class="btn btn-primary" id="edit-' . $value['id_formacao'] . '" value="' . $value['id_formacao'] . '">
                                Editar
                            </button>
                            <button class="btn btn-danger" value="' . $value['id_formacao'] . '" onclick="excluirFormacao(' . $value['id_formacao'] . ')">
                                Excluir
                            </button>
                        </td>
                    </tr>';
                }
                
                
                     $html .='
                </tbody>
            </table>';
        };
        return $html ? $html : '<div class="alert alert-danger" role="alert">Não foram encontradas formações cadastradas!</div>';
    }

    
    public function getAllFormacao(int $id, int $idAluno): array{
        return $this->formacaoModel->getAllformacao($idAluno, $id );
    }

    public function getFormacao(){
        return $this->formacaoModel->getFormacao($_SESSION['id']);
    }
}

