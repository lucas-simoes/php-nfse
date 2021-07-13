<?php

namespace NFePHP\NFSe\Models\IPM;

use stdClass;
use NFePHP\Common\Certificate;
use NFePHP\NFSe\Common\DateTime;
use NFePHP\NFSe\Common\Tools as ToolsBase;

/**
 * Classe para a comunicação com os webservices
 * conforme o modelo IPM
 * 
 *
 * @category  NFePHP
 * @package   NFePHP\NFSe\Models\IPM
 * @copyright NFePHP Copyright (c) 2016
 * @license   http://www.gnu.org/licenses/lgpl.txt LGPLv3+
 * @license   https://opensource.org/licenses/MIT MIT
 * @license   http://www.gnu.org/licenses/gpl.txt GPLv3+
 * @author    Maykon da S. de Siqueira <maykon at multilig dot com dot br>
 * @link      http://github.com/nfephp-org/sped-nfse for the canonical source repository
 */

class Tools extends ToolsBase
{
    protected $url = "";

    /**
     * Constructor
     * @param stdClass $config
     * @param \NFePHP\Common\Certificate $certificate
     */
    public function __construct(stdClass $config, Certificate $certificate = null)
    {
        $this->config = $config;

        //Se o model já possuia  versão não tem necessidade de pegar da configuração
        if (empty($this->versao)) {
            $this->versao = $config->versao;
        }

        $this->remetenteCNPJCPF = $config->cpf;
        $this->remetenteRazao = $config->razaosocial;
        $this->remetenteIM = $config->im;
        $this->remetenteTipoDoc = 1;
        if ($config->cnpj != '') {
            $this->remetenteCNPJCPF = $config->cnpj;
            $this->remetenteTipoDoc = 2;
        }
        $this->certificate = $certificate;
        $this->timezone = DateTime::tzdBR($config->siglaUF);


        if (empty($this->versao)) {
            throw new \LogicException('Informe a versão do modelo.');
        }

        $this->setUrlIPM();
    }

    protected function setUrlIPM()
    {
        $url = "http://sync%s.nfs-e.net/datacenter/include/nfw/importa_nfw/nfw_import_upload.php";

        #http://www.fazenda.mg.gov.br/governo/assuntos_municipais/codigomunicipio/codmunicoutest_(rs|pr|sc).html
        $subUrl = '';
        #boa esperanca do iguacu [5471] / cascavel [7493] / Rio Negro [7823]
        if (in_array($this->config->cod_tom_municipio, [5471, 7493, 7823])) {
            $subUrl = '-pr';
        }

        #barra bonita [0894] / gravatal [8121] / paraiso [5747] / santa rosa do sul [8307] / seara [8345]
        if (in_array($this->config->cod_tom_municipio, ['0894', 8121, 5747, 8307, 8345])) {
            $subUrl = '-sc';
        }

        #Campo Novo [8579] / Esperança do Sul [0980] / Novo Hamburgo [8771] / Palmeira das Missões [8777] / Rolante [8823] / São João do Polêsine [5791]
        if (in_array($this->config->cod_tom_municipio, [8579, '0980', 8771, 8777, 8823, 5791])) {
            $subUrl = '-rs';
        }

        $this->url = sprintf($url, $subUrl);
    }

    /**
     * Emitir nota de servico
     * @param Rps $rps
     * @return string
     */
    public function gerarNota($rps)
    {
        $class = "NFePHP\\NFSe\\Models\\IPM\\Factories\\v{$this->versao}\\GerarNota";
        $this->method = "gerarNota";

        $this->url .= "?eletron=1";
        $fact       = new $class($this->certificate);
        $message    = $fact->render($rps, $this->config);
        return $this->sendRequest('', $message);
    }

    /**
     * Cancelar um nota de servico
     * @param CancelarRps $rps
     * @return string
     */
    public function cancelarNota($rps)
    {
        $class = "NFePHP\\NFSe\\Models\\IPM\\Factories\\v{$this->versao}\\CancelarNota";
        $this->method = "cancelarNota";

        $this->url .= "?eletron=1";
        $fact       = new $class($this->certificate);
        $message    = $fact->render($rps, $this->config);
        return $this->sendRequest('', $message);
    }

    /**
     * Solicitar o cancelamento de nota de servico
     * Se informado pode ser utilizado por cancelamento por substituicao
     * @param CancelarRps $rps
     * @return string
     */
    public function solicitarCancelamentoNota($rps)
    {
        $class = "NFePHP\\NFSe\\Models\\IPM\\Factories\\v{$this->versao}\\SolicitarCancelamentoNota";
        $this->method = "solicitarCancelamentoNota";

        $this->url .= "?eletron=1";
        $fact       = new $class($this->certificate);
        $message    = $fact->render($rps, $this->config);
        return $this->sendRequest('', $message);
    }

    /**
     * Consultar pelo codigo de autenticidade da Nfse
     * @param string $codigoAutenticidade
     * @return string
     */
    public function consultarByCodigoAutentticidade($codigoAutenticidade)
    {
        $class = "NFePHP\\NFSe\\Models\\IPM\\Factories\\v{$this->versao}\\ConsultarCodigoAutenticidade";
        $this->method = "consultarByCodigoAutentticidade";

        $this->url .= "?formato_saida=2";
        $fact       = new $class($this->certificate);
        $message    = $fact->render($codigoAutenticidade);
        return $this->sendRequest('', $message);
    }


    /**
     * Consultar pelo codigo TOM do municipio numero e serie RPS
     * @param int $cidade
     * @param int $serie
     * @param int $numero
     * @return string
     */
    public function consultarByCidadeSerieNumeroRps($cidade, $serie, $numero)
    {
        $class = "NFePHP\\NFSe\\Models\\IPM\\Factories\\v{$this->versao}\\ConsultarCidadeSerieNumeroRps";
        $this->method = "consultarByCidadeSerieNumeroRps";

        $this->url .= "?formato_saida=2";
        $fact       = new $class($this->certificate);
        $message    = $fact->render($cidade, $serie, $numero);
        return $this->sendRequest('', $message);
    }

    /**
     * Consultar pelo cadastro economico do prestador com numero e serie da nfse
     * @param int $numero
     * @param int $serie
     * @return int $cadastro
     */
    public function consultarByConsultarNumeroSerieCadastro($numero, $serie, $cadastro)
    {
        $class = "NFePHP\\NFSe\\Models\\IPM\\Factories\\v{$this->versao}\\ConsultarNumeroSerieCadastro";
        $this->method = "consultarByConsultarNumeroSerieCadastro";

        $this->url .= "?formato_saida=2";
        $fact       = new $class($this->certificate);
        $message    = $fact->render($numero, $serie, $cadastro);
        return $this->sendRequest('', $message);
    }

    /**
     * Send request to webservice
     * @param string $message
     * @return string
     */
    protected function sendRequest($url, $message)
    {
        $this->xmlRequest = $message;
        
        try {
            #cria o arquivo com o conteudo xml
            $tmpfname = tempnam(sys_get_temp_dir(), "xml");
            $handle   = fopen($tmpfname, "w");
            fwrite($handle, $message);
            fclose($handle);

            $arquivo = curl_file_create($tmpfname);

            $data['login']  = $this->config->login;
            $data['senha']  = $this->config->senha;
            $data['cidade'] = $this->config->cod_tom_municipio;
            $data['f1']     = $arquivo;

            $action = '';
            //Realiza a request REST
            $response = $this->soap->send(
                $this->url,
                $this->method,
                $action,
                $this->soapversion,
                $data,
                $this->namespaces,
                ''
            );

            return $response;
        } catch (\Throwable $th) {
            throw $th;
        } finally {
            #com sucesso ou com erro, sempre apagar o arquivo temporario na propria request
            unlink($tmpfname);
        }
    }
}
