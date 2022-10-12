<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Traits\ApiResponser;
use App\Traits\Bridge\SSHBridge;
use App\Traits\LoadMessages;
use Illuminate\Http\Request;


class DatacomController extends Controller
{
    use SSHBridge, LoadMessages, ApiResponser;

    
    private function checkDocumentation($sshConsult)
    {
        // Se houver erro de comunicação.
        if ($sshConsult == "E00160") return $this->error($this->getMessage("apierror", "ErrorTryingInitiateConnectionHost"), 200);

        // Se houver erro de autenticação.
        if ($sshConsult == "E00161") return $this->error($this->getMessage("apierror", "ErrorSSHCredentials"), 200);

        // Se houver erro na saída do comando.
        if ($sshConsult == "E00171") return $this->error($this->getMessage("apierror", "ErrorStdOut"), 200);

        // Se houve um erro de sintax no comando.
        if ($sshConsult == "E00172") return $this->error($this->getMessage("apierror", "ErrorSintaxCommand"), 200);

        // Retorna erro se nenhuma entrada for encontrada para o comando executado.
        if ($sshConsult == "E00173") return $this->error($this->getMessage("apierror", "ErrorNoEntriesFound"), 200);
    }

    /**
     * Método de consulta de PONs.
     * Retorna total de ONUs Off e Total de ONUs On em valor de porcentagem.
     * Retorna o total de ONUs na PON.
     *
     * @param Request $request
     * @return Array
     */
    public function loadPons(Request $request)
    {
        // Consulta.
        $sshConsult = $this->bridgeLoadPons($request);

        // Verifica se algum erro é gerado no resultado, de acordo com a Documentação de erros.
        $this->checkDocumentation($sshConsult);

        // Tratar dados...
        $sshArray = explode("\n", $sshConsult);
        $totalDown = 0; // Totaliza quantas ONUs estão off.
        $totalUp = 0; // Totaliza quantas ONUs estão on.
        $total = 0; // Totaliza quantas ONUs estão visiveis a serem tratadas.

        // Percorrendo dados recebidos e realizando o tratamento.
        foreach ($sshArray as $line) {

            // Contagem de quantas ONUs estão Off.
            if (\str_contains($line, "Down")) {
                if (!\str_contains($line, "Serial Number")) {
                    $totalDown+=1;
                    $total+=1;
                }
            }
            // Contagem de quantas ONUs estão On.
            elseif (\str_contains($line, "Up")) {
                if (!\str_contains($line, "Serial Number")) {
                    $totalUp+=1;
                    $total+=1;
                }
            }

        }

        // Dados de retorno.
        $responseStatus = [
            "totalONUs" => $total,
            "totalUp"=> floatval(round((($totalUp/$total)*100), 2)),
            "totalDown" => floatval(round((($totalDown/$total)*100), 2)),
        ];

        // Retornar resultado do processamento.
        return $this->success($responseStatus);
    }

    /**
     * Undocumented function
     * OLT: Datacom
     * @param Request $request
     * @return void
     */
    public function discoveryPonsDatacom(Request $request)
    {
        // Consulta.
        $sshConsult = $this->bridgeDiscoveryPonsDatacom($request);

        // Verifica se algum erro é gerado no resultado, de acordo com a Documentação de erros.
        $this->checkDocumentation($sshConsult);

        // Tratar dados...
        $sshArray = explode("\n", $sshConsult);
        $newSSHArray = [];
        foreach ($sshArray as $line) {
            if (\str_contains($line, "Physical interface")) {
                if (\str_contains($line, "Enabled") && \str_contains($line, "link is Up")) {
                    $tmp01 = explode(",", $line)[0];
                    $tmp02 = explode(":", $tmp01)[1];
                    $tmp03 = explode("gpon ", $tmp02)[1];
                    $tmp04 = explode("/", $tmp03)[2];
                    \array_push($newSSHArray, ["pon" => $tmp04]);
                }
            }
        }

        // Retornar resultado do processamento.
        return $this->success($newSSHArray);
    }


    public function loadAlarmsInPons(Request $request)
    {
        // Consulta.
        $sshConsult = $this->bridgeLoadAlarmsInPons($request);

        // Verifica se algum erro é gerado no resultado, de acordo com a Documentação de erros.
        $this->checkDocumentation($sshConsult);

        // Tratar dados...
        /// GPON_LOSi - ID de Alarms do tipo LOSS.
        /// GPON_DGi - ID de Alarms do tipo energia .
        $sshArray = explode("\n", $sshConsult);
        $totalGPON_LOSi = 0;
        $totalGPON_DGi = 0;
        foreach ($sshArray as $line) {
            if (\str_contains($line, "GPON_DGi")) {
                $totalGPON_DGi+=1;
            }

            if (\str_contains($line, "GPON_LOSi")) {
                $totalGPON_LOSi+=1;
            }
        }

        // Dados de retorno.
        $responseStatus = [
            "GPON_LOSi" => $totalGPON_LOSi,
            "GPON_DGi" => $totalGPON_DGi,
        ];

        // Retornar resultado do processamento.
        return $this->success($responseStatus);
    }

}

