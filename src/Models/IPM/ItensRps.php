<?php 

namespace NFePHP\NFSe\Models\IPM;

use NFePHP\NFSe\Models\IPM\Rps;
use Respect\Validation\Validator;

/**
 * @author Tiago Franco
 * Definicao das informacoes da tag itens para geracao das notas
 */
class ItensRps
{
 
    /**
     * @var int
     */
    public $infCodigoLocalPrestacaoServico;

    /**
     * @var int
     */
    public $infTributaMunicipioPrestador;

    /**
     * @var int
     */
    public $infUnidadeCodigo;

    /**
     * @var float
     */
    public $infUnidadeQuantidade;

    /**
     * @var float
     */
    public $infUnidadeValorUnitario;

    /**
     * @var int
     */
    public $infCodigoItemListaServico;
    /**
     * @var string
     */
    public $infDescritivo;
    /**
     * @var float
     */
    public $infAliquotaItemListaServico;
    /**
     * @var float
     */
    public $infSituacaoTributaria;
    /**
     * @var float
     */
    public $infValorTributavel;
    /**
     * @var float
     */
    public $infValorDeducao;
    /**
     * @var float
     */
    public $infValorIssrf;
    /**
     * Set Codigo TOM county code where service was realized Receita Federal
     * @param int $value
     * @param string $campo - String com o nome do campo caso queira mostrar na mensagem de validação
     * @throws InvalidArgumentException
     */
    public function codigoLocalPrestacaoServico($value, $campo = null)
    {
        if (!$campo) {
            $msg = "Deve ser passado o código TOM junto a receita federal.";
        } else {
            $msg = "O item '$campo' deve ser inteiro, referente ao código TOM junto a receita federal. Informado: '$value'";
        }

        if (!Validator::numeric()->intVal()->validate($value)) {
            throw new \InvalidArgumentException($msg);
        }
        $this->infCodigoLocalPrestacaoServico = $value;
    }

    /**
     * Set opting for Simple National tax regime
     * @param int $value
     * @param string $campo - String com o nome do campo caso queira mostrar na mensagem de validação
     * @throws InvalidArgumentException
     */
    public function tributaMunicipioPrestador($value = Rps::SIM, $campo = null)
    {
        if (!$campo) {
            $msg = "Tributa municipio prestador deve ser 1 ou 2.";
        } else {
            $msg = "O item '$campo' deve ser 1 ou 2. Informado: '$value'";
        }

        if (!Validator::numeric()->intVal()->between(1, 2)->validate($value)) {
            throw new \InvalidArgumentException($msg);
        }
        $this->infTributaMunicipioPrestador = $value;
    }

    /**
     * Set opting for Code Unidad
     * @param int $value
     * @param string $campo - String com o nome do campo caso queira mostrar na mensagem de validação
     * @throws InvalidArgumentException
     */
    public function unidadeCodigo($value, $campo = null)
    {
        if (!$campo) {
            $msg = "Unidade Codigo deve ser númerico e possuir até 9 dígitos.";
        } else {
            $msg = "O item '$campo' deve ser númerico e possuir até 9 dígitos. Informado: '$value'";
        }

        if (!Validator::numeric()->intVal()->length(1, 9)->validate($value)) {
            throw new \InvalidArgumentException($msg);
        }
        $this->infUnidadeCodigo = $value;
    }

    /**
     * Set opting for Code Unidad
     * @param float $value
     * @param string $campo - String com o nome do campo caso queira mostrar na mensagem de validação
     * @throws InvalidArgumentException
     */
    public function unidadeQuantidade($value = 0.00, $campo = null)
    {
        if (!$campo) {
            $msg = "Os valores devem ser numericos tipo float.";
        } else {
            $msg = "O item '$campo' deve ser numérico tipo float. Informado: '$value'";
        }

        if (!Validator::numeric()->floatVal()->min(0)->validate($value)) {
            throw new \InvalidArgumentException($msg);
        }
        $this->infUnidadeQuantidade = $this->getValorFormatado($value);
    }

    /**
     * Set opting for Code Unidad
     * @param float $value
     * @param string $campo - String com o nome do campo caso queira mostrar na mensagem de validação
     * @throws InvalidArgumentException
     */
    public function unidadeValorUnitario($value = 0.00, $campo = null)
    {
        if (!$campo) {
            $msg = "Os valores devem ser numericos tipo float.";
        } else {
            $msg = "O item '$campo' deve ser numérico tipo float. Informado: '$value'";
        }

        if (!Validator::numeric()->floatVal()->min(0)->validate($value)) {
            throw new \InvalidArgumentException($msg);
        }
        $this->infUnidadeValorUnitario = $this->getValorFormatado($value);
    }

    /**
     * Set opting for Code Unidad
     * @param int $value
     * @param string $campo - String com o nome do campo caso queira mostrar na mensagem de validação
     * @throws InvalidArgumentException
     */
    public function codigoItemListaServico($value, $campo = null)
    {
        if (!$campo) {
            $msg = "O codigo do subitem da lista de serviços.";
        } else {
            $msg = "O item '$campo' deve ser inteiro, referente a subitem da lista de serviços. Informado: '$value'";
        }

        if (!Validator::numeric()->intVal()->validate($value)) {
            throw new \InvalidArgumentException($msg);
        }
        $this->infCodigoItemListaServico = $value;
    }

    /**
     * Set opting for Code Unidad
     * @param int $value
     * @param string $campo - String com o nome do campo caso queira mostrar na mensagem de validação
     * @throws InvalidArgumentException
     */
    public function descritivo($value, $campo = null)
    {
        if (!$campo) {
            $msg = "Descritivo coloquial do serviço prestado nao pode ser vazio e deve ter até 1000 caracteres";
        } else {
            $msg = "O item '$campo' deve ser númerico e possuir até 9 dígitos. Informado: '$value'";
        }

        if (!Validator::length(1, 1000)->validate($value)) {
            throw new \InvalidArgumentException($msg);
        }
        $this->infDescritivo = $value;
    }

    /**
     * Set opting for Code Unidad
     * @param float $value
     * @param string $campo - String com o nome do campo caso queira mostrar na mensagem de validação
     * @throws InvalidArgumentException
     */
    public function aliquotaItemListaServico($value = 0.00, $campo = null)
    {
        if (!$campo) {
            $msg = "Os valores devem ser numericos tipo float.";
        } else {
            $msg = "O item '$campo' deve ser numérico tipo float. Informado: '$value'";
        }

        if (!Validator::numeric()->floatVal()->min(0)->validate($value)) {
            throw new \InvalidArgumentException($msg);
        }
        $this->infAliquotaItemListaServico = $this->getValorFormatado($value);
    }

    /**
     * Set opting for Code Unidad
     * @param int $value
     * @param string $campo - String com o nome do campo caso queira mostrar na mensagem de validação
     * @throws InvalidArgumentException
     */
    public function situacaoTributaria($value, $campo = null)
    {
        if (!$campo) {
            $msg = "Código da situacao tributaria deve ser númerico e possuir até 4 digitos";
        } else {
            $msg = "O item '$campo' deve ser númerico e possuir até 4 dígitos. Informado: '$value'";
        }

        if (!Validator::numeric()->length(1, 4)->validate($value)) {
            throw new \InvalidArgumentException($msg);
        }
        $this->infSituacaoTributaria = $value;
    }

    /**
     * Set opting for Code Unidad
     * @param float $value
     * @param string $campo - String com o nome do campo caso queira mostrar na mensagem de validação
     * @throws InvalidArgumentException
     */
    public function valorTributavel($value = 0.00, $campo = null)
    {
        if (!$campo) {
            $msg = "Os valores devem ser numericos tipo float.";
        } else {
            $msg = "O item '$campo' deve ser numérico tipo float. Informado: '$value'";
        }

        if (!Validator::numeric()->floatVal()->min(0)->validate($value)) {
            throw new \InvalidArgumentException($msg);
        }
        $this->infValorTributavel = $this->getValorFormatado($value);
    }

    /**
     * Set opting for Code Unidad
     * @param float $value
     * @param string $campo - String com o nome do campo caso queira mostrar na mensagem de validação
     * @throws InvalidArgumentException
     */
    public function valorDeducao($value = 0.00, $campo = null)
    {
        if (!$campo) {
            $msg = "Os valores devem ser numericos tipo float.";
        } else {
            $msg = "O item '$campo' deve ser numérico tipo float. Informado: '$value'";
        }

        if (!Validator::numeric()->floatVal()->min(0)->validate($value)) {
            throw new \InvalidArgumentException($msg);
        }
        $this->infValorDeducao = $this->getValorFormatado($value);
    }

    /**
     * Set opting for Code Unidad
     * @param float $value
     * @param string $campo - String com o nome do campo caso queira mostrar na mensagem de validação
     * @throws InvalidArgumentException
     */
    public function valorIssrf($value = 0.00, $campo = null)
    {
        if (!$campo) {
            $msg = "Os valores devem ser numericos tipo float.";
        } else {
            $msg = "O item '$campo' deve ser numérico tipo float. Informado: '$value'";
        }

        if (!Validator::numeric()->floatVal()->min(0)->validate($value)) {
            throw new \InvalidArgumentException($msg);
        }
        $this->infValorIssrf = $this->getValorFormatado($value);
    }

    private function getValorFormatado($value)
    {
        return \number_format(round($value, 2), 2, ',', '');
    }
}
