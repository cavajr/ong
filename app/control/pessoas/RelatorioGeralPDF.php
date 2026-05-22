<?php

class RelatorioGeralPDF extends FPDF
{
    function Header()
    {
        /*
        |--------------------------------------------------------------------------
        | LOGO
        |--------------------------------------------------------------------------
        */

        $logo = 'app/images/logo1.jpeg';

        if (file_exists($logo))
        {
            $this->Image($logo, 10, 8, 15);
        }

        /*
        |--------------------------------------------------------------------------
        | TITULO
        |--------------------------------------------------------------------------
        */

        $this->SetFont('Arial', 'B', 16);

        $this->Cell(
            0,
            10,
            mb_convert_encoding(
                'RELATÓRIO GERAL',
                'ISO-8859-1',
                'UTF-8'
            ),
            0,
            1,
            'C'
        );

        $this->Ln(10);

        /*
        |--------------------------------------------------------------------------
        | HEADER GRID
        |--------------------------------------------------------------------------
        */

        $this->SetFillColor(89, 89, 89);

        $this->SetTextColor(255, 255, 255);

        $this->SetFont('Arial', 'B', 7);

        $this->Cell(20, 6, 'TIPO', 1, 0, 'C', true);

        $this->Cell(
            70,
            6,
            mb_convert_encoding('NOME', 'ISO-8859-1', 'UTF-8'),
            1,
            0,
            'L',
            true
        );

        $this->Cell(20, 6, 'CPF', 1, 0, 'L', true);

        $this->Cell(20, 6, 'NIS', 1, 0, 'L', true);

        $this->Cell(
            50,
            6,
            mb_convert_encoding('BAIRRO', 'ISO-8859-1', 'UTF-8'),
            1,
            0,
            'L',
            true
        );

        $this->Cell(
            70,
            6,
            mb_convert_encoding('CIDADE', 'ISO-8859-1', 'UTF-8'),
            1,
            0,
            'L',
            true
        );

        $this->Cell(
            20,
            6,
            mb_convert_encoding('TELEFONE', 'ISO-8859-1', 'UTF-8'),
            1,
            1,
            'L',
            true
        );

        $this->Ln(1);
    }

    function Footer()
    {
        $this->SetY(-10);

        $this->SetFont('Arial', 'I', 7);

        $this->Cell(
            0,
            5,
            mb_convert_encoding(
                'Página ' . $this->PageNo(),
                'ISO-8859-1',
                'UTF-8'
            ),
            0,
            0,
            'C'
        );
    }
}