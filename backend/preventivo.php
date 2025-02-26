<?php
require_once 'fpdf/fpdf.php';
require_once 'backend/database.php';
require_once 'backend/cantieri.php';

class PreventivoPDF extends FPDF
{
    public function Header()
    {
        // Logo
        $this->SetFillColor(31, 73, 125);
        $this->SetTextColor(255, 255, 255);
        $this->SetFont('Arial', 'B', 12);
        $this->Cell(120, 8, 'SN Infissi', 0, 1, 'L', true);
        $this->SetFont('Arial', '', 8);
        $this->Cell(120, 8, 'Di Nocentini Stefano', 0, 1, 'L', true);

        // Preventivo
        $this->SetXY(130, 10);
        $this->SetFillColor(31, 73, 125);
        $this->SetTextColor(255, 255, 255);
        $this->SetFont('Arial', 'B', 12);
        $this->Cell(60, 16, 'Preventivo', 0, 1, 'R', true);

        // Spazio
        $this->Ln(5);

        // Intestazione
        $this->SetFont('Arial', '', 10);
        $this->SetTextColor(0, 0, 0);
        $this->Cell(100, 5, 'Corso Arnaldo Fraccaroli, 102, 37049 Villa Bartolomea (VR)', 0, 1);
        $this->Cell(100, 5, 'P.Iva: 02341520514 | N.Rea: VR-455532', 0, 1);
        $this->Cell(100, 5, 'Cell./W.App: +39 3773480855', 0, 1);
        $this->Cell(100, 5, 'Pec: stefano.nocentini@pec.it', 0, 1);
        $this->Cell(100, 5, 'Email: stefano.nocentini@gmail.com', 0, 1);

        // Data
        $this->SetXY(130, 20);
        $this->SetFont('Arial', '', 10);
        $this->SetXY(130, 20);
        $this->Cell(60, 26, 'Data: ' . date('d/m/Y'), 0, 1, 'R');

        // Offerta
        $this->SetXY(130, 35);
        $this->Cell(60, 5, 'Offerta n.: ' . rand(1000, 9999), 0, 1, 'R');
    }

    public function Footer()
    {
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        $this->Cell(0, 10, 'Pagina ' . $this->PageNo(), 0, 0, 'C');
    }
}

class Preventivo
{
    private $db;
    private $cantieri;

    public function __construct()
    {
        $this->db = (new Database())->connect();
        $this->cantieri = new Cantieri($this->db);
    }

    public function generaPDF($idCantiere)
    {
        if (!$idCantiere) {
            die("ID cantiere mancante");
        }

        $dettagli = $this->cantieri->getDettagli($idCantiere);
        $cantiere = $dettagli['cantiere'];
        $voci = $dettagli['voci'];

        $pdf = new PreventivoPDF();
        $pdf->AddPage();
        $pdf->SetFont('Arial', '', 10);

        // Cliente e cantiere
        $pdf->SetXY(130, 45);
        $pdf->Cell(60, 5, 'Cantiere di: ' . mb_convert_encoding($cantiere['comune_nome'] . ' (' . $cantiere['provincia_nome'] . ')', "ISO-8859-1", "UTF-8"), 0, 1, 'R');
        $pdf->SetXY(130, 51);
        $pdf->Cell(60, 5, 'Cliente: ' . mb_convert_encoding($cantiere['cliente_nome'] . ' ' . $cantiere['cliente_cognome'], "ISO-8859-1", "UTF-8"), 0, 1, 'R');
        

        $pdf->Ln(10);
        $pdf->SetFillColor(31, 73, 125);
        $pdf->SetTextColor(255, 255, 255);
        $pdf->SetDrawColor(194, 201, 209);
        $pdf->SetFont('Arial', 'B', 10);

        // Intestazione tabella
        $pdf->Cell(20, 8, 'Q.ta', 1, 0, 'C', true);
        $pdf->Cell(100, 8, 'Descrizione', 1, 0, 'C', true);
        $pdf->Cell(30, 8, 'Prezzo ('.chr(128).')', 1, 0, 'C', true);
        $pdf->Cell(30, 8, 'Importo ('.chr(128).')', 1, 1, 'C', true);

        $pdf->SetFont('Arial', '', 10);
        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetFillColor(242, 242, 242);
        $pdf->SetDrawColor(194, 201, 209);
        $fill = false;

        foreach ($voci as $voce) {
            $importo = $voce['quantita'] * $voce['prezzo'];
            $pdf->Cell(20, 6, $voce['quantita'], 1, 0, 'C', $fill);
            $pdf->Cell(100, 6, mb_convert_encoding($voce['nome_voce'], "ISO-8859-1", "UTF-8"), 1, 0, 'L', $fill);
            $pdf->Cell(30, 6, number_format($voce['prezzo'], 2, ',', '.') . ' '.chr(128), 1, 0, 'R', $fill);
            $pdf->Cell(30, 6, number_format($importo, 2, ',', '.') . ' '.chr(128), 1, 1, 'R', $fill);
            $fill = !$fill;
        }

        // Totali
        $pdf->Ln(5);
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->Cell(150, 7, 'Subtotale:', 0, 0, 'R');
        $pdf->Cell(30, 7, number_format($cantiere['importo_totale'], 2, ',', '.') . ' '.chr(128), 1, 1, 'R');
        $pdf->Cell(150, 7, 'IVA 0,00%:', 0, 0, 'R');
        $pdf->Cell(30, 7, '0,00 '.chr(128), 1, 1, 'R');
        $pdf->Cell(150, 7, 'Totale:', 0, 0, 'R');
        
        $pdf->SetFillColor(31, 73, 125);
        $pdf->SetTextColor(255, 255, 255);
        $pdf->Cell(30, 7, number_format($cantiere['importo_totale'], 2, ',', '.') . ' '.chr(128), 1, 1, 'R', true);

        $pdf->Output();
    }
}
